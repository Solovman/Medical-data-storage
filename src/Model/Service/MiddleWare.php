<?php

namespace N_ONE\App\Model\Service;

use Closure;
use N_ONE\App\Application;
use N_ONE\Core\Routing\Route;
use N_ONE\Core\Routing\Router;

class MiddleWare
{
	public static function adminMiddleware(callable $action): Closure
	{
		return static function(Route $route) use ($action) {
			$di = Application::getDI();
			$userRepo = $di->getComponent('userRepository');
			session_start();
			if (!isset($_SESSION['user_id'])) //Проверяем есть ли сессия с пользователем
			{
				if (isset($_COOKIE['remember_me'])) //проверяем есть ли кука с токеном
				{
					$userId = $userRepo->getUserByToken($_COOKIE['remember_me']); //Ищем пользователя по токену
					if ($userId) //Пользователь есть, начинаем/продляем сессию
					{
						$_SESSION['user_id'] = $userId;
					}
				}
				if (!isset($_SESSION['user_id'])) //Пользователя нет, перенаправляем на логин
				{
					Router::redirect('/login');
					exit();
				}
			}

			return $action($route);
		};
	}
	// public static function adminMiddleware(callable $action): Closure
	// {
	// 	return static function(Route $route) use ($action) {
	// 		session_start();
	// 		if (!isset($_SESSION['user_id']))
	// 		{
	// 			Router::redirect('/login');
	// 			exit();
	// 		}
	//
	// 		return $action($route);
	// 	};
	// }

	public static function processFilters(callable $action): Closure
	{
		return static function(Route $route) use ($action) {
			$tagsToFilter = ($_GET['selectedTags']) ?? null;
			$tagGroups = null;
			if ($tagsToFilter)
			{
				$tagGroups = explode(';', $tagsToFilter);
			}

			$finalTags = [];
			if ($tagGroups)
			{
				foreach ($tagGroups as $tagGroup)
				{
					$tags = explode(':[', trim($tagGroup, '[]'));
					$parentId = ($tags[0]) ?? null;
					$childIds = ($tags[1]) ?? null;
					foreach (explode(',', $childIds) as $childId)
					{
						$finalTags[(int)$parentId][] = (int)trim($childId);
					}
				}
			}

			$attributesToFilter = ($_GET['attributes']) ?? null;
			$attributeGroups = null;
			if ($attributesToFilter)
			{
				$attributeGroups = explode(';', $attributesToFilter);
			}
			$finalAttributes = [];

			if ($attributeGroups)
			{
				foreach ($attributeGroups as $attributeGroup)
				{
					$attributes = explode('=[', trim($attributeGroup, '[]'));
					$parentId = ($attributes[0]) ?? null;
					$childIds = ($attributes[1]) ?? null;
					$range = explode('-', $childIds);
					$from = ($range[0]) ?? null;
					$to = ($range[1]) ?? null;
					$finalAttributes[(int)$parentId]['from'] = (int)$from;
					$finalAttributes[(int)$parentId]['to'] = (int)$to;
				}
			}

			$sortField = $_GET['sortOrder'] ?? null;
			if ($sortField)
			{
				$sorting = explode('-', $sortField);
			}
			$attributeId = $sorting[0] ?? null;
			$sortDirection = $sorting[1] ?? null;
			$sortOrder = ['column' => $attributeId, 'direction' => $sortDirection];
			$currentSearchRequest = $_GET['searchRequest'] ?? null;
			unset($finalTags[0], $finalAttributes[0]);

			return $action(
				$route,
				$currentSearchRequest,
				$finalTags,
				$finalAttributes,
				$sortOrder,
			);
		};
	}
}