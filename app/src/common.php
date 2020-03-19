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
	function service(string $serviceName = null)
	{
		return \projectorangebox\app\App::container($serviceName);
	}
} /* end service */

/* Get ENV with default */
if (!function_exists('env')) {
	function env(string $key, $default = '#NOVALUE#') /* mixed */
	{
		return \projectorangebox\app\App::env($key, $default);
	}
}
