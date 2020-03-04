<?php

namespace projectorangebox\view\parsers\page\plugins;

use pear;
use projectorangebox\view\parsers\page\pear\PearAbstract;

class Pear_parent extends PearAbstract
{
	public function render(string $name = null)
	{
		$name = ($name) ?? end(pear::$fragment);

		echo service('view')->page->getBlock($name);
	}
} /* end plugin */
