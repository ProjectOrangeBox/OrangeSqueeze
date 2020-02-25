<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class zip extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is invalid.';

		return (bool) preg_match('#^\d{5}$|^\d{5}-\d{4}$#', $field);
	}
} /* end class */
