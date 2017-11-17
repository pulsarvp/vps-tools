<?php

	namespace vps\tools\modules\setting\models;

	use vps\tools\helpers\ArrayHelper;
	use Yii;
	use yii\db\ActiveRecord;

	/**
	 * @property string $name
	 * @property string $value
	 * @property string $description
	 */
	class Setting extends ActiveRecord
	{
		/**
		 * @inheritdoc
		 */
		public static function primaryKey ()
		{
			return [ 'name' ];
		}

		/**
		 * @inheritdoc
		 */
		public static function tableName ()
		{
			return 'setting';
		}

		/**
		 * @inheritdoc
		 */
		public function attributeLabels ()
		{
			return [
				'name'        => Yii::tr('Name', [], 'setting'),
				'value'       => Yii::tr('Value', [], 'setting'),
				'description' => Yii::tr('Description', [], 'setting'),
				'type'        => Yii::tr('Type', [], 'setting'),
				'rule'        => Yii::tr('Rule', [], 'setting'),
			];
		}

		/**
		 * @inheritdoc
		 */
		public function rules ()
		{
			switch ($this->type)
			{
				case 'command':
					$rules = [ [ 'value', 'validateCommand' ] ];
					break;
				case 'json':
					$rules = [ [ 'value', 'validateJson' ] ];
					break;
				case 'date':
					$rules = [ [ 'value', 'date', 'format' => 'php:Y-m-d' ] ];
					break;
				case 'datetime':
					$rules = [ [ 'value', 'datetime', 'format' => 'php:Y-m-d H:i:s' ] ];
					break;
				case 'time':
					$rules = [ [ 'value', 'time', 'format' => 'php:H:i:s' ] ];
					break;
				case '':
					$rules = [ [ 'value', 'string' ] ];
					break;
				default:
					if ($this->rule != '')
						$rules = [ [ 'value', $this->type ] + json_decode($this->rule, true) ];
					else
						$rules = [ [ 'value', $this->type ] ];
			}

			return ArrayHelper::merge($rules, [
				[ [ 'name' ], 'required' ],
				[ [ 'name', 'value', 'description' ], 'trim' ],
				[ [ 'name' ], 'string', 'max' => 45 ],
				[ [ 'value', 'description' ], 'string' ],
				[ [ 'name' ], 'unique' ] ]);
		}

		/**
		 * Command validates.
		 *
		 * @param string $attribute the attribute currently being validated
		 */
		public function validateCommand ($attribute)
		{
			if (!$this->hasErrors())
			{
				exec($this->$attribute . ' -version 2>&1', $output, $return_var);
				if ($return_var)
					$this->addError($attribute, Yii::tr('Command not found.', [], 'setting'));
			}
		}

		/**
		 * Json validates.
		 *
		 * @param string $attribute the attribute currently being validated
		 */
		public function validateJson ($attribute)
		{
			if (!$this->hasErrors())
			{
				if (!( is_string($this->$attribute) && is_array(json_decode($this->$attribute, true)) && ( json_last_error() == JSON_ERROR_NONE ) ))
					$this->addError($attribute, Yii::tr('This field must be well-formed JSON.', [], 'setting'));
			}
		}

	}