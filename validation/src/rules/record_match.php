<?php

namespace projectorangebox\validation\rules;

use projectorangebox\validation\ValidateRuleAbstract;
use projectorangebox\validation\ValidateRuleInterface;

class record_match extends ValidateRuleAbstract implements ValidateRuleInterface {
	public function validate(&$field, $options) {
		/* record_match[model name.column 1.value 1.column 2.value 2] */
		$this->error_string = 'You don\'t has access to this record.';

		// has_access[admin_notice_model.msg_id.$msg_id.user_id.$user_id]
		list($model, $record_column1, $record_value1, $record_column2, $record_value2) = explode('.', $options, 5);

		$required = 'model,record_column1,record_value1,record_column2,record_value2';

		foreach (explode(',', $required) as $r) {
			if (empty($$r)) {
				return false;
			}
		}

		/* try to load the model */
		ci()->load->model($model);

		$record = ci()->$model->get_by([$record_column1 => $record_value1]);

		return ($record->$record_column2 == $record_value2);
	}
} /* end class */
