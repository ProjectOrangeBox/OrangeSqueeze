<?php

namespace projectorangebox\data;

class Data implements DataInterface
{
	protected $data = [];

	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	public function __get($name)
	{
		return $this->data[$name] ?? null;
	}

	public function __isset($name)
	{
		return isset($this->data[$name]);
	}

	public function __unset($name)
	{
		unset($this->data[$name]);
	}

	public function all(): array
	{
		return $this->data;
	}
} /* end class */
