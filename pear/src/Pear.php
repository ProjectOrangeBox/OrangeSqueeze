<?php

#namespace \

class Pear
{
	protected static $plugins = [];

	public static $fragment = [];
	public static $fragmentContents = [];

	public static function __callStatic(string $name, array $arguments = [])
	{
		if (empty(self::$plugins)) {
			self::$plugins = service('pear_helper')->plugins();
		}

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
} /* end class */
