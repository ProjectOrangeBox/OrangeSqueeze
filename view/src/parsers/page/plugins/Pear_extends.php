<?php

namespace projectorangebox\view\parsers\page\plugins;

use projectorangebox\view\parsers\page\pear\PearAbstract;

class Pear_extends extends PearAbstract
{
	public function render(string $name = null)
	{
		service('view')->page->extend($name);
	}
} /* end plugin */
