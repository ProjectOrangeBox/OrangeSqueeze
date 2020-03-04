<?php

namespace projectorangebox\view\parsers\page\plugins;

use Pear;
use Exception;
use projectorangebox\view\parsers\page\Page;
use projectorangebox\view\parsers\page\pear\PearAbstract;

class Pear_end extends PearAbstract
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

		/* replace what ever is in this block */
		service('view')->page->setVar($name, $output, Page::SINGLE);
	}
} /* end plugin */
