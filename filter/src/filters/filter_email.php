<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_email extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = filter_var($field, FILTER_SANITIZE_EMAIL);

		/* options is max length */
		$this->field($field)->length($options);
	}
} /* end class */
