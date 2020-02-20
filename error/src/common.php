<?php

if (!function_exists('show_error')) {
	function show_error(string $title, string $body)
	{
		$errorService = service('error');

		$rootGroup = $errorService::ROOT;

		$errorService
			->add('title', $title, $rootGroup)
			->add('body', $body, $rootGroup)
			->display->error($rootGroup);
	}
}
