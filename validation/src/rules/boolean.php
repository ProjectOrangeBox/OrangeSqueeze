<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class boolean extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function filter(&$field, $options) {
		$field = strtolower($field);

		return ($field == 'y' || $field == 'yes' || $field === 1 || $field == '1' || $field == 'true' || $field == 't');
	}
} /* end class */
