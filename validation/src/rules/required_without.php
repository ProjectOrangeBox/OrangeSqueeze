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

class required_without extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		$this->error_string = '%s is required.';

		$with          = $this->field_data[$options];
		$with_filledin = is_array($with) ? (bool) count($with) : (trim($with) !== '');

		/* if it's filled in then it's not required */
		if ($with_filledin) {
			return true;
		}

		/* if it is filled in we end up here and it is required */
		return is_array($field) ? (bool) count($field) : (trim($field) !== '');
	}
} /* end class */
