<?php

namespace projectorangebox\viewresponse;

interface ViewResponseInterface
{

	public function __construct(array &$config);
	public function response(int $statusCode = null, string $type = null, string $charset = 'UTF-8'): ViewResponseInterface;
	public function view(array $array = [], string $view = null): string;
}
