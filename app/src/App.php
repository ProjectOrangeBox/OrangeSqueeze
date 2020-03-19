<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

namespace projectorangebox\app;

use FS;
use Exception;
use projectorangebox\container\ContainerInterface;

class App implements AppInterface
{
	protected static $container;
	protected static $env = [];

	public function __construct(array $config)
	{
		self::$env = (isset($config['env'])) ? array_merge($_ENV, $config['env']) : $_ENV;

		/* set End Of Line based on request type */
		define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		/* default to production environment */
		define('ENVIRONMENT', ($config['environment'] ?? 'production'));

		/* set debug value if set */
		define('DEBUG', ($config['debug'] ?? false));

		if (DEBUG) {
			error_reporting(E_ALL & ~E_NOTICE);
			ini_set('display_errors', 1);
		} else {
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
			ini_set('display_errors', 0);
		}

		/* Check for this because it's required */
		if (!\defined('__ROOT__')) {
			throw new Exception('__ROOT__ not defined.');
		}

		/* Check for this because it's required */
		if (!\class_exists('FS')) {
			throw new Exception('FS Class not found.');
		}

		/* Set File System Functions Root Directory and chdir to it */
		FS::setRoot(__ROOT__, true);

		/* get absolute path */
		$servicesPath = FS::resolve($config['services']);

		/* Is the services configuration file there? */
		if (!file_exists($servicesPath)) {
			throw new Exception('Services file not found.');
		}

		/* load the services array from the config file */
		$services = require $servicesPath;

		/* Create container manually and send in the services array */
		self::$container = $services['container'][0]($services);

		/* We are going to manaully instancate and attach the Config Singleton which is registered in the service array */
		self::$container->config = $services['config'][0]($config);
	}

	/**
	 * return our dependency container
	 *
	 * App::container();
	 *
	 * @return ContainerInterface
	 */
	static public function container(string $serviceName = null) /* mixed */
	{
		return ($serviceName) ? self::$container->get($serviceName) : self::$container;
	}

	static public function env(string $key, $default = '#NOVALUE#')
	{
		if (!isset(self::$env[$key]) && $default === '#NOVALUE#') {
			throw new \Exception('The environmental variable "' . $key . '" is not set and no default was provided.');
		}

		return self::$env[$key] ?? $default;
	}

	public function dispatch(): void
	{
		\log_message('info', __CLASS__);

		self::$container->dispatcher->dispatch();
	}
} /* end app */
