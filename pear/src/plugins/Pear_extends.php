<?php

namespace projectorangebox\pear\plugins;

use projectorangebox\pear\PearPluginAbstract;

class Pear_extends extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		service('view')->page->extend($name);
	}
} /* end plugin */
