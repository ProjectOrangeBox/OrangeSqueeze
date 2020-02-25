<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class float extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is not a floating number.';

		return (bool) filter_var($field, FILTER_VALIDATE_FLOAT) || (string) $field === '0';
	}
} /* end class */
