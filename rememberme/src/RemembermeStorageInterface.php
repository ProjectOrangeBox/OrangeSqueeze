<?php

namespace projectorangebox\rememberme;

interface RemembermeStorageInterface
{
	public function __construct(array &$config);
	public function create(string $token, int $userId, int $expireSeconds): bool;
	public function read(string $token): int;
	public function update(string $token, int $userId, int $expireSeconds): bool;
	public function delete(string $token): bool;
	public function garbageCollection(): bool;
}
