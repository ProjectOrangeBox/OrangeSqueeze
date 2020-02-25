<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_serialized_str extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s must be a serialized string.';

		if (!is_string($field)) {
			return false;
		}

		$field = trim($field);

		if ('N;' == $field) {
			return true;
		}

		if (!preg_match('/^([adObis]):/', $field, $badions)) {
			return false;
		}

		switch ($badions[1]):
	case 'a':
	case 'O':
	case 's':
		if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $field)) {
			return true;
		}
		break;
	case 'b':
	case 'i':
	case 'd':
		if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $field)) {
			return true;
		}
		break;
		endswitch;

		return false;
	}
} /* end class */
