<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class allowed_types extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s must contain one of the allowed file extensions.';

		/* allowed_type[png,gif,jpg,jpeg] */
		$types = (array) explode(',', $options);

		return (in_array(pathinfo($field, PATHINFO_EXTENSION), $types, true));
	}
} /* end class */
