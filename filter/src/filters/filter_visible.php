<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_visible extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $field);

		/* options is max length - filter is in orange core */
		$this->field($field);
		$this->length($options);
	}
} /* end class */
