<?php

/* wrapper */
if (!function_exists('filterRequest')) {
	function filterRequest($rules, string $key)
	{
		$field = service('request')->request($key, null);

		service('validate')->rule($rules, $field);

		return $field;
	}
}

/* wrapper */
if (!function_exists('filter')) {
	function filter($rules, $field)
	{
		service('validate')->rule($rules, $field);

		return $field;
	}
}
