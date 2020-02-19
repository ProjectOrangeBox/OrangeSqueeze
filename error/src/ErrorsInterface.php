<?php

namespace projectorangebox\error;

use projectorangebox\view\ViewInterface;
use projectorangebox\response\ResponseInterface;

interface ErrorsInterface
{

	public function __construct(array $config);
	public function setGroup(string $group): ErrorsInterface;
	public function getGroup(): string;
	public function getGroups($groups = null): array;

	public function get($groups = null): array;

	public function add(string $index, $value, string $group = null): ErrorsInterface;
	public function has($groups = null): bool;
	public function clear($groups = null): ErrorsInterface;
}
