<?php

namespace projectorangebox\cache\handlers;

use projectorangebox\cache\CacheInterface;

class CacheDummy implements CacheInterface
{
	public function __construct(array &$config)
	{
	}

	public function get(string $key) /* mixed */
	{
		\log_message('info', 'Dummy Cache Get ' . $key);

		return false;
	}

	public function getMetadata(string $key): array
	{
		\log_message('info', 'Dummy Cache Get Meta Data ' . $key);

		return [];
	}

	public function save(string $key, $value, int $ttl = null): bool
	{
		\log_message('info', 'Dummy Cache Save ' . $key);

		return true;
	}

	public function delete(string $key): bool
	{
		\log_message('info', 'Dummy Cache Delete ' . $key);

		return true;
	}

	public function cache_info(): array
	{
		\log_message('info', 'Dummy Cache Info');

		return [];
	}

	public function cache_debug(): array
	{
		\log_message('info', 'Dummy Cache debug');

		return [];
	}

	public function clean(): bool
	{
		\log_message('info', 'Dummy Cache Clean');

		return true;
	}
} /* end class */
