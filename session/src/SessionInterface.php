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

namespace projectorangebox\session;

interface SessionInterface
{
	public function __construct(array &$config);
	public function __set($name, $value);
	public function __get(string $name);
	public function __unset($key);
	public function __isset($key);
	public function start();
	public function destroy();
	public function set(string $key, $value);
	public function get($key, $default = null);
	public function merge($key, $value);
	public function delete($key);
	public function clear();
	public function exists($key);
	public function id($new = false);
	public function count();
	public function getIterator();
	public function offsetExists($offset);
	public function offsetGet($offset);
	public function offsetSet($offset, $value);
	public function offsetUnset($offset);
}
