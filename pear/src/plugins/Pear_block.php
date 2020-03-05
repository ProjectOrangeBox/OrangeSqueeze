<?php

namespace projectorangebox\pear\plugins;

use Pear;
use projectorangebox\pear\PearPluginAbstract;

class Pear_block extends PearPluginAbstract
{
	public function render(string $name = null)
	{
		Pear::$fragment[$name] = $name;

		ob_start();
	}
} /* end plugin */
