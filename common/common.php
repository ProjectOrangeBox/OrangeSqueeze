<?php

/**
 * All of these can be overridden
 * by declaring the same function
 * before the App class is instantiated
 */

/* Wrapper */
if (!function_exists('service')) {
	function service(string $serviceName = null, \projectorangebox\container\ContainerInterface &$setContainer = null)
	{
		static $container;

		if ($setContainer) {
			$container = $setContainer;
		}

		return ($serviceName) ? $container->get($serviceName) : $container;
	}
} /* end service */

/* Wrapper */
if (!function_exists('log_message')) {
	function log_message(string $type, string $msg): void
	{
		static $logService;

		/* Is log even attached to the container yet? */
		if (!$logService) {
			$container = service();

			if ($container !== null) {
				if ($container->has('log')) {
					$logService = service('log');
				}
			}
		}

		if ($logService) {
			if (!method_exists($logService, $type)) {
				throw new \Exception('Log Message does not support the method "' . $type . '".');
			} else {
				$logService->$type($msg);
			}
		}
	}
} /* end log_message */

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


/* The most basic exception handler */
if (!function_exists('showException')) {
	function showException($exception): void
	{
		$exception = (string) $exception;

		log_message('critical', $exception);

		if (PHP_SAPI == 'cli') {
			echo 'Exception Thrown:' . PHP_EOL . $exception . PHP_EOL;
		} else {
			echo '<h2>Exception Thrown:</h2><pre>Error: ' . $exception . '</pre>';
		}

		exit(1);
	}
} /* end showException */

/* Get ENV with default */
if (!function_exists('env')) {
	function env(string $key, $default = '#NOVALUE#') /* mixed */
	{
		if (!isset($_ENV[$key]) && $default === '#NOVALUE#') {
			throw new \Exception('The environmental variable "' . $key . '" is not set and no default was provided.');
		}

		return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
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
