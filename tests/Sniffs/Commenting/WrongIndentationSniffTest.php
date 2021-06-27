<?php declare(strict_types = 1);

namespace OpenApiRules\Sniffs\Commenting;

use OpenApiRules\Sniffs\TestCase;

class WrongIndentationSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/wrongIndentationSniffNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/wrongIndentationSniffErrors.php');



		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 7, WrongIndentationSniff::INVALID_INDENTATION);
		self::assertSniffError($report, 23, WrongIndentationSniff::INVALID_INDENTATION);
		self::assertSniffError($report, 89, WrongIndentationSniff::INVALID_INDENTATION);
		self::assertSniffError($report, 128, WrongIndentationSniff::INVALID_INDENTATION);
		self::assertSniffError($report, 165, WrongIndentationSniff::INVALID_FORMAT);

		self::assertAllFixedInFile($report);
	}

}
