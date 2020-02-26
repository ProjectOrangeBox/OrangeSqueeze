<?php

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
