<?php

namespace projectorangebox\models;

use Exception;
use projectorangebox\models\MedooDatabaseModel;

class UserModel extends MedooDatabaseModel implements UserModelInterface
{
	protected $table = 'orange_users';
	protected $table_join = 'orange_user_role';
	protected $rules = [
		'id' => ['field' => 'id', 'label' => 'Id', 'rules' => 'required|integer|max_length[10]|less_than[4294967295]|filter_int[10]'],
		'username' => ['field' => 'username', 'label' => 'User Name', 'rules' => 'required|trim|projectorangebox\models\rules\aclUnique[users,username]'],
		'email' => ['field' => 'email', 'label' => 'Email', 'rules' => 'required|trim|strtolower|valid_email|projectorangebox\models\rules\aclUnique[users,email]|max_length[255]|filter_input[255]'],
		'password' => ['field' => 'password', 'label' => 'Password', 'rules' => 'required|max_length[255]|filter_input[255]'],
		'dashboard_url' => [],
		'is_active' => ['field' => 'is_active', 'label' => 'Active', 'rules' => 'if_empty[0]|in_list[0,1]|filter_int[1]|max_length[1]|less_than[2]'],
		'meta' => [],
		'is_deleted' => [],
	];
	protected $ruleSets = [
		'insert' => 'username,email,password,dashboard_url,is_active,meta',
		'update' => 'id,username,email,password,dashboard_url,is_active,meta',
	];

	protected $userTable;
	protected $roleTable;
	protected $userRoleTable;
	protected $rolePermissionTable;
	protected $permissionTable;
	protected $details = false;

	public function __construct(array $config = [])
	{
		parent::__construct($config);

		$defaults = [
			'user table' => 'orange_users',
			'role table' => 'orange_roles',
			'permission table' => 'orange_permissions',
			'user role table' => 'orange_user_role',
			'role permission table' => 'orange_role_permission',
		];

		$this->config['auth tables'] = \array_replace($defaults, $config['auth tables']);

		$this->userTable = $this->config['auth tables']['user table'];
		$this->roleTable = $this->config['auth tables']['role table'];
		$this->permissionTable = $this->config['auth tables']['permission table'];
		$this->userRoleTable = $this->config['auth tables']['user role table'];
		$this->rolePermissionTable = $this->config['auth tables']['role permission table'];
	}

	public function create(array $columns): bool
	{
		if (isset($columns['password'])) {
			$columns['password'] = $this->passwordHash($columns['password']);
		}

		return parent::create($columns);
	}

	public function update(array $columns): bool
	{
		if (isset($columns['password'])) {
			$columns['password'] = $this->passwordHash($columns['password']);
		}

		return parent::update($columns);
	}

	public function delete(string $id): bool
	{
		if (parent::delete($id)) {
			$this->db->delete($this->table_join, ['user_id' => $id]);
		}

		return $this->captureDBError();
	}

	protected function passwordHash(string $password): string
	{
		$info = password_get_info($password);

		if ($info['algo'] == 0) {
			$password = \password_hash($password, PASSWORD_DEFAULT);
		}

		return $password;
	}

	public function relink(int $userId, array $roleIds): bool
	{
		$this->db->beginTransaction();

		$this->db->delete($this->table_join, ['user_id' => $userId]);

		foreach ($roleIds as $roleId) {
			$this->db->insert($this->table_join, ['role_id' => $roleId, 'user_id' => $userId]);
		}

		$this->db->commit();

		return $this->captureDBError();
	}

	public function readDetailedBy(string $value, string $column = 'id')
	{
		$allowed_columns = $this->config['allowed columns'] ?? ['username', 'email', 'id'];

		if (!in_array($column, $allowed_columns)) {
			throw new Exception('Can not search for user by "' . $column . '".');
		}

		$dbc = $this->db->select($this->userTable, '*', [$column => $value, 'is_deleted' => 0]);

		$record = $dbc[0] ?? false;

		if ($record) {
			$record = \array_replace($record, $this->_getRolesPermissions($record['id']));
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
