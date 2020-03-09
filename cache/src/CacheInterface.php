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

namespace projectorangebox\cache;

interface CacheInterface
{
	public function __construct(array &$config);
	public function get(string $key); /* mixed */
	public function getMetadata(string $key): array;
	public function save(string $key, $value, int $ttl = null): bool;
	public function delete(string $key): bool;
	public function cache_info(): array;
	public function clean(): bool;
}
