<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_url_safe extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		/* $field pass by ref,options is the length */
		$this->field($field)->human()->length($options)->strip('~`!@$^()* {}[]|\;"\'<>,');
	}
} /* end class */
