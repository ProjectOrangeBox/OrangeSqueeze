<?php

namespace projectorangebox\request;

class Request implements RequestInterface
{
	protected $config;
	protected $server = [];
	protected $request = [];
	protected $isAjax = false;
	protected $baseUrl = '';
	protected $requestMethod = '';
	protected $uri = '';
	protected $segments = [];
	protected $isCli = false;

	public function __construct(array $config)
	{
		$this->config = $config;

		$this->isCli = (php_sapi_name() == 'cli');

		$this->server = $this->config['server'] ?? array_change_key_case($_SERVER, CASE_LOWER);

		/* parse the raw stream input */
		$request = [];

		parse_str(file_get_contents('php://input'), $request);

		$this->request = $this->config['request'] ?? $request;

		/* is this a ajax request? */
		$this->isAjax = (isset($this->server['http_x_requested_with']) && strtolower($this->server['http_x_requested_with']) === 'xmlhttprequest') ? true : false;

		/* what's our base url */
		$this->baseUrl = rtrim($this->server['http_host'] . dirname($this->server['script_name']), '/.');

		/* get the http request method */
		$this->requestMethod = ($this->isCli) ? 'cli' : strtolower($this->server['request_method']);

		/* get the uri (uniform resource identifier) */
		if ($this->isCli) {
			$argv = $this->server['argv'];

			/* shift off index.php */
			array_shift($argv);

			$uri = ltrim(trim(implode(' ', $argv)), '/');
		} else {
			$allow = $this->config['allow'] ?? 'A BCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/0_-.+';

			$uri = trim(urldecode(substr(parse_url($this->server['request_uri'], PHP_URL_PATH), strlen(dirname($this->server['script_name'])))), '/');

			/* filter out NOT in allow */
			$uri = preg_replace("/[^" . preg_quote($allow, '/') . "]/i", '', $uri);
		}

		/* get the uri pieces */
		$this->segments = explode('/', $uri);

		$this->uri = $uri;
	}

	/* is this a ajax request */
	public function isAjax(): bool
	{
		return $this->isAjax;
	}

	/* is this a ajax request */
	public function isCli(): bool
	{
		return $this->isCli;
	}

	/* what is the base url */
	public function baseUrl(): string
	{
		return $this->baseUrl;
	}

	/* what is the current request method (this may return cli if it is in fact a command line interface request) */
	public function requestMethod(): string
	{
		return $this->requestMethod;
	}

	/* what is the current uri */
	public function uri(): string
	{
		return '/' . $this->uri;
	}

	/* what are the uri segments */
	public function segments(): array
	{
		return $this->segments;
	}

	/* grab a uri segement with default value if nothing set */
	public function segment(int $index, $default = null) /* mixed */
	{
		return (isset($this->segments[$index])) ? $this->segments[$index] : $default;
	}

	/* get a server value */
	public function server(string $name = null, $default = null) /* mixed */
	{
		if ($name) {
			$name = strtolower($name);

			$return = (isset($this->server[$name])) ? $this->server[$name] : $default;
		} else {
			$return = $this->server;
		}

		return $return;
	}

	/* get a post, put request with default */
	public function request(string $name = null, $default = null) /* mixed */
	{
		if ($name) {
			return (isset($this->request[$name])) ? $this->request[$name] : $default;
		} else {
			return $this->request;
		}
	}

	/* get a get request value with default */
	public function get(string $name = null, $default = null) /* mixed */
	{
		return (isset($this->$_GET[$name])) ? $this->_GET[$name] : $default;
	}
} /* end class */
