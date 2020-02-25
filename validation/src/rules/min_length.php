<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class min_length extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must be at least %s characters in length.';

		if (!is_numeric($options)) {
			return false;
		}

		return ($options <= mb_strlen($field));
	}
}
