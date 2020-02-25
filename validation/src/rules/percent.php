<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class percent extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s Out of Range.';

		return (bool) preg_match('#^\s*(\d{0,2})(\.?(\d*))?\s*\%?$#', $field);
	}
} /* end class */
