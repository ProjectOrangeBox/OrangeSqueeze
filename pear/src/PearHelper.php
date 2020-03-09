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
