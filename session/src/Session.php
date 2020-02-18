<?php

namespace projectorangebox\session;

use FS;
use Exception;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use projectorangebox\session\SessionInterface;

class Session implements ArrayAccess, Countable, IteratorAggregate, SessionInterface
{
	protected $config = [];

	public function __construct(array $config)
	{
		$this->config = $config;

		$defaults = [
			'lifetime'     => '20 minutes',
			'path'         => '/',
			'domain'       => null,
			'secure'       => false,
			'httponly'     => false,
			'name'         => 'session',
			'autorefresh'  => false,
			'handler'      => '\projectorangebox\session\FileSessionHandler',
			'ini_settings' => [],
		];

		$this->config = array_merge($defaults, $this->config);

		if (is_string($lifetime = $this->config['lifetime'])) {
			$this->config['lifetime'] = strtotime($lifetime) - time();
		}

		$this->start();
	}

	public function __set($name, $value)
	{
		return $this->set($name, $value);
	}

	public function __get(string $name)
	{
		return $this->get($name);
	}

	public function __unset($key)
	{
		$this->delete($key);
	}

	public function __isset($key)
	{
		return $this->exists($key);
	}

	public function start()
	{
		/* has a session already been started? */
		if (session_status() === PHP_SESSION_NONE) {
			$name = $this->config['name'];

			if (isset($this->config['session save path'])) {
				session_save_path(FS::resolve($this->config['session save path']));
			}

			session_set_cookie_params($this->config['lifetime'], $this->config['path'], $this->config['domain'], $this->config['secure'], $this->config['httponly']);

			/* Refresh session cookie when "inactive", else PHP won't know we want this to refresh */
			if ($this->config['autorefresh'] && isset($_COOKIE[$name])) {
				setcookie($name, $_COOKIE[$name], time() + $this->config['lifetime'], $this->config['path'], $this->config['domain'], $this->config['secure'], $this->config['httponly']);
			}

			session_name($name);

			$handler = new $this->config['handler']();

			if (!($handler instanceof \SessionHandlerInterface)) {
				throw new Exception('Session Handler is not an instance of SessionHandlerInterface.');
			}

			session_set_save_handler($handler, true);

			session_cache_limiter(false);

			session_start();
		}
	}

	public function destroy()
	{
		session_unset();
		session_destroy();
		session_write_close();

		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 4200, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
	}

	public function set(string $key, $value)
	{
		$_SESSION[$key] = $value;

		return $this;
	}

	public function get($key, $default = null)
	{
		return $this->exists($key) ? $_SESSION[$key] : $default;
	}

	public function merge($key, $value)
	{
		if (is_array($value) && is_array($old = $this->get($key))) {
			$value = array_merge_recursive($old, $value);
		}

		return $this->set($key, $value);
	}

	public function delete($key)
	{
		if ($this->exists($key)) {
			unset($_SESSION[$key]);
		}

		return $this;
	}

	public function clear()
	{
		$_SESSION = [];

		return $this;
	}

	public function exists($key)
	{
		return array_key_exists($key, $_SESSION);
	}

	public function id($new = false)
	{
		if ($new && session_id()) {
			session_regenerate_id(true);
		}

		return session_id() ?: '';
	}

	public function count()
	{
		return count($_SESSION);
	}

	public function getIterator()
	{
		return new \ArrayIterator($_SESSION);
	}

	public function offsetExists($offset)
	{
		return $this->exists($offset);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->delete($offset);
	}
}
