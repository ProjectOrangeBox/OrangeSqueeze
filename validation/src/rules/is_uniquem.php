<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class is_uniquem extends ValidateRuleAbstract implements ValidateRuleInterface {

	public function validate(&$field, $options) {
		/* is_uniquem[model_name.column_name.$_POST[primary_key]] */
		$this->error_string = '%s is already being used.';

		list($model, $column, $postkey) = explode('.', $options, 3);

		if (empty($model)) {
			return false;
		}

		if (empty($column)) {
			return false;
		}

		if (empty($postkey)) {
			return false;
		}

		/* try to load the model */
		ci()->load->model($model);

		return ci()->$model->is_uniquem($field, $column, $postkey);
	}

} /* end class */
