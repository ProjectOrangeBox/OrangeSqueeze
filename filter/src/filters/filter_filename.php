<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_filename extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		/*
		only word characters - from a-z, A-Z, 0-9, including the _ (underscore) character
		then trim any _ (underscore) characters from the beginning and end of the string
		*/
		$field = strtolower(trim(preg_replace('#\W+#', '_', $field), '_'));

		/* options is max length - filter is in orange core */
		$this->field($field);
		$this->length($options);
	}
} /* end class */
