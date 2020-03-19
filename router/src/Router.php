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

class Router implements RouterInterface
{
	protected $routes = [];
	protected $captured = [];

	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		$this->routes = $config['routes'] ?? [];
	}

	public function handle(string $uri, string $httpMethod): array
	{
		/* clear captured */
		$this->captured = [];

		/* Don't allow any protected files or folder - these start with _ */
		$uri = str_replace('/_', '/', $uri);

		log_message('info', 'URI ' . $uri);

		/* default to no match */
		$matched = [];

		if (is_array($this->routes[$httpMethod])) {
			foreach ($this->routes[$httpMethod] as $regex => $matched) {
				if (preg_match($regex, $uri, $params)) {
					log_message('info', 'Matched the URI: ' . $uri . ' Against: ' . $regex);

					foreach ($params as $key => $value) {
						$this->captured[$key] = $value;
					}

					break; /* found one no need to stay in loop */
				}
			}
		}

		return $matched;
	}

	public function captured(): array
	{
		return $this->captured;
	}
} /* end class */
