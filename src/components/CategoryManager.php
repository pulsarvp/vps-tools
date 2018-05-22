<?php
	namespace vps\tools\components;

	use Yii;

	/**
	 * This class is intended to manage category tree which is in turn based on
	 * nested sets behavior. Yii2 nested set behavior is used - https://github.com/creocoder/yii2-nested-set
	 *
	 * @author        Anna Manaenkova <anna.manaenkova@phystech.edu>
	 * @package       vps\tools\components
	 *
	 * @property  array   $all
	 * @property Category $root
	 * @property  string  $modelClass
	 */
	class CategoryManager extends \yii\base\BaseObject
	{
		/**
		 * @var Category[] Category tree.
		 */
		protected $_data = [];

		/**
		 * @var string This is for imploding GUIDs in guid paths.
		 */
		protected $_guidPathDelimiter = ':';

		/**
		 * @var string
		 */
		protected $_modelClass = '\common\models\Category';

		/**
		 * @var Category Root category.
		 */
		protected $_root;

		/**
		 * @var string This is for imploding titles in title paths.
		 */
		protected $_titlePathDelimiter = ' : ';

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
				$this->_data = [];
			}
			else
				$this->_data = $this->_root->children()->all();

			$this->buildPaths();
		}

		/**
		 * @property-read Category[] $all
		 * @return Category[]
		 */
		public function getAll ()
		{
			return $this->_data;
		}

		/**
		 * Gets children of current category.
		 * @param Category $category
		 * @return array
		 */
		public function getChildren ($category)
		{
			$children = [];
			foreach ($this->_data as $item)
				if ($item->lft > $category->lft and $item->rgt < $category->rgt)
					$children[] = $item;

			return $children;
		}

		/**
		 * Finds category parent with given depth.
		 * @param  Category $category
		 * @param int       $depth
		 * @return Category|null
		 */
		public function getParent ($category, $depth = 1)
		{
			if ($category instanceof $this->_modelClass and $depth > 0)
			{
				if ($category->depth == $depth)
					return $category;
				foreach ($this->_data as $item)
					if ($category->lft > $item->lft and $category->rgt < $item->rgt and $item->depth == $depth)
						return $item;
			}

			return null;
		}

		/**
		 * Finds all parents from top one to nearest.
		 * @param  Category $category
		 * @return Category[]|null
		 */
		public function getParents ($category)
		{
			if ($category instanceof $this->_modelClass)
			{
				$parents = [];
				foreach ($this->_data as $item)
					if ($category->lft > $item->lft and $category->rgt < $item->rgt)
						$parents[] = $item;

				return $parents;
			}

			return null;
		}

		/**
		 * @property-read Category $root
		 * @return Category
		 */
		public function getRoot ()
		{
			return $this->_root;
		}

		/**
		 * Gets single category by its ID.
		 * @param integer $id
		 * @return Category|null
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
		 * @return Category|null
		 */
		public function getByGuidPath ($guidPath)
		{
			foreach ($this->_data as $category)
				if ($category->guidPath === $guidPath)
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
		 * Reloads data from database.
		 */
		public function reload ()
		{
			$class = $this->_modelClass;

			$this->_root = $class::find()->roots()->one();
			$this->_data = $this->_root->children()->all();
			$this->buildPaths();
		}

		/**
		 * Finds category GUID path by given ID.
		 * @param integer $id
		 * @return null|string
		 */
		public function guidPath ($id)
		{
			$category = $this->get($id);

			if ($category == null)
				return null;
			elseif (empty($category->guidPath))
				$this->buildPaths();

			return $category->guidPath;
		}

		/**
		 * Finds category title path by given ID.
		 * @param integer $id
		 * @return null|string
		 */
		public function titlePath ($id)
		{
			$category = $this->get($id);

			if ($category == null)
				return null;
			elseif (empty($category->titlePath))
				$this->buildPaths();

			return $category->titlePath;
		}

		/**
		 * Builds full title and GUID paths for all categories.
		 */
		protected function buildPaths ()
		{
			$titles = [];
			$guids = [];

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

				$parent->titlePath = implode($this->_titlePathDelimiter, $titles[ $parent->id ]);
				$parent->guidPath = implode($this->_guidPathDelimiter, $guids[ $parent->id ]);
			}
		}
	}
