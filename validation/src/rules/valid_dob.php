<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class valid_dob extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$yrs                = ($options) ? $options : 18;
		$this->error_string = '%s must be more than ' . $yrs . ' years.';

		/* is this a valid date? strtotime */
		if (!strtotime($field)) {
			return false;
		}

		/* less than the time */
		if (strtotime($field) > strtotime('-' . $yrs . ' year', time())) {
			return false;
		}

		/* greater than a super old person */
		if (strtotime($field) < strtotime('-127 year', time())) {
			return false;
		}

		return true;
	}
} /* end class */
