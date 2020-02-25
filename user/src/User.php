<?php

namespace projectorangebox\user;

use Exception;
use projectorangebox\mock\Cache;
use projectorangebox\auth\AuthInterface;
use projectorangebox\cache\CacheInterface;
use projectorangebox\session\SessionInterface;
use projectorangebox\models\UserModelInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class User implements UserInterface
{
	protected $id; /* primary key */

	protected $email;
	protected $username;
	protected $isActive;
	protected $dashboardUrl;
	protected $meta;

	protected $userModel;
	protected $authService;
	protected $sessionService;

	protected $sessionKey = 'user::id';

	protected $guestUserId = 0;
	protected $adminRoleId = 0;

	protected $roles = [];
	protected $permissions = [];

	protected $lazyLoaded = false;

	public function __construct(array $config)
	{
		$this->config = $config;

		/* check for required */
		$required = ['admin user', 'guest user', 'admin role', 'everyone role'];

		foreach ($required as $key) {
			if (!isset($this->config['auth'][$key])) {
				throw new Exception('The required configuration value "' . $key . '" is not set.');
			}
		}

		$this->guestUserId = $this->config['auth']['guest user'];
		$this->adminRoleId = $this->config['auth']['admin role'];

		/* default to guest */
		$this->id = $this->guestUserId;

		if (!isset($this->config['auth']['cacheService'])) {
			/* create fake cache handler */
			$this->config['auth']['cacheService'] = new Cache([]);
		}

		if (!($this->config['auth']['cacheService'] instanceof CacheInterface)) {
			throw new IncorrectInterfaceException('CacheInterface');
		}

		$this->sessionService = $this->config['sessionService'];

		if (!($this->sessionService instanceof SessionInterface)) {
			throw new IncorrectInterfaceException('SessionInterface');
		}

		$this->authService = $config['authService'];

		if (!($this->authService instanceof AuthInterface)) {
			throw new IncorrectInterfaceException('AuthInterface');
		}

		$this->userModel = $config['userModel'];

		if (!($this->userModel instanceof UserModelInterface)) {
			throw new IncorrectInterfaceException('UserModelInterface');
		}

		/* try to restore session */
		$this->retrieve();
	}

	public function retrieve(): bool
	{
		$savedUserId = $this->sessionService->get($this->sessionKey, null);

		$userId = ((int) $savedUserId > 0) ? (int) $savedUserId : $this->guestUserId;

		$this->setUserId($userId);

		return true;
	}

	public function setUserId(int $userId): bool
	{
		$this->id = $userId;

		$this->flush();

		$this->save();

		return true;
	}

	public function save(): bool
	{
		$this->sessionService->set($this->sessionKey, $this->id);

		return true;
	}

	public function flush(): bool
	{
		$this->lazyLoaded = false;

		return true;
	}

	public function __debugInfo()
	{
		$this->lazyLoad();

		return [
			'id' => $this->id,
			'email' => $this->email,
			'username' => $this->username,
			'is active' => $this->isActive,
			'dashboard Url' => $this->dashboardUrl,
			'meta' => $this->meta,
			'roles' => $this->roles(),
			'permissions' => $this->permissions(),
		];
	}

	public function roles(): array
	{
		$this->lazyLoad();

		return $this->roles;
	}

	public function can(string $permission): bool
	{
		$this->lazyLoad();

		return (in_array($permission, $this->permissions, true));
	}

	public function permissions(): array
	{
		$this->lazyLoad();

		return $this->permissions;
	}

	public function hasRole(int $role): bool
	{
		$this->lazyLoad();

		return array_key_exists($role, $this->roles);
	}

	public function hasRoles(array $roles): bool
	{
		foreach ($roles as $r) {
			if (!$this->hasRole($r)) {
				return false;
			}
		}

		return true;
	}

	public function hasOneRoleOf(array $roles): bool
	{
		foreach ((array) $roles as $r) {
			if ($this->hasRole($r)) {
				return true;
			}
		}

		return false;
	}

	public function hasPermissions(array $permissions): bool
	{
		foreach ($permissions as $p) {
			if ($this->cannot($p)) {
				return false;
			}
		}

		return true;
	}

	public function hasOnePermissionOf(array $permissions): bool
	{
		foreach ($permissions as $p) {
			if ($this->can($p)) {
				return true;
			}
		}

		return false;
	}

	public function hasPermission(string $permission): bool
	{
		return $this->can($permission);
	}

	public function cannot(string $permission): bool
	{
		return !$this->can($permission);
	}

	public function loggedIn(): bool
	{
		return ($this->id != $this->guestUserId);
	}

	public function isAdmin(): bool
	{
		return $this->hasRole($this->adminRoleId);
	}

	protected function lazyLoad(): void
	{
		if (!$this->lazyLoaded) {
			/* false or array */
			$userRecord = $this->userModel->readDetailedBy($this->id);

			if ($userRecord) {
				$this->username = $userRecord['username'];
				$this->email = $userRecord['email'];
				$this->dashboardUrl = $userRecord['dashboard_url'];
				$this->isActive = ((int) $userRecord['is_active'] == 1);
				$this->meta = json_decode($userRecord['meta']);

				$this->readRoleId = $userRecord['user_read_role_id'];
				$this->editRoleId = $userRecord['user_edit_role_id'];
				$this->deleteRoleId = $userRecord['user_delete_role_id'];

				$this->roles       = (array) $userRecord['roles'];
				$this->permissions = (array) $userRecord['permissions'];
			}

			$this->lazyLoaded = true;
		}
	}

	/* auth service */

	public function login(string $login, string $password): bool
	{
		return $this->authService->login($login, $password);
	}

	public function logout(): bool
	{
		return $this->authService->logout();
	}

	public function error(): string
	{
		return $this->authService->errors();
	}

	public function has(): bool
	{
		return $this->authService->has();
	}
} /* end class */
