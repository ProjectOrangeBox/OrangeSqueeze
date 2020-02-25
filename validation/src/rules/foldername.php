<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class foldername extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is not a valid folder name.';

		return (bool) preg_match("/^([a-zA-Z0-9_\- ])+$/i", $field);
	}
} /* end class */
