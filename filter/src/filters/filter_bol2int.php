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

class filter_bol2int extends FilterRuleAbstract implements FilterRuleInterface
{
	public function filter(&$field, string $options = ''): void
	{
		$field = (in_array(strtolower($field), $this->true_array, true)) ? 1 : 0;
	}
} /* end class */
