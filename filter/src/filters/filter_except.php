<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_except extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		/* options is what is stripped "except" */
		$field = preg_replace("/[^" . preg_quote($options, "/") . "]/", '', $field);
	}
} /* end class */
