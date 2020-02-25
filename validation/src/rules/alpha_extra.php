<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class alpha_extra extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		// Alpha-numeric with periods,underscores,spaces and dashes
		$this->error_string = '%s may only contain alpha-numeric characters,spaces,periods,underscores,and dashes.';

		return (bool) preg_match("/^([\.\s-a-z0-9_-])+$/i", $field);
	}
} /* end class */
