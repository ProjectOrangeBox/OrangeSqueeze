<?php

namespace projectorangebox\auth\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class aclUnique extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s is already being used.';

		list($model, $column) = explode(',', $options, 2);

		return service('acl')->$model->_isUnique($column, $field, $this->field_data['id']);
	}
}
