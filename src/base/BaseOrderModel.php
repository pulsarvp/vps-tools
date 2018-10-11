<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2018
	 */

	namespace vps\tools\base;

	/**
	 * @inheritdoc
	 */
	class BaseOrderModel extends BaseModel
	{

		/**
		 * Looking for the biggest order in the table, and the current model doing +1.
		 *
		 * @param bool $insert
		 * @return bool
		 */
		public function beforeSave ($insert)
		{
			if ($parent = parent::beforeSave($insert))
			{
				if ($this->isNewRecord)
				{
					if ($this->hasAttribute('order') and empty($this->order))
					{
						$order = self::find()->select('order')->orderBy([ 'order' => SORT_DESC ])->scalar();

						$this->order = !is_null($order) ? $order + 1 : 1;
					}
				}
			}

			return $parent;
		}

		/**
		 * Which order more than the remote model, do for order -1.
		 */
		public function afterDelete ()
		{
			$parent = parent::afterDelete();

			if ($this->hasAttribute('order'))
			{
				$objects = self::find()->where([ '>', 'order', $this->order ])->all();
				foreach ($objects as $object)
				{
					$object->order -= 1;
					$object->save();
				}
			}

			return $parent;
		}

		/**
		 * Looking for the next higher order, and changing places with it
		 *  ```php
		 *  $object->moveUp();
		 *  ```
		 */
		public function moveUp ()
		{
			if ($this->hasAttribute('order'))
			{
				$object = self::find()->where([ '>', 'order', $this->order ])->one();
				if ($object != null)
				{
					$order = $object->order;
					$object->order = $this->order;
					$this->order = $order;
					$object->save();
					$this->save();
				}
			}
		}

		/**
		 * Looking for the next descending order order, and changing places with it
		 *  ```php
		 *  $object->moveDown();
		 *  ```
		 */
		public function moveDown ()
		{
			if ($this->hasAttribute('order'))
			{
				$object = self::find()->where([ '<', 'order', $this->order ])->one();
				if ($object != null)
				{
					$order = $object->order;
					$object->order = $this->order;
					$this->order = $order;
					$object->save();
					$this->save();
				}
			}
		}

		/**
		 * Make a selection of all models and assign the first model order = 1, all subsequent +1 and save
		 *  ```php
		 *  Model::toggleAttribute();
		 *  ```
		 */
		public static function rebuildOrder ()
		{

			$objects = self::find()->orderBy([ 'order' => SORT_ASC ])->all();
			foreach ($objects as $k => $object)
			{
				$object->order = $k + 1;
				$object->save();
			}
		}

	}