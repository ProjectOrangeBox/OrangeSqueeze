<?php

namespace projectorangebox\models;

use PDO;
use Exception;
use Medoo\Medoo;
use projectorangebox\models\DatabaseModel;
use projectorangebox\models\DatabaseModelInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class MedooDatabaseModel extends DatabaseModel implements DatabaseModelInterface
{
	protected $columns = '*';
	protected $orderBy = [];
	protected $limit = 0;
	protected $pdoStatement;

	public function __construct(array $config)
	{
		$connection = $this->connection ?? 'default';

		parent::__construct(new \Medoo\Medoo([
			'database_type' => $config['connections'][$connection]['type'],
			'database_name' => $config['connections'][$connection]['name'],
			'server' => $config['connections'][$connection]['server'],
			'username' => $config['connections'][$connection]['username'],
			'password' => $config['connections'][$connection]['password'],
		]), $config);

		if (!($this->db instanceof Medoo)) {
			throw new IncorrectInterfaceException('Medoo');
		}
	}

	public function get($primaryId)
	{
		return $this->getBy([$this->primaryKey => $primaryId]);
	}

	public function getBy(array $where)
	{
		$results = $this->db->get($this->tablename, $this->columns, $this->buildMedooWhere($where));

		$this->reset();

		return $results;
	}

	public function getMany()
	{
		return $this->getManyBy([]);
	}

	public function getManyBy(array $where)
	{
		$where = (count($where)) ? $this->buildMedooWhere($where) : null;

		$results = $this->db->select($this->tablename, $this->columns, $where);

		$this->reset();

		return $results;
	}

	public function insert(array $columns): int
	{
		$this->db->insert($this->tablename, $columns);

		$this->reset();

		return $this->db->id() ?? 0;
	}

	public function update(array $columns): int
	{
		if (!isset($columns[$this->primaryKey])) {
			throw new Exception('Primary Key Not Supplied.');
		}

		$primaryId = $columns[$this->primaryKey];

		unset($columns[$this->primaryKey]);

		return $this->updateBy($columns, [$this->primaryKey => $primaryId]);
	}

	public function updateBy(array $columns, array $where): int
	{
		$this->pdoStatement = $this->db->update($this->tablename, $columns, $this->buildMedooWhere($where));

		$this->reset();

		return $this->pdoStatement->rowCount();
	}

	public function delete($primaryId): int
	{
		return $this->db->deleteBy([$this->primaryKey => $primaryId]);
	}

	public function deleteBy(array $where): int
	{
		$this->pdoStatement = $this->db->delete($this->tablename, $this->buildMedooWhere($where));

		$this->reset();

		return $this->pdoStatement->rowCount();
	}

	public function columns($columns): DatabaseModelInterface
	{
		$this->columns = $columns;

		return $this;
	}

	public function exists(array $where): bool
	{
		$bool = $this->db->db($this->tablename, $this->buildMedooWhere($where));

		$this->reset();

		return $bool;
	}

	public function upsert(array $columns): int
	{
		return $this->upsertBy($this->tablename, $columns, []);
	}

	public function upsertBy(array $columns, array $where): int
	{
		$where = (count($where)) ? $this->buildMedooWhere($where) : null;

		$this->pdoStatement = $this->db->replace($this->tablename, $columns, $where);

		$this->reset();

		return $this->pdoStatement->rowCount();
	}

	public function isUnique($columnName, $columnValue, $primary): bool
	{
		$records = $this->db->select($this->table, $this->primaryKey, [$columnName => $columnValue, 'LIMIT' => 3]);

		$rowsFound = count($records);

		/* none? then we are good! */
		if ($rowsFound == 0) {
			return true; /* test for really true === */
		}

		/* more than 1? that's really bad return false */
		if ($rowsFound > 1) {
			return false; /* test for really false === */
		}

		/* is the one we found this actual record? */
		return ($records[0] == $primary);
	}

	public function lastQuery(): string
	{
		return $this->db->last();
	}

	public function orderBy($column, $value = 'ASC'): DatabaseModelInterface
	{
		if (\is_array($column)) {
			foreach ($column as $c => $v) {
				$this->orderBy($c, $v);
			}
		} elseif (\is_string($column)) {
			if (is_string($value)) {
				$value = strtoupper($value);
			}

			$this->orderBy[] = [$column, $value];
		}

		return $this;
	}

	public function limit(int $limit): DatabaseModelInterface
	{
		$this->limit = $limit;

		return $this;
	}

	protected function captureErrors(): void
	{
		$errorInfo = $this->db->pdo->errorInfo();

		/* database error */
		if (!empty($errorInfo[2])) {
			$this->errorCode = 500; /* Driver-specific error code. */
			$this->errorsArray = ['db' => $errorInfo[1] . ': ' . $errorInfo[2]]; /* Driver-specific error message. */
		}
	}

	protected function reset(): DatabaseModelInterface
	{
		$this->errorCode = 0;
		$this->errorsArray = [];
		$this->orderBy = [];
		$this->limit = 0;
		$this->columns = '*';

		return $this;
	}

	protected function buildMedooWhere(array $array): array
	{
		$where = [];

		foreach ($array as $field => $rule) {
			if (\strpos($field, ' ') !== false) {
				$field = str_replace(' ', '[', $field) . ']';
			}

			$where[$field] = $rule;
		}

		if (count($this->orderBy)) {
			$where['ORDER'] = $this->orderBy;

			$this->orderBy = [];
		}

		if ($this->limit > 0) {
			$where['LIMIT'] = $this->limit;

			$this->limit = 0;
		}

		return $where;
	}

	protected function selectQuery(string $sql, array $execute = [], $onEmpty = false)
	{
		$query = $this->db->prepare($sql);
		$query->execute($execute);
		$records = $query->fetchAll(PDO::FETCH_ASSOC);

		switch (count($records)) {
			case 0:
				$return = $onEmpty;
				break;
			case 1:
				$return = $records[0];
				break;
			default:
				$return = $records;
		}

		return $return;
	}
}
