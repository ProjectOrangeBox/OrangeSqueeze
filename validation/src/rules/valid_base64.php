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

class valid_base64 extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s is not valid Base64.';

		return (bool) (base64_encode(base64_decode($field)) === $field);
	}
}
