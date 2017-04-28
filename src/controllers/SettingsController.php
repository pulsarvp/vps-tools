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
		 *
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
			$list = $class::find()->select('name,value,description')->orderBy([ 'name' => SORT_ASC ])->asArray()->all();
			Console::printTable($list, [ 'Name', 'Value', 'Description' ]);
		}

		/**
		 * Get setting with given name.
		 *
		 * @param $name
		 * @param $value
		 */
		public function actionGet ($name)
		{
			$class = $this->_modelClass;
			$object = $class::find()->select('name,value,description')->where([ 'name' => $name ])->asArray()->one();
			if ($object == null)
			{
				Console::printColor('Setting not found', Console::FG_RED);
				die;
			}
			Console::printTable([ $object ], [ 'Name', 'Value', 'Description' ]);
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
				Console::printColor('Setting not found', Console::FG_RED);
				die;
			}
			else
			{
				$object->value = $value;
			}
			$object->save();
			$this->actionList();
		}

		/**
		 * Get setting with given name.
		 *
		 * @param $name
		 * @param $value
		 */
		public function actionDelete ($name)
		{
			$class = $this->_modelClass;
			$object = $class::find()->where([ 'name' => $name ])->one();
			if ($object == null)
			{
				Console::printColor('Setting not found', Console::FG_RED);
			}

			if ($this->confirm("Remove setting '" . $name . "'?"))
			{
				if ($object->delete())
					Console::printColor('Setting deleted', Console::FG_GREEN);
				else
					Console::printColor('Setting not deleted', Console::FG_GREEN);
			}
		}
	}