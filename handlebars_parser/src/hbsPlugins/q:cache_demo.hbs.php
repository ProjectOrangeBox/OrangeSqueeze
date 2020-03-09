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

$helpers['q:cache_demo'] = function($options)
{
	if (!$html = HBCache::get($options)) {
		$html = $options['fn']($options['_this']).PHP_EOL;
		$html .= 'Cached on: '.date('Y-m-d H:i:s').PHP_EOL;
		$html .= 'For '.$options['hash']['cache'].' Minutes'.PHP_EOL;
		$html .= 'At '.date('Y-m-d H:i:s',strtotime('+'.(int)$options['hash']['cache'].' minutes'));

		HBCache::set($options,$html);
	}

	return $html;
};
