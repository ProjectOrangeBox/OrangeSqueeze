<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRule;
use projectorangebox\validation\ValidateRuleInterface;

class alpha_numeric_spaces extends ValidateRule implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s may only contain alpha-numeric characters and spaces.';

		return (bool) preg_match('/^[A-Z0-9 ]+$/i', $field);
	}
}
