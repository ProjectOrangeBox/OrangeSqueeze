<?php

namespace projectorangebox\auth;

interface UserInterface
{
	public function __construct(array $config);
	public function set(int $userId, bool $save = true): bool;

	public function UserACLConstruct();
	public function roles(): array;
	public function hasRole(int $role): bool;
	public function hasRoles(array $roles): bool;
	public function hasOneRoleOf(array $roles): bool;

	public function permissions(): array;
	public function can(string $permission): bool;
	public function cannot(string $permission): bool;
	public function hasPermissions(array $permissions): bool;
	public function hasOnePermissionOf(array $permissions): bool;
	public function hasPermission(string $permission): bool;

	public function loggedIn(): bool;
	public function isAdmin(): bool;

	public function UserSessionConstruct();
	public function error(): string;
	public function has(): bool;
	public function save(): bool;
	public function restore(): bool;
	public function login(string $login, string $password): bool;
	public function logout(): bool;
	public function refresh(): bool;
}
