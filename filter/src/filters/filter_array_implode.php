<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_array_implode extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$options = ($options) ? $options : ' ';

		if (is_array($field)) {
			$field = implode($options, $field);
		}
	}
} /* end class */
