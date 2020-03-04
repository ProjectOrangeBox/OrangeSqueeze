<?php

namespace projectorangebox\view\parsers\page\plugins;

use projectorangebox\view\parsers\page\pear\PearAbstract;

class Pear_getBlock extends PearAbstract
{
	public function render(string $name = null)
	{
		return service('view')->page->getBlock($name);
	}
} /* end plugin */
