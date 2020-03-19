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

namespace projectorangebox\config;

use FS;
use Exception;
use projectorangebox\config\ConfigInterface;
use projectorangebox\container\ContainerInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Config implements ConfigInterface
{
	protected $config = [];

	protected $configFolder;
	protected $container;

	public function __construct(array &$config)
	{
		if (!isset($config['config folder'])) {
			throw new Exception('Config folder not provided.');
		}

		$this->configFolder = trim($config['config folder'], '/');

		if (!FS::is_dir($this->configFolder)) {
			throw new Exception('Config folder ' . $this->configFolder . 'is not a valid path.');
		}

		/* load the config file */
		$this->loadFile('config');
	}

	/**
	 * Merge sent in array with current config
	 * array keys can be in dot notation
	 *
	 * @param array &$array
	 * @return ContainerInterface
	 */
	public function merge(array $array): ConfigInterface
	{
		foreach ($array as $index => $value) {
			$this->set($index, $value);
		}

		return $this;
	}

	/**
	 * Return entire configuration array
	 *
	 * @return array
	 */
	public function collect(): array
	{
		return $this->config;
	}

	/**
	 * Get a value with default based on dot notation
	 *
	 * @param string $notation
	 * @param mixed default if not found
	 * @return mixed
	 */
	public function get(string $notation,/* mixed */ $default = null) /* mixed */
	{
		$value = $default;

		if (array_key_exists($notation, $this->config)) {
			$value = $this->config[$notation];
		} else {
			$segments = explode('.', $notation);

			/**
			 * if the config array key is empty maybe they are trying to load a config file?
			 * have they also included a config file folder?
			 * because we need that to know where the config file are
			 */
			if (!isset($this->config[$segments[0]]) && $this->configFolder) {
				$this->loadFile($segments[0]);
			}

			$array = $this->config;

			foreach ($segments as $segment) {
				if (array_key_exists($segment, $array)) {
					$value = $array = $array[$segment];
				} else {
					$value = $default;
					break;
				}
			}
		}

		return $value;
	}

	/**
	 * Set a value based on dot notation
	 *
	 * @param string $notation
	 * @param mixed $value
	 * @return ConfigInterface
	 */
	public function set(string $notation, $value = null): ConfigInterface
	{
		$array = &$this->config;

		foreach (explode('.', $notation) as $step) {
			if (!isset($array[$step])) {
				$array[$step] = [];
			}

			$array = &$array[$step];
		}

		$array = $value;

		return $this;
	}

	/**
	 * Try to load a config file
	 * use the filename as the root level key and it's contents as the value
	 *
	 * @param string $filename
	 * @return void
	 */
	protected function loadFile(string $filename)
	{
		$absolutePath = FS::resolve($this->configFolder . '/' . strtolower($filename) . '.php');

		if (\file_exists($absolutePath)) {
			$array = require $absolutePath;

			if (\is_array($array)) {
				$this->config[basename($absolutePath, '.php')] = $array;
			}
		}
	}
} /* end class */
