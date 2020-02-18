<?php

namespace projectorangebox\auth;

use Exception;

class Auth implements AuthInterface
{
	protected $config;
	protected $userModel;
	protected $getBy;

	protected $userId;
	protected $login;
	protected $error;

	public function __construct(array $config)
	{
		$this->config = $config;

		$this->getBy = $config['get by'] ?? 'email';

		$this->clear();

		$userModelClass = $this->config['User Model Class'] ?? '\projectorangebox\auth\UserModel';

		$this->userModel = new $userModelClass($config);

		if (!($this->userModel instanceof userModelInterface)) {
			throw new Exception('User Model is not an instance of userModelInterface.');
		}
	}

	public function error(): string
	{
		return $this->error;
	}

	public function has(): bool
	{
		return !empty($this->error);
	}

	public function userId(): int
	{
		return $this->userId;
	}

	public function login(string $login, string $password): bool
	{
		$this->clear();

		/* Does login and password contain anything empty values are NOT permitted for any reason */
		if ((strlen(trim($login)) == 0) or (strlen(trim($password)) == 0)) {
			$this->error = $this->config['empty fields error'];

			return false;
		}

		$user = $this->userModel->getBy($login, $this->getBy);

		/* Try to locate a user by there email */
		if (!$user) {
			$this->error = $this->config['general failure error'];

			return false;
		}

		/* Verify the Password entered with what's in the user object */
		if (password_verify($password, $user['password']) !== true) {
			$this->error = $this->config['general failure error'];

			return false;
		}

		/* Is this user activated? */
		if ((int) $user['is_active'] !== 1) {
			$this->error = $this->config['general failure error'];

			return false;
		}

		/* save the passed login */
		$this->login = $login;
		$this->userId = (int) $user['id'];

		return true;
	}

	public function logout(): Bool
	{
		$this->clear();

		return true;
	}

	public function refresh(): bool
	{
		return true;
	}

	protected function clear(): void
	{
		$this->error = '';
		$this->login = null;
		$this->userId = -1;
	}
} /* end class */
