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

class is_between_dates extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		list($after, $before) = explode(',', $options);
		$this->error_string   = '%s must be between ' . date('F j,Y', strtotime($after)) . ' and ' . date('F j,Y', strtotime($before)) . '.';

		/* are either of these not valid times? */
		if (!strtotime($after) || !strtotime($before)) {
			return false;
		}
		$is_after  = (strtotime($field) > strtotime($after)) ? true : false;
		$is_before = (strtotime($field) < strtotime($before)) ? true : false;
		return (bool) ($is_after && $is_before);
	}
} /* end class */
