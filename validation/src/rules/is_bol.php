<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_bol extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is invalid.';

		/* PHP's built in function */
		if (is_bool($field)) {
			return true;
		}
		/* our tests */
		return (in_array(strtolower($field), array_merge($this->true_array, $this->false_array), true)) ? true : false;
	}
} /* end class */
