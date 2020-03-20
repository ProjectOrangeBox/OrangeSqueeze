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

namespace projectorangebox\session\handlers;

use FS;
use SessionIdInterface;
use SessionHandlerInterface;
use projectorangebox\session\SessionHandlerAbstract;

class FileSessionHandler extends SessionHandlerAbstract implements SessionHandlerInterface, SessionIdInterface
{
	protected $savePath;

	public function open($save_path, $session_name): bool
	{
		$save_path = FS::resolve($this->config['file']['path']);

		if (!FS::is_dir($save_path)) {
			FS::mkdir($save_path, 0777);
		}

		$this->savePath = $save_path . '/' . $this->sessionPrefix;

		return true;
	}

	public function destroy($session_id): bool
	{
		$file = $this->savePath  . $session_id;

		return (FS::file_exists($file)) ? FS::unlink($file) : true;
	}

	public function read($session_id): string
	{
		$file = $this->savePath  . $session_id;

		return (FS::file_exists($file)) ? FS::file_get_contents($file) : '';
	}

	public function write($session_id, $session_data): bool
	{
		return (FS::file_put_contents($this->savePath  . $session_id, $session_data, LOCK_EX) > 0);
	}

	public function gc($maxlifetime): int
	{
		foreach (FS::glob($this->savePath . '*', 0, false, false) as $file) {
			if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
				unlink($file);
			}
		}

		return true;
	}
} /* end class */
