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

class not_one_of extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		// not_in_list[1,2,3,4]
		$types              = ($options) ? $options : '';
		$this->error_string = '%s must not contain one of the available selections.';

		return (!in_array($field, explode(',', $types)));
	}
} /* end class */
