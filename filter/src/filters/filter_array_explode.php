<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_array_explode extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$options = ($options) ? $options : ' ';

		if (is_string($field)) {
			$field = explode($options, $field);
		}
	}
} /* end class */
