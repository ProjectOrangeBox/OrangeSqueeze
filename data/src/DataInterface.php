<?php

namespace projectorangebox\data;

interface DataInterface
{
	public function __set($name, $value);
	public function __get($name);
	public function __isset($name);
	public function __unset($name);
	public function all(): array;
}
