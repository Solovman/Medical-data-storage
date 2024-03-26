<?php

namespace N_ONE\Core\Migrator;

use DateTime;
use Exception;
use mysqli;
use N_ONE\Core\Configurator\Configurator;
use N_ONE\Core\DbConnector\DbConnector;
use N_ONE\Core\Exceptions\DatabaseException;
use N_ONE\Core\Exceptions\FileException;

class Migrator
{
	static private ?Migrator $instance = null;

	private mysqli|false $connection;

	private string $migrationTable;

	private string $migrationPath;

	/**
	 * @throws DatabaseException
	 */
	private function __construct()
	{
		$this->connection = DbConnector::getInstance()->getConnection();
		$this->migrationTable = Configurator::option('MIGRATION_TABLE');
		$this->migrationPath = Configurator::option('MIGRATION_PATH');
	}

	private function __clone()
	{
	}

	/**
	 * @throws Exception
	 */
	public function __wakeup()
	{
		throw new Exception("Cannot unserialize singleton");
	}

	public static function getInstance(): Migrator
	{
		if (static::$instance)
		{
			return static::$instance;
		}

		return static::$instance = new self();
	}

	/**
	 * @throws FileException
	 * @throws DatabaseException
	 */
	public function migrate(): void
	{
		// 1. смотрим последнюю применённую миграцию, которая записана в таблице migration (если таблица пуста то делаем все миграции)
		$lastMigration = $this->getLastMigration();

		// 2. проходимся по /core/Migration/migrations и ищем новые миграции
		$newMigrations = $this->findNewMigrations($lastMigration);

		// 3. выполняем новые миграции
		foreach ($newMigrations as $migration)
		{
			$this->executeMigration($migration);
			$this->updateLastMigration($migration);
		}
	}

	/**
	 * @throws DatabaseException
	 */
	private function getLastMigration()
	{
		$connection = $this->connection;

		$tableExistsQuery = mysqli_query($connection, "SHOW TABLES LIKE '$this->migrationTable'");

		if (mysqli_num_rows($tableExistsQuery) === 0)
		{
			return null; // Возвращаем null, если таблица отсутствует
		}

		$result = mysqli_query(
			$connection,
			"
			SELECT *
			FROM {$this->migrationTable}
			ORDER BY ID DESC
			LIMIT 1;"
		);

		if (!$result)
		{
			throw new DatabaseException(mysqli_error($connection));
		}

		// Если результат пустой, также возвращаем null
		return mysqli_fetch_assoc($result)["TITLE"];
	}

	private function findNewMigrations($lastMigration): array
	{
		$pattern = '/(\d{4}_\d{2}_\d{2}_\d{2}_\d{2})/';
		$migrations = [];
		$files = glob(ROOT . $this->migrationPath . '/*.sql');

		if ($lastMigration === null)
		{
			foreach ($files as $file)
			{
				$migrations[] = basename($file);
			}

			return $migrations;
		}

		preg_match($pattern, $lastMigration, $matches);
		$currentTimestamp = ($matches) ? DateTime::createFromFormat('Y_m_d_H_i', $matches[0])->getTimestamp() : 0;

		foreach ($files as $file)
		{
			$filename = basename($file);
			// Ищем соответствие паттерну в строке пути к файлу
			if (preg_match($pattern, $file, $matches))
			{
				$timestamp = DateTime::createFromFormat('Y_m_d_H_i', $matches[0])->getTimestamp();

				if ($timestamp > $currentTimestamp)
				{
					$migrations[] = $filename;
				}
			}
		}

		return $migrations;
	}

	/**
	 * @throws FileException
	 * @throws DatabaseException
	 */
	private function executeMigration($migration): void
	{
		// Получение соединения с базой данных
		$connection = $this->connection;

		// Чтение содержимого SQL файла
		$sql = file_get_contents(ROOT . $this->migrationPath . '/' . $migration);

		if (!$sql)
		{
			throw new FileException("Read migration file $migration");
		}

		$queries = explode(';', $sql);

		foreach ($queries as $query)
		{
			// Удаляем лишние пробелы и символы перевода строки
			$query = trim($query);

			if (!empty($query))
			{
				// Выполнение SQL запроса
				$result = mysqli_query($connection, $query);

				if (!$result)
				{
					throw new DatabaseException(mysqli_error($connection));
				}
			}
		}
	}

	/**
	 * @throws DatabaseException
	 */
	private function updateLastMigration($migration): void
	{
		$connection = $this->connection;

		$sql = "INSERT INTO $this->migrationTable (TITLE) VALUE ('$migration');";

		$result = mysqli_query($connection, $sql);
		if (!$result)
		{
			throw new DatabaseException(mysqli_error($connection));
		}
	}
}




