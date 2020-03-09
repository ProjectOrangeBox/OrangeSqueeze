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

namespace projectorangebox\view;

interface ParserInterface
{
	public function __construct(array &$config);
	public function exists(string $name): bool;
	public function addView(string $name, string $value): ParserInterface;
	public function parse(string $view, array $data = []): string;
	public function parseString(string $string, array $data = []): string;
}
