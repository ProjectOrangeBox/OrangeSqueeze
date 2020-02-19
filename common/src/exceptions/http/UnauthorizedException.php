<?php

namespace projectorangebox\common\exceptions\data;

use projectorangebox\common\exceptions\HttpException;

class unauthorizedException extends HttpException
{
	protected $code = 401;
}
