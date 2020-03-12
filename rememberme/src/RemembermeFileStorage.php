<?php

namespace projectorangebox\rememberme;

use FS;
use Exception;

class RemembermeFileStorage implements RemembermeStorageInterface
{
	protected $config = [];
	protected $folder = '';

	public function __construct(array &$config)
	{
		$this->config = $config;

		if (!isset($this->config['key'])) {
			throw new Exception('Please provide a Shared secret key.');
		}

		$this->folder = $config['folder'] ?? '';

		FS::mkdir($this->folder);
	}

	public function create(string $token, int $userId, int $expireSeconds): bool
	{
		return (FS::file_put_contents($this->path($token), \json_encode(['token' => $token, 'userid' => $userId, 'expires' => time() + $expireSeconds])) > 0);
	}

	public function read(string $token): int
	{
		$userid = 0;

		if (FS::file_exists($this->path($token))) {
			$record = \json_decode(FS::file_get_contents($this->path($token)));

			if ($record['expires'] >= time()) {
				$userid = (int) $record['userid'];
			}
		}

		return $userid;
	}

	public function update(string $token, int $userId, int $expireDatabase): bool
	{
		$this->delete($token);

		return $this->create($token, $userId, $expireDatabase);
	}

	public function delete(string $token): bool
	{
		$success = true;

		if (FS::file_exists($this->path($token))) {
			$success = FS::unlink($this->path($token));
		}

		return $success;
	}

	public function garbageCollection(): bool
	{
		$files = FS::glob('*.txt', 0, false, false);

		foreach ($files as $file) {
			if (!$this->read(basename($file))) {
				$this->delete(basename($file));
			}
		}

		return true;
	}

	protected function path(string $key): string
	{
		return $this->folder . '/token.' . $key . '.json';
	}
}/* end class */
