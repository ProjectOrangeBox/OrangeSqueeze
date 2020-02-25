<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class valid_ip extends ValidateRuleAbstract implements ValidateRuleInterface
{
	public function validate(&$field, string $options = ''): bool
	{
		$this->error_string = '%s must contain a valid IP.';

		$options = (!empty($options)) ? $options : 'ipv4';

		return (bool) ci()->input->valid_ip($field, $options);
	}
}
