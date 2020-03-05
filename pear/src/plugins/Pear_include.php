<?php

namespace projectorangebox\view\parsers\page\plugins;

use Pear;
use projectorangebox\view\parsers\page\pear\PearPluginAbstract;

class Pear_include extends PearPluginAbstract
{
	public function render(string $view = null, array $data = [], $name = true)
	{
		$viewService = service('view');

		if ($templatePath = $viewService->page->getView($view)) {
			$output = $viewService->page->_parse($templatePath, $data, $name);

			if (is_string($name)) {
				Pear::$fragmentContents[$name] = $output;
			} else {
				echo $output;
			}
		}
	}
} /* end plugin */
