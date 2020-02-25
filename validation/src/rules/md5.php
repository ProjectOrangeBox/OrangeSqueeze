<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class md5 extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$options            = ($options) ? $options : 32;
		$this->error_string = '%s is not a valid hash.';

		/* default message */
		return (bool) preg_match('/^([a-fA-F0-9]{' . (int) $options . '})$/', $field);
	}
} /* end class */
