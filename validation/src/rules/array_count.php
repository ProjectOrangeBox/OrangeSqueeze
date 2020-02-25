<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class array_count extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s should contain ' . $options . ' items.';

		if (!is_numeric($options)) {
			return false;
		}

		return (count($field) == $options);
	}
} /* end class */
