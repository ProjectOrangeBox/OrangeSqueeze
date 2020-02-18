<?php

namespace projectorangebox\auth;

interface UserAuthModelInterface
{
	public function __construct(array $config);
	public function get(int $userId);
	public function getBy(string $value, string $column);
}
