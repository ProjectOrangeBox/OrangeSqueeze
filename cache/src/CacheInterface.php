<?php

namespace projectorangebox\cache;

interface CacheInterface
{
	public function __construct(array $config);
	public function get(string $key);
	public function getMetadata(string $key): array;
	public function save(string $key, $value, int $ttl = null);
	public function delete(string $key);
	public function cache_info(): array;
	public function clean(): void;
}
