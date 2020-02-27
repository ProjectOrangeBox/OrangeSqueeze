<?php

namespace projectorangebox\page\plugins;

use Pear;
use Exception;
use projectorangebox\page\PearAbstract;

class Pear_end extends PearAbstract
{
	public function render()
	{
		if (!count(Pear::$fragment)) {
			throw new Exception('Cannot end section because you are not in a section.');
		}

		$name = array_pop(Pear::$fragment);
		$buffer = ob_get_contents();
		ob_end_clean();

		service('page')->var($name, $buffer);
	}
} /* end plugin */
