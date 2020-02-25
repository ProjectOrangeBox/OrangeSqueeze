<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_json_str extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is not a json string.';

		if (is_string($field)) {
			$json = json_decode($field, TRUE);
			return ($json !== NULL AND $field != $json);
		}

		return false;
	}
} /* end class */
