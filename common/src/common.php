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

if (!function_exists('show_error')) {
	function show_error(string $title, string $body)
	{
		service('formatter')->display(['title' => $title, 'body' => $body], 'show_error');
	}
}

if (!function_exists('dieOnError')) {
	function dieOnError(int $code, string $keys)
	{
		if (service('collector')->has($keys)) {
			service('formatter')->send($code)->display(['errors' => service('collector')->collect($keys)]);
		}
	}
}

if (!function_exists('redirect')) {
	/**
	 * redirect
	 *
	 * @param mixed string
	 * @param mixed int
	 * @return void
	 */
	function redirect(string $url = '', int $http_response_code = NULL): void
	{
		$request = service('request');

		$protocol = ($request->server('https', false) === false) ? 'http' : 'https';
		$host = $request->server('http_host');

		header("Location: $protocol://$host$url", true, $http_response_code);

		/* terminate the program successfully */
		exit(0);
	}
}

if (!function_exists('e')) {
	function e($input) : string
	{
		return (empty($input)) ? '' : html_escape($input);
	}
}

/**
 * End the current session and store session data.
 * (7.2 returns a boolean but prior it was null)
 * therefore we don't return anything
 *
 * @return void
 *
 */
if (!function_exists('unlock_session')) {
	function unlock_session() : void
	{
		session_write_close();
	}
}

/**
 * Show output in Browser Console
 *
 * @param mixed $var converted to json
 * @param string $type - browser console log types [log]
 *
 */
if (!function_exists('console')) {
	function console($var, string $type = 'log') : void
	{
		echo '<script type="text/javascript">console.'.$type.'('.json_encode($var).')</script>';
	}
}

/**
 * Try to convert a value to it's real type
 * this is nice for pulling string from a database
 * such as configuration values stored in string format
 *
 * @param string $value
 *
 * @return mixed
 *
 */
if (!function_exists('convert_to_real')) {
	function convert_to_real(string $value)
	{
		/* return on first match multiple exists */
		switch (trim(strtolower($value))) {
		case 'true':
			return true;
			break;
		case 'false':
			return false;
			break;
		case 'empty':
			return '';
			break;
		case 'null':
			return null;
			break;
		default:
			if (is_numeric($value)) {
				return (is_float($value)) ? (float)$value : (int)$value;
			}
		}

		$json = @json_decode($value, true);

		return ($json !== null) ? $json : $value;
	}
}

/**
 * Try to convert a value back into a string
 * this is nice for storing string into a database
 * such as configuration values stored in string format
 *
 * @param mixed $value
 *
 * @return string
 *
 */
if (!function_exists('convert_to_string')) {
	function convert_to_string($value) : string
	{
		/* return on first match multiple exists */

		if (is_array($value)) {
			return str_replace('stdClass::__set_state', '(object)', var_export($value, true));
		}

		if ($value === true) {
			return 'true';
		}

		if ($value === false) {
			return 'false';
		}

		if ($value === null) {
			return 'null';
		}

		return (string) $value;
	}
}

/**
 *
 * Simple view merger
 * replace {tags} with data in the passed data array
 *
 * @access
 *
 * @param string $template
 * @param array $data []
 *
 * @return string
 *
 * #### Example
 * ```
 * $html = quick_merge('Hello {name}',['name'=>'Johnny'])
 * ```
 */
if (!function_exists('quick_merge')) {
	function quick_merge(string $template, array $data=[]) : string
	{
		if (preg_match_all('/{([^}]+)}/m', $template, $matches)) {
			foreach ($matches[1] as $key) {
				$template = str_replace('{'.$key.'}', $data[$key], $template);
			}
		}

		return $template;
	}
}


/**
 * Add some stateless functions
 */

function array_get_by(array $array, string $notation, $default = null) /* mixed */
{
	$value = $default;

	if (is_array($array) && array_key_exists($notation, $array)) {
		$value = $array[$notation];
	} elseif (is_object($array) && property_exists($array, $notation)) {
		$value = $array->$notation;
	} else {
		$segments = explode('.', $notation);

		foreach ($segments as $segment) {
			if (is_array($array) && array_key_exists($segment, $array)) {
				$value = $array = $array[$segment];
			} elseif (is_object($array) && property_exists($array, $segment)) {
				$value = $array = $array->$segment;
			} else {
				$value = $default;
				break;
			}
		}
	}

	return $value;
}

function array_set_by(array &$array, string $notation, $value): void
{
	$keys = explode('.', $notation);

	while (count($keys) > 1) {
		$key = array_shift($keys);

		if (!isset($array[$key])) {
			$array[$key] = [];
		}

		$array = &$array[$key];
	}

	$key = reset($keys);

	$array[$key] = $value;
}

function array_sort_by_column(array &$array, string $column, int $dir = SORT_ASC, int $flags = null)
{
	$sortColumn = array_column($array, $column);

	array_multisort($sortColumn, $dir, $array, $flags);
}
