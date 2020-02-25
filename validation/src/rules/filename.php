<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class filename extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is not a valid file name.';

		return (bool) preg_match("/^[0-9a-zA-Z_\-. ]+$/i", $field);
	}
} /* end class */
