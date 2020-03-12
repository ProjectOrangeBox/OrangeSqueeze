<?php

namespace projectorangebox\rememberme;

use PDO;
use Exception;
use PDOStatement;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class RemembermeDatabaseStorage implements RemembermeStorageInterface
{
	public function __construct(array &$config)
	{
		$this->config = $config;

		if (!isset($this->config['key'])) {
			throw new Exception('Please provide a Shared secret key.');
		}

		$this->db = $config['db'];

		if (!($this->db instanceof PDO)) {
			throw new IncorrectInterfaceException('PDO');
		}

		$this->tablename = $config['table'] ?? 'remember_me';
	}

	public function create(string $token, int $userId, int $expireSeconds): bool
	{
		$expireDatabase = date('Y-m-d H:i:s', time() + $expireSeconds); /* as string */

		return $this->query('insert into {{tablename}} (`token`,`userid`,`expires`) values (:token, :userid, :expires)', [':token' => $token, ':userid' => $userId, ':expires' => $expireDatabase]);
	}

	public function update(string $token, int $userId, int $expireDatabase): bool
	{
		return $this->query('update {{tablename}} set `userid` = :userid `expires` = :expires where `token` = :token', [':token' => $token, ':userid' => $userId, ':expires' => $expireDatabase]);
	}

	public function read(string $token): int
	{
		$userid = 0;

		$pdoStatement = $this->query('select userid from {{tablename}} where `token` = :token and expires >= now()', [':token' => $token], false);

		if ($pdoStatement) {
			$record = $pdoStatement->fetch(PDO::FETCH_ASSOC);

			$userid = (int) $record['userid'];
		}

		return $userid;
	}

	public function delete(string $token): Bool
	{
		return $this->query('delete from {{tablename}} where `token` = :token', [':token' => $token]);
	}

	public function garbageCollection(): bool
	{
		$this->query('delete from {{tablename}} where expires <= now()', []);

		return true;
	}

	protected function query(string $sql, array $execute, bool $asBool = true)
	{
		$sql = str_replace('{{tablename}}', '`' . $this->tablename . '`', $sql);

		$pdoStatement = $this->db->prepare($sql);

		$pdoStatement->execute($execute);

		// https: //docstore.mik.ua/orelly/java-ent/jenut/ch08_06.htm
		$error = $pdoStatement->errorInfo();

		if (!empty($error[2])) {
			\log_message('info', __METHOD__ . ' ' . $error[2]);
		}

		return ($asBool) ? (($pdoStatement) ? true : false) : $pdoStatement;
	}
} /* end class */
