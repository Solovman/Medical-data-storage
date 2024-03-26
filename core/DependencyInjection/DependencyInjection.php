<?php

namespace N_ONE\Core\DependencyInjection;

use Exception;
use http\Exception\InvalidArgumentException;
use N_ONE\Core\Log\Logger;
use N_ONE\Core\TemplateEngine\TemplateEngine;
use ReflectionClass;

class DependencyInjection
{
	private array $components = [];

	private string $configurationPath;

	public function __construct(string $configurationPath)
	{
		$this->configurationPath = $configurationPath;
		$this->configure();
	}

	private function configure(): void
	{
		if (!file_exists($this->configurationPath))
		{
			return;
		}

		$configuration = simplexml_load_string(file_get_contents($this->configurationPath));

		foreach ($configuration as $service)
		{
			$arguments = [];
			$serviceName = (string)$service['name'];
			$className = (string)$service->class['name'];
			$isSingleton = (bool)$service->class['isSingleton'];

			foreach ($service->class as $class)
			{
				foreach ($class->arg as $arg)
				{
					$serviceArgument = (string)$arg['service'];
					if ($serviceArgument)
					{
						$arguments[] = [
							'service' => $serviceArgument,
						];
					}
				}
			}

			$this->components[$serviceName] = function() use ($className, $arguments, $isSingleton) {
				$loadedArguments = [];
				foreach ($arguments as $argument)
				{
					if ($argument['service'])
					{
						$loadedArguments[] = $this->getComponent($argument['service']);
					}
				}

				if ($isSingleton)
				{
					try
					{
						return $className::getInstance();
					}
					catch (Exception)
					{
						Logger::error("Failed to create singleton class", __METHOD__);

						return TemplateEngine::renderFinalError();
					}
				}

				return (new ReflectionClass($className))->newInstanceArgs($loadedArguments);
			};
		}
	}

	public function getComponent(string $serviceName)
	{
		if ($this->components[$serviceName])
		{
			return $this->components[$serviceName]();
		}

		throw new InvalidArgumentException("There is no service $serviceName");
	}
}