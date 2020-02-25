<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class required_with extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is required.';

		$with          = $this->field_data[$options];
		$with_filledin = is_array($with) ? (bool) count($with) : (trim($with) !== '');

		/* if it's not filled in then it's not required */
		if (!$with_filledin) {
			return true;
		}

		/* if it is filled in we end up here and it is required */
		return is_array($field) ? (bool) count($field) : (trim($field) !== '');
	}
} /* end class */
