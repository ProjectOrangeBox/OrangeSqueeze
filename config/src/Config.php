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
use projectorangebox\config\ConfigInterface;
use projectorangebox\container\ContainerInterface;

class Config implements ConfigInterface
{
	protected $config = [];

	public function __construct(array &$config = [])
	{
		$this->config = &$config;
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
	 * Completely replace the entire Configuration Array
	 *
	 * @param array &$array
	 * @return ContainerInterface
	 */
	public function replace(array $array): ConfigInterface
	{
		$this->config = &$array;

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
			if (!isset($this->config[$segments[0]]) && isset($this->config['config']['folder'])) {
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
		/* load cached */
		if (isset($this->config['config']['cache folder']) && ($this->config['config']['environment'] == 'production' || $this->config['config']['debug'] != true)) {
			$this->loadFiles();
		} else {
			/* load a single */
			$this->import(FS::resolve(trim($this->config['config']['folder'], '/') . '/' . strtolower($filename) . '.php'));
		}
	}

	protected function loadFiles()
	{
		$cacheFile = trim($this->config['config']['cache folder'], '/') . '/combined-config.php';

		if (!FS::file_exists($cacheFile)) {
			foreach (FS::glob(trim($this->config['config']['folder'], '/') . '/*.php', 0, false, false) as $file) {
				$this->import($file);
			}

			FS::file_put_contents($cacheFile, FS::var_export_php($this->config));
		} else {
			$this->config = FS::require($cacheFile);
		}
	}

	protected function import(string $absolutePath): void
	{
		if (\file_exists($absolutePath)) {
			$array = require $absolutePath;

			if (\is_array($array)) {
				$this->config[strtolower(basename($absolutePath, '.php'))] = $array;
			}
		}
	}
} /* end class */
