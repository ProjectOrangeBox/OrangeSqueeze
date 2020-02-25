<?php

namespace projectorangebox\auth\traits;

use Exception;
use projectorangebox\auth\auth\AuthInterface;
use projectorangebox\session\SessionInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

trait UserSessionTrait
{
	protected $sessionService;
	protected $sessionKey = 'user::id';
	protected $authLibrary;

	public function UserSessionTraitConstruct()
	{
		$this->sessionService = $this->config['sessionService'];

		if (!($this->sessionService instanceof SessionInterface)) {
			throw new IncorrectInterfaceException('SessionInterface');
		}

		/* create a instance of auth */
		$userAuthClass = $this->config['User Auth Class'];

		$this->authLibrary = new $userAuthClass($this->config);

		if (!($this->authLibrary instanceof AuthInterface)) {
			throw new IncorrectInterfaceException('AuthInterface');
		}

		/* try to restore session */
		$this->retrieve();
	}

	public function error(): string
	{
		return $this->authLibrary->error();
	}

	public function has(): bool
	{
		return $this->authLibrary->has();
	}

	public function save(): bool
	{
		$this->sessionService->set($this->sessionKey, $this->id);

		return true;
	}

	public function retrieve(): bool
	{
		$savedUserId = $this->sessionService->get($this->sessionKey, null);

		$userId = ((int) $savedUserId > 0) ? (int) $savedUserId : (int) $this->config['guest user'];

		$this->setUserId($userId);

		return true;
	}

	public function login(string $login, string $password): bool
	{
		$success = $this->authLibrary->login($login, $password);

		if ($success) {
			$this->setUserId($this->authLibrary->userId());
		}

		return $success;
	}

	public function logout(): bool
	{
		$success = $this->authLibrary->logout();

		if ($success) {
			/* remove there session and make them a everyone */
			$this->sessionService->delete($this->sessionKey);

			$this->setUserId($this->config['guest user'], true);
		}

		return $success;
	}

	public function refresh(): bool
	{
		$success = $this->authLibrary->refresh();

		if ($success) {
			$this->lazyLoaded = false;
		}

		return $success;
	}
} /* end class */
