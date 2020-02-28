<?php

namespace projectorangebox\user;

interface UserInterface
{
	public function __construct(array $config);
	public function __get(string $name); /* mixed */
	public function setUserId(int $userId): bool;
	public function setUserGuest(): bool;

	/* roles */
	public function hasRole(int $role): bool;
	public function hasRoles(array $roles): bool;
	public function hasOneRoleOf(array $roles): bool;

	/* permissions */
	public function can(string $permission): bool;
	public function cannot(string $permission): bool;
	public function hasPermissions(array $permissions): bool;
	public function hasOnePermissionOf(array $permissions): bool;
	public function hasPermission(string $permission): bool;

	public function loggedIn(): bool;
	public function isAdmin(): bool;

	public function save(): bool;
	public function retrieve(): bool;
	public function flush(): bool;
}
