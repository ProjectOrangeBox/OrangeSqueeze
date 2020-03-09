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

class md5 extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$options            = ($options) ? $options : 32;
		$this->error_string = '%s is not a valid hash.';

		/* default message */
		return (bool) preg_match('/^([a-fA-F0-9]{' . (int) $options . '})$/', $field);
	}
} /* end class */
