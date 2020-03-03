<?php

namespace projectorangebox\models;

interface ModelsInterface
{
	public function __construct(array &$config);
	public function __get(string $name);
	public function has(string $name): bool;
}
