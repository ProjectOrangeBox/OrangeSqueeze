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

namespace projectorangebox\models\models;

use projectorangebox\models\MedooValidateDatabaseModel;

class PermissionModel extends MedooValidateDatabaseModel implements PermissionModelInterface
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
