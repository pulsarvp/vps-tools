<?php
	namespace vps\tools\html;

	use Yii;
	use \vps\tools\helpers\Html;
	use \yii\base\InvalidConfigException;

	/**
	 * @inheritdoc
	 * @property-write bool $upload
	 */
	class Form extends \yii\bootstrap\ActiveForm
	{
		/**
		 * @inheritdoc
		 */
		public $fieldClass = '\vps\tools\html\Field';

		/**
		 * Adds 'role' attribute.
		 *
		 * @inheritdoc
		 */
		public $options = [ 'role' => 'form' ];

		/**
		 * Default layout is set to horizontal.
		 *
		 * @inheritdoc
		 */
		public $layout = 'horizontal';

		/**
		 * Default layout for single form group.
		 *
		 * @inheritdoc
		 */
		public $fieldConfig = [
			'template'             => '{beginLabel}{labelTitle}{endLabel}{beginWrapper}{input}{hint}{error}{endWrapper}',
			'horizontalCssClasses' => [
				'label'   => 'col-sm-3 col-form-label',
				'wrapper' => 'col-sm-9',
				'hint'    => '',
				'error'   => 'error-block'
			],
			'errorOptions'         => [ 'encode' => false ],
		];

		/**
		 * @inheritdoc
		 */
		public $enableClientScript = false;

		/**
		 * @inheritdoc
		 */
		public $method = 'post';

		/**
		 * @var string Form name.
		 */
		public $name;

		/**
		 * Adds some default configuration. I.e. form name and layout class.
		 *
		 * @inheritdoc
		 */
		public function init ()
		{
			if (!in_array($this->layout, [ 'default', 'horizontal', 'inline' ]))
				throw new InvalidConfigException('Invalid layout type: ' . $this->layout);

			if ($this->layout !== 'default')
				Html::addCssClass($this->options, 'form-' . $this->layout);

			if ($this->name)
				$this->options[ 'name' ] = $this->name;

			parent::init();
		}

		/**
		 * Whether the form should perform file upload.
		 *
		 * @property-set bool $upload
		 * @param $upload
		 */
		public function setUpload ($upload)
		{
			if ($upload)
				$this->options[ 'enctype' ] = 'multipart/form-data';
		}
	}
