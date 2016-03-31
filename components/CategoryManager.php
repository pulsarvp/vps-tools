<?php
	namespace vps\components;

	use Yii;

	/**
	 * This class is intended to manage category tree which is in turn based on nested sets behavior.
	 * [[https://github.com/creocoder/yii2-nested-sets]]
	 * @package common\components
	 * @property-read array    $all
	 * @property-read Category $root
	 * @property-write string  $modelClass
	 */
	class CategoryManager extends \yii\base\Object
	{
		/**
		 * @var string
		 */
		private $_modelClass = '\common\models\Category';

		/**
		 * @var Category[] Category tree.
		 */
		private $_data = [ ];

		/**
		 * @var [[Category]] Root category.
		 */
		private $_root;

		/**
		 * Populates category tree with data loaded from database.
		 * @inheritdoc
		 */
		public function init ()
		{
			$class = $this->_modelClass;

			$this->_root = $class::find()->roots()->one();
			if ($this->_root == null)
			{
				$root = new $class([ 'guid' => 'root', 'title' => 'ROOT' ]);
				$root->makeRoot();

				$this->_root = $class::find()->roots()->one();
				$this->_data = [ ];
			}
			else
				$this->_data = $this->_root->children()->all();
			$this->buildPaths();
		}

		/**
		 * @property-read \common\models\Category[] $all
		 * @return \common\models\Category[]
		 */
		public function getAll ()
		{
			return $this->_data;
		}

		/**
		 * Gets children of current category.
		 * @param [[Category]] $category
		 * @return array
		 */
		public function getChildren ($category)
		{
			$children = [ ];
			foreach ($this->_data as $item)
				if ($item->lft > $category->lft and $item->rgt < $category->rgt)
					$children[] = $item;

			return $children;
		}

		/**
		 * Finds category parent with given depth.
		 * @param  [[Category]] $category
		 * @param int $depth
		 * @return [[Category]]|null
		 */
		public function getParent ($category, $depth = 1)
		{
			if ($category->depth == $depth)
				return $category;
			foreach ($this->_data as $item)
				if ($category->lft > $item->lft and $category->rgt < $item->rgt and $item->depth == $depth)
					return $item;

			return null;
		}

		/**
		 * @property-read \common\models\Category $root
		 * @return \common\models\Category
		 */
		public function getRoot ()
		{
			return $this->_root;
		}

		/**
		 * Gets single category by its ID.
		 * @param integer $id
		 * @return [[Category]]|null
		 */
		public function get ($id)
		{
			foreach ($this->_data as $category)
				if ($category->id == $id)
					return $category;

			return null;
		}

		/**
		 * Gets single category by its GUID path.
		 * @param string $guidPath
		 * @return [[Category]]|null
		 */
		public function getByGuidPath ($guidPath)
		{
			foreach ($this->_data as $category)
				if ($category->guidPath == $guidPath)
					return $category;

			return null;
		}

		/**
		 * Setting for model class.
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
		 * Checks if category exists.
		 * @param integer $id Category ID.
		 * @return bool
		 */
		public function exists ($id)
		{
			foreach ($this->_data as $category)
				if ($category->id == $id)
					return true;

			return false;
		}

		/**
		 * Recount videos number for every category.
		 */
		public function recountVideos ()
		{
			foreach ($this->_data as $item)
				$item->recountVideos();
		}

		/**
		 * Reloads data from database.
		 */
		public function reload ()
		{
			$class = $this->_modelClass;

			$this->_root = $class::find()->roots()->one();
			$this->_data = $this->_root->children()->all();
		}

		public function guidPath ($id)
		{
			$category = $this->get($id);

			return ( $category == null ) ? null : $category->guidPath;
		}

		public function titlePath ($id)
		{
			$category = $this->get($id);

			return ( $category == null ) ? null : $category->titlePath;
		}

		/**
		 * Builds full title and GUID paths for all categories.
		 */
		private function buildPaths ()
		{
			$titles = [ ];
			$guids = [ ];

			$n = count($this->_data);
			for ($i = 0; $i < $n; $i++)
			{
				$parent = $this->_data[ $i ];
				for ($j = $i + 1; $j < $n; $j++)
				{
					$child = $this->_data[ $j ];
					if ($child->lft > $parent->lft and $child->rgt < $parent->rgt)
					{
						$titles[ $child->id ][] = $parent->title;
						$guids[ $child->id ][] = $parent->guid;
					}
				}
				$titles[ $parent->id ][] = $parent->title;
				$guids[ $parent->id ][] = $parent->guid;

				$parent->titlePath = implode(' : ', $titles[ $parent->id ]);
				$parent->guidPath = implode(':', $guids[ $parent->id ]);
			}
		}
	}