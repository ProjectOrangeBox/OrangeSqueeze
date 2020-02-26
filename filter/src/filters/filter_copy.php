<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_copy extends FilterRuleAbstract implements FilterRuleInterface
{
	/* copy[field] */
	public function filter(&$field, string $options = ''): void
	{
		$field = $this->field_data[$options];
	}
} /* end class */
