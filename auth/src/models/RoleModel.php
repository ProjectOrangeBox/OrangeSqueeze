<?php

namespace projectorangebox\auth\models;

use projectorangebox\models\MedooDatabaseModel;

class RoleModel extends MedooDatabaseModel implements RoleModelInterface
{
	protected $table = 'orange_roles';
	protected $table_join = 'orange_role_permission';
	protected $rules = [
		'id' => ['field' => 'id', 'label' => 'Id', 'rules' => 'required|integer|max_length[10]|less_than[4294967295]|filter_int[10]'],
		'name' => ['field' => 'name', 'label' => 'Name', 'rules' => 'required|trim|projectorangebox\auth\rules\aclUnique[roles,name]'],
		'description' => ['field' => 'description', 'label' => 'Description', 'rules' => 'required|trim'],
	];
	protected $ruleSets = [
		'insert' => 'name,description',
		'update' => 'id,name,description',
	];

	public function delete(string $id): bool
	{
		if (parent::delete($id)) {
			$this->db->delete($this->table_join, ['role_id' => $id]);
		}

		return $this->captureDBError();
	}

	public function relink(int $roleId, array $permissionIds): bool
	{
		$this->db->beginTransaction();

		$this->db->delete($this->table_join, ['role_id' => $roleId]);

		foreach ($permissionIds as $permissionId) {
			$this->db->insert($this->table_join, ['role_id' => $roleId, 'permission_id' => $permissionId]);
		}

		$this->db->commit();

		return $this->captureDBError();
	}
}
