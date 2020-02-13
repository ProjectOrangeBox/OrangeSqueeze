<?php

namespace projectorangebox\validation;

class RuleFinder
{
	static protected $found = [];

	static public function search(array $regexArray)
	{
		foreach ($regexArray as $regex) {
			foreach (\FS::regexGlob(__ROOT__, $regex) as $file) {
				self::$found[\strtolower(basename($file, '.php'))] = self::extractNameSpace($file);
			}
		}

		return self::$found;
	}

	static public function extractNameSpace(string $filepath): string
	{
		$contents = \FS::file_get_contents($filepath);

		// namespace projectorangebox\validation\rules;
		// class alpha_dash extends

		return '\\' . trim(self::between('namespace', ';', $contents)) . '\\' . trim(self::between('class', 'extends', $contents));
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
