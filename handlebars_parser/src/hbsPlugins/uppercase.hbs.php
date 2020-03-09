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

$helpers['exp:uppercase'] = function($options) {
	/*
	if (!$output = ci()->handlebars->cache($options)) {
		$output = strtoupper($options['fn']($options['_this']));

		ci()->handlebars->cache($options,$output);
	}
	*/

	return strtoupper($options['fn']($options['_this']));
};
