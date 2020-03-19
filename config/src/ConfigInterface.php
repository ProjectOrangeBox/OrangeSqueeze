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

namespace projectorangebox\config;

interface ConfigInterface
{
	public function __construct(array &$config);
	public function get(string $name,/* mixed */ $default = null); /* mixed */
	public function set(string $name,/* mixed */ $value = null): ConfigInterface;
	public function merge(array $array): ConfigInterface;
	public function collect(): array;
}
