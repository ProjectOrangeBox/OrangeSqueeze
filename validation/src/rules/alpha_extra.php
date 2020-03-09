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

class alpha_extra extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		// Alpha-numeric with periods,underscores,spaces and dashes
		$this->error_string = '%s may only contain alpha-numeric characters,spaces,periods,underscores,and dashes.';

		return (bool) preg_match("/^([\.\s-a-z0-9_-])+$/i", $field);
	}
} /* end class */
