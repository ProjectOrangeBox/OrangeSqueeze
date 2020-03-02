<?php

namespace projectorangebox\models;

use projectorangebox\common\DataModel;

class DatabaseModel extends DataModel
{
	protected $db;
	protected $tablename;
	protected $primaryKey = 'id';
	protected $connection = 'default database';

	public function __construct($db, array $config)
	{
		parent::__construct($config);

		$this->db = $db;

		if (empty($this->tablename)) {
			throw new \Exception('Database Model Table Not Specified.');
		}
	}
}
