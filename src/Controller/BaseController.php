<?php

namespace N_ONE\App\Controller;

use mysqli_sql_exception;
use N_ONE\App\Model\Repository\AttributeRepository;
use N_ONE\App\Model\Repository\ImageRepository;
use N_ONE\App\Model\Repository\ItemRepository;
use N_ONE\App\Model\Repository\OrderRepository;
use N_ONE\App\Model\Repository\RepositoryFactory;
use N_ONE\App\Model\Repository\TagRepository;
use N_ONE\App\Model\Repository\UserRepository;
use N_ONE\App\Model\Service\ImageService;
use N_ONE\App\Model\Service\TagService;
use N_ONE\Core\Exceptions\DatabaseException;
use N_ONE\Core\Log\Logger;
use N_ONE\Core\TemplateEngine\TemplateEngine;

abstract class BaseController
{
	public function __construct()
	{
	}

	public function renderPublicView($content): string
	{
		return TemplateEngine::render('layouts/publicLayout', [
			'content' => $content,
		]);
	}

	public function renderAdminView($content): string
	{
		if (session_status() === PHP_SESSION_NONE)
		{
			session_start();
		}

		try
		{
			$user = $this->userRepository->getById($_SESSION['user_id'], true);
		}
		catch (DatabaseException $e)
		{
			Logger::error("Failed to fetch data from repository", $e->getFile(), $e->getLine());

			return TemplateEngine::renderAdminError(';(', "Что-то пошло не так");
		}
		catch (mysqli_sql_exception $e)
		{
			Logger::error("Failed to run query", $e->getFile(), $e->getLine());

			return TemplateEngine::renderPublicError(";(", "Что-то пошло не так");
		}

		return TemplateEngine::render('layouts/adminLayout', [
			'user' => $user,
			'content' => $content,
		]);
	}
}