<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2018
	 */

	namespace vps\tools\base;

	use vps\tools\helpers\TimeHelper;
	use vps\tools\helpers\UuidHelper;
	use Yii;
	use yii\db\ActiveRecord;

	/**
	 * @inheritdoc
	 */
	class BaseModel extends ActiveRecord
	{

		/**
		 * @inheritdoc
		 */
		public function beforeSave ($insert)
		{
			if ($parent = parent::beforeSave($insert))
			{
				if ($this->isNewRecord)
				{
					if ($this->hasAttribute('createDT') and empty($this->createDT))
						$this->createDT = TimeHelper::now();
					if ($this->hasAttribute('uuid') and empty($this->uuid))
						$this->uuid = UuidHelper::generate();
				}
				else
				{
					if ($this->hasAttribute('dt'))
						$this->dt = TimeHelper::now();
				}
				try
				{
					if (Yii::$app->has('kafka'))
						Yii::$app->kafka->send($this->isNewRecord ? 'create' : 'update', $this);
				}
				catch (\Exception $e)
				{
					Yii::$app->logging->error(Yii::tr('Kafka error: {error}', [ 'error' => $e->getMessage() ]));
				}
			}

			return $parent;
		}

		public function beforeDelete ()
		{
			if (parent::beforeDelete())
			{
				try
				{
					if (Yii::$app->has('kafka'))
						Yii::$app->kafka->send('delete', $this);
				}
				catch (\Exception $e)
				{
					Yii::$app->logging->error(Yii::tr('Kafka error: {error}', [ 'error' => $e->getMessage() ]));
				}

				return true;
			}

			return false;
		}

		/***
		 * Finds one model by condition or creates it if search result is empty.
		 *
		 * @param mixed      $condition  Condition that will be passed to `where` statement.
		 * @param array|null $attributes Array of attributes that will be used to create new model. If empty $condition
		 *                               will be used.
		 * @return BaseModel|null
		 */
		public static function findOrCreate ($condition, $attributes = null)
		{
			$model = self::find()->where($condition)->one();
			if ($model == null)
			{
				if (is_array($attributes))
					$model = new self($attributes);
				else
					$model = new self($condition);
				if (!$model->save())
					return null;
			}

			return $model;
		}

		/**
		 * @param      $name
		 * @param bool $save
		 *
		 *  ```php
		 *  $object->toggleAttribute('flag',false);
		 *  ```
		 */
		public function toggleAttribute ($name, $save = true)
		{
			if ($this->hasAttribute($name))
			{
				$this->$name = 1 - $this->$name;
				if ($save)
					$this->save(true, [ $name ]);
			}
		}

	}