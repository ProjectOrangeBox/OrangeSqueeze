<?php

namespace projectorangebox\view\parsers\page\plugins;

use projectorangebox\view\parsers\page\pear\PearAbstract;

class Pear_getVar extends PearAbstract
{
	public function render(string $name = null)
	{
		return service('view')->page->getPageVar($name);
	}
} /* end plugin */
