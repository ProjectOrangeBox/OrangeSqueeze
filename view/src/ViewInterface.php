<?php

namespace projectorangebox\view;

use projectorangebox\view\parsers\ParserInterface;

interface ViewInterface
{
	public function __construct(array &$config);

	/* get handler for extension */
	public function __get(string $extension);

	/* set handler for extension */
	public function __set(string $extension, ParserInterface $parser);

	public function parse(string $view, array $data = [], string $ext = null): string;
	public function parseString(string $string, array $data = [], string $ext = null): string;

	public function var(string $name, $value): ViewInterface;
	public function vars(array $array): ViewInterface;
	public function getVar(string $name); /* mixed */
	public function getVars(): array;
	public function clearVars(): ViewInterface;
}
