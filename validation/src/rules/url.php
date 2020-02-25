<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class url extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is a invalid url.';

		return (bool) (preg_match('#^([\.\/-a-z0-9_*-])+$#i', $field));
	}
} /* end class */
