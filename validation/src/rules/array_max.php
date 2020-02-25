<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class array_max extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		if (!is_numeric($options)) {
			return false;
		}

		$this->error_string = '%s should contain less than ' . $options . ' items.';

		return (count($field) < $options);
	}
} /* end class */
