<?php

namespace projectorangebox\view\parsers;

interface ParserInterface
{
	public function __construct(array $config, array $views);
	public function exists(string $name): bool;
	public function add(string $name, string $value): ParserInterface;
	public function parse(string $view, array $data = []): string;
	public function parse_string(string $string, array $data = []): string;
}
