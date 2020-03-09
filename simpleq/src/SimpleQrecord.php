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

namespace projectorangebox\simpleq;

class SimpleQrecord
{
	public $payload;

	protected $record;
	protected $_parent;

	public function __construct($record, $parent)
	{
		$this->record = $record;
		$this->payload = $record->payload;
		$this->_parent = $parent;
	}

	public function __get($name)
	{
		return (isset($this->record->$name)) ? $this->record->$name : null;
	}

	public function complete()
	{
		$this->_parent->complete($this->token);
	}

	public function new()
	{
		$this->_parent->new($this->token);
	}

	public function error()
	{
		$this->_parent->error($this->token);
	}
} /* end class */
