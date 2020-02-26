<?php

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
