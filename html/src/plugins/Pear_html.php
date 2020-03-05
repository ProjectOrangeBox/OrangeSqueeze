<?php

namespace projectorangebox\html\plugins;

use projectorangebox\view\parsers\page\pear\PearPluginAbstract;

class Pear_html extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		echo service('html')->get($name);
	}
} /* end plugin */
