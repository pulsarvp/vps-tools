<?php
	namespace vps\tools\net;

	use Yii;
	use vps\tools\helpers\StringHelper;
	use vps\tools\helpers\FileHelper;

	/**
	 * This class manages resumable uploads provided by Flow.js library.
	 *
	 * @property-read string  $chunkPath           Full path to the current chunk file.
	 * @property-read bool    $isComplete          Indicates whether file uploading is complete.
	 * @property-read bool    $isNew               Indicates whether file uploading is new.
	 * @property-read bool    $isUploading         Indicates whether chunk is being uploaded or tested.
	 * @property-read string  $savedFilename       Name of the saved file.
	 * @property-write string $targetDir           Target directory to save video file.
	 * @property-write string $tmpDir              Temporary directory where chunks are saved.
	 *
	 * @see    https://github.com/flowjs/flow.js
	 *
	 * @author Anna Manaenkova <anna.manaenkova@phystech.edu>
	 */
	class Flow extends \yii\base\BaseObject
	{
		/**
		 * @var string Pre-name for request paramters.
		 */
		private $_basename = 'flow';

		/**
		 * @var array File info get from $_FILES array.
		 */
		private $_file;

		/**
		 * @var bool Whether uploading is complete.
		 */
		private $_isComplete = false;

		/**
		 * @var bool Whether uploading is new.
		 */
		private $_isNew = true;

		/**
		 * @var array Parameters gathered from request.
		 */
		private $_params;

		/**
		 * @var string Name of the saved file (after uploading is complete).
		 */
		private $_savedFilename;

		/**
		 * @var string Target directory to save video file.
		 */
		private $_targetDir;

		/**
		 * @var string Temporary directory where chuncks are saved.
		 */
		private $_tmpDir;

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			$names = [
				'chunkNumber',
				'totalChunks',
				'chunkSize',
				'totalSize',
				'identifier',
				'filename',
				'relativePath'
			];

			$r = Yii::$app->request;
			foreach ($names as $name)
			{
				$paramName = $this->_basename . ucfirst($name);
				$p = $r->post($paramName, $r->get($paramName));
				if ($p !== null)
					$this->_params[ $name ] = $p;
			}

			// Get file if so.
			if (!empty( $_FILES ))
				$this->_file = array_shift($_FILES);
		}

		/**
		 * Gets full path to chunk file.
		 * @param null|integer $n Chunk number.
		 * @return string
		 */
		public function getChunkPath ($n = null)
		{
			if ($n === null)
				$n = $this->_params[ 'chunkNumber' ];

			return $this->_tmpDir . '/' . $this->_params[ 'filename' ] . '.part' . $n;
		}

		/**
		 * Check whether file uploading is complete.
		 * @return bool
		 */
		public function getIsComplete ()
		{
			if ($this->_isComplete)
				return true;

			for ($i = 1; $i <= $this->_params[ 'totalChunks' ]; $i++)
			{
				if (!$this->chunkIsUploaded($i))
				{
					$this->_isComplete = false;

					return false;
				}
			}

			$this->_isComplete = true;

			return true;
		}

		/**
		 * Check whether file uploading is new.
		 * @return bool
		 */
		public function getIsNew ()
		{
			if ($this->_isNew == false)
				return false;

			for ($i = 1; $i <= $this->_params[ 'totalChunks' ]; $i++)
			{
				if ($this->chunkIsUploaded($i))
				{
					$this->_isNew = false;

					return false;
				}
			}

			$this->_isNew = true;

			return true;
		}

		/**
		 * Detects whether chunk is being tested or uploaded.
		 * @return bool
		 */
		public function getIsUploading ()
		{
			return isset( $this->_file );
		}

		/**
		 * Gets param by its name.
		 * @param string $name Parameter name.
		 * @return null|mixed Parameter if exists, null otherwise.
		 */
		public function getParam ($name)
		{
			if (isset( $this->_params[ $name ] ))
				return $this->_params[ $name ];
			else
				return null;
		}

		/**
		 * Gets name of the saved file.
		 * @return string
		 */
		public function getSavedFilename ()
		{
			return $this->_savedFilename;
		}

		/**
		 * Sets temporary directory.
		 * @param string $dir
		 * @throws \yii\base\Exception
		 */
		public function setTmpDir ($dir)
		{
			$this->_tmpDir = $dir . '/' . $this->_params[ 'identifier' ];
			if (!is_dir($this->_tmpDir))
				FileHelper::createDirectory($this->_tmpDir);
		}

		/**
		 * Sets path to target directory where to save video file.
		 * @param string $dir
		 * @throws \yii\base\Exception
		 */
		public function setTargetDir ($dir)
		{
			$this->_targetDir = $dir;
			if (!is_dir($this->_targetDir))
				FileHelper::createDirectory($this->_targetDir);
		}

		/**
		 * Uploads or tests current chunk.
		 */
		public function process ()
		{
			if (!empty( $this->_params ))
			{
				if ($this->getIsUploading())
					$this->uploadChunk();
				else
					$this->testChunk();
			}
		}

		/**
		 * Saves uploaded file.
		 * @param null|string $name File name (without extension).
		 * @return string Saved file name with extension.
		 * @throws \yii\base\ErrorException
		 */
		public function save ($name = null)
		{
			if ($name == null)
				$name = StringHelper::random();

			$ext = pathinfo($this->_params[ 'filename' ], PATHINFO_EXTENSION);
			$this->_savedFilename = $name . '.' . $ext;
			if (( $file = fopen($this->_targetDir . '/' . $this->_savedFilename, 'w') ) !== false)
			{
				if (Yii::$app->settings->get('upload_concat') == 'cat')
				{
					fclose($file);
					setlocale(LC_ALL, 'ru_RU.UTF-8');
					for ($i = 1; $i <= $this->_params[ 'totalChunks' ]; $i++)
						shell_exec('cat ' . escapeshellarg($this->getChunkPath($i)) . ' >> ' . escapeshellarg($this->_targetDir . '/' . $this->_savedFilename));
					setlocale(LC_ALL, null);
				}
				else
				{
					for ($i = 1; $i <= $this->_params[ 'totalChunks' ]; $i++)
						fwrite($file, file_get_contents($this->getChunkPath($i)));
					fclose($file);
				}
			}
			FileHelper::removeDirectory($this->_tmpDir);

			return $this->_savedFilename;
		}

		/**
		 * Sets response status based on chunk uploaded status.
		 */
		public function testChunk ()
		{
			if ($this->chunkIsUploaded())
				Yii::$app->response->setStatusCode(200);
			else
				Yii::$app->response->setStatusCode(204);
		}

		/**
		 * Uploads current chunk.
		 * @throws \yii\base\ErrorException
		 */
		public function uploadChunk ()
		{
			move_uploaded_file($this->_file[ 'tmp_name' ], $this->chunkPath);

			Yii::$app->response->setStatusCode(200);
		}

		/**
		 * Checks if given chunk is uploaded.
		 * @param null|integer $n Chunk number.
		 * @return bool
		 */
		private function chunkIsUploaded ($n = null)
		{
			return file_exists($this->getChunkPath($n));
		}
	}
