<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

namespace projectorangebox\cache;

use Exception;
use projectorangebox\cache\CacheAbstract;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Cache extends CacheAbstract implements CacheInterface
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

	public function has(string $name): bool
	{
		return \array_key_exists($name, $this->knownCacheServices);
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

	public function cacheInfo(): array
	{
		return $this->defaultService->cacheInfo();
	}

	public function clean(): bool
	{
		return $this->defaultService->clean();
	}
} /* end class */
