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

namespace projectorangebox\session\handlers;

use PDO;
use SessionIdInterface;
use SessionHandlerInterface;
use projectorangebox\session\SessionHandlerAbstract;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class DatabaseSessionHandler extends SessionHandlerAbstract implements SessionHandlerInterface, SessionIdInterface
{
	protected $pdo;
	protected $tablename = 'session';

	public function __construct(array &$config)
	{
		parent::__construct($config);

		$this->tablename = $config['database']['tablename'] ?? $this->table;

		$this->pdo = $config['pdo'] ?? null;

		if (!($this->pdo instanceof PDO)) {
			throw new IncorrectInterfaceException('PDO');
		}
	}

	public function destroy($session_id): bool
	{
		return $this->query('DELETE FROM {%tablename%}	WHERE sessionId = :sessionId', ['sessionId' => $session_id]);
	}

	public function read($session_id): string
	{
		$sessionData = '';

		if ($pdoStatement = $this->query('SELECT sessionData FROM {%tablename%} WHERE sessionId = :sessionId', [':sessionId' => $session_id], false)) {
			if ($result = $pdoStatement->fetch(PDO::FETCH_ASSOC)) {
				$sessionData = $result['sessionData'];
			}
		}

		return $sessionData;
	}

	public function write($session_id, $session_data): bool
	{
		return $this->query('REPLACE INTO {%tablename%} (sessionId, sessionLastSave, sessionData) VALUES (:sessionId, :sessionLastSave, :sessionData)', [':sessionId' => $session_id, ':sessionLastSave' => time(), ':sessionData' => $session_data]);
	}

	public function gc($maxlifetime): int
	{
		return $this->query('DELETE FROM {%tablename%} WHERE sessionLastSave < :sessionLastSave	OR sessionLastSave IS NULL', ['sessionLastSave' => (time() - $maxlifetime)]);
	}

	protected function query(string $sql, array $execute, bool $asBool = true)
	{
		$sql = str_replace('{%tablename%}', '`' . $this->tablename . '`', $sql);

		$pdoStatement = $this->pdo->prepare($sql);

		$pdoStatement->execute($execute);

		// https: //docstore.mik.ua/orelly/java-ent/jenut/ch08_06.htm
		$error = $pdoStatement->errorInfo();

		if (!empty($error[2])) {
			\log_message('info', __METHOD__ . ' ' . $error[2]);
		}

		return ($asBool) ? (($pdoStatement) ? true : false) : $pdoStatement;
	}
} /* end class */
