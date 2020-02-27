<?php

namespace projectorangebox\page\traits;

use projectorangebox\page\PageInterface;

trait DataTrait
{
	public function var(string $name, $value): PageInterface
	{
		$this->viewData[$name] = $value;

		return $this;
	}

	public function vars(array $array): PageInterface
	{
		foreach ($array as $key => $value) {
			$this->viewData[$key] = $value;
		}

		return $this;
	}

	public function getVar(string $name) /* mixed */
	{
		$response = '';

		/* view variable or page variable? */
		if (isset($this->viewData[$name])) {
			/* view */
			$response = $this->viewData[$name];
		} elseif (isset($this->viewData[$this->pageVariablePrefix . $name])) {
			/* has this already been sent? */
			$response = $this->viewData[$this->pageVariablePrefix . $name];
		}

		if (isset($this->variables[$this->variablesPrefix . $name])) {
			/* has it already been sorted */
			if (!$this->variables[$this->variablesPrefix . $name][0]) {
				/* no we must sort it */
				array_multisort($this->variables[$this->variablesPrefix . $name][1], SORT_DESC, SORT_NUMERIC, $this->variables[$this->variablesPrefix . $name][2]);

				/* mark it as sorted */
				$this->variables[$this->variablesPrefix . $name][0] = true;
			}

			foreach ($this->variables[$this->variablesPrefix . $name][2] as $append) {
				$response .= $append;
			}
		}

		return $response;
	}

	public function getVars(): array
	{
		return $this->viewData;
	}

	public function clearVars(): PageInterface
	{
		$this->viewData = [];

		return $this;
	}
}
