<?php

namespace projectorangebox\page\plugins;

use pear;
use projectorangebox\page\PearAbstract;

class Pear_section extends PearAbstract
{
	public function render(string $name = null)
	{
		pear::$fragment[$name] = $name;
		ob_start();
	}
} /* end plugin */
