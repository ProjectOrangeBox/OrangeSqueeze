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

namespace projectorangebox\response;

use projectorangebox\response\ResponseInterface;

class ResponseStream extends Response implements ResponseInterface
{
	protected $functionName = '_i';
	protected $injectorSent = false;
	protected $isCli = false;

	public function __construct(array &$config)
	{
		$this->isCli = $config['is cli'] ?? false;

		ini_set('implicit_flush', 1);
		ob_implicit_flush(1);
	}

	/**
	 * send
	 *
	 * @param string $name - element id
	 * @param mixed string - element content
	 * @return void
	 */
	public function send(string $name, string $output = null): ResponseInterface
	{
		if (!$this->headerSend) {
			$this->sendHeader();
		}

		if (!$this->injectorSent) {
			$this->injectorSent = true;

			if (!$this->isCli) {
				echo '<script>function ' . $this->functionName . '(i,c){let e=document.getElementById(i);if(e){e.outerHTML=c}}</script>';
			}
		}

		/* Flush (send) the output buffer and turn off output buffering */
		ob_end_flush();

		/* Output a Injectable Block or Direct Output? */
		if ($output && $name) {
			echo '<script>' . $this->functionName . '("' . $name . '",' . json_encode($output) . ');</script>';
		} else {
			echo $name;
		}

		/* Flush (send) the output buffer */
		ob_flush();

		/* Flush system output buffer */
		flush();

		return $this;
	}
} /* end class */
