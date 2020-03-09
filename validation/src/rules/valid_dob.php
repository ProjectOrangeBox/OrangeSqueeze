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

class valid_dob extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$yrs                = ($options) ? $options : 18;
		$this->error_string = '%s must be more than ' . $yrs . ' years.';

		/* is this a valid date? strtotime */
		if (!strtotime($field)) {
			return false;
		}

		/* less than the time */
		if (strtotime($field) > strtotime('-' . $yrs . ' year', time())) {
			return false;
		}

		/* greater than a super old person */
		if (strtotime($field) < strtotime('-127 year', time())) {
			return false;
		}

		return true;
	}
} /* end class */
