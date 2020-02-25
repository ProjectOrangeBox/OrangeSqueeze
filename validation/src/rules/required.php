<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class required extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s is required.';

		return is_array($field) ? (bool) count($field) : (trim($field) !== '');
	}
}
