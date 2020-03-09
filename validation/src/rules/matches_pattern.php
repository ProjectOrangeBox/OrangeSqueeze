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

class matches_pattern extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$pattern            = ($options) ? $options : '';
		$this->error_string = '%s does not match the required pattern.';

		return (bool) preg_match($pattern, $field);
	}
} /* end class */
