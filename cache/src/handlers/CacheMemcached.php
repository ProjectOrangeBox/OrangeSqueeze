<?php

namespace projectorangebox\cache\handlers;

use projectorangebox\cache\CacheAbstract;
use projectorangebox\cache\CacheInterface;

class CacheMemcached extends CacheAbstract implements CacheInterface
{
	protected $mcache;

	public function __construct(array &$config)
	{
		$this->ttl = $config['ttl'] ?? 0;

		if (class_exists('Memcached', FALSE)) {
			$this->mcache = new \Memcached();
		} elseif (class_exists('Memcache', FALSE)) {
			$this->mcache = new \Memcache();
		} else {
			\log_message('error', 'Cache: Failed to create Memcache(d) object; extension not loaded?');
			return;
		}

		foreach ($config['memcache']['servers'] as $cacheServer) {
			$cacheServer['port'] = $cacheServer['port'] ?? 11211;
			$cacheServer['weight'] = $cacheServer['weight'] ?? 1;

			if ($this->mcache instanceof \Memcache) {
				// Third parameter is persistence and defaults to TRUE.
				$this->mcache->addServer($cacheServer['hostname'], $cacheServer['port'], TRUE, $cacheServer['weight']);
			} elseif ($this->mcache instanceof \Memcached) {
				$this->mcache->addServer($cacheServer['hostname'], $cacheServer['port'], $cacheServer['weight']);
			}
		}
	}

	public function get(string $id)
	{
		$data = $this->mcache->get($id);

		return is_array($data) ? $data[0] : $data;
	}

	public function save(string $id, $data, int $ttl = null): bool
	{
		$data = array($data, time(), $this->ttl($ttl));

		if ($this->mcache instanceof \Memcached) {
			return $this->mcache->set($id, $data, $ttl);
		} elseif ($this->mcache instanceof \Memcache) {
			return $this->mcache->set($id, $data, 0, $ttl);
		}

		return false;
	}

	public function delete(string $id): bool
	{
		return $this->mcache->delete($id);
	}

	public function clean(): bool
	{
		return $this->mcache->flush();
	}

	public function cacheInfo(): array
	{
		return $this->mcache->getStats();
	}

	public function getMetadata(string $id): array
	{
		$stored = $this->mcache->get($id);

		if (count($stored) !== 3) {
			return [];
		}

		list($data, $time, $ttl) = $stored;

		return [
			'expire'	=> $time + $ttl,
			'mtime'		=> $time,
			'data'		=> $data
		];
	}

	public function is_supported(): bool
	{
		return (extension_loaded('memcached') or extension_loaded('memcache'));
	}

	public function __destruct()
	{
		if ($this->mcache instanceof \Memcache) {
			$this->mcache->close();
		} elseif ($this->mcache instanceof \Memcached && method_exists($this->mcache, 'quit')) {
			$this->mcache->quit();
		}
	}
} /* end class */
