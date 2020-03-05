<?php

#namespace \

class Pear
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
			throw new \projectorangebox\common\exceptions\mvc\PluginNotFoundException($name);
		}

		$namespacedClass = self::$plugins[$name];

		if (!class_exists($namespacedClass, true)) {
			throw new \projectorangebox\common\exceptions\php\ClassNotFoundException($namespacedClass);
		}

		$plugin = new $namespacedClass;

		if (!($plugin instanceof \projectorangebox\pear\PearPluginAbstract)) {
			throw new \projectorangebox\common\exceptions\php\IncorrectInterfaceException('PearPluginAbstract');
		}

		if (!method_exists($plugin, 'render')) {
			throw new \BadMethodCallException('PearInterface');
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
				return \projectorangebox\pear\PluginFinder::search($config['search']);
			});

			self::$setup = true;
		}
	}
} /* end class */
