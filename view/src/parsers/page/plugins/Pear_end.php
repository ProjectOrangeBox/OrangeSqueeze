<?php

namespace projectorangebox\view\parsers\page\plugins;

use Pear;
use Exception;
use projectorangebox\view\parsers\page\pear\PearPluginAbstract;

class Pear_end extends PearPluginAbstract
{
	public function render()
	{
		if (!count(Pear::$fragment)) {
			throw new Exception('Cannot end section because you are not in a section.');
		}

		/* Pop the element off the end of array */
		$name = array_pop(Pear::$fragment);

		/* Flush the output buffer, return it as a string and turn off output buffering */
		$output = ob_get_clean();

		Pear::$fragmentContents[$name] = $output;
	}
} /* end plugin */
