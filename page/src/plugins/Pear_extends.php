<?php

namespace projectorangebox\page\plugins;

use projectorangebox\page\PearAbstract;

class Pear_extends extends PearAbstract
{
	public function render(string $name = null, array $data = [])
	{
		service('page')->vars($data)->extend($name);
	}
} /* end plugin */
