<?php

/* wrapper */
if (!function_exists('dieOnError')) {
	function dieOnError(array $errors, int $statusCode = 406, string $view = null)
	{
		if (count($errors)) {
			foreach ($errors as $fieldname => $errorTxt) {
				service('error')->add($fieldname, $errorTxt, 'generic');
			}

			/* if there is a error then you never return from this method call */
			service('error')->displayOnError('generic', $statusCode, $view);

			exit(1); /* safety net */
		}
	}
}
