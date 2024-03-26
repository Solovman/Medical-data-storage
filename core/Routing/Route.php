<?php

namespace N_ONE\Core\Routing;

use Closure;

class Route
{
	private array $variables = [];

	public function __construct(
		public string  $method,
		public string  $uri,
		public Closure $action
	)
	{
	}

	public function match($uri): bool
	{
		$regexpVar = '([A-Za-z0-9_-]+)';
		$regexp = '#^' . preg_replace('(:[A-Za-z]+)', $regexpVar, $this->uri) . '$#';

		$result = preg_match($regexp, $uri, $matches);

		if ($result)
		{
			array_shift($matches);
			$this->variables = $matches;
		}

		return $result;
	}

	public function getVariables(): array
	{
		return $this->variables;
	}
}