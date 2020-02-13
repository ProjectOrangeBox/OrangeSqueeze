<?php

/**
 * Merge configuration with defaults.
 * If no value is included for a default key
 * then it is required and a value must be included in the passed config
 */
if (!function_exists('mergeConfig')) {
	function mergeConfig(array $passedConfig, array $defaults): array
	{
		$missing = [];

		foreach ($defaults as $name => $value) {
			if (\is_integer($name)) {
				$name = $value;
				$value = '#NOVALUE#';
			}

			if (!isset($passedConfig[$name])) {
				if ($value === '#NOVALUE#') {
					$missing[$name] = $name;
				} else {
					$passedConfig[$name] = $value;
				}
			}
		}

		if (count($missing)) {
			/* fatal */
			throw new \Exception('The following configuration values are required and no default was given ' . implode(',', $missing) . ' .');
		}

		return $passedConfig;
	}
}
