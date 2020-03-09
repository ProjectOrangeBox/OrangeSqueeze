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

use projectorangebox\view\ParserInterface;

interface ViewInterface
{
	public function __construct(array &$config);

	/* get handler for extension */
	public function __get(string $extension);

	/* set handler for extension */
	public function __set(string $extension, ParserInterface $parser);

	public function build(string $view): string;

	public function var(string $name, $value): ViewInterface;
	public function vars(array $array): ViewInterface;
	public function getVar(string $name); /* mixed */
	public function getVars(): array;
	public function clearVars(): ViewInterface;
}
