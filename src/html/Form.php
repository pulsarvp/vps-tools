<?php
	namespace vps\tools\html;

	use Yii;
	use \vps\tools\db\Model;
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
				'offset'  => 'offset-sm-3',
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

		/***
		 * @param array      $submitOptions Options for the submit button.
		 * @param array|null $cancelOptions Options for the cancel link. Use 'referrer' to set cancel link.
		 * @return string
		 */
		public function submitBlock ($submitOptions = [], $cancelOptions = null)
		{
			$submitOptions = array_merge([
				'type'  => 'submit',
				'title' => Yii::tr('Save'),
				'name'  => 's-save',
				'class' => 'btn btn-primary'
			], $submitOptions);

			$submitButton = Html::submitButton($submitOptions[ 'title' ], $submitOptions);

			$cancelLink = '';
			if (is_array($cancelOptions))
			{
				$link = $cancelOptions[ 'referrer' ] ?? Yii::$app->request->referrer ?? Yii::$app->request->baseUrl;
				unset($cancelOptions[ 'referrer' ]);
				$cancelOptions = array_merge([
					'title' => Yii::tr('Cancel'),
					'class' => 'btn btn-warning ml-1'
				], $cancelOptions);
				$cancelLink = Html::a($cancelOptions[ 'title' ], $link, $cancelOptions);
			}

			/** @var \vps\tools\html\Field $field */
			$fieldConfig = array_merge([
				'form'        => $this,
				'model'       => new Model(),
				'attribute'   => 'submit',
				'enableLabel' => false
			], $this->fieldConfig);

			$field = new $this->fieldClass($fieldConfig);
			if ($this->layout === 'horizontal')
				Html::addCssClass($field->wrapperOptions, $fieldConfig[ 'horizontalCssClasses' ][ 'offset' ]);
			$field->parts[ '{input}' ] = $submitButton . $cancelLink;

			return $field->render();
		}
	}
