<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class matches_pattern extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$pattern            = ($options) ? $options : '';
		$this->error_string = '%s does not match the required pattern.';

		return (bool) preg_match($pattern, $field);
	}
} /* end class */
