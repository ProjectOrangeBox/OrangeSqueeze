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
 * @help get page variable. This allows for further processing before display.
 *
 * @package CodeIgniter / Orange
 * @author Don Myers
 * @copyright 2019
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v2.0
 *
 */

class Pear_page extends PearAbstract
{
	public function render(string $name = null)
	{
		return service('page')->var($name);
	}
} /* end plugin */
