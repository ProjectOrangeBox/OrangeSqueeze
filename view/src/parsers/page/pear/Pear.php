<?php

#namespace \

use projectorangebox\view\parsers\page\pear\PearInterface;
use projectorangebox\view\parsers\page\pear\PearPluginAbstract;
use projectorangebox\common\exceptions\php\ClassNotFoundException;
use projectorangebox\common\exceptions\mvc\PluginNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Pear implements PearInterface
{
	protected static $plugins = [];

	public static $fragment = [];
	public static $fragmentContents = [];

	public static function _construct(array $plugins): void
	{
		self::$plugins = $plugins;
	}

	public static function __callStatic(string $name, array $arguments = [])
	{
		println($name);

		$name = 'pear_' . strtolower($name);

		\log_message('info', 'Pear::__callStatic::' . $name);

		if (!isset(self::$plugins[$name])) {
			throw new PluginNotFoundException($name);
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
