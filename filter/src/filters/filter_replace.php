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

class filter_replace extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		/* built the key value pair */
		$items  = explode(',', $options);
		$idx    = 0;
		$keys   = [];
		$values = [];

		foreach ($items as $item) {
			$idx++;
			if ($idx % 2) {
				$keys[] = $item;
			} else {
				$values[] = $item;
			}
		}

		if (count($keys) > 0 && count($values) > 0) {
			$field = str_replace($keys, $values, $field);
		}
	}
} /* end class */
