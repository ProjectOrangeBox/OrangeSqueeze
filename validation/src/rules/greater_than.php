<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class greater_than extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must contain a number greater than %s.';

		if (!is_numeric($field)) {
			return false;
		}

		return is_numeric($field) ? ($field > $options) : false;
	}
}
