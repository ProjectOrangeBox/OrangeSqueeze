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

	public function parse(string $view, array $data = []): string;

	public function parse_string(string $string, array $data = []): string;
}
