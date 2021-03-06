<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class ip extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		/* *.*.*.*,10.1.1.*,10.*.*.*,etc... */
		$this->error_string = '%s is a invalid ip.';

		$sections = explode('.', $field);
		$match    = ($options) ? explode('.', $options) : ['*', '*', '*', '*'];
		if (count($sections) != 4 || count($match) != 4) {
			return false;
		}
		for ($idx = 0; $idx <= 3; $idx++) {
			if ($match[$idx] != '*' && $sections[$idx] != $match[$idx]) {
				return false;
			}
		}
		return true;
	}
} /* end class */
