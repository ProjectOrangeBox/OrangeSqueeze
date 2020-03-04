<?php

#namespace \

use projectorangebox\view\ViewInterface;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\view\parsers\page\pear\PearAbstract;
use projectorangebox\view\parsers\page\pear\PearInterface;
use projectorangebox\common\exceptions\php\ClassNotFoundException;
use projectorangebox\common\exceptions\mvc\PluginNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Pear implements PearInterface
{
	protected static $pageClass;
	protected static $plugins = [];

	public static $fragment = [];

	public static function _construct(array $plugins, ParserInterface $pageClass): void
	{
		self::$plugins = $plugins;
		self::$pageClass = $pageClass;
	}

	public static function __callStatic(string $name, array $arguments = [])
	{
		$name = 'pear_' . strtolower($name);

		\log_message('info', 'Pear::__callStatic::' . $name);

		if (!isset(self::$plugins[$name])) {
			throw new PluginNotFoundException($name);
		}

		$namespacedClass = self::$plugins[$name];

		if (!class_exists($namespacedClass, true)) {
			throw new ClassNotFoundException($namespacedClass);
		}

		$plugin = new $namespacedClass(self::$pageClass);

		if (!($plugin instanceof PearAbstract)) {
			throw new IncorrectInterfaceException('PearInterface');
		}

		/* using call_user_func_array because arguments is undetermined */
		return call_user_func_array([$plugin, 'render'], $arguments);
	}
} /* end class */
