<?php

namespace projectorangebox\auth\auth;

use Exception;
use Medoo\Medoo;
use projectorangebox\common\DatabaseModel;
use projectorangebox\common\exceptions\php\IncorrectInterfaceException;

class UserModel extends DatabaseModel implements UserModelInterface
{
	protected $config;

	protected $userTable;
	protected $roleTable;
	protected $userRoleTable;
	protected $rolePermissionTable;
	protected $permissionTable;

	public function __construct($db, array $config = [])
	{
		parent::__construct($db, $config);

		if (!($this->db instanceof Medoo)) {
			throw new IncorrectInterfaceException('Medoo');
		}

		$defaults = [
			'user table' => 'orange_users',
			'role table' => 'orange_roles',
			'permission table' => 'orange_permissions',
			'user role table' => 'orange_user_role',
			'role permission table' => 'orange_role_permission',
		];

		$this->config = \array_replace($defaults, $config);

		$this->userTable = $this->config['user table'];
		$this->roleTable = $this->config['role table'];
		$this->permissionTable = $this->config['permission table'];
		$this->userRoleTable = $this->config['user role table'];
		$this->rolePermissionTable = $this->config['role permission table'];
	}

	public function get(int $userId)
	{
		return $this->getBy($userId, 'id');
	}

	public function getBy(string $value, string $column)
	{
		$allowed_columns = $this->config['allowed columns'] ?? ['username', 'email', 'id'];

		if (!in_array($column, $allowed_columns)) {
			throw new Exception('Can not search for user by "' . $column . '".');
		}

		$record = $this->getWhere($value, $column);

		if ($record) {
			$record = \array_replace($record, $this->getRolesPermissions($record['id']));
		}

		return $record;
	}

	protected function getWhere(string $value, string $column)
	{
		$dbc = $this->db->select($this->userTable, '*', [$column => $value, 'is_deleted' => 0]);

		return $dbc[0] ?? false;
	}

	protected function getRolesPermissions(int $userId): array
	{
		$rolesPermissions = [];

		$sql = "select
			`user_id`,
			`" . $this->roleTable . "`.`id` `orange_roles_id`,
			`" . $this->roleTable . "`.`name` `orange_roles_name`,
			`permission_id`,
			`key`
			from " . $this->userRoleTable . "
			left join " . $this->roleTable . " on " . $this->roleTable . ".id = " . $this->userRoleTable . ".role_id
			left join " . $this->rolePermissionTable . " on " . $this->rolePermissionTable . ".role_id = " . $this->roleTable . ".id
			left join " . $this->permissionTable . " on " . $this->permissionTable . ".id = " . $this->rolePermissionTable . ".permission_id
			where " . $this->userRoleTable . ".user_id = " . $userId;

		$dbc = $this->db->query($sql);

		while ($dbr = $dbc->fetchObject()) {
			if ($dbr->orange_roles_name) {
				if (!empty($dbr->orange_roles_name)) {
					$rolesPermissions['roles'][(int) $dbr->orange_roles_id] = $dbr->orange_roles_name;
				}
			}
			if ($dbr->key) {
				if (!empty($dbr->key)) {
					$rolesPermissions['permissions'][(int) $dbr->permission_id] = $dbr->key;
				}
			}
		}

		return $rolesPermissions;
	}
}
