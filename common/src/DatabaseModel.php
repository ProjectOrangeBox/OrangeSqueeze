<?php

namespace projectorangebox\common;

class DatabaseModel
{
	protected $db;
	protected $table;

	public function __construct($db)
	{
		$this->db = $db;

		if (empty($this->table)) {
			throw new \Exception('Database Model Table Not Specified.');
		}
	}
}
