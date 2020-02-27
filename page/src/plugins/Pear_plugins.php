<?php

namespace projectorangebox\page\plugins;

use Pear;
use projectorangebox\page\PearAbstract;

/**
 * Validation Filter
 *
 * @help load pear plugin(s).
 *
 * @package CodeIgniter / Orange
 * @author Don Myers
 * @copyright 2019
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v2.0
 *
 */

class Pear_plugins extends PearAbstract
{
	public function render($plugins = null)
	{
		/* load the plug in and throw a error if it's not found */
		foreach ((array) $plugins as $plugin) {
			/* setup default of no parameters */
			$parameters = [];

			/* do we have parameters if so split them out */
			if (preg_match('/^(?<plugin>.*?)\((?<parameters>.*?)\)$/', $plugin, $matches)) {
				$plugin  = $matches['plugin'];
				$parameters = explode(',', $matches['parameters']);
			}

			Pear::__callStatic($plugin, $parameters);
		}
	}
} /* end plugin */
