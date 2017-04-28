<?php
	namespace vps\tools\controllers;

	use vps\tools\helpers\Console;
	use Yii;

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
		 * Gets setting with given name.
		 *
		 * @param $name
		 */
		public function actionGet ($name)
		{
			$class = $this->_modelClass;
			$object = $class::find()->select('name,value,description')->where([ 'name' => $name ])->asArray()->one();
			if ($object == null)
			{
				Console::printColor('Setting not found.', 'red');
				Yii::$app->end();
			}
			Console::printTable([ $object ], [ 'Name', 'Value', 'Description' ]);
		}

		/**
		 * Updates or creates setting with given name and value.
		 *
		 * @param $name
		 * @param $value
		 * @param $description
		 */
		public function actionSet ($name, $value, $description = null)
		{
			$class = $this->_modelClass;
			$object = $class::find()->where([ 'name' => $name ])->one();
			if ($object == null)
			{
				Console::printColor('Setting not found.', 'red');
				Yii::$app->end();
			}
			else
			{
				$object->value = $value;
				if (!is_null($description))
					$object->description = $description;
			}
			$object->save();
			$this->actionList();
		}

		/**
		 * Deletes setting with given name.
		 *
		 * @param $name
		 */
		public function actionDelete ($name)
		{
			$class = $this->_modelClass;
			$object = $class::find()->where([ 'name' => $name ])->one();
			if ($object == null)
			{
				Console::printColor('Setting not found.', 'red');
				Yii::$app->end();
			}

			if ($this->confirm("Remove setting '" . $name . "'?"))
			{
				if ($object->delete())
					Console::printColor('Setting deleted.', 'green');
				else
					Console::printColor(current($object->getFirstErrors()), 'red');
			}
		}
	}