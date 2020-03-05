<?php

namespace projectorangebox\html\plugins;

use projectorangebox\view\parsers\page\pear\PearPluginAbstract;

class Pear_element extends PearpluginAbstract
{
	public function render($element, $attributes, $content = '')
	{
		echo service('html')->ary2element($element, $attributes, $content);
	}
} /* end plugin */
