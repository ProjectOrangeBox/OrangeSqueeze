<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class hmac extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function filter(&$field, $options) {
		$success = true;

		/* if it dosn't start with out HMAC prefix then just return TRUE and don't modify it */
		if (substr($field, 0, 3) === '$H$') {
			$key                = ci()->config->item('encryption_key');
			list($value, $hmac) = explode(chr(0), base64_decode(substr($field, 3)), 2);
			if (md5($value . $key) === $hmac) {
				$field = $value;
			} else {
				$field   = null;
				$success = false;
			}
		}

		return $success;
	}
} /* end class */
