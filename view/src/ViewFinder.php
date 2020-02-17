<?php

namespace projectorangebox\view;

class ViewFinder
{
	static protected $found = [];

	static public function search($regexPaths)
	{
		foreach ($regexPaths as $regex) {
			foreach (\FS::regexGlob($regex) as $match) {
				self::$found[$match['key']] = $match[0];
			}
		}

		return self::$found;
	}
}
