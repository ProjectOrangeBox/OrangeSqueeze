<?php

namespace projectorangebox\models;

use projectorangebox\models\MedooDatabaseModel;
use projectorangebox\validation\ValidateInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class MedooValidateDatabaseModel extends MedooDatabaseModel implements DatabaseModelInterface
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
