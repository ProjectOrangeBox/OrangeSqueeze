<?php

namespace projectorangebox\page\plugins;

/**
 * Orange
 *
 * An open source extensions for CodeIgniter 3.x
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2019, Project Orange Box
 */

use projectorangebox\page\PearAbstract;

/**
 * Validation Filter
 *
 * @help Include another view file with optional data and the ability to capture into a variable.
 *
 * @package CodeIgniter / Orange
 * @author Don Myers
 * @copyright 2019
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v2.0
 *
 */

class Pear_include extends PearAbstract
{
	public function render(string $view = null, array $data = [], $name = true)
	{
		if ($name === true) {
			echo service('page')->view($view, $data, $name);
		} else {
			service('page')->view($view, $data, $name);
		}
	}
} /* end plugin */
