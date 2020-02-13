<?php

/* Wrapper */
if (!function_exists('log_message')) {
	function log_message(string $type, string $msg): void
	{
		static $logService;

		/* Is log even attached to the container yet? */
		if (!$logService) {
			$container = service();

			if ($container !== null) {
				if ($container->has('log')) {
					$logService = service('log');
				}
			}
		}

		if ($logService) {
			if (!method_exists($logService, $type)) {
				throw new \Exception('Log Message does not support the method "' . $type . '".');
			} else {
				$logService->$type($msg);
			}
		}
	}
} /* end log_message */
