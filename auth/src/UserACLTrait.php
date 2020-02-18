<?php

namespace projectorangebox\auth;

use projectorangebox\auth\UserModel;
use Exception;

trait UserACLTrait
{
	protected $roles = [];
	protected $permissions = [];

	protected $lazyLoaded = false;

	protected $userModel;

	public function UserACLConstruct()
	{
		$userModelClass = $this->config['User Model Class'] ?? '\projectorangebox\auth\UserModel';

		$this->userModel = new $userModelClass($this->config);

		if (!($this->userModel instanceof userModelInterface)) {
			throw new Exception('User Model is not an instance of userModelInterface.');
		}
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
		return ($this->id != $this->config['guest user']);
	}

	public function isAdmin(): bool
	{
		return $this->hasRole($this->config['admin role']);
	}

	protected function lazyLoad(): void
	{
		if (!$this->lazyLoaded) {
			/* false or array */
			$userRecord = $this->userModel->get($this->id);

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
} /* end class */
