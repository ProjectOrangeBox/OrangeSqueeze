<?php

namespace projectorangebox\auth;

interface AuthInterface
{

	public function __construct(array $config);
	public function error(): string;
	public function has(): bool;
	public function userId(): int;
	public function login(string $login, string $password): bool;
	public function logout(): bool;
	public function refresh(): bool;
}
