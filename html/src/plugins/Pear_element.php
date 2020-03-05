<?php

namespace projectorangebox\html\plugins;

use projectorangebox\pear\PearPluginAbstract;

class Pear_element extends PearpluginAbstract
{
	public function render($element, $attributes, $content = '')
	{
		echo service('html')->ary2element($element, $attributes, $content);
	}
} /* end plugin */
