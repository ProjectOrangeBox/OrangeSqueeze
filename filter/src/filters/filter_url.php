<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_url extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		if ($field === 'http://' or $field === '') {
			$field = '';
		}

		if (strpos($field, 'http://') !== 0 && strpos($field, 'https://') !== 0) {
			$field = 'http://' . $field;
		}
	}
} /* end class */
