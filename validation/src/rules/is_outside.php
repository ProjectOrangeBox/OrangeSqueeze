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

class is_outside extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		list($lo, $hi) = explode(',', $options, 2);

		$this->error_string = '%s must not be between ' . $lo . ' &amp; ' . $hi;

		return (bool) ($field > $hi || $field < $lo);
	}
} /* end class */
