<?php

namespace projectorangebox\cookie;

interface CookieInterface
{
	public function __construct(array &$config);
	public function set(string $name, string $value, $expire = null, string $domain = null, string $path = null, bool $secure = null, bool $httponly = null): bool;
	public function get(string $name, string $default = null);
	public function has(string $name): bool;
	public function remove(string $name, string $domain = null, string $path = null): bool;
}
