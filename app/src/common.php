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

/* Wrapper */
if (!function_exists('service')) {
	function service(string $serviceName = null, \projectorangebox\container\ContainerInterface &$setContainer = null)
	{
		static $container;

		if ($setContainer) {
			$container = $setContainer;
		}

		return ($serviceName) ? $container->get($serviceName) : $container;
	}
} /* end service */

/* Get ENV with default */
if (!function_exists('env')) {
	function env(string $key, $default = '#NOVALUE#') /* mixed */
	{
		if (!isset($_ENV[$key]) && $default === '#NOVALUE#') {
			throw new \Exception('The environmental variable "' . $key . '" is not set and no default was provided.');
		}

		return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
	}
}
