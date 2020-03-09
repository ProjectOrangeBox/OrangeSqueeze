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
<div class="date">Posted on {{format:date entry_date format="Y-m-d H:i:s"}}</div>
*/
$helpers['format:date'] = function($arg1,$options) {
	return date($options['hash']['format'],$arg1);
};