<?php

namespace N_ONE\Core\Routing;

use Exception;
use N_ONE\Core\Configurator\Configurator;
use N_ONE\Core\Exceptions\NotFoundException;

class Router
{
	public array $routes = [];

	static private ?Router $instance = null;

	private function __construct()
	{
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

	public static function getInstance(): Router
	{
		if (static::$instance)
		{
			return static::$instance;
		}

		return static::$instance = new self();
	}

	public function get(string $uri, callable $action): void
	{
		$this->add('GET', $uri, $action);
	}

	public function add(string $method, string $uri, callable $action): void
	{
		// self::$routes[] = new Route($method, $uri, $action(...));
		$this->routes[] = new Route($method, $uri, function() use ($action) {
			$route = self::find($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
			if ($route instanceof Route)
			{
				return $action($route);
			}
			throw new NotFoundException("There is no route");
		});
	}

	public function find(string $method, string $uri): ?Route
	{
		$uriInArray = explode('?', $uri);
		$path = ($uriInArray[0]) ?? null;
		$getParams = ($uriInArray[1]) ?? null;
		if ($path === null)
		{
			return null;
		}
		if (str_ends_with($path, '/') && strlen($path) !== 1)
		{
			$path = rtrim($path, "/");
			if ($getParams !== null)
			{
				self::redirect($path . '?' . $getParams);
			}
			else
			{
				self::redirect($path);
			}
		}
		foreach ($this->routes as $route)
		{
			if ($route->method !== $method)
			{
				continue;
			}
			if ($route->match($path))
			{
				return $route;
			}
		}

		return null;
	}

	public function post(string $uri, callable $action): void
	{
		$this->add('POST', $uri, $action);
	}

	public static function redirect($url): void
	{
		$host = Configurator::option('HOST_NAME');
		header("Location: http://$host$url");
	}
}