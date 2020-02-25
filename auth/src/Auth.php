<?php

namespace projectorangebox\auth;

use projectorangebox\common\exceptions\php\IncorrectInterfaceException;
use projectorangebox\models\UserModelInterface;

class Auth implements AuthInterface
{
	protected $config;
	protected $userId;
	protected $getBy;
	protected $error;
	protected $userModel;

	public function __construct(array $config)
	{
		/* defaults */
		$defaults = [
			'empty fields error' => 'Missing Required Field',
			'general failure error' => 'Login Error',
		];

		$this->config = array_replace($defaults, $config);

		$this->getBy = $config['get by'] ?? 'email';

		$this->userModel = $config['userModel'];

		if (!($this->userModel instanceof UserModelInterface)) {
			throw new IncorrectInterfaceException('UserModelInterface');
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

	public function login(string $login, string $password): bool
	{
		$this->logout();

		/* Does login and password contain anything empty values are NOT permitted for any reason */
		if ((strlen(trim($login)) == 0) or (strlen(trim($password)) == 0)) {
			$this->error = $this->config['empty fields error'];

			return false;
		}

		$user = $this->userModel->readBy($this->getBy, $login);

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

	public function logout(): bool
	{
		$this->error = '';
		$this->userId = null;

		return true;
	}
} /* end class */
