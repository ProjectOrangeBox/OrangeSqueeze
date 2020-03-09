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

class access extends ValidateRuleAbstract implements ValidateRuleInterface {
	/* access['edit::monkeys'] translates to user can('edit::monkeys')  */
	public function validate(&$field, $options) {
		$this->error_string = 'You do not have access to %s';

		/* get the current user data */
		return (is_object(ci()->user)) ? ci('user')->can($options) : false;
	}
} /* end class */
