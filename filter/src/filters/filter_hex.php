<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_hex extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = preg_replace('/[^0-9a-f]+/', '', strtolower(trim($field)));

		/* options is max length */
		$this->field($field)->length($options);
	}
} /* end class */
