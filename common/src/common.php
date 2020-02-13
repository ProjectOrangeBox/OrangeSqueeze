<?php

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
