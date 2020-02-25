<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class ip extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		/* *.*.*.*,10.1.1.*,10.*.*.*,etc... */
		$this->error_string = '%s is a invalid ip.';

		$sections = explode('.', $field);
		$match    = ($options) ? explode('.', $options) : ['*', '*', '*', '*'];
		if (count($sections) != 4 || count($match) != 4) {
			return false;
		}
		for ($idx = 0; $idx <= 3; $idx++) {
			if ($match[$idx] != '*' && $sections[$idx] != $match[$idx]) {
				return false;
			}
		}
		return true;
	}
} /* end class */
