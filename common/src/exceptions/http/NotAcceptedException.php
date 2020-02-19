<?php

namespace projectorangebox\common\exceptions\data;

use projectorangebox\common\exceptions\HttpException;

class notAcceptedException extends HttpException
{
	protected $code = 406;
}
