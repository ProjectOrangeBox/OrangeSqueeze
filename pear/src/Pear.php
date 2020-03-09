<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

#namespace \

use BadMethodCallException;
use projectorangebox\pear\PearPluginAbstract;
use projectorangebox\pear\exceptions\PearHelperNotFoundException;
use projectorangebox\pear\exceptions\PearPluginNotFoundException;
use projectorangebox\common\exceptions\php\ClassNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Pear
{
	protected static $plugins = [];

	public static $fragment = [];
	public static $fragmentContents = [];

	public static function __callStatic(string $name, array $arguments = [])
	{
		/* have we loaded the plugins from the pear helper yet? */
		if (empty(self::$plugins)) {
			if (service()->has('pear_helper')) {
				self::$plugins = service('pear_helper')->plugins();
			} else {
				throw new PearHelperNotFoundException();
			}
		}

		$name = 'pear_' . strtolower($name);

		\log_message('info', 'Pear::__callStatic::' . $name);

		if (!isset(self::$plugins[$name])) {
			throw new PearPluginNotFoundException($name);
		}

		$namespacedClass = self::$plugins[$name];

		if (!class_exists($namespacedClass, true)) {
			throw new ClassNotFoundException($namespacedClass);
		}

		$plugin = new $namespacedClass;

		if (!($plugin instanceof PearPluginAbstract)) {
			throw new IncorrectInterfaceException('PearPluginAbstract');
		}

		if (!method_exists($plugin, 'render')) {
			throw new BadMethodCallException('PearInterface');
		}

		/* using call_user_func_array because arguments is undetermined */
		return call_user_func_array([$plugin, 'render'], $arguments);
	}
} /* end class */
