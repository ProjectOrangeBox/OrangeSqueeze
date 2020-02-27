<?php

namespace projectorangebox\page\plugins;

use projectorangebox\page\PearAbstract;

class Pear_page extends PearAbstract
{
	public function render(string $name = null)
	{
		return service('page')->getVar($name);
	}
} /* end plugin */
