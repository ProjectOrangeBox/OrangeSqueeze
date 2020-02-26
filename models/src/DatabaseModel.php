<?php

namespace projectorangebox\models;

use PDO;
use projectorangebox\common\DataModel;

class DatabaseModel extends DataModel
{
	protected $db;
	protected $table;
	protected $primaryKey = 'id';
	protected $lastPrimaryKey = 0;
	protected $connection = 'default';

	public function __construct($db, array $config = [])
	{
		parent::__construct($config);

		$this->db = $db;

		if (empty($this->table)) {
			throw new \Exception('Database Model Table Not Specified.');
		}
	}

	public function create(array $columns): bool
	{
		if ($this->validate($columns, $this->ruleSets['insert'])) {
			$this->db->insert($this->table, $columns);

			$this->lastPrimaryKey = $this->db->id();
		}

		return $this->captureDBError();
	}

	public function update(array $columns): bool
	{
		return $this->updateBy($this->primaryKey, $columns);
	}

	public function updateBy(string $columnName, array $columns): bool
	{
		if ($this->validate($columns, $this->ruleSets['update'])) {
			$where = [$columnName => $columns[$columnName]];

			unset($columns[$columnName]);

			$this->db->update($this->table, $columns, $where);
		}

		return $this->captureDBError();
	}

	public function get(int $id = null)
	{
		$where = ($id) ? [$this->primaryKey => $id] : null;

		return $this->get($this->table, '*', $where);
	}

	public function getBy(string $column, string $value)
	{
		return $this->get($this->table, '*', [$column => $value]);
	}

	public function read(int $id = null): array
	{
		$where = ($id) ? [$this->primaryKey => $id] : null;

		return $this->db->select($this->table, '*', $where);
	}

	public function readBy(string $column, string $value): array
	{
		return $this->db->select($this->table, '*', [$column => $value]);
	}

	public function delete(string $primaryId): bool
	{
		return $this->deleteBy($this->primaryKey, $primaryId);
	}

	public function deleteBy(string $primaryKey, string $primaryId): bool
	{
		$this->db->delete($this->table, [$primaryKey => $primaryId]);

		return $this->captureDBError();
	}

	/* PDO simple as beans select query wrapper */
	protected function query(string $sql, array $execute = [], $onEmpty = false)
	{
		$query = $this->db->pdo->prepare($sql);

		$query->execute($execute);

		$count = $query->rowCount();

		switch ($count) {
			case 0:
				$return = $onEmpty;
				break;
			case 1:
				$return = $query->fetch(PDO::FETCH_ASSOC);
				break;
			default:
				$return = $query;
		}

		$this->captureDBError();

		return $return;
	}

	protected function captureDBError(): bool
	{
		$errorInfo = $this->db->pdo->errorInfo();

		/* database error */
		if (!empty($errorInfo[2])) {
			$this->errorCode = $errorInfo[1]; /* Driver-specific error code. */
			$this->errors = ['db' => $errorInfo[2]]; /* Driver-specific error message. */
		}

		return ($this->errorCode === 0);
	}

	public function _isUnique($columnName, $columnValue, $primary): bool
	{
		$records = $this->db->select($this->table, $this->primaryKey . ',' . $columnName, [$columnName => $columnValue, 'LIMIT' => 3]);

		$rows_found = count($records);

		/* none? then we are good! */
		if ($rows_found == 0) {
			return true; /* test for really true === */
		}

		/* more than 1? that's really bad return false */
		if ($rows_found > 1) {
			return false; /* test for really false === */
		}

		return ($records[0] == $primary);
	}
}