<?php

namespace projectorangebox\auth;

class Auth implements AuthInterface
{
	protected $config;
	protected $error;
	protected $userId;

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
			'empty fields error' => 'Missing Required Field',
			'general failure error' => 'Login Error',
			'table' => 'users',
			'username column' => 'email',
			'is active column' => 'is_active',
			'password column' => 'password',
		];

		$this->config = array_replace($defaults, $config);

		$this->db = $config['db'];
		$this->table = $config['table'];
		$this->username_column = $config['username column'];
		$this->password_column = $config['password column'];
		$this->is_active_column = $config['is active column'];
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
		if ((strlen(trim($login)) == 0) or (strlen(trim($password)) == 0)) {
			$this->error = $this->config['empty fields error'];

			return false;
		}

		$user = $this->get($login);

		if ($user == false || !is_array($user)) {
			$this->error = $this->config['general failure error'];

			return false;
		}

		/* Verify the Password entered with what's in the user object */
		if (password_verify($password, $user[$this->password_column]) !== true) {
			$this->error = $this->config['general failure error'];

			return false;
		}

		/* Is this user activated? */
		if ((int) $user[$this->is_active_column] !== 1) {
			$this->error = $this->config['general failure error'];

			return false;
		}

		$this->userId = (int) $user['id'];

		return true;
	}

	public function logout(): bool
	{
		$this->error = '';
		$this->userId = null;

		return true;
	}

	public function userId(): int
	{
		return $this->userId;
	}

	protected function get(string $login)
	{
		$user = $this->db->select($this->table, [$this->password_column, $this->is_active_column], [$this->username_column => $login]);

		return count($user) ? $user[0] : false;
	}
} /* end class */
