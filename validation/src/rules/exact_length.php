<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class exact_length extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must be exactly %s characters in length.';

		if (!is_numeric($options)) {
			return false;
		}

		return (MB_ENABLED === true) ? (mb_strlen($field) === (int) $options) : (strlen($field) === (int) $options);
	}
}
