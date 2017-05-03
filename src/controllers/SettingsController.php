<?php
	namespace vps\tools\controllers;

	use vps\tools\helpers\Console;

	/**
	 * Allows to manage settings via console.
	 */
	class SettingsController extends \yii\console\Controller
	{
		public $defaultAction = 'list';

		private $_modelClass = 'vps\tools\modules\setting\models\Setting';

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

		/**
		 * List all settings in database.
		 */
		public function actionList ()
		{
			$class = $this->_modelClass;
			$list = $class::find()->select('name,value')->orderBy([ 'name' => SORT_ASC ])->asArray()->all();
			Console::printTable($list, [ 'Name', 'Value' ]);
		}

		/**
		 * Updates or creates setting with given name and value.
		 *
		 * @param $name
		 * @param $value
		 */
		public function actionSet ($name, $value)
		{
			$class = $this->_modelClass;
			$object = $class::find()->where([ 'name' => $name ])->one();
			if ($object == null)
			{
				$object = new $class([
					'name'  => $name,
					'value' => $value
				]);
			}
			else
			{
				$object->value = $value;
			}
			$object->save();
			$this->actionList();
		}
	}