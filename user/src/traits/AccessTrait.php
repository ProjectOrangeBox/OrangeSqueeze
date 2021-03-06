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

namespace projectorangebox\user\traits;

trait AccessTrait
{
	public function can(string $permission): bool
	{
		$this->lazyLoad();

		return (in_array($permission, $this->permissions, true));
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
}
