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

namespace projectorangebox\request;

interface RequestInterface
{
	public function __construct(array &$config);
	public function isCli(): bool;
	public function isAjax(): bool;
	public function baseUrl(): string;
	public function requestMethod(): string;
	public function uri(): string;
	public function segments(): array;
	public function segment(int $index, $default = null); /* mixed */
	public function server(string $name = null, $default = null); /* mixed */
	public function request(string $name = null, $default = null); /* mixed */
	public function get(string $name = null, $default = null); /* mixed */
}
