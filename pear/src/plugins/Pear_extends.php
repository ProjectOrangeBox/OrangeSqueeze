<?php

namespace projectorangebox\view\parsers\page\plugins;

use projectorangebox\view\parsers\page\pear\PearPluginAbstract;

class Pear_extends extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		service('view')->page->extend($name);
	}
} /* end plugin */
