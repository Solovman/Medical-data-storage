<?php

namespace N_ONE\Core\TemplateEngine;

use N_ONE\Core\Configurator\Configurator;
use RuntimeException;

class TemplateEngine
{
	public static function renderFinalError(): string
	{
		$errorViewFile = Configurator::option("FINAL_ERROR_PAGE");

		return self::render($errorViewFile);
	}

	public static function renderPublicError(int|string $errorCode, string $errorMessage): string
	{
		$errorViewFile = Configurator::option("PUBLIC_ERROR_PAGE");

		$variables = [
			'errorCode' => $errorCode,
			'errorMessage' => $errorMessage,
		];

		return self::render($errorViewFile, $variables);
	}

	public static function renderAdminError(int|string $errorCode, string $errorMessage): string
	{
		$errorViewFile = Configurator::option("ADMIN_ERROR_PAGE");

		$variables = [
			'errorCode' => $errorCode,
			'errorMessage' => $errorMessage,
		];

		return self::render($errorViewFile, $variables);
	}

	public static function render(string $file, array $variables = []): string
	{
		if (!preg_match('/^[0-9A-Za-z\/_-]+$/', $file))
		{
			throw new RuntimeException('Invalid template path');
		}

		$absolutePath = Configurator::option("VIEWS_PATH") . $file . ".php";
		if (!file_exists($absolutePath))
		{
			echo self::renderPublicError('404', 'Страница не найдена');
			exit(404);
		}

		extract($variables);

		ob_start();

		require $absolutePath;

		return ob_get_clean();
	}

	public static function renderTable(array $entities, int $isActive): string
	{
		$tableViewFile = 'components/table';
		$variables = [
			'entities' => $entities,
			'isActive' => $isActive,
		];

		return self::render($tableViewFile, $variables);
	}
}