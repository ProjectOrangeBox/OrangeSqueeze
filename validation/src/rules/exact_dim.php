<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class exact_dim extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		/* need this for the message */
		$dim                = explode(',', $options);
		$this->error_string = 'The width & height must be ' . $dim[0] . 'x' . $dim[1];

		/* change the message */
		if (!file_exists($field)) {
			$this->error_string = 'File Not Found.';

			return false;
		}
		if (!function_exists('getimagesize')) {
			throw new Exception('Get Image Size Function Not Supported');
		}

		$size = getimagesize($field);

		return (bool) ($size[0] == $dim[0] && $size[1] == $dim[1]);
	}
} /* end class */
