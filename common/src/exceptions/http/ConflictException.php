<?php

namespace projectorangebox\common\exceptions\data;

use projectorangebox\common\exceptions\HttpException;

class conflictException extends HttpException
{
	protected $code = 409;
}
