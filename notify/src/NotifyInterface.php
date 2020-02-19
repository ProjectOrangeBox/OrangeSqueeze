<?php

namespace projectorangebox\notify;

use projectorangebox\session\SessionInterface;

interface NotifyInterface
{
	public function __construct(array $config, SessionInterface $sessionService);
	public function clear(): NotifyInterface;
	public function add(string $msg, string $status = null, array $payload = []): NotifyInterface;
	public function as(string $as): NotifyInterface;
	public function keep(): NotifyInterface;
	public function get(string $param = null);
	public function redirect(string $uri = '', string $method = 'auto', int $code = NULL): void;
}
