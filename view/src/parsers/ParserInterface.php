<?php

namespace projectorangebox\view\parsers;

interface ParserInterface
{
	public function __construct(array &$config);
	public function exists(string $name): bool;
	public function addView(string $name, string $value): ParserInterface;
	public function parse(string $view, array $data = []): string;
	public function parseString(string $string, array $data = []): string;
}
