<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class less_than_equal_to extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must contain a number less than or equal to %s.';

		if (!is_numeric($field)) {
			return false;
		}

		return is_numeric($field) ? ($field <= $options) : false;
	}
}
