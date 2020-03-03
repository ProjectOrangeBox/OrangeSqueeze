<?php

namespace projectorangebox\cache\handlers;

use FS;
use projectorangebox\cache\CacheInterface;

class CacheFile implements CacheInterface
{
	protected $cachePath = '';
	protected $ttl;

	public function __construct(array &$config)
	{
		/* make cache path ready to use */
		$this->cachePath = rtrim($config['path'], '/') . '/';
		$this->ttl = $config['ttl'] ?? 0;

		FS::mkdir($this->cachePath);
	}

	public function get(string $key) /* mixed */
	{
		\log_message('info', 'Cache Get ' . $key);

		$get = false;

		if (FS::file_exists($this->cachePath . $key . '.meta' . $this->suffix) && FS::file_exists($this->cachePath . $key)) {
			$meta = $this->getMetadata($key);

			if ($this->isExpired($meta['expire'])) {
				$this->delete($key);
			} else {
				$get = include FS::resolve($this->cachePath . $key);
			}
		}

		return $get;
	}

	protected function isExpired(int $expire): bool
	{
		return (time() > $expire);
	}

	public function getMetadata(string $key): array
	{
		$file = $this->cachePath . $key;

		$metaData = [];

		if (FS::is_file($file . '.meta') && FS::is_file($file)) {
			$metaData = include FS::resolve($file . '.meta');
		}

		return $metaData;
	}

	public function save(string $key, $value, int $ttl = null): bool
	{
		\log_message('info', 'Cache Save ' . $key);

		$valuePHP = FS::var_export_php($value);
		$metaPHP = FS::var_export_php($this->buildMetadata($valuePHP, $this->ttl($ttl)));

		return ((bool) FS::atomic_file_put_contents($this->cachePath . $key . '.meta', $metaPHP) && (bool) FS::atomic_file_put_contents($this->cachePath . $key, $valuePHP));
	}

	public function buildMetadata(string $valueString, int $ttl): array
	{
		return [
			'strlen' => strlen($valueString),
			'time' => time(),
			'ttl' => (int) $ttl,
			'expire' => (time() + $ttl)
		];
	}

	public function delete(string $key): bool
	{
		\log_message('info', 'Cache Delete ' . $key);

		$file = $this->cachePath . $key;

		if (FS::file_exists($file)) {
			FS::unlink($file);
		}

		return true;
	}

	public function cache_info(): array
	{
		$keys = [];

		foreach (FS::glob($this->cachePath . '*') as $path) {
			$keys[] = FS::basename($path);
		}

		return $keys;
	}

	public function clean(): bool
	{
		foreach (FS::glob($this->cachePath . '*') as $path) {
			self::delete($path);
		}

		return true;
	}

	public function ttl(int $cacheTTL = null, bool $useWindow = true): int
	{
		$cacheTTL = $cacheTTL ?? $this->ttl;

		/* are we using the window option? */
		if ($useWindow) {
			/*
			let determine the window size based on there cache time to live length no more than 5 minutes
			if your traffic to the cache data is that light then cache stampede shouldn't be a problem
			*/
			$window = min(300, ceil($cacheTTL * .02));

			/* add it to the cache_ttl to get our "new" cache time to live */
			$cacheTTL += mt_rand(-$window, $window);
		}

		return $cacheTTL;
	}
} /* end class */
