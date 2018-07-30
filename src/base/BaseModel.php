<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @author    Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @copyright Copyright (c) 2018
	 */

	namespace vps\tools\base;

	use vps\tools\helpers\TimeHelper;
	use vps\tools\helpers\UuidHelper;
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
					if ($this->hasAttribute('createDT'))
						$this->createDT = TimeHelper::now();
					if ($this->hasAttribute('uuid'))
						$this->uuid = UuidHelper::generate();
				}
				else
				{
					if ($this->hasAttribute('dt'))
						$this->dt = TimeHelper::now();
				}
			}

			return $parent;
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