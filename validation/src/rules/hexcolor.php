<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class hexcolor extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is not a hex value.';

		return (bool) preg_match('/^#?[a-fA-F0-9]{3,6}$/', $field);
	}
} /* end class */
