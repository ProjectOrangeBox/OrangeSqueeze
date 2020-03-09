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
$in is a reference to the data array sent in

{{set age="28" name=page_title food="pizza" }}
*/
$helpers['set'] = function($options) use (&$in) {
	//$in[$options['hash']['name']] = $options['hash']['value'];

	$in = array_replace($in,$options['hash']);

	return '';
};
