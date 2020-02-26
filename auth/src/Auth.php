<?php

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
	protected $username_column;
	protected $password_column;
	protected $is_active_column;

	public function __construct(array $config)
	{
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
		$this->username_column = $config['username column'];
		$this->password_column = $config['password column'];
		$this->is_active_column = $config['is active column'];

		$this->logout();
	}

	public function error(): string
	{
		return $this->error;
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

			return false;
		}

		/* try to load the user */
		$user = $this->getUser($login);

		if (!is_array($user)) {
			$this->error = $this->config['general error'];

			return false;
		}

		/* Verify the Password entered with what's in the user object */
		if (password_verify($password, $user[$this->password_column]) !== true) {
			$this->error = $this->config['incorrect password error'];

			return false;
		}

		/* Is this user activated? */
		if ((int) $user[$this->is_active_column] !== 1) {
			$this->error = $this->config['not activated error'];

			return false;
		}

		$this->userId = (int) $user['id'];

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
		return $this->query('select `id`,`' . $this->password_column . '`,`' . $this->is_active_column . '` from `' . $this->table . '` where ' . $this->username_column . ' = :login limit 1', [':login' => $login], false);
	}

	public function refresh(): bool
	{
		return true;
	}

	/* PDO simple as beans select query wrapper */
	protected function query(string $sql, array $execute = [], $onEmpty = false)
	{
		$query = $this->db->prepare($sql);

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

		return $return;
	}
} /* end class */
