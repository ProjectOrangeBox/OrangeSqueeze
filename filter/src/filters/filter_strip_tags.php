<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_strip_tags extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$allowable_tags = (!empty($options)) ? $options : '';

		$field = strip_tags($field, $allowable_tags);
	}
} /* end class */
