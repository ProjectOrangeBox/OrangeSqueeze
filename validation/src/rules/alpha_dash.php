<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class alpha_dash extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s may only contain alpha characters, underscores, and dashes.';

		return (bool) preg_match('/^[a-z_-]+$/i', $field);
	}
}
