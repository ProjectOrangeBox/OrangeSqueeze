<?php

namespace projectorangebox\models;

use Exception;
use projectorangebox\models\MedooDatabaseModel;

class UserModel extends MedooDatabaseModel implements UserModelInterface
{
	protected $tablename = 'orange_users';
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

	public function insert(array $columns): int
	{
		if (isset($columns['password'])) {
			$columns['password'] = $this->passwordHash($columns['password']);
		}

		return parent::insert($columns);
	}

	public function update(array $columns): int
	{
		if (isset($columns['password'])) {
			$columns['password'] = $this->passwordHash($columns['password']);
		}

		return parent::update($columns);
	}

	public function delete($id): int
	{
		if (parent::delete($id)) {
			$this->db->delete($this->table_join, ['user_id' => $id]);
			$this->captureDBError();
		}

		return ($this->errorCode() > 0);
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
			$this->captureDBError();
		}

		$this->db->commit();

		return ($this->errorCode() > 0);
	}
} /* end class */
