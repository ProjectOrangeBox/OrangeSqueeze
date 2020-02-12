<?php

namespace projectorangebox\view;

class ViewFinder
{
	static protected $found = [];

	static public function search($paths)
	{
		foreach ((array) $paths as $path) {
			self::_search($path);
		}

		return self::$found;
	}

	static public function _search(string $path): void
	{
		$pathinfo = \pathinfo($path);

		$stripFromBeginning = $pathinfo['dirname'];
		$stripLen = \strlen($stripFromBeginning) + 1;

		$extension = $pathinfo['extension'];
		$extensionLen = \strlen($extension) + 1;

		foreach (\FS::glob($path, 0, true, true) as $file) {
			self::$found[\strtolower(\substr($file, $stripLen, -$extensionLen))] = $file;
		}
	}
}
