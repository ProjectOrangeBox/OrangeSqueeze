<?php

namespace projectorangebox\pear\plugins;

use pear;
use projectorangebox\pear\PearPluginAbstract;

class Pear_parentBlock extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		$name = ($name) ?? end(pear::$fragment);

		echo Pear::$fragmentContents[$name];
	}
} /* end plugin */
