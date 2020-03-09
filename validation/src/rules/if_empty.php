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

class if_empty extends ValidateRuleAbstract implements ValidateRuleInterface
{
	/*
	if_empty[never()] - in the future
	if_empty[now()] - now defaults to U
	if_empty[user()] - defaults to id
	if_empty[user(name)] - user name
	if_empty[#foobar] - if empty put the value foobar in there
	 */
	public function validate(&$field, string $options = ''): bool
	{
		if (trim($field) === '' || $field === null) {
			/* save a copy for later */
			$replace = $options;

			/* either pass right thru or run use one of these values */
			if (preg_match('/(.*)\((.*?)\)/', $options, $matches)) {
				switch ($matches[1]) {
					case 'never':
						$format  = ($matches[2]) ? $matches[2] : 'U';
						$replace = date($format, strtotime('2999-12-31 23:59:59'));
						break;
					case 'now':
						$format  = ($matches[2]) ? $matches[2] : 'U';
						$replace = date($format);
						break;
					case 'user':
						$param = ($matches[2]) ? $matches[2] : 'id';
						$container = service();

						if ($container->has('user')) {
							$user = $container->user;
							/* if it's empty make it 1 */
							$replace = (!empty($user->$param)) ? $user->$param : 1;
						} else {
							$replace = 1; /* default to root user id / root user default group */
						}
						break;
					default:
						if (substr($matches[1], 0, 1) == '#') {
							$replace = substr($matches[2], 1);
						}
				}
			}

			$field = $replace;
		}

		return true;
	}
}
