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

namespace projectorangebox\html\plugins;

use projectorangebox\pear\PearPluginAbstract;

class Pear_html extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		echo service('html')->get($name);
	}
} /* end plugin */
