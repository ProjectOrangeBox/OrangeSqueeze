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
use projectorangebox\pear\PearPluginAbstract;

class Pear_include extends PearPluginAbstract
{
	public function render(string $view = null, array $data = [], $name = true)
	{
		$viewService = service('view');

		if ($viewService->page->exists($view)) {
			$output = $viewService->page->parse($view, $data);

			if (is_string($name)) {
				Pear::$fragmentContents[$name] = $output;
			} else {
				echo $output;
			}
		}
	}
} /* end plugin */
