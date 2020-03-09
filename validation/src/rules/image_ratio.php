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

class image_ratio extends ValidateRuleAbstract implements ValidateRuleInterface {
	/* options can be a number or a string like 4:3,16:9 */
	public function validate(&$field, $options) {
		$this->error_string = '%s does must have a ratio (width/height) of ' . $options . '.';

		if (strpos($options, ':') === false) {
			return false;
		}
		list($width, $height) = explode(':', $options);
		if (!is_numeric($width) || !is_numeric($height)) {
			return false;
		}
		$ratio = $width / $height;
		if (!function_exists('getimagesize')) {
			throw new Exception('Get Image Size Function Not Supported');
		}
		$imageInfo   = getimagesize($field);
		$actualRatio = $imageInfo[0] / $imageInfo[1];
		return abs($actualRatio - $ratio);
	}
} /* end class */
