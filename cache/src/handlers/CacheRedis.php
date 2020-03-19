<?php

namespace projectorangebox\cache\handlers;

use Redis;
use RedisException;
use projectorangebox\cache\CacheAbstract;
use projectorangebox\cache\CacheInterface;

class CacheRedis extends CacheAbstract implements CacheInterface
{
	protected $redis;
	protected $redisSerialized = 'redisSerialized';
	protected $serialized = [];

	public function __construct(array &$config)
	{
		$this->ttl = $config['ttl'] ?? 0;

		if (!$this->isSupported()) {
			\log_message('error', 'Cache: Failed to create Redis object; extension not loaded?');

			return;
		}

		$this->redis = new Redis();

		try {
			if ($config['redis']['socket_type'] === 'unix') {
				$success = $this->redis->connect($config['redis']['socket']);
			} else {
				/* tcp socket */
				$success = $this->redis->connect($config['redis']['host'], $config['redis']['port'], $config['redis']['timeout']);
			}

			if (!$success) {
				\log_message('error', 'Cache: Redis connection failed. Check your configuration.');
			}

			if (isset($config['redis']['password']) && !$this->redis->auth($config['redis']['password'])) {
				\log_message('error', 'Cache: Redis authentication failed.');
			}
		} catch (RedisException $e) {
			\log_message('error', 'Cache: Redis connection refused (' . $e->getMessage() . ')');
		}
	}

	public function get(string $key)
	{
		$value = $this->redis->get($key);

		if ($value !== false && $this->redis->sIsMember($this->redisSerialized, $key)) {
			return unserialize($value);
		}

		return $value;
	}

	public function save(string $id, $data, int $ttl = null): bool
	{
		if (is_array($data) || is_object($data)) {
			if (!$this->redis->sIsMember($this->redisSerialized, $id) && !$this->redis->sAdd($this->redisSerialized, $id)) {
				return false;
			}

			$this->serialized[$id] = $this->serialized[$id] ?? true;

			$data = serialize($data);
		} else {
			$this->redis->sRemove($this->redisSerialized, $id);
		}

		return $this->redis->set($id, $data, $this->ttl($ttl));
	}

	public function delete(string $key): bool
	{
		if ($this->redis->delete($key) !== 1) {
			return false;
		}

		$this->redis->sRemove($this->redisSerialized, $key);

		return true;
	}

	public function clean(): bool
	{
		return $this->redis->flushDB();
	}

	public function cacheInfo(): array
	{
		return $this->redis->info();
	}

	public function getMetadata(string $key): array
	{
		$value = $this->get($key);

		if ($value !== false) {
			return [
				'expire' => time() + $this->redis->ttl($key),
				'data' => $value
			];
		}

		return [];
	}

	public function isSupported(): bool
	{
		return extension_loaded('redis');
	}

	public function __destruct()
	{
		if ($this->redis) {
			$this->redis->close();
		}
	}
} /* end class */
