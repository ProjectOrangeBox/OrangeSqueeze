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
if (!function_exists('isValid')) {
	function isValid($rules, $field, &$errors = null)
	{
		$v = service('validate');

		$v->rule($rules, $field);

		$errors = $v->errors();

		return $v->success();
	}
}
