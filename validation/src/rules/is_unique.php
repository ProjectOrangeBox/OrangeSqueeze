<?php

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
