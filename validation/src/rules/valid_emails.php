<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class valid_emails extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must contain all valid email addresses.';

		foreach (explode(',', $field) as $email) {
			/* bail on first failure */
			if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				return false;
			}
		}

		return true;
	}
}
