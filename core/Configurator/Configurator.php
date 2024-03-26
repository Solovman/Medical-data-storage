<?php

namespace N_ONE\Core\Configurator;

use http\Exception\InvalidArgumentException;

class Configurator
{
	/**
	 * @throws InvalidArgumentException
	 */
	public static function option(string $name, $defaultValue = null)
	{
		static $config = null;

		if ($config === null)
		{
			$masterConfig = require ROOT . '/config/config.php';
			if (file_exists(ROOT . '/config/config.local.php'))
			{
				$localConfig = require ROOT . '/config/config.local.php';
			}
			else
			{
				$localConfig = [];
			}
			$config = array_merge($masterConfig, $localConfig);
		}

		if (array_key_exists($name, $config))
		{
			return $config[$name];
		}

		if ($defaultValue !== null)
		{
			return $defaultValue;
		}

		throw new InvalidArgumentException("There is no configuration option $name");
	}
}