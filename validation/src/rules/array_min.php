<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class array_min extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		if (!is_numeric($options)) {
			return false;
		}

		$this->error_string = '%s should contain more than ' . $options . ' items.';

		return (count($field) > $options);
	}
} /* end class */
