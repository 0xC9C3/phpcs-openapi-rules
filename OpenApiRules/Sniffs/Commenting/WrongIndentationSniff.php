<?php declare(strict_types = 1);

namespace OpenApiRules\Sniffs\Commenting;

use OpenApi\Analyser;
use OpenApiRules\Helpers\TokenHelper;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use OpenApiRules\Helpers\Annotation\GenericAnnotation;
use OpenApiRules\Helpers\AnnotationHelper;
use OpenApiRules\Helpers\IndentationHelper;
use function count;
use const T_DOC_COMMENT_OPEN_TAG;

class WrongIndentationSniff implements Sniff
{

	public const INVALID_FORMAT = 'InvalidFormat';
	public const INVALID_INDENTATION = 'InvalidIndentation';

	protected const CLOSING_BRACKETS = [')', '}'];
	protected const OPENING_BRACKETS = ['(', '{'];
	protected const QUOTES = ['"', '\'', '`'];

	/** @return array<int, (int|string)> */
	public function register(): array
	{
		return [T_DOC_COMMENT_OPEN_TAG];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $docCommentStartPointer
	 * @throws \Exception
	 */
	public function process(File $phpcsFile, $docCommentStartPointer): void
	{
		/** @var GenericAnnotation[] $annotations */
		$annotationsByType = AnnotationHelper::getAnnotations($phpcsFile, $docCommentStartPointer);

		if (count($annotationsByType) === 0) {
			return;
		}

		foreach ($annotationsByType as $name => $annotations) {

			if (strpos($name, '@OA\\') === false) {
				continue;
			}

			foreach ($annotations as $annotation) {
				$indentation = IndentationHelper::getIndentation($phpcsFile, $annotation->getStartPointer());
				$sourceAnnotationText = $phpcsFile->getTokensAsString(
					$annotation->getStartPointer(),
					$annotation->getEndPointer() - $annotation->getStartPointer() + 1
				);
				$sourceAnnotationText = str_replace($indentation, '', $sourceAnnotationText);

				$result = $this->formatAnnotation($annotation->export(), $phpcsFile->eolChar);

				if ($result === null) {
					$phpcsFile->addError(
						'Unable to validate OpenAPI doc',
						$annotation->getStartPointer(),
						self::INVALID_FORMAT
					);
					continue;
				}

				if ($result === $sourceAnnotationText) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(
					'Invalid OpenAPI indentation',
					$annotation->getStartPointer(),
					self::INVALID_INDENTATION
				);

				if (!$fix) {
					continue;
				}

				// check that our formatted annotations are still working for safety
				$analyser = new Analyser();
				$analysedAnnotation = $analyser->fromComment($result)[0];

				if (!$analysedAnnotation->validate()){
					throw new \Exception('Unable to format annotations.');
				}

				$phpcsFile->fixer->beginChangeset();

				$replacementStart = TokenHelper::findFirstTokenOnLine($phpcsFile, $annotation->getStartPointer());
				// remove old annotation
				for ($i = $replacementStart; $i <= $annotation->getEndPointer(); $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}

				// add formatted annotation
				$lines = explode($phpcsFile->eolChar, $result);
				$lineCount = count($lines);
				for ($i = 0; $i < $lineCount; $i++) {
					$phpcsFile->fixer->addContent($replacementStart + $i, $indentation . $lines[$i]);
					if ($i !== $lineCount -1) {
						$phpcsFile->fixer->addNewline($replacementStart + $i);
					}
				}

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * "stupid" formatting for openapi docs
	 *
	 * @param string $content
	 * @param string $eolChar
	 * @return string|null
	 */
	protected function formatAnnotation(string $content, string $eolChar): ?string
	{
		$analyser = new Analyser();
		$annotation = $analyser->fromComment($content)[0];
		if (!$annotation->validate()){
			return null;
		}

		$content = str_replace($eolChar, '', $content);

		$bracketsOpen = 0;
		$quoteOpen = null;
		$formattedContent = '';
		$contentLength = strlen($content);
		for ($i = 0; $i < $contentLength; $i++) {
			$previousChar = $i - 1 < $contentLength ? $content[$i - 1] : null;
			$char = $content[$i];
			$nextChar = $i + 1 < $contentLength ? $content[$i + 1] : null;

			// just append if inside quotes
			if ($quoteOpen !== null && !$this->isQuote($char)) {
				$formattedContent .= $char;
				continue;
			}

			// if found quote and not already inside quotes, start ignoring
			if ($this->isQuote($char) && $quoteOpen === null) {
				$quoteOpen = $char;
				$formattedContent .= $char;
				continue;
			}

			// if same quote found as current one stop ignoring
			if ($this->isQuote($char) && $quoteOpen === $char && $previousChar !== '\\') {
				$quoteOpen = null;
			}

			if ($this->isOpeningBracket($char)) {
				$bracketsOpen++;
			}

			if ($this->isClosingBracket($char)) {
				// skip unnecessary closing brackets
				if ($bracketsOpen === 0) {
					continue;
				}

				$bracketsOpen--;
			}

			$indentation = $this->getIndentation($bracketsOpen);

			if ($this->isClosingBracket($char)) {
				$formattedContent .= $this->getEol($char, $previousChar, $eolChar) . $indentation;
			}

			$formattedContent .= $char;

			if ($this->isOpeningBracket($char) || $char === ',') {
				$formattedContent .= $this->isClosingBracket($nextChar) ? '' : $this->getEol($char, $previousChar, $eolChar) . $indentation;
			}
		}

		return $formattedContent;
	}

	/**
	 * @param int $num number of times to add indentation
	 * @return string
	 */
	private function getIndentation(int $num): string
	{
		return str_repeat("   ", $num > 0 ? $num : 0);
	}

	/**
	 * @param ?string $char character to check
	 * @return bool
	 */
	private function isOpeningBracket(?string $char): bool
	{
		return in_array($char, self::OPENING_BRACKETS);
	}

	/**
	 * @param ?string $char character to check
	 * @return bool
	 */
	private function isClosingBracket(?string $char): bool
	{
		return in_array($char, self::CLOSING_BRACKETS);
	}

	/**
	 * @param ?string $char character to check
	 * @return bool
	 */
	private function isQuote(?string $char): bool
	{
		return in_array($char, self::QUOTES);
	}

	/**
	 * @param string $char current char
	 * @param string $previousChar previous char
	 * @param string $eolChar end of line character
	 * @return string
	 */
	private function getEol(string $char, string $previousChar, string $eolChar): string
	{
		$eol = '';
		if (
			$previousChar !== ',' &&
			$char !== ',' &&
			!$this->isOpeningBracket($previousChar) &&
			!$this->isOpeningBracket($char)
		) {
			$eol .= ',';
		}
		return $eol . $eolChar;
	}
}
