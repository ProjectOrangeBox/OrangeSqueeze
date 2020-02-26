<?php

namespace projectorangebox\user;

use PDO;
use Exception;
use projectorangebox\mock\Cache;
use projectorangebox\cache\CacheInterface;
use projectorangebox\session\SessionInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class User implements UserInterface
{
	protected $id; /* primary key integer */

	protected $email;
	protected $username;
	protected $isActive;
	protected $dashboardUrl;
	protected $meta;

	protected $db;
	protected $sessionService;
	protected $cacheService;

	protected $sessionKey = 'user::id';

	protected $guestUserId = 0;
	protected $adminRoleId = 0;

	protected $roles = [];
	protected $permissions = [];

	protected $lazyLoaded = false;

	protected $userTable;
	protected $roleTable;
	protected $userRoleTable;
	protected $rolePermissionTable;
	protected $permissionTable;

	public function __construct(array $config)
	{
		$this->config = $config;

		/* check for required */
		$required = [
			'admin user',
			'guest user',
			'admin role',
			'everyone role',
			'user table',
			'role table',
			'permission table',
			'user role table',
			'role permission table',
		];

		foreach ($required as $key) {
			if (!isset($this->config[$key])) {
				throw new Exception('The required configuration value "' . $key . '" is not set.');
			}
		}

		$this->userTable = $this->config['user table'];
		$this->roleTable = $this->config['role table'];
		$this->permissionTable = $this->config['permission table'];
		$this->userRoleTable = $this->config['user role table'];
		$this->rolePermissionTable = $this->config['role permission table'];

		$this->guestUserId = (int) $this->config['guest user'];
		$this->adminRoleId = (int) $this->config['admin role'];

		if (!isset($this->config['cacheService'])) {
			/* create fake cache handler */
			$this->cacheService = new Cache([]);
		} else {
			$this->cacheService = $this->config['cacheService'];
		}

		if (!($this->cacheService instanceof CacheInterface)) {
			throw new IncorrectInterfaceException('CacheInterface');
		}

		$this->sessionService = $this->config['sessionService'];

		if (!($this->sessionService instanceof SessionInterface)) {
			throw new IncorrectInterfaceException('SessionInterface');
		}

		$this->db = $config['db'];

		if (!($this->db instanceof PDO)) {
			throw new IncorrectInterfaceException('PDO');
		}

		/* default as guest */
		$this->id = $this->guestUserId;

		/* unloaded */
		$this->lazyLoaded = false;

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

	public function setUserGuest(): bool
	{
		$this->id = $this->guestUserId;

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

	/* protected */

	protected function lazyLoad(): void
	{
		if (!$this->lazyLoaded) {
			$this->getUser($this->id);

			$this->lazyLoaded = true;
		}
	}

	protected function getUser(int $userId): void
	{
		$userRecord = $this->_getUserCached($userId);

		if ($userRecord) {
			$this->username = $userRecord->username;
			$this->email = $userRecord->email;
			$this->dashboardUrl = $userRecord->dashboard_url;
			$this->isActive = ((int) $userRecord->is_active == 1);
			$this->meta = json_decode($userRecord->meta);

			$this->readRoleId = $userRecord->user_read_role_id ?? 0;
			$this->editRoleId = $userRecord->user_edit_role_id ?? 0;
			$this->deleteRoleId = $userRecord->user_delete_role_id ?? 0;

			$rolesPermissions = $this->_getRolesPermissionsCached($userId);

			$this->roles = (array) $rolesPermissions['roles'];
			$this->permissions = (array) $rolesPermissions['permissions'];
		}
	}

	protected function _getUserCached(int $userId)
	{
		$cacheKey = 'user.id.' . $userId . '.details';

		if (!$record = $this->cacheService->get($cacheKey)) {
			$record = $this->query('select * from ' . $this->userTable . ' where id = :userid limit 1', [':userid' => (int) $userId]);

			if ($record) {
				$this->cacheService->save($cacheKey, $record);
			}
		}

		return $record;
	}

	protected function _getRolesPermissionsCached(int $userId): array
	{
		$cacheKey = 'user.id.' . $userId . '.roles.permissions';

		if (!$record = $this->cacheService->get($cacheKey)) {
			$record = $this->_getRolesPermissions($userId);

			$this->cacheService->save($cacheKey, $record);
		}

		return $record;
	}

	protected function _getRolesPermissions(int $userId): array
	{
		$rolesPermissions = [];

		$sql = "select
			`user_id`,
			`" . $this->roleTable . "`.`id` `orange_roles_id`,
			`" . $this->roleTable . "`.`name` `orange_roles_name`,
			`" . $this->rolePermissionTable . "`.`permission_id` `orange_permission_id`,
			`" . $this->permissionTable . "`.`key` `orange_permission_key`
			from " . $this->userRoleTable . "
			left join " . $this->roleTable . " on " . $this->roleTable . ".id = " . $this->userRoleTable . ".role_id
			left join " . $this->rolePermissionTable . " on " . $this->rolePermissionTable . ".role_id = " . $this->roleTable . ".id
			left join " . $this->permissionTable . " on " . $this->permissionTable . ".id = " . $this->rolePermissionTable . ".permission_id
			where " . $this->userRoleTable . ".user_id = :userid";

		$dbc = $this->query($sql, [':userid' => (int) $userId]);

		if ($dbc) {
			while ($dbr = $dbc->fetchObject()) {
				if ($dbr->orange_roles_name) {
					if (!empty($dbr->orange_roles_name)) {
						$rolesPermissions['roles'][(int) $dbr->orange_roles_id] = $dbr->orange_roles_name;
					}
				}
				if ($dbr->orange_permission_key) {
					if (!empty($dbr->orange_permission_key)) {
						$rolesPermissions['permissions'][(int) $dbr->orange_permission_id] = $dbr->orange_permission_key;
					}
				}
			}
		}

		return $rolesPermissions;
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
				$return = $query->fetchObject();
				break;
			default:
				$return = $query;
		}

		return $return;
	}
} /* end class */
