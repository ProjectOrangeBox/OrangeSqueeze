<?php

namespace projectorangebox\fake;

use projectorangebox\cache\CacheInterface;

class Cache implements CacheInterface
{
	public function __construct(array $config)
	{
	}

	public function get(string $key) /* mixed */
	{
		return false;
	}

	public function getMetadata(string $key): array
	{
		return [];
	}

	public function save(string $key, $value, int $ttl = null): bool
	{
		return true;
	}

	public function delete(string $key): bool
	{
		return true;
	}

	public function cache_info(): array
	{
		return [];
	}

	public function clean(): bool
	{
		return true;
	}
} /* end class */
