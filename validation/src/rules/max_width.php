<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class max_width extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = 'Width is greater than %s.';

		if (!file_exists($field)) {
			$this->error_string = 'File Not Found.';

			return false;
		}

		if (!function_exists('getimagesize')) {
			throw new Exception('Get Image Size Function Not Supported');
		}

		$size = getimagesize($field);

		return (bool) ($size[0] <= $options);
	}
} /* end class */
