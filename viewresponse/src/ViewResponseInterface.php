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

namespace projectorangebox\viewresponse;

interface ViewResponseInterface
{

	public function __construct(array &$config);
	public function response(int $statusCode = null, string $type = null, string $charset = 'UTF-8'): ViewResponseInterface;
	public function view(array $array = [], string $view = null): string;
}
