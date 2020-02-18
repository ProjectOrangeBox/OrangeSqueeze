<?php

namespace projectorangebox\app;

use Medoo\Medoo;

class DatabaseModel
{
	protected $db;
	protected $table;

	public function __construct(Medoo $db)
	{
		$this->db = $db;

		if (empty($this->table)) {
			throw new \Exception('Database Model Table Not Specified.');
		}
	}
}
