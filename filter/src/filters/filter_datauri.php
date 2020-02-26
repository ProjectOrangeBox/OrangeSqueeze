<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_datauri extends FilterRuleAbstract implements FilterRuleInterface
{

	public function filter(&$field, string $options = ''): void
	{
		$field = ci('data_uri')->extract_data_img($field);
	}
} /* end class */
