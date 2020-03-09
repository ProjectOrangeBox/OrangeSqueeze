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

class has_errors extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		if (strpos($options, ',')) {
			list($group, $field) = explode(',', $options);

			$errors = ci('errors')->as_array($group);

			$does_not_have_error = !isset($errors[$field]);
		} else {
			$does_not_have_error = !ci('errors')->has($options);
		}

		return (bool) $does_not_have_error;
	}
}
