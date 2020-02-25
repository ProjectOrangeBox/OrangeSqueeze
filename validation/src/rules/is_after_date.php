<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_after_date extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$format = 'F j,Y,g:ia';
		$time   = strtotime('now');
		$error  = 'now';

		if (strpos($options, '@') !== false) {
			list($time, $format) = explode('@', $options, 2);
			$time                = strtotime($time);
			$error               = date($format, $time);
		}

		$this->error_string = '%s must be after ' . $error . '.';

		return (!strtotime($field)) ? false : (strtotime($field) > $time);
	}
} /* end class */
