<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-19
	 * @package   vps\tools\db
	 */

	namespace vps\tools\db;

	use vps\tools\helpers\TimeHelper;
	use yii\db\ActiveRecord;

	class Model extends ActiveRecord
	{

		public function beforeSave ($insert)
		{
			if (parent::beforeSave($insert))
			{
				if ($this->hasAttribute('dt'))
					$this->dt = TimeHelper::now();

				return true;
			}

			return false;
		}

	}