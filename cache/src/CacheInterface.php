<?php

namespace projectorangebox\cache;

interface CacheInterface
{
	public function __construct(array $config);
	public function get(string $key); /* mixed */
	public function getMetadata(string $key): array;
	public function save(string $key, $value, int $ttl = null): bool;
	public function delete(string $key): bool;
	public function cache_info(): array;
	public function clean(): bool;
}
