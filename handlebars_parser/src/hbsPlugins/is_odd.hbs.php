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

/*
{{#is_odd variable}}
	is odd!
{{/is_odd}}

{{#is_odd variable}}
	is odd!
{{else}}
	is not odd!
{{/is_odd}}
*/
$helpers['is_odd'] = function($value,$options) {
	/* parse the "then" (fn) or the "else" (inverse) */
	$return = '';

	if ($value % 2) {
		$return = $options['fn']($options['_this']);
	} elseif ($options['inverse'] instanceof \Closure) {
		$return = $options['inverse']($options['_this']);
	}

	return $return;
};
