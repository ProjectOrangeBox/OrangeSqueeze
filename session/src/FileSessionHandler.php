<?php

namespace projectorangebox\session;

use SessionHandlerInterface;
use SessionIdInterface;

class FileSessionHandler implements SessionHandlerInterface, SessionIdInterface
{
	protected $savePath;

	public function close(): bool
	{
		return true;
	}

	public function create_sid(): string
	{
		return sha1(uniqid('', true));
	}

	public function destroy($session_id): bool
	{
		$file = $this->savePath . '/sess_' . $session_id;

		if (\FS::file_exists($file)) {
			\FS::unlink($file);
		}

		return true;
	}

	public function gc($maxlifetime): int
	{
		$file = $this->savePath . '/sess_*';

		foreach (\fs::glob($file) as $file) {
			if (\FS::filemtime($file) + $maxlifetime < time() && \FS::file_exists($file)) {
				\FS::unlink($file);
			}
		}

		return true;
	}

	public function open($save_path, $session_name): bool
	{
		$this->savePath = $save_path;

		if (!\FS::is_dir($this->savePath)) {
			\FS::mkdir($this->savePath, 0777);
		}

		return true;
	}

	public function read($session_id): string
	{
		$file = $this->savePath . '/sess_' . $session_id;

		return (\FS::file_exists($file)) ? \FS::file_get_contents($file) : '';
	}

	public function write($session_id, $session_data): bool
	{
		$file = $this->savePath . '/sess_' . $session_id;

		return !(\FS::file_put_contents($file, $session_data, LOCK_EX) === false);
	}
}
