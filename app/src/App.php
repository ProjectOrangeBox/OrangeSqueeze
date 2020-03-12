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

use Exception;
use FS;
use projectorangebox\container\ContainerInterface;

class App implements AppInterface
{
	protected static $container;

	public function __construct(array &$config)
	{
		/* set End Of Line based on request type */
		define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

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

		/* default to production environment */
		define('ENVIRONMENT', ($config['environment'] ?? 'production'));

		/* load the users ENVIRONMENT bootstrap file if present */
		if (FS::file_exists('Bootstrap.' . ENVIRONMENT . '.php')) {
			require FS::resolve('Bootstrap.' . ENVIRONMENT . '.php');
		}

		/* load the users bootstrap file if present */
		if (FS::file_exists('Bootstrap.php')) {
			require FS::resolve('Bootstrap.php');
		}

		/* set the most basic exception handler inside common.php file */
		set_exception_handler('showException');

		/* use default */
		$config['services config file'] = $config['services config file'] ?? '/config/services.php';

		/* Is the services configuration file there? */
		if (!FS::file_exists($config['services config file'])) {
			throw new Exception('Services configuration file not found.');
		}

		/* load the services array from the config file */
		$services = FS::require($config['services config file']);

		/* did this return an array? */
		if (!\is_array($services)) {
			throw new Exception('Services configuration file is not an array.');
		}

		/* use default */
		$config['containerClass'] = $config['containerClass'] ?? '\projectorangebox\container\Container';

		$containerClass = $config['containerClass'];

		/* does the container class exists? */
		if (!\class_exists($containerClass, true)) {
			throw new Exception('Services Class file not found.');
		}

		/* Create container and send in the services array */
		self::$container = new $containerClass($services);

		/**
		 * Send the container service into the services common function
		 * This is in common.php and therefore based on your composer.json autoloader
		 * should be already loaded
		 * self::container is passed by reference
		 */
		if (\function_exists('service')) {
			service(null, self::$container);
		}

		/* Setup our configuration object with the configuration array if it's not found it will throw an error */
		self::$container->config->replace(['config' => $config]);
	}

	/**
	 * return our dependency container
	 *
	 * App::container();
	 *
	 * @return ContainerInterface
	 */
	static public function container(): ContainerInterface
	{
		return self::$container;
	}

	public function dispatch(): void
	{
		\log_message('info', __CLASS__);

		self::$container->dispatcher->dispatch();
	}
} /* end app */
