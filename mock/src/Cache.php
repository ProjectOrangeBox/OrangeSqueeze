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

namespace projectorangebox\mock;

use projectorangebox\cache\CacheInterface;

class Cache implements CacheInterface
{
	public function __construct(array &$config)
	{
	}

	public function get(string $key) /* mixed */
	{
		return false;
	}

	public function getMetadata(string $key): array
	{
		return [];
	}

	public function save(string $key, $value, int $ttl = null): bool
	{
		return true;
	}

	public function delete(string $key): bool
	{
		return true;
	}

	public function cache_info(): array
	{
		return [];
	}

	public function clean(): bool
	{
		return true;
	}
} /* end class */
