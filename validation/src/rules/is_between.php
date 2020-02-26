<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_between extends ValidateRuleAbstract implements ValidateRuleInterface {
	/* is_between[1,100] */
	public function validate(&$field, $options) {
		list($lo, $hi)      = explode(',', $options, 2);
		$this->error_string = '%s must be between ' . $lo . ' &amp; ' . $hi;

		return (bool) ($field <= $hi && $field >= $lo);
	}
} /* end class */