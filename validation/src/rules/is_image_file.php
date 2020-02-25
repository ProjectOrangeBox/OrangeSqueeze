<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_image_file extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is not a valid file.';

		if (!file_exists($field)) {
			$this->error_string = 'File Not Found.';

			return false;
		}

		return (bool) (preg_match("/(.)+\\.(jp(e) {0,1}g$|gif$|png$)/i", $field));
	}
} /* end class */
