<?php

namespace projectorangebox\models;

use projectorangebox\common\exceptions\mvc\ServiceNotFoundException;
use projectorangebox\common\exceptions\php\ClassNotFoundException;

class Models implements ModelsInterface
{
	protected $models = [];
	protected $config;

	public function __construct(array &$config)
	{
		$this->config = &$config;
	}

	public function __get(string $name)
	{
		$name = strtolower($name);

		if (!isset($this->models[$name])) {
			if (!isset($this->config['models'][$name])) {
				throw new ServiceNotFoundException($name);
			}

			$modelClass = $this->config['models'][$name];

			if (!\class_exists($modelClass, true)) {
				throw new ClassNotFoundException($modelClass);
			}

			$this->models[$name] = new $modelClass($this->config);
		}

		return $this->models[$name];
	}

	public function has(string $name): bool
	{
		return isset($this->models[strtolower($name)]);
	}
}
