<?php

namespace projectorangebox\models;

use Exception;

class Models implements ModelsInterface
{
	protected $models = [];

	public function __construct(array $config)
	{
		foreach ($config['models'] as $modelName => $modelClass) {
			if (!\class_exists($modelClass, true)) {
				throw new Exception('Model "' . $modelName . '" class "' . $modelClass . '" not found.');
			}

			$this->models[strtolower($modelName)] = new $modelClass($config);
		}
	}

	public function __get(string $name)
	{
		if (!isset($this->models[strtolower($name)])) {
			throw new Exception('Model "' . $name . '" not found.');
		}

		return $this->models[strtolower($name)];
	}

	public function has(string $name): bool
	{
		return isset($this->models[strtolower($name)]);
	}
}
