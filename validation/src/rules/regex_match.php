<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class regex_match extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		if (empty($options)) {
			$this->error_string = '%s expression match option empty.';

			return false;
		}

		$this->error_string = '%s is not in the correct format.';

		return (bool) preg_match($options, $field);
	}
}
