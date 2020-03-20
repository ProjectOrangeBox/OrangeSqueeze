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

namespace projectorangebox\acl\models;

use projectorangebox\acl\models\RoleModelInterface;
use projectorangebox\models\MedooValidateDatabaseModel;

class RoleModel extends MedooValidateDatabaseModel implements RoleModelInterface
{
	protected $tablename = 'orange_roles';
	protected $table_join = 'orange_role_permission';
	protected $rules = [
		'id' => ['field' => 'id', 'label' => 'Id', 'rules' => 'required|integer|max_length[10]|less_than[4294967295]|filter_int[10]'],
		'name' => ['field' => 'name', 'label' => 'Name', 'rules' => 'required|trim|is_unique[projectorangebox\acl\models\RoleModel,name,id]'],
		'description' => ['field' => 'description', 'label' => 'Description', 'rules' => 'required|trim'],
	];
	protected $ruleSets = [
		'insert' => 'name,description',
		'update' => 'id,name,description',
	];

	public function delete($id): int
	{
		if (parent::delete($id)) {
			$rows = $this->db->delete($this->table_join, ['role_id' => $id]);
		}

		$this->captureErrors();

		return $rows;
	}

	public function addPermission(int $roleId, int $permissionId): bool
	{
		return ($this->db->insert($this->table_join, ['role_id' => $roleId, 'permission_id' => $permissionId]) > 0);
	}

	public function removePermission(int $roleId, int $permissionId): bool
	{
		return $this->db->delete($this->table_join, ['role_id' => $roleId, 'permission_id' => $permissionId]);
	}

	public function relink(int $roleId, array $permissionIds): bool
	{
		$this->db->beginTransaction();

		$this->db->delete($this->table_join, ['role_id' => $roleId]);

		foreach ($permissionIds as $permissionId) {
			$this->db->insert($this->table_join, ['role_id' => $roleId, 'permission_id' => $permissionId]);
		}

		$this->db->commit();

		return $this->hasError();
	}
} /* end class */
