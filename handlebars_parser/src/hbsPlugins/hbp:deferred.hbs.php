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

$helpers['hbp:deferred'] = function($options) {
	if (isset($options['hash']['id'])) {
		return new \LightnCandy\SafeString('<i id="'.$options['hash']['id'].'"></i>');
	} else {
		return new \LightnCandy\SafeString(ci('output')->injector(true));
	}
};