<?php

namespace projectorangebox\auth;

interface AuthInterface
{
	public function __construct(array $config);
	public function userId(): int;

	public function error(): string;
	public function hasError(): bool;

	public function login(string $login, string $password): bool;
	public function logout(): bool;
	public function refresh(): bool;
}
