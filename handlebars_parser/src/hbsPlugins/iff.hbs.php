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

$helpers['iif'] = function($value1,$op,$value2,$options) {
	/*
	{{#iif page_title "=" "Current Projects"}}
		True Do This
	{{else}}
		False Do This
	{{/iif}}
	*/

	$return = '';

	switch ($op) {
		case '=';
			if ($value1 == $value2) {
				$return = $options['fn']();
			} elseif ($options['inverse'] instanceof \Closure) {
				$return = $options['inverse']();
			}
		break;
		case '>';
			if ($value1 > $value2) {
				$return = $options['fn']();
			} elseif ($options['inverse'] instanceof \Closure) {
				$return = $options['inverse']();
			}
		break;
		case '<';
			if ($value1 < $value2) {
				$return = $options['fn']();
			} elseif ($options['inverse'] instanceof \Closure) {
				$return = $options['inverse']();
			}
		break;
		case '!=';
		case '<>';
			if ($value1 != $value2) {
				$return = $options['fn']();
			} elseif ($options['inverse'] instanceof \Closure) {
				$return = $options['inverse']();
			}
		break;
		case '>=';
		case '=>';
			if ($value1 >= $value2) {
				$return = $options['fn']();
			} elseif ($options['inverse'] instanceof \Closure) {
				$return = $options['inverse']();
			}
		break;
		case '<=';
		case '=<';
			if ($value1 <= $value2) {
				$return = $options['fn']();
			} elseif ($options['inverse'] instanceof \Closure) {
				$return = $options['inverse']();
			}
		break;
	}

	return $return;
};
