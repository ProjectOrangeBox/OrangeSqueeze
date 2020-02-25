<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class dollars extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is Out of Range.';

		return (bool) preg_match('#^\$?\d+(\.(\d{2}))?$#', $field);
	}
} /* end class */
