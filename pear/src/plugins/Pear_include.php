<?php

namespace projectorangebox\pear\plugins;

use Pear;
use projectorangebox\pear\PearPluginAbstract;

class Pear_include extends PearPluginAbstract
{
	public function render(string $view = null, array $data = [], $name = true)
	{
		$viewService = service('view');

		if ($templatePath = $viewService->page->getView($view)) {
			$output = $viewService->page->parseSingle($templatePath, $data);

			if (is_string($name)) {
				Pear::$fragmentContents[$name] = $output;
			} else {
				echo $output;
			}
		}
	}
} /* end plugin */
