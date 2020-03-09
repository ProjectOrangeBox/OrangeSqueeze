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

namespace projectorangebox\event;

use Closure;

interface EventInterface
{
	public function register(string $name, Closure $closure, int $priority = EVENT::PRIORITY_NORMAL): EventInterface;
	public function trigger(string $name, &...$arguments): EventInterface;
	public function has(string $name): bool;
	public function events(): array;
	public function count(string $name): int;
	public function unregister(string $name, $listener): bool;
	public function unregister_all(string $name = ''): EventInterface;
}
