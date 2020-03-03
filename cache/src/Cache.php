<?php

namespace projectorangebox\cache;

use Exception;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Cache implements CacheInterface
{
	protected $knownCacheServices;
	protected $defaultService;

	public function __construct(array &$config)
	{
		$default = $config['default'] ?? 'dummy';

		/* Add the default dummy incase they didn't */
		$config['caches']['dummy'] = 'projectorangebox\cache\handlers\CacheDummy';

		foreach ($config['caches'] as $name => $cacheClass) {
			$this->knownCacheServices[$name] = new $cacheClass($config);

			if (!($this->knownCacheServices[$name] instanceof CacheInterface)) {
				throw new IncorrectInterfaceException('CacheInterface');
			}
		}

		if (!\array_key_exists($default, $config['caches'])) {
			throw new Exception($default . ' cache not found.');
		}

		$this->defaultService = $this->knownCacheServices[$default];
	}

	public function __get(string $name): CacheInterface
	{
		return $this->knownCacheServices[$name] ?? null;
	}

	public function get(string $key) /* mixed */
	{
		return $this->defaultService->get($key);
	}

	public function getMetadata(string $key): array
	{
		return $this->defaultService->getMetadata($key);
	}

	public function save(string $key, $value, int $ttl = null): bool
	{
		return $this->defaultService->save($key, $value, $ttl);
	}

	public function delete(string $key): bool
	{
		return $this->defaultService->delete($key);
	}

	public function cache_info(): array
	{
		return $this->defaultService->cache_info();
	}

	public function clean(): bool
	{
		return $this->defaultService->clean();
	}
} /* end class */
