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

class is_after_date extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$format = 'F j,Y,g:ia';
		$time   = strtotime('now');
		$error  = 'now';

		if (strpos($options, '@') !== false) {
			list($time, $format) = explode('@', $options, 2);
			$time                = strtotime($time);
			$error               = date($format, $time);
		}

		$this->error_string = '%s must be after ' . $error . '.';

		return (!strtotime($field)) ? false : (strtotime($field) > $time);
	}
} /* end class */
