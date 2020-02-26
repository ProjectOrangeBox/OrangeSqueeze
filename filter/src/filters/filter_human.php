<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_human extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		/*
		only word characters - from a-z, A-Z, 0-9, including the _ (underscore) character
		then trim any _ (underscore) characters from the beginning and end of the string
		convert to lowercase
		replace _ (underscore) characters with spaces
		uppercase words
		*/
		$field = ucwords(str_replace('_', ' ', strtolower(trim(preg_replace('#\W+#', ' ', $field), ' '))));

		/* options is max length */
		$this->field($field);
		$this->length($options);
	}
} /* end class */
