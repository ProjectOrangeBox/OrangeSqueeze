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

class array_max extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		if (!is_numeric($options)) {
			return false;
		}

		$this->error_string = '%s should contain less than ' . $options . ' items.';

		return (count($field) < $options);
	}
} /* end class */
