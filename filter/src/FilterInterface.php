<?php

namespace projectorangebox\filter;

interface FilterInterface
{
	public function __construct(array $config);
	public function attachFilter(string $name, \closure $closure): FilterInterface;
	public function filter(string $rules, $field); /* mixed */
}
