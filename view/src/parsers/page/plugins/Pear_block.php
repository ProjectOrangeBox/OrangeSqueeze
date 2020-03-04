<?php

namespace projectorangebox\view\parsers\page\plugins;

use Pear;
use projectorangebox\view\parsers\page\pear\PearAbstract;

class Pear_block extends PearAbstract
{
	public function render(string $name = null)
	{
		Pear::$fragment[$name] = $name;

		ob_start();
	}
} /* end plugin */
