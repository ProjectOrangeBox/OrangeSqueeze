<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class numeric extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must contain only numeric characters.';

		return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $field);
	}
}
