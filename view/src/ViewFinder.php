<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

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
