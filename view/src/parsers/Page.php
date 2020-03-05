<?php

namespace projectorangebox\view\parsers;

use Pear;
use projectorangebox\view\parsers\ParserAbstract;
use projectorangebox\view\parsers\ParserInterface;
use projectorangebox\common\exceptions\mvc\ViewNotFoundException;
use projectorangebox\common\exceptions\mvc\TemplateNotFoundException;

class Page extends ParserAbstract implements ParserInterface
{
	protected $views = [];
	protected $cacheFolder = '';
	protected $defaultView = '';
	protected $extending = [];

	public function __construct(array &$config)
	{
		parent::__construct($config);

		/* if a default view was sent in set it */
		if (isset($config['default view'])) {
			$this->setDefaultView($config['default view']);
		}

		/* global namespaced static class pear:: so we must manually load it */
		require __DIR__ . '/page/pear/Pear.php';

		/* "inject" plugins */
		Pear::_construct($config['plugins'], $this);

		\log_message('info', 'Page Class Initialized');
	}

	public function render(string $view = null, array $data = []): string
	{
		$view = ($view) ?? $this->defaultView;

		if ($view == null) {
			throw new ViewNotFoundException();
		}

		return trim($this->parse($view, $data));
	}

	public function parse(string $view, array $data = []): string
	{
		if (!$this->exists($view)) {
			throw new TemplateNotFoundException($view);
		}

		$this->extending[] = $view;

		while ($view = array_pop($this->extending)) {
			$viewContent = $this->_parse($this->views[$view], $data);
		}

		return trim($viewContent);
	}

	public function findView(string $name)
	{
		return $this->views[$name] ?? false;
	}

	/* set by router */
	public function setDefaultView(string $defaultView): ParserInterface
	{
		$this->defaultView = $defaultView;

		return $this;
	}

	public function extend(string $extend): ParserInterface
	{
		$this->extending[] = $extend;

		return $this;
	}
} /* end class */
