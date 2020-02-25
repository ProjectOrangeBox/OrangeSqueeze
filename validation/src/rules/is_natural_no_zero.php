<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_natural_no_zero extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must only contain digits and must be greater than zero.';

		return (bool) ($field != 0 && ctype_digit((string) $field));
	}
}
