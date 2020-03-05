<?php

namespace projectorangebox\view\parsers\page\plugins;

use Pear;
use projectorangebox\view\parsers\page\pear\PearPluginAbstract;

class Pear_getBlock extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		echo Pear::$fragmentContents[$name] ?? '';
	}
} /* end plugin */
