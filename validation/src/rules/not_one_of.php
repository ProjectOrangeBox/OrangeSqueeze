<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class not_one_of extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		// not_in_list[1,2,3,4]
		$types              = ($options) ? $options : '';
		$this->error_string = '%s must not contain one of the available selections.';

		return (!in_array($field, explode(',', $types)));
	}
} /* end class */
