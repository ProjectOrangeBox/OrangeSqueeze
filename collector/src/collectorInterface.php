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

namespace projectorangebox\collector;

interface collectorInterface
{

	public function __construct(array &$config);
	public function __call($key, $arguments): collectorInterface;
	public function __toString();
	public function add(string $key, $context, bool $persist = false): collectorInterface;
	public function collect($keys = null);
	public function has($keys = null): bool;
	public function clear($keys = null): collectorInterface;
}
