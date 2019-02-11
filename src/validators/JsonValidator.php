<?php

	namespace vps\tools\validators;

	use Yii;
	use yii\validators\Validator;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2019
	 * @date      2019-02-11
	 */
	class JsonValidator extends Validator
	{
		public function validateAttribute ($model, $attribute)
		{
			if (!empty($model->$attribute))
			{
				$json = json_decode($model->$attribute);
				if (json_last_error() !== JSON_ERROR_NONE)
					$this->addError($model, $attribute, '{attribute} should be in JSON format.', [ 'attribute' => $attribute ]);
			}
		}
	}