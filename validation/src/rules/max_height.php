<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class max_height extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = 'Height is greater than %s.';

		if (!file_exists($field)) {
			$this->error_string = 'File Not Found.';

			return false;
		}

		if (!function_exists('getimagesize')) {
			throw new Exception('Get Image Size Function Not Supported');
		}

		$size = getimagesize($field);

		return (bool) ($size[1] <= $options);
	}
} /* end class */
