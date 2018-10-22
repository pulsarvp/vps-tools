<?php
	namespace vps\tools\components;

	use vps\tools\helpers\FileHelper;
	use vps\tools\helpers\UuidHelper;
	use yii\base\Component;
	use Yii;
	use yii\base\InvalidConfigException;

	class FileManager extends Component
	{
		/**
		 * Absolute base path to save files.
		 *
		 * @var string
		 */
		private $_path;

		/**
		 * Number of sub directories. For example, if this values is 3 and file name is
		 * 3e494366-858f-43a4-9c9d-001cebf72772.jpg it will be saved to relative path
		 * 3/e/4/3e494366-858f-43a4-9c9d-001cebf72772.jpg
		 *
		 * @var int
		 */
		private $_sublevels = 2;

		public function __construct (array $config = [])
		{
			parent::__construct($config);
		}

		public function setPath ($path)
		{
			$this->_path = $path;
		}

		public function setSublevels ($sublevels)
		{
			$min = 0;
			$max = 8;

			$sublevels = max($min, $sublevels);
			$sublevels = min($sublevels, $max);

			$this->_sublevels = $sublevels;
		}

		public function init ()
		{
			parent::init();
			if (empty($this->_path))
				$this->_path = Yii::$app->settings->get('datapath') . '/file';
			if (!is_dir($this->_path))
				FileHelper::createDirectory($this->_path);
			if (!is_writable($this->_path))
				throw new InvalidConfigException(Yii::tr('Directory path is not writable: {path}', [ 'path' => $this->_path ]));
		}

		/**
		 * @param string      $tmpname
		 * @param string|null $path Relative path to save files.
		 * @param bool        $returnRelative Whether to return relative or absolute path.
		 * @return string Path to saved file. Absolute or relative based on $returnRelative.
		 * @throws \yii\base\Exception
		 * @throws \yii\base\InvalidConfigException
		 */
		public function save ($tmpname, $path = null, $returnRelative = true)
		{
			$ext = pathinfo($tmpname, PATHINFO_EXTENSION);
			$name = UuidHelper::generate();
			if ($ext)
				$name .= '.' . $ext;

			if ($path)
				$dirpath = $this->_path . '/' . $path;
			else
				$dirpath = $this->_path;

			$relativepath = '';
			for ($i = 0; $i < $this->_sublevels; $i++)
				$relativepath .= '/' . $name[ $i ];
			$relativepath = trim($relativepath, '/');

			if (!is_dir($dirpath))
				FileHelper::createDirectory($dirpath);
			if (!is_writable($dirpath))
				throw new InvalidConfigException(Yii::tr('Directory path is not writable: {path}', [ 'path' => $dirpath ]));

			$relativename = $relativepath . '/' . $name;
			$fullname = $dirpath . '/.' . $name;
			copy($tmpname, $fullname);

			return $returnRelative ? $relativename : $fullname;
		}
	}