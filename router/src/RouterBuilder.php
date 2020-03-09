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

namespace projectorangebox\router;

class RouterBuilder
{
	static public function build(array $config): array
	{
		$formatted = [];

		if (is_array($config['routes'])) {

			$config['default'] = $config['default'] ?? ['get', 'cli'];
			$config['all'] = $config['all'] ?? ['get', 'cli', 'post', 'put', 'delete'];

			$re = '/<(.[^>]*)>/m';

			foreach ($config['routes'] as $regex => $rewrite) {
				/* regex passed by reference */
				$httpMethod = self::GetMethods($regex, $config['default'], $config['all']);

				if (preg_match_all($re, $regex, $matches)) {
					foreach ($matches[0] as $idx => $match) {
						/* (?<folder>[^/]*) */
						$regex = str_replace($match, '(?<' . $matches[1][$idx] . '>[^/]*)', $regex);
					}
				}

				$regex = '#^/' . ltrim($regex, '/') . '$#im';

				foreach ($httpMethod as $method) {
					$formatted[$method][$regex] = $rewrite;
				}
			}
		}

		return $formatted;
	}

	static protected function GetMethods(&$regex, array $defaults, array $all): array
	{
		/* default */
		$httpMethods = $defaults;

		if ($regex[0] == '@') {
			$firstSlash = \strpos($regex, '/');
			$methods = substr($regex, 1, $firstSlash - 1);
			$regex = \substr($regex, $firstSlash);

			if (strlen($methods)) {
				/* use supplied */
				$httpMethods = explode(',', $methods);
			} else {
				/* nothing specific supplied */
				$httpMethods = $all;
			}
		}

		return $httpMethods;
	}
}
