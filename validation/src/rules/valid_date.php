<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class valid_date extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is a invalid date.';

		if (empty($field)) {
			return true;
		}

		/* basic format check */
		if (!preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/', $field)) {
			return false;
		}

		list($d, $m, $y) = explode('/', $field);

		return checkdate($d, $m, $y);
	}
} /* end class */
