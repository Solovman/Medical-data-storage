<?php

namespace N_ONE\App\Model\Repository;

use mysqli_sql_exception;
use N_ONE\App\Model\Entity;
use N_ONE\Core\DbConnector\DbConnector;
use N_ONE\Core\Exceptions\DatabaseException;

abstract class Repository
{
	public function __construct(protected readonly DbConnector $dbConnection)
	{
	}

	abstract public function getById(int $id, bool $isPublic = false): ?Entity;

	abstract public function add(Entity $entity): int;

	abstract public function update(Entity $entity): bool;

	/**
	 * @return Entity[]
	 */
	abstract public function getList(array $filter = null): array;

	/**
	 * @throws DatabaseException
	 * @throws mysqli_sql_exception
	 */
	public function changeActive(string $entities, int $entityId, int $isActive): bool
	{
		$connection = $this->dbConnection->getConnection();
		$entities = mysqli_real_escape_string($connection, $entities);

		$result = mysqli_query(
			$connection,
			"
			UPDATE N_ONE_$entities 
			SET IS_ACTIVE = $isActive
			WHERE ID = $entityId"
		);

		if (!$result)
		{
			throw new DatabaseException(mysqli_error($connection));
		}

		return true;
	}

	/**
	 * @throws DatabaseException
	 */
	public function reactivate(string $entities, int $entityId): bool
	{
		$connection = $this->dbConnection->getConnection();
		$entities = mysqli_real_escape_string($connection, $entities);

		$result = mysqli_query(
			$connection,
			"
			UPDATE N_ONE_$entities 
			SET IS_ACTIVE = 1
			WHERE ID = $entityId"
		);

		if (!$result)
		{
			throw new DatabaseException(mysqli_error($connection));
		}

		return true;
	}
}