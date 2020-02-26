<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_substr extends FilterRuleAbstract implements FilterRuleInterface
{
	/* copy[field] */
	public function filter(&$field, string $options = ''): void
	{
		list($a, $b) = explode($options, 2);

		$field = substr($field, $a, $b);
	}
} /* end class */
