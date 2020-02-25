<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class in_list extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must contain one of the available selections.';

		$types = ($options) ? $options : '';

		return (bool) (in_array($field, explode(',', $types)));
	}
}
