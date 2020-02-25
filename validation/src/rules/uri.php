<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class uri extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is an invalid uniform resource identifier';

		return (bool) (preg_match("#^/[/0-9a-z_*-]+$#", $field));
	}
} /* end class */
