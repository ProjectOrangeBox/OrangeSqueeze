<?php

namespace projectorangebox\pear\plugins;

use Pear;
use projectorangebox\pear\PearPluginAbstract;

class Pear_getBlock extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		echo Pear::$fragmentContents[$name] ?? '';
	}
} /* end plugin */
