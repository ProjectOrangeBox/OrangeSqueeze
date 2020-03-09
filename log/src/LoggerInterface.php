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

namespace projectorangebox\log;

interface LoggerInterface
{
	public function __construct(array &$config);

	public function emergency(string $message, array $context = []): bool;
	public function alert(string $message, array $context = []): bool;
	public function critical(string $message, array $context = []): bool;
	public function error(string $message, array $context = []): bool;
	public function warning(string $message, array $context = []): bool;
	public function notice(string $message, array $context = []): bool;
	public function info(string $message, array $context = []): bool;
	public function debug(string $message, array $context = []): bool;

	public function log(string $level, string $message, array $context = []): bool;
}
