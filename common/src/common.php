<?php

if (!function_exists('redirect')) {
	/**
	 * Header Redirect
	 *
	 * Header redirect in two flavors
	 * For very fine grained control over headers, you could use the Output
	 * Library's set_header() function.
	 *
	 * @param	string	$uri	URL
	 * @param	string	$method	Redirect method
	 *			'auto', 'location' or 'refresh'
	 * @param	int	$code	HTTP Response status code
	 * @return	void
	 */
	function redirect($uri = '', $method = 'auto', $code = NULL)
	{
		// IIS environment likely? Use 'refresh' for better compatibility
		if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
			$method = 'refresh';
		} elseif ($method !== 'refresh' && (empty($code) or !is_numeric($code))) {
			if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
				$code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
					? 303	// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
					: 307;
			} else {
				$code = 302;
			}
		}

		switch ($method) {
			case 'refresh':
				header('Refresh:0;url=' . $uri);
				break;
			default:
				header('Location: ' . $uri, TRUE, $code);
				break;
		}
		exit;
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
