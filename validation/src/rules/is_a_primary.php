<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_a_primary extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is not a primary id.';

		$field = trim($field);
		/* is it empty? */
		if ($field == '') {
			return false;
		}
		/* is it a sql primary id? */
		if (is_numeric($field)) {
			return true;
		}
		/* is it a mongoid */
		if ((bool) preg_match('/^([a-fA-F0-9]{24})$/', $field)) {
			return true;
		}
		return false;
	}
} /* end class */
