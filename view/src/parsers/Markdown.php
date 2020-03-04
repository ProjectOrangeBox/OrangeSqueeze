<?php

namespace projectorangebox\view\parsers;

use FS;
use Michelf\Markdown as MichelfMarkdown;
use projectorangebox\common\exceptions\mvc\TemplateNotFoundException;
use projectorangebox\view\ParserAbstract;
use projectorangebox\view\parsers\ParserInterface;

class Markdown extends ParserAbstract implements ParserInterface
{
	public function parse(string $view, array $data = []): string
	{
		if (!$this->exists($view)) {
			throw new TemplateNotFoundException($view);
		}

		$content = FS::file_get_contents($this->views[$view]);

		return $this->parse_string($content, $data);
	}

	protected function _parse(string $template, array $data): string
	{
		$compiledFile = $this->compileFile($template);

		$content = FS::file_get_contents($compiledFile);

		return $this->merge($content, $data, $this->delimiters);
	}

	protected function compileFile(string $template): string
	{
		$compiledFile = $this->cacheFolder . '/' . md5($template) . '.php';

		if ($this->forceCompile || !FS::file_exists($compiledFile)) {
			FS::file_put_contents($compiledFile, MichelfMarkdown::defaultTransform($template));
		}

		return $compiledFile;
	}
} /* end class */
