<?php

	namespace vps\tools\modules\setting\components;

	use vps\tools\modules\setting\models\Setting;
	use Yii;

	/**
	 * Class SettingManager
	 *
	 * @package vps\tools\modules\setting\components
	 * @property-read Setting[] $all
	 * @property-write string   $modelClass
	 */
	class SettingManager extends \yii\base\BaseObject
	{
		/**
		 * @var string
		 */
		private $_modelClass = '\vps\tools\modules\setting\models\Setting';

		/**
		 * @var Setting[] Category tree.
		 */
		private $_data = [];

		/**
		 * @inheritdoc
		 * Loads all settings from database.
		 */
		public function init ()
		{
			/** @var yii\db\ActiveRecord $class */
			$class = $this->_modelClass;
			if (Yii::$app->db->schema->getTableSchema($class::tableName()))
				$this->_data = $class::find()->cache(60)->all();
		}

		/**
		 * Gets specific setting by its name. Return default value if not found.
		 *
		 * @param string $name
		 * @param mixed  $default
		 * @return null
		 */
		public function get ($name, $default = null)
		{
			foreach ($this->_data as $d)
			{
				if ($d->name === $name)
					return $d->value;
			}

			return $default;
		}

		/**
		 * Returns all data.
		 *
		 * @return Setting[]
		 */
		public function getAll ()
		{
			return $this->_data;
		}

		/**
		 * Setting for model class.
		 *
		 * @param $class
		 * @throws \yii\base\InvalidConfigException
		 */
		public function setModelClass ($class)
		{
			if (!class_exists($class))
				throw new \yii\base\InvalidConfigException('Given model class not found.');
			$this->_modelClass = $class;
		}

		public function getTableName ()
		{
			return $this->_modelClass::tableName();
		}
	}
