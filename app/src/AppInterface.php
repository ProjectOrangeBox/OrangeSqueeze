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

namespace projectorangebox\app;

use projectorangebox\container\ContainerInterface;

interface AppInterface
{
	public function __construct(array $config);
	public function dispatch(): void;

	static public function container(string $serviceName = null); /* mixed */
	static public function env(string $key, $default = '#NOVALUE#'); /* mixed */
}
