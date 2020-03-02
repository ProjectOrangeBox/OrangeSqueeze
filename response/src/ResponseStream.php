<?php

namespace projectorangebox\response;

use projectorangebox\response\ResponseInterface;

class ResponseStream extends Response implements ResponseInterface
{
	protected $functionName = '_i';
	protected $injectorSent = false;
	protected $isCli = false;

	public function __construct(array $config)
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

		ob_end_flush();

		/* Output String */
		if ($output && $name) {

			echo '<script>' . $this->functionName . '("' . $name . '",' . json_encode($output) . ');</script>';

			\file_put_contents(__ROOT__ . '/debug.log', '<script>' . $this->functionName . '("' . $name . '",' . json_encode($output) . ');</script>', FILE_APPEND | LOCK_EX);
		} else {
			echo $name;
		}

		/* Send the output buffer & Flush system output buffer */
		ob_flush();
		flush();

		return $this;
	}
} /* end class */
