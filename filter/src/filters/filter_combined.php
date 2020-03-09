<?php

/**
 * OrangeSqueeze
 *
 * This content is released under the MIT License (MIT)
 * Copyright (c) 2014 - 2020, Project Orange Box
 *
 * @package Project Orange Box
 * @author Don Myers
 * @copyright 2020
 * @license http://opensource.org/licenses/MIT MIT License
 * @link https://github.com/ProjectOrangeBox
 * @version v1.0
 * @filesource
 *
 */

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
