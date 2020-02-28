<?php

namespace projectorangebox\models;

use projectorangebox\models\MedooValidateDatabaseModel;

class SnippetModel extends MedooValidateDatabaseModel
{
	protected $tablename = 'snippets';
	protected $rules = [
		'id' => ['field' => 'id', 'label' => 'Id', 'rules' => 'required|integer|max_length[10]|less_than[4294967295]|filter_int[10]'],
		'short_name' => ['field' => 'short_name', 'label' => 'Tag', 'rules' => 'required|trim'],
		'text' => ['field' => 'text', 'label' => 'HTML', 'rules' => 'required|trim'],
	];
	protected $ruleSets = [
		'insert' => 'short_name,text',
		'update' => 'id,short_name,text',
	];
} /* end class */
