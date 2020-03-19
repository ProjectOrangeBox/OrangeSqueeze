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

use SessionIdInterface;
use SessionHandlerInterface;
use projectorangebox\session\SessionHandlerAbstract;

class MemcacheSessionHandler extends SessionHandlerAbstract implements SessionHandlerInterface, SessionIdInterface
{
	protected $mcache;

	public function __construct(array &$config)
	{
		parent::__construct($config);

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

	public function destroy($session_id): bool
	{
		return $this->mcache->delete($this->sessionPrefix . $session_id);
	}

	public function read($session_id): string
	{
		return $this->mcache->get($this->sessionPrefix . $session_id) ?? '';
	}

	public function write($session_id, $session_data): bool
	{
		if ($this->mcache instanceof \Memcached) {
			return $this->mcache->set($this->sessionPrefix . $session_id, $session_data, $this->lifetime);
		} elseif ($this->mcache instanceof \Memcache) {
			return $this->mcache->set($this->sessionPrefix . $session_id, $session_data, 0, $this->lifetime);
		}

		return true;
	}
} /* end class */
