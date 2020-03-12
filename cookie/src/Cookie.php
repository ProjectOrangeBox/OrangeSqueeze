<?php

namespace projectorangebox\cookie;

use projectorangebox\cookie\CookieInterface;

class Cookie implements CookieInterface
{
	protected $config = [];
	protected $cookies = [];

	public function __construct(array &$config)
	{
		$this->config = $config;

		$params = session_get_cookie_params();

		foreach ($params as $key => $value) {
			$this->config[$key] = $this->config[$key] ?? $value;
		}

		$this->cookies = $config['mock cookies'] ?? $_COOKIE;
	}

	public function set(string $name, string $value, $expire = null, string $domain = null, string $path = null, bool $secure = null, bool $httponly = null): bool
	{
		/* get the session cookie parameters */
		if ($expire === null) {
			$expire = $this->config['lifetime'];
		} elseif (is_string($expire)) {
			$expire = strtotime($expire, 0); /* in seconds */
		}

		$path = $path ?? $this->config['path'];
		$domain = $domain ?? $this->config['domain'];
		$secure = $secure ?? $this->config['secure'];
		$httponly = $path ?? $this->config['httponly'];

		return setcookie($name, $value, time() + $expire, $path, $domain, $secure, $httponly);
	}

	public function get(string $name, string $default = null)
	{
		return $this->cookies[$name] ?? $default;
	}

	public function has(string $name): bool
	{
		return (isset($this->cookies[$name])) ? true : false;
	}

	public function remove(string $name, string $domain = null, string $path = null): bool
	{
		return $this->set($name, '', -600, $domain, $path);
	}
} /* end class */
