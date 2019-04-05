<?php
	namespace vps\tools\html;

	/**
	 * @inheritdoc
	 * @property-write bool $upload
	 */
	class FormB4 extends Form
	{
		/**
		 * @inheritdoc
		 */
		public $fieldClass = '\vps\tools\html\FieldB4';

		public $errorCssClass = 'has-error is-invalid';

		public $validationStateOn = self::VALIDATION_STATE_ON_INPUT;

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
				'offset'  => 'offset-sm-3 col-sm-offset-3',
				'hint'    => '',
				'error'   => 'error-block invalid-feedback'
			],
			'errorOptions'         => [ 'encode' => false ],
		];
	}
