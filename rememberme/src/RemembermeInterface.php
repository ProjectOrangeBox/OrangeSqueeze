<?php

namespace projectorangebox\rememberme;

interface RemembermeInterface
{

	public function __construct(array &$config);
	public function save(int $userId): bool;
	public function get(): int;
	public function remove(): bool;
	public function garbageCollection(): bool;
}
