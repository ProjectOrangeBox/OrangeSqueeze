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

class is_unique extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must contain a unique value.';

		list($tablename, $columnname) = explode('.', $options, 2);

		if (empty($tablename)) {
			return false;
		}

		if (empty($columnname)) {
			return false;
		}

		return isset(ci()->db) ? (ci()->db->limit(1)->get_where($tablename, [$columnname => $field])->num_rows() === 0) : false;
	}
}
