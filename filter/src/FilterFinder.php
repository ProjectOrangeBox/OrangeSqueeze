<?php

namespace projectorangebox\filter;

use FS;

class FilterFinder
{
	static public function search(array $regexPaths)
	{
		$found = [];

		foreach ($regexPaths as $regex) {
			foreach (FS::regexGlob($regex) as $match) {
				$found[\strtolower($match['key'])] = self::extractNameSpace($match[0]);
			}
		}

		return $found;
	}

	static public function extractNameSpace(string $filepath): string
	{
		$contents = \FS::file_get_contents($filepath);

		// namespace projectorangebox\validation\rules;
		// class alpha_dash extends

		return '\\' . trim(self::between('namespace ', ';', $contents)) . '\\' . trim(self::between('class ', ' extends ', $contents));
	}

	static public function between($start, $end, $string)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);

		if ($ini == 0) return '';

		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;

		return substr($string, $ini, $len);
	}
}
