<?php

/**
 * All of these can be overridden
 * by declaring the same function
 * before the App class is instantiated
 */

/* wrapper to get service from container which is attached to the application */
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
 * $cached = searchFor('/folder/folder/*.md', 'markdown.plugins', $container->cache),
 * $cached = searchFor('/folder/folder/*.hbs', 'handlebar.templates', $container->cache),
 * $cached = searchFor('/folder/folder/*.php', 'php.templates', $container->cache),
 */
if (!function_exists('searchFor')) {
	function searchFor(string $path, string $cacheKey, \projectorangebox\cache\CacheInterface $cache): array
	{
		/* build the complete cache key */
		$cacheKey = 'app.searchFor.' . $cacheKey . '.php';

		if (!$found = $cache->get($cacheKey)) {
			$pathinfo = \pathinfo($path);

			$stripFromBeginning = $pathinfo['dirname'];
			$stripLen = \strlen($stripFromBeginning) + 1;

			$extension = $pathinfo['extension'];
			$extensionLen = \strlen($extension) + 1;

			$found = [];

			foreach (\FS::glob($path, 0, true, true) as $file) {
				$found[\strtolower(\substr($file, $stripLen, -$extensionLen))] = $file;
			}

			$cache->save($cacheKey, $found);
		}

		return $found;
	}
}

if (!function_exists('mergeConfig')) {
	function mergeConfig(array $array, array $defaults): array
	{
		foreach ($defaults as $name => $default) {
			if (\is_integer($name)) {
				$name = $default;
				$default = '#NOVALUE#';
			}

			if (!isset($array[$name])) {
				if ($default === '#NOVALUE#') {
					/* fatal */
					throw new \Exception('Could not locate a configuration value ' . $name . ' and no default was provided.');
				}

				$array[$name] = $default;
			}
		}

		return $array;
	}
}
