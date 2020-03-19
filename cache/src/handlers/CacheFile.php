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

namespace projectorangebox\cache\handlers;

use FS;
use projectorangebox\cache\CacheAbstract;
use projectorangebox\cache\CacheInterface;

class CacheFile extends CacheAbstract implements CacheInterface
{
	protected $cachePath = '';

	public function __construct(array &$config)
	{
		$this->ttl = $config['ttl'] ?? 0;

		/* make cache path ready to use */
		$this->cachePath = rtrim($config['file']['path'], '/') . '/';

		FS::mkdir($this->cachePath);
	}

	public function get(string $key) /* mixed */
	{
		\log_message('info', 'Cache Get ' . $key);

		$get = false;

		if (FS::file_exists($this->cachePath . $key . '.meta') && FS::file_exists($this->cachePath . $key)) {
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

	public function cacheInfo(): array
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
			$this->delete($path);
		}

		return true;
	}
} /* end class */
