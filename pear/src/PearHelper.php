<?php

namespace projectorangebox\pear;

class PearHelper
{
	protected $plugins = [];

	public function __construct(array &$config)
	{
		$this->plugins = $config['plugins'] ?? [];
	}

	public function plugins(): array
	{
		return $this->plugins;
	}
} /* end class */
