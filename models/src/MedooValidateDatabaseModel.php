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

use projectorangebox\models\MedooDatabaseModel;

class MedooValidateDatabaseModel extends MedooDatabaseModel
{
	public function insert(array $columns): int
	{
		$id = 0;

		if ($this->validate($columns, $this->ruleSets['insert'])) {
			$this->db->insert($this->tablename, $columns);

			$id = $this->db->id();
		}

		return $id ?? 0;
	}

	public function updateBy(array $columns, array $where): int
	{
		$count = 0;

		if ($this->validate($columns, $this->ruleSets['update'])) {
			$pdoStatement = $this->db->update($this->tablename, $columns, $this->buildMedooWhere($where));

			$count = $pdoStatement->rowCount();
		}

		return $count;
	}

	public function upsertBy(array $columns, array $where): int
	{
		$count = 0;

		if ($this->validate($columns, $this->ruleSets['upsert'])) {
			$where = (count($where)) ? $this->buildMedooWhere($where) : null;

			$pdoStatement = $this->db->replace($this->tablename, $columns, $where);

			$count = $pdoStatement->rowCount();
		}

		return $count;
	}

	protected function captureErrors(): void
	{
		parent::captureErrors();

		if (!$this->validateService->success()) {
			$this->errorCode = 406;
			$this->errorsArray = $this->validateService->errors();
		}
	}
} /* end class */
