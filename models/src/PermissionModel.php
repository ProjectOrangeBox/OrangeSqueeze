<?php

namespace projectorangebox\models;

use projectorangebox\models\MedooDatabaseModel;

class PermissionModel extends MedooDatabaseModel implements PermissionModelInterface
{
	protected $tablename = 'orange_permissions';
	protected $rules = [
		'id' => ['field' => 'id', 'label' => 'Id', 'rules' => 'required|integer|max_length[10]|less_than[4294967295]|filter_int[10]'],
		'key' => ['field' => 'key', 'label' => 'Key', 'rules' => 'required|trim|projectorangebox\auth\models\aclUnique[permissions,key]'],
		'description' => ['field' => 'description', 'label' => 'Description', 'rules' => 'required|trim'],
		'group' => ['field' => 'group', 'label' => 'Group', 'rules' => 'required|trim|filter_lowercase'],
	];
	protected $ruleSets = [
		'insert' => 'key,description,group',
		'update' => 'id,key,description,group',
	];
}
