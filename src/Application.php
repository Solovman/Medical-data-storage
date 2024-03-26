<?php

namespace N_ONE\App;

use mysqli;
use N_ONE\Core\Configurator\Configurator;
use N_ONE\Core\DbConnector\DbConnector;
use N_ONE\Core\DependencyInjection\DependencyInjection;
use N_ONE\Core\Exceptions\DatabaseException;
use N_ONE\Core\Log\Logger;
use N_ONE\Core\Routing\Router;
use N_ONE\Core\TemplateEngine\TemplateEngine;

class Application
{
	private static ?DependencyInjection $di = null;

	public static function run(): void
	{

		error_reporting(0);
		Logger::setRootLogDir(Configurator::option("ROOT_LOG_DIR"));
		try
		{
			self::createDatabase();
			DbConnector::getInstance();
		}
		catch (DatabaseException $e)
		{
			Logger::error("Failed to create database connection", $e->getFile(), $e->getLine());
			echo TemplateEngine::renderFinalError();
			exit();
		}

		if (self::$di === null)
		{
			$di = new DependencyInjection(Configurator::option('SERVICES_PATH'));
			self::$di = $di;
		}
		if (Configurator::option('MIGRATION_NEEDED'))
		{
			$migrator = self::$di->getComponent('migrator');
			$migrator->migrate();
		}


		$router = Router::getInstance();
		$route = $router->find($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

		if (!$route)
		{
			http_response_code(404);
			echo(TemplateEngine::renderPublicError(404, "Page not found"));
			exit;
		}
		$action = $route->action;
		$variables = $route->getVariables();
		echo $action(...$variables);
	}

	public static function getDI(): DependencyInjection
	{
		if (self::$di === null)
		{
			$di = new DependencyInjection(Configurator::option('SERVICES_PATH'));

			return self::$di = $di;
		}

		return self::$di;
	}

	private static function setDI($di): void
	{
		self::$di = $di;
	}

	/**
	 * @throws DatabaseException
	 */
	private static function createDatabase(): void
	{
		$servername = Configurator::option('DB_OPTIONS')['DB_HOST'];
		$username = Configurator::option('DB_OPTIONS')['DB_USER'];
		$password = Configurator::option('DB_OPTIONS')['DB_PASSWORD'];
		$dbname = Configurator::option('DB_OPTIONS')['DB_NAME'];

		// Создание подключения к серверу MySQL
		$connection = new mysqli($servername, $username, $password);

		$result = mysqli_query(
			$connection,
			"SELECT SCHEMA_NAME 
			FROM INFORMATION_SCHEMA.SCHEMATA 
			WHERE SCHEMA_NAME = '$dbname'"
		);

		if (mysqli_num_rows($result) === 0)
		{
			$result = mysqli_query(
				$connection,
				"CREATE DATABASE $dbname;"
			);
		}

		if (!$result)
		{
			throw new DatabaseException(mysqli_error($connection));
		}

		// Закрытие соединения
		$connection->close();
	}
}
