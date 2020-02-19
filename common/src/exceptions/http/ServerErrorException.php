<?php

namespace projectorangebox\common\exceptions\data;

use projectorangebox\common\exceptions\HttpException;

class serverErrorException extends HttpException
{
	protected $code = 500;
}
