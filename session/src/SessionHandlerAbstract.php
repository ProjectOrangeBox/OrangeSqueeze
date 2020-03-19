<?php

namespace projectorangebox\session;

abstract class SessionHandlerAbstract
{
	protected $keyLength = 64;
	protected $lifetime = 0;
	protected $sessionPrefix = 'session/';

	public function __construct(array &$config)
	{
		$this->keyLength = $config['key length'] ?? $this->keyLength;
		$this->lifetime = $config['lifetime'] ?? $this->lifetime;
		$this->sessionPrefix = $config['prefix'] ?? $this->sessionPrefix;
	}

	public function create_sid(): string
	{
		return substr(bin2hex(openssl_random_pseudo_bytes(256)), 0, $this->keyLength);
	}

	public function open($save_path, $session_name): bool
	{
		return true;
	}

	public function close(): bool
	{
		return true;
	}

	public function destroy($session_id): bool
	{
		return true;
	}

	public function read($session_id): string
	{
		return '';
	}

	public function write($session_id, $session_data): bool
	{
		return true;
	}

	public function gc($maxlifetime): int
	{
		return true;
	}
}/* end class */
