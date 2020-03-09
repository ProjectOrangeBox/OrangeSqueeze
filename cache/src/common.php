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
if (!function_exists('cache')) {
	function cache(string $cacheKey, Closure $closure, int $ttl = null)
	{
		if (!$cached = service('cache')->get($cacheKey)) {
			$cached = $closure();

			service('cache')->save($cacheKey, $cached, $ttl);
		}

		return $cached;
	}
}
