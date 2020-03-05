<?php

#namespace \

use projectorangebox\view\parsers\page\pear\PearInterface;
use projectorangebox\view\parsers\page\pear\PearPluginAbstract;
use projectorangebox\common\exceptions\php\ClassNotFoundException;
use projectorangebox\common\exceptions\mvc\PluginNotFoundException;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Pear implements PearInterface
{
	protected static $setup = false;
	protected static $plugins = [];

	public static $fragment = [];
	public static $fragmentContents = [];

	public static function __callStatic(string $name, array $arguments = [])
	{
		self::setup();

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

	/**
	 * little hacky single this is a global class and which mean this file
	 * was loaded by composer autoload but at that point everything
	 * isn't setup so we set it up the first time
	 * pear:: is called which is after everything is setup
	 */
	public static function setup(): void
	{
		if (!self::$setup) {
			$config = service('config')->get('pear', []);

			self::$plugins = cache('pear.plugins', function () use ($config) {
			});

			self::$setup = true;
		}
	}
} /* end class */
