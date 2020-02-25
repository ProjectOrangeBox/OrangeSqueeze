<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class matches extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s does not match %s.';

		return isset($this->field_data[$options]) ? ($field === $this->field_data[$options]) : false;
	}
}
