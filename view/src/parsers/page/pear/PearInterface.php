<?php

namespace projectorangebox\view\parsers\page\pear;

use projectorangebox\view\ViewInterface;
use projectorangebox\view\parsers\ParserInterface;

interface PearInterface
{
	public static function _construct(array $plugins, ParserInterface $pageClass): void;
	public static function __callStatic(string $name, array $arguments = []);
}
