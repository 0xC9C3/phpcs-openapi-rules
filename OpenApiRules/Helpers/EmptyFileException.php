<?php declare(strict_types = 1);

namespace OpenApiRules\Helpers;

use Exception;
use Throwable;
use function sprintf;

/**
 * @internal
 */
class EmptyFileException extends Exception
{

	/** @var string */
	private $filename;

	public function __construct(string $filename, ?Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'File %s is empty',
			$filename
		), 0, $previous);

		$this->filename = $filename;
	}

	public function getFilename(): string
	{
		return $this->filename;
	}

}
