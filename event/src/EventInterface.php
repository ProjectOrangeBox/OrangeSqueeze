<?php

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
