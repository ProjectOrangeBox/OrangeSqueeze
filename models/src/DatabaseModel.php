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

namespace projectorangebox\models;

use projectorangebox\common\DataModel;

class DatabaseModel extends DataModel
{
	protected $db;
	protected $tablename;
	protected $primaryKey = 'id';
	protected $connection = 'default database';

	public function __construct($db, array &$config)
	{
		parent::__construct($config);

		$this->db = $db;

		if (empty($this->tablename)) {
			throw new \Exception('Database Model Table Not Specified.');
		}
	}
}
