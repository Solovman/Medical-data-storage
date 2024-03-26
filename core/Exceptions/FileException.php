<?php

namespace N_ONE\Core\Exceptions;

use Exception;
use Throwable;

class FileException extends Exception
{
	public function __construct(string $needed, int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct("$needed is failed", $code, $previous);
	}
}