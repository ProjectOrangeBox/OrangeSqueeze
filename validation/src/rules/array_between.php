<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class array_between extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s should contain between ' . $min . ' and ' . $max . ' items.';

		list($min, $max) = explode(',', $options, 2);

		if (!is_numeric($min) || !is_numeric($max)) {
			return false;
		}

		$count = count($field);

		return ($count > $min && $count < $max);
	}
} /* end class */
