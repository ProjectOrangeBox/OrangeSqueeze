<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_uri extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = '/' . trim(trim(strtolower($field)), '/');
		$field = preg_replace("#^/^[0-9a-z_*/]*$#", '', $field);

		/* options is max length */
		$this->field($field)->length($options);
	}
} /* end class */
