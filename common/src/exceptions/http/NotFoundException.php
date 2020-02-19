<?php

namespace projectorangebox\common\exceptions\data;

use projectorangebox\common\exceptions\HttpException;

class notFoundException extends HttpException
{
	protected $code = 404;
}
