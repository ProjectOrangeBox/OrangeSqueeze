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

namespace projectorangebox\auth;

use PDO;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Auth implements AuthInterface
{
	protected $config;
	protected $error = '';
	protected $userId = 0;

	/* database configuration */
	protected $db;
	protected $table;
	protected $usernameColumn;
	protected $passwordColumn;
	protected $isActiveColumn;

	public function __construct(array &$config)
	{
		\log_message('info', __METHOD__);

		/* defaults */
		$defaults = [
			'empty fields error' => 'Missing Required Field.',
			'general error' => 'Login Error.',
			'incorrect password error' => 'Login Error.',
			'not activated error' => 'Your user is not active.',
			'table' => 'users',
			'username column' => 'email',
			'is active column' => 'is_active',
			'password column' => 'password',
		];

		$this->config = array_replace($defaults, $config);

		$this->db = $config['db'];

		if (!($this->db instanceof PDO)) {
			throw new IncorrectInterfaceException('PDO');
		}

		$this->table = $config['table'];
		$this->usernameColumn = $config['username column'];
		$this->passwordColumn = $config['password column'];
		$this->isActiveColumn = $config['is active column'];

		$this->logout();
	}

	public function error(): string
	{
		return $this->error ?? '';
	}

	public function hasError(): bool
	{
		return !empty($this->error);
	}

	public function login(string $login, string $password): bool
	{
		$this->logout();

		/* Does login and password contain anything empty values are NOT permitted for any reason */
		if ((strlen(trim($login)) == 0) || (strlen(trim($password)) == 0)) {
			$this->error = $this->config['empty fields error'];

			/* fail */
			return false;
		}

		/* try to load the user */
		$user = $this->getUser($login);

		if (!is_array($user)) {
			$this->error = $this->config['general error'];

			/* fail */
			return false;
		}

		/* Verify the Password entered with what's in the database */
		if (password_verify($password, $user[$this->passwordColumn]) !== true) {
			$this->error = $this->config['incorrect password error'];

			/* fail */
			return false;
		}

		/* Is this user activated? */
		if ((int) $user[$this->isActiveColumn] !== 1) {
			$this->error = $this->config['not activated error'];

			/* fail */
			return false;
		}

		/* save our user id */
		$this->userId = (int) $user['id'];

		/* successful */
		return true;
	}

	public function logout(): bool
	{
		$this->error = '';
		$this->userId = 0;

		return true;
	}

	public function userId(): int
	{
		return $this->userId;
	}

	protected function getUser(string $login)
	{
		$pdoStatement = $this->db->prepare('select `id`,`' . $this->passwordColumn . '`,`' . $this->isActiveColumn . '` from `' . $this->table . '` where `' . $this->usernameColumn . '` = :login limit 1');

		$pdoStatement->execute([':login' => $login]);

		// https://docstore.mik.ua/orelly/java-ent/jenut/ch08_06.htm
		$error = $pdoStatement->errorInfo();

		if (!empty($error[2])) {
			\log_message('info', __METHOD__ . ' ' . $error[2]);
		}

		return $pdoStatement->fetch(PDO::FETCH_ASSOC);
	}
} /* end class */
