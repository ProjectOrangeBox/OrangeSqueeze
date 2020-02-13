<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRule;
use projectorangebox\validation\ValidateRuleInterface;

class alpha_numeric extends ValidateRule implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s may only contain alpha-numeric characters.';

		return (bool) ctype_alnum((string) $field);
	}
}
