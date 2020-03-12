<?php

namespace projectorangebox\rememberme;

use Exception;
use projectorangebox\cookie\CookieInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Rememberme implements RemembermeInterface
{
	protected $config = [];
	protected $cookieName = '';
	protected $cookieService;
	protected $storage;
	protected $rememberFor = '1 week';
	protected $hash = 'sha512';

	public function __construct(array &$config)
	{
		$this->config = $config;

		$this->hash = $this->config['hash'] ?? $this->hash;
		$this->rememberFor = $this->config['remember for'] ?? $this->rememberFor;

		$this->storage = $config['storage'];

		if (!($this->storage instanceof RemembermeStorageInterface)) {
			throw new IncorrectInterfaceException('RemembermeStorageInterface');
		}

		$this->cookieService = $config['cookieService'];

		if (!($this->cookieService instanceof CookieInterface)) {
			throw new IncorrectInterfaceException('CookieInterface');
		}

		$this->cookieName = $config['cookie name'] ?? 'thankyouforvisiting';

		if (mt_rand(0, 99) < ($config['gc percent'] ?? 50)) {
			$this->garbageCollection();
		}
	}

	public function save(int $userId): bool
	{
		if (!$this->cookieService->has($this->cookieName)) {
			$token = hash($this->hash, \uniqid(rand(), true));
			$expireSeconds = \strtotime($this->rememberFor, 0); /* in seconds */

			if (!$this->cookieService->set($this->cookieName, $token, $expireSeconds)) {
				throw new Exception('Could not set cookie.');
			}

			$this->storage->create($token, $userId, $expireSeconds);
		}

		return true;
	}

	/* get and push forward if present */
	public function get(): int
	{
		$userId = 0;

		$token = $this->cookieService->get($this->cookieName);

		if ($token) {
			$userId = $this->storage->read($token);

			if ($userId > 0) {
				/* push forward */
				$expireSeconds = \strtotime($this->rememberFor, 0);

				if (!$this->cookieService->set($this->cookieName, $token, $expireSeconds)) {
					throw new Exception('Could not set cookie.');
				}

				$this->storage->update($token, $userId, $expireSeconds);
			}
		}

		return $userId;
	}

	public function remove(): bool
	{
		$token = $this->cookieService->get($this->cookieName);

		if ($token) {
			$this->storage->delete($token);
		}

		return $this->cookieService->remove($this->cookieName);
	}

	public function garbageCollection(): bool
	{
		return $this->storage->garbageCollection();
	}
} /* end class */
