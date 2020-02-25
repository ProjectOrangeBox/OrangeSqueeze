<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class primary_exists extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		/* little assumption here $this->database is loaded */
		/* $options = model name */
		$this->error_string = '%s that you requested is unavailable.';

		if (empty($options)) {
			return false;
		}

		/* try to load the model */
		ci()->load->model($options);

		return ci()->$options->primary_exists($field);
	}
} /* end class */
