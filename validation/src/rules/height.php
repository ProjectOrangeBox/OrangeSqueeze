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

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class height extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function filter(&$field, $options) {
		$success = false;
		$file    = WWW . $this->field_data[$options];

		if (file_exists($file)) {
			if (!function_exists('getimagesize')) {
				throw new Exception('Get Image Size Function Not Supported');
			}

			$size    = getimagesize($file);
			$field   = $size[1];
			$success = true;
		}

		return $success;
	}
} /* end class */
