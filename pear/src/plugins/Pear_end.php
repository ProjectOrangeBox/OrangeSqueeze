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

namespace projectorangebox\pear\plugins;

use Pear;
use Exception;
use projectorangebox\pear\PearPluginAbstract;

class Pear_end extends PearPluginAbstract
{
	public function render()
	{
		if (!count(Pear::$fragment)) {
			throw new Exception('Cannot end section because you are not in a section.');
		}

		/* Pop the element off the end of array */
		$name = array_pop(Pear::$fragment);

		/* Flush the output buffer, return it as a string and turn off output buffering */
		$output = ob_get_clean();

		Pear::$fragmentContents[$name] = $output;
	}
} /* end plugin */
