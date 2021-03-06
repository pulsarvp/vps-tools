<?php

	namespace vps\tools\modules\setting\models;

	use vps\tools\helpers\ArrayHelper;
	use vps\tools\modules\log\components\LogManager;
	use Yii;
	use yii\db\ActiveRecord;

	/**
	 * @property string $name
	 * @property string $value
	 * @property string $description
	 * @property string $type
	 * @property string $rule
	 * @property string $group
	 * @property string $fixed
	 */
	class Setting extends ActiveRecord
	{
		const G_GENERAL = 'general';

		public function getHidden ()
		{
			$rule = json_decode($this->rule, true);
			if (isset($rule[ 'hidden' ]))
				return $rule[ 'hidden' ];

			return null;
		}

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
				'group'       => Yii::tr('Group', [], 'setting'),
				'fixed'       => Yii::tr('Fixed', [], 'setting'),
			];
		}

		public function beforeSave ($insert)
		{
			if (parent::beforeSave($insert))
			{
				LogManager::info(Yii::tr('User has changed setting {name} from {oldValue} to {newValue}.', [ 'name' => $this->name, 'oldValue' => $this->getOldAttribute('value'), 'newValue' => $this->value ]));

				return true;
			}

			return false;
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
				case 'path':
					$rules = [ [ 'value', 'validatePath' ] ];
					break;
				case 'url':
					$rules = [ [ 'value', 'validateUrl' ] ];
					break;
				case 'string':
					if ($this->rule != '')
					{
						$rule = json_decode($this->rule, true);
						unset($rule[ 'hidden' ]);
						$rules = [ [ 'value', $this->type ] + $rule ];
					}
					else
						$rules = [ [ 'value', $this->type ] ];
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
				[ [ 'value', 'description', 'rule', 'group', 'type' ], 'string' ],
				[ [ 'fixed' ], 'integer' ],
				[ [ 'fixed' ], 'in', 'range' => [ 0, 1 ] ],
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
				$return_var1 = $return_var2 = $return_var3 = 0;
				exec('command -v ' . $this->$attribute . '', $output, $return_var);
				if ($return_var == 127)
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

		/**
		 * Url validates.
		 *
		 * @param string $attribute the attribute currently being validated
		 */
		public function validateUrl ($attribute)
		{
			if (!$this->hasErrors())
			{
				if (filter_var($this->$attribute, FILTER_VALIDATE_URL) === false)
					$this->addError($attribute, Yii::tr('{attribute} is not a valid URL.', [ 'attribute' => $this->name ], 'setting'));
			}
		}

		/**
		 * Path validates.
		 *
		 * @param string $attribute the attribute currently being validated
		 */
		public function validatePath ($attribute)
		{
			if (!$this->hasErrors())
			{
				if (!file_exists($this->$attribute))
					$this->addError($attribute, Yii::tr('{attribute} does not exist.', [ 'attribute' => $attribute ], 'setting'));
				if ($this->rule != '')
				{
					$rule = json_decode($this->rule, true);
					if (isset($rule[ 'writable' ]) and $rule[ 'writable' ] == true)
					{
						if (!is_writable($this->$attribute))
							$this->addError($attribute, Yii::tr('Path is not writable.', [], 'setting'));
					}
				}
			}
		}

	}