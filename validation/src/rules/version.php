<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class version extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is not a valid version number.';

		return (bool) preg_match('/^[0-9]+\.[0-9]+\.[0-9]+([+-][^+-][0-9A-Za-z-.]*)?$/', $field);
	}
} /* end class */
