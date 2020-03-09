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

namespace projectorangebox\auth;

interface AuthInterface
{
	public function __construct(array &$config);
	public function userId(): int;

	public function error(): string;
	public function hasError(): bool;

	public function login(string $login, string $password): bool;
	public function logout(): bool;
	public function refresh(): bool;
}
