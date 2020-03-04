<?php

namespace projectorangebox\view\parsers\page\plugins;

use projectorangebox\view\parsers\page\Page;
use projectorangebox\view\parsers\page\pear\PearAbstract;

class Pear_include extends PearAbstract
{
	public function render(string $view = null, array $data = [], $name = true)
	{
		$viewService = service('view');

		if ($viewService->page->exists($view)) {
			$output = $viewService->page->_parse($viewService->page->findView($view), $data, $name);

			if (is_string($name)) {
				$viewService->page->setVar($name, $output, Page::SINGLE);
			} else {
				echo $output;
			}
		}
	}
} /* end plugin */
