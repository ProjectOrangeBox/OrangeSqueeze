<?php

namespace projectorangebox\auth;

interface UserModelInterface
{
	public function __construct(array $config);
	public function get(int $userId);
	public function getBy(string $value, string $column);
}
