<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class phone extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is invalid.';

		return (bool) preg_match('/^\(?[\d]{3}\)?[\s-]?[\d]{3}[\s-]?[\d]{4}$/', $field);
	}
} /* end class */
