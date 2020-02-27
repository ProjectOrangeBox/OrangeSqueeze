<?php

namespace projectorangebox\page\plugins;

use projectorangebox\page\PearAbstract;

class Pear_include extends PearAbstract
{
	public function render(string $view = null, array $data = [], $name = true)
	{
		if ($name === true) {
			echo service('page')->view($view, $data, $name);
		} else {
			service('page')->view($view, $data, $name);
		}
	}
} /* end plugin */
