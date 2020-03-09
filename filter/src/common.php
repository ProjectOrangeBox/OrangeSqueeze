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

/* wrapper */
if (!function_exists('filterRequest')) {
	function filterRequest($rules, string $key)
	{
		$field = service('request')->request($key, null);

		return service('filter')->filter($rules, $field);
	}
}

/* wrapper */
if (!function_exists('filter')) {
	function filter($rules, $field)
	{
		return service('filter')->filter($rules, $field);
	}
}
