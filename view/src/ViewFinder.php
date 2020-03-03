<?php

namespace projectorangebox\view;

use FS;

class ViewFinder
{
	static public function search(array $regexPaths)
	{
		$found = [];

		foreach ($regexPaths as $regex) {
			foreach (FS::regexGlob($regex) as $match) {
				$found[$match['key']] = $match[0];
			}
		}

		return $found;
	}
}
