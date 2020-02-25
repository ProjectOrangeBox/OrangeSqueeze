<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class file_size_min extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = 'File %s size is less than ' . $options . ' bytes';

		if (!file_exists($field)) {
			$this->error_string = 'File Not Found.';

			return false;
		}

		$size = filesize($field);

		return (bool) ($size > $options);
	}
} /* end class */
