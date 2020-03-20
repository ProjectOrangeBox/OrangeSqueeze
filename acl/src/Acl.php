<?php

namespace projectorangebox\acl;

use PDO;
use Exception;
use projectorangebox\acl\models\RoleModelInterface;
use projectorangebox\acl\models\UserModelInterface;
use projectorangebox\acl\models\PermissionModelInterface;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class Acl
{
	public $permissions;
	public $permission;

	public $roles;
	public $role;

	public $users;
	public $user;

	protected $userTable;
	protected $roleTable;
	protected $permissionTable;
	protected $userRoleTable;
	protected $rolePermissionTable;

	protected $adminUserId = 0;
	protected $guestUserId = 0;
	protected $adminRoleId = 0;
	protected $everyoneRoleId = 0;

	protected $db;

	public function __construct(array &$config)
	{
		$this->config = &$config;

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
			if (!isset($config[$key])) {
				throw new Exception('The required configuration value "' . $key . '" is not set.');
			}
		}

		$this->userTable = $config['user table'];
		$this->roleTable = $config['role table'];
		$this->permissionTable = $config['permission table'];
		$this->userRoleTable = $config['user role table'];
		$this->rolePermissionTable = $config['role permission table'];

		$this->adminUserId = (int) $config['admin user'];
		$this->guestUserId = (int) $config['guest user'];
		$this->adminRoleId = (int) $config['admin role'];
		$this->everyoneRoleId = (int) $config['everyone role'];

		$this->db = $config['db'];

		if (!($this->db instanceof PDO)) {
			throw new IncorrectInterfaceException('PDO');
		}

		$this->permissions = &$config['permissionsModel'];
		$this->permission = &$this->permissions;

		if (!($this->permission instanceof PermissionModelInterface)) {
			throw new IncorrectInterfaceException('PermissionModelInterface');
		}

		$this->roles = &$config['rolesModel'];
		$this->role = &$this->roles;

		if (!($this->role instanceof RoleModelInterface)) {
			throw new IncorrectInterfaceException('RoleModelInterface');
		}

		$this->users = &$config['usersModel'];
		$this->user = &$this->users;

		if (!($this->users instanceof UserModelInterface)) {
			throw new IncorrectInterfaceException('UserModelInterface');
		}
	}

	public function getUser(int $userId): array
	{
		$query = $this->db->prepare('select * from `' . $this->userTable . '` where id = :userid limit 1');
		$query->execute([':userid' => (int) $userId]);
		$user = $query->fetch(PDO::FETCH_NAMED);

		$rolesPermissions = $this->_getRolesPermissions($userId);

		$user['roles'] = $rolesPermissions['roles'];
		$user['permissions'] = $rolesPermissions['permissions'];

		return $user;
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
			from `" . $this->userRoleTable . "`
			left join `" . $this->roleTable . "` on `" . $this->roleTable . "`.`id` = `" . $this->userRoleTable . "`.`role_id`
			left join `" . $this->rolePermissionTable . "` on `" . $this->rolePermissionTable . "`.`role_id` = `" . $this->roleTable . "`.`id`
			left join `" . $this->permissionTable . "` on `" . $this->permissionTable . "`.`id` = `" . $this->rolePermissionTable . "`.`permission_id`
			where `" . $this->userRoleTable . "`.`user_id` = :userid";

		$query = $this->db->prepare($sql);
		$query->execute([':userid' => (int) $userId]);

		if ($dbc = $query->fetchAll(PDO::FETCH_CLASS)) {
			foreach ($dbc as $dbr) {
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

		/* everybody */
		$rolesPermissions['roles'][$this->everyoneRoleId] = 'Everyone';

		return $rolesPermissions;
	}
} /* end class */
