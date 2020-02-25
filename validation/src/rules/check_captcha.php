<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class check_captcha extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		// !todo -- captcha
		$this->error_string = 'Captcha is incorrect.';

		return true;
	}
} /* end class */
