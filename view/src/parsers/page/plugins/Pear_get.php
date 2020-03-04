<?php

namespace projectorangebox\view\parsers\page\plugins;

use projectorangebox\view\parsers\page\pear\PearAbstract;

class Pear_get extends PearAbstract
{
	public function render(string $name = null)
	{
		return service('view')->page->getVar($name);
	}
} /* end plugin */
