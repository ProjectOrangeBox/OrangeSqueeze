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

use Redis;
use SessionIdInterface;
use SessionHandlerInterface;
use projectorangebox\session\SessionHandlerAbstract;

class RedisSessionHandler extends SessionHandlerAbstract implements SessionHandlerInterface, SessionIdInterface
{
	protected $redis;

	public function __construct(array &$config)
	{
		parent::__construct($config);

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

	public function destroy($session_id): bool
	{
		return ($this->redis->delete($this->sessionPrefix . $session_id) > 0);
	}

	public function read($session_id): string
	{
		$value = $this->redis->get($this->sessionPrefix . $session_id);

		return $value ?? '';
	}

	public function write($session_id, $session_data): bool
	{
		return $this->redis->set($this->sessionPrefix . $session_id, $session_data, $this->lifetime);
	}

	public function __destruct()
	{
		if ($this->redis) {
			$this->redis->close();
		}
	}
} /* end class */
