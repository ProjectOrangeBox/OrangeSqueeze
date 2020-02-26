<?php

namespace projectorangebox\filter\filters;

use projectorangebox\filter\FilterRuleAbstract;
use projectorangebox\filter\FilterRuleInterface;

class filter_combined extends FilterRuleAbstract implements FilterRuleInterface
{
	/*
	combined[{fielda},{fieldb},::,{fieldc}]
	 */
	public function filter(&$field, string $options = ''): void
	{
		$fields = explode(',', $options);

		foreach ($fields as $f) {
			if (substr($f, 0, 1) == '{' && substr($f, -1) == '}') {
				$combined .= $this->field_data[substr($f, 1, -1)];
			} else {
				$combined .= $f;
			}
		}

		$field = $combined;
	}
} /* end class */
