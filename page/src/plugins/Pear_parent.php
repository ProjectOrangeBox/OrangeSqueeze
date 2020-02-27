<?php

namespace projectorangebox\page\plugins;

use pear;
use projectorangebox\page\PearAbstract;

class Pear_parent extends PearAbstract
{
	public function render(string $name = null)
	{
		$name = ($name) ?? end(pear::$fragment);

		echo service('page')->getVar($name);
	}
} /* end plugin */
