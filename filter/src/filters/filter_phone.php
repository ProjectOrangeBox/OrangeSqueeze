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

class filter_phone extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		/* this needs to be passed by reference */
		$field = preg_replace('/[^0-9x]+/', ' ', $field);
		$field = preg_replace('/ {2,}/', ' ', $field);

		/* $field pass by ref,options is the length */
		$this->field($field)->human()->length($options);
	}
} /* end class */
