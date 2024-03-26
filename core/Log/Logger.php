<?php

namespace N_ONE\Core\Log;

use Exception;
use N_ONE\Core\Configurator\Configurator;

class Logger
{
	private static string $rootLogDir;

	public static function setRootLogDir(string $rootLogDir): void
	{
		self::$rootLogDir = $rootLogDir;
	}

	public static function log(string $level, string $message, string $file, string $line): void
	{
		$logFile = self::$rootLogDir . date('Y-m-d') . '.log';
		$time = date('H:i:s');
		$logEntry = "[$time][$level][$file, line $line] $message" . PHP_EOL;

		file_put_contents($logFile, $logEntry, FILE_APPEND);
	}

	public static function info(string $message, string $file, string $line): void
	{
		self::log('info', $message, $file, $line);
	}

	public static function notice(string $message, string $file, string $line): void
	{
		self::log('notice', $message, $file, $line);
	}

	public static function warning(string $message, string $file, string $line): void
	{
		self::log('warning', $message, $file, $line);
	}

	public static function error(string $message, string $file, string $line): void
	{
		self::log('error', $message, $file, $line);
	}
}