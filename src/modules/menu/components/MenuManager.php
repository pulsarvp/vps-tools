<?php

	namespace vps\tools\modules\menu\components;

	use vps\tools\modules\menu\models\Menu;
	use vps\tools\modules\menu\models\MenuType;
	use Yii;

	/**
	 * The class for reading menu.
	 */
	class MenuManager extends yii\base\Object
	{
		private $_types;
		private $_data = [];

		public function init ()
		{
			$this->_types = MenuType::find()->all();
			if (count($this->_data) < count($this->_types))
			{
				foreach ($this->_types as $type)
				{
					$root = Menu::find()->where([ 'typeID' => $type->id ])->roots()->one();
					if ($root == null)
					{
						$this->_data[ $type->guid ] = [];
					}
					else
					{
						$this->_data[ $type->guid ] = Menu::find()
							->where('depth > 0')
							->andWhere([ 'typeID' => $type->id, 'visible' => '1' ])
							->orderBy('lft')
							->all();
					}
				}
			}
			$this->setActive();
		}

		public function forType ($typename)
		{
			if (!isset($this->_data[ $typename ]))
				$this->_data[ $typename ] = [];

			return $this->_data[ $typename ];
		}

		private function setActive ()
		{
			foreach ($this->_data as $type => $list)
			{
				foreach ($list as $menu)
				{
					$url = trim(Yii::$app->request->url, "/");
					$url = str_replace("/", "\/", $url);
					if (( "/" . $url == $menu->url ) or
						( $menu->url != "/" and preg_match("/" . Yii::$app->controller->id . "($|\/)/", $menu->path) )
					)
					{
						$menu->active = true;
					}
				}
			}
		}

		public function getTypes ()
		{
			return $this->_types;
		}
	}
