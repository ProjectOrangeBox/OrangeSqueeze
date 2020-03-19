<?php

namespace projectorangebox\cache\handlers;

use projectorangebox\cache\CacheAbstract;
use projectorangebox\cache\CacheInterface;

class CacheApc extends CacheAbstract implements CacheInterface
{
	protected $cacheType = 'user';

	public function __construct(array &$config)
	{
		$this->ttl = $config['ttl'] ?? 0;

		if (!$this->isSupported()) {
			log_message('error', 'Cache: Failed to initialize APC; extension not loaded/enabled?');
		}
	}

	public function get(string $key)
	{
		$success = false;
		$data = apcu_fetch($key, $success);

		return ($success === true) ? $data : false;
	}

	public function save(string $key, $value, int $ttl = null): bool
	{
		return apcu_store($key, $value, $this->ttl($ttl));
	}

	public function delete(string $key): bool
	{
		return apcu_delete($key);
	}

	public function clean(): bool
	{
		return apcu_clear_cache($this->cacheType);
	}

	public function cacheInfo(): array
	{
		$info = apcu_cache_info($this->cacheType);

		return (is_array($info)) ? $info : [];
	}

	public function getMetadata(string $key): array
	{
		$cache_info = apcu_cache_info($this->cacheType, false);

		if (empty($cache_info) or empty($cache_info['cache_list'])) {
			return [];
		}

		foreach ($cache_info['cache_list'] as &$entry) {
			if ($entry['info'] !== $key) {
				continue;
			}

			$success  = false;
			$metadata = [
				'expire' => ($entry['ttl'] ? $entry['mtime'] + $entry['ttl'] : 0),
				'mtime'  => $entry['ttl'],
				'data'   => apcu_fetch($key, $success)
			];

			return ($success === true) ? $metadata : [];
		}

		return [];
	}

	public function is_supported(): bool
	{
		return (extension_loaded('apc') && ini_get('apc.enabled'));
	}
} /* end class */
