<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class valid_datetime extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s must contain a valid date & time.';

		/*
		optionally we are saying 0000-00-00 00:00:00 is valid
		this could be helpful as a "default" or "empty" value
		 */

		return ($field == '0000-00-00 00:00:00') ? true : (strtotime($field) > 1);
	}
} /* end class */
