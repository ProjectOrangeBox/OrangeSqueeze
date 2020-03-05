<?php

namespace projectorangebox\view\parsers\page\pear;

interface PearInterface
{
	public static function _construct(array $plugins): void;
	public static function __callStatic(string $name, array $arguments = []);
}
