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

use pear;
use projectorangebox\page\PearAbstract;

/**
 * Validation Filter
 *
 * @help start a page section.
 *
 * @package CodeIgniter / Orange
 * @author Don Myers
 * @copyright 2019
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v2.0
 *
 */

class Pear_section extends PearAbstract
{
	public function render(string $name = null)
	{
		pear::$fragment[$name] = $name;
		ob_start();
	}
} /* end plugin */
