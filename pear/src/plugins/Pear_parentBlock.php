<?php

namespace projectorangebox\view\parsers\page\plugins;

use pear;
use projectorangebox\view\parsers\page\pear\PearPluginAbstract;

class Pear_parentBlock extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		$name = ($name) ?? end(pear::$fragment);

		echo Pear::$fragmentContents[$name];
	}
} /* end plugin */
