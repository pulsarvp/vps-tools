<?php
	namespace vps\tools\html;

	use Yii;
	use vps\tools\helpers\ArrayHelper;
	use vps\tools\helpers\Html;
	use yii\bootstrap\Collapse;

	/**
	 * @inheritdoc
	 */
	class Field extends \yii\bootstrap\ActiveField
	{
		/***
		 * @inheritdoc
		 */
		public $options = [ 'class' => 'form-group row' ];

		/**
		 * Renders [datetimepicker](https://github.com/Eonasdan/bootstrap-datetimepicker) input.
		 *
		 * @param bool  $dateOnly Whether to show only datepicker without time.
		 * @param array $options
		 * @return $this
		 */
		public function datetimepicker ($dateOnly = false, $options = [])
		{
			$options = array_merge($this->inputOptions, $options);
			$this->adjustLabelFor($options);
			$options[ 'id' ] = $this->attribute;

			$this->parts[ '{input}' ] = Html::activeHiddenInput($this->model, $this->attribute, $options);
			$this->parts[ '{input}' ] .= Html::tag('div', '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>' . Html::textInput('', '', [ 'class' => 'form-control', 'tabindex' => '-1' ]), [ 'class' => 'input-group date' . ( $dateOnly ? '' : 'time' ) . 'picker', 'id' => $this->attribute . '-picker' ]);

			return $this;
		}

		/**
		 * Generates hidden input inside hidden form-group.
		 *
		 * @param array $options
		 * @return $this
		 */
		public function hidden ($options = [])
		{
			$this->options[ 'class' ] = ( isset($this->options[ 'class' ]) ? $this->options[ 'class' ] . ' ' : '' ) . 'hide';
			$this->parts[ '{input}' ] = Html::activeHiddenInput($this->model, $this->attribute, $options);

			return $this;
		}

		/**
		 * Prepares data for [Jasny file input plugin](http://www.jasny.net/bootstrap/javascript/#fileinput).
		 *
		 * @param null  $path Path to the image.
		 * @param array $options
		 * @return $this
		 */
		public function image ($path = null, $options = [])
		{
			$options = array_merge($this->inputOptions, $options);
			$this->adjustLabelFor($options);

			$this->parts[ '{input}' ] = Html::tag('div',
				Html::tag('div',
					Html::tag('div',
						$path ? Html::img($path . '?' . time()) : '',
						[ 'class' => 'fileinput-preview thumbnail', 'data-trigger' => 'fileinput' ]
					)
					. Html::tag('div',
						Html::tag('div',
							Html::tag('span', Yii::tr('Select image'), [ 'class' => 'fileinput-new' ])
							. Html::tag('span', Yii::tr('Change'), [ 'class' => 'fileinput-exists' ])
							. Html::activeFileInput($this->model, $this->attribute),
							[ 'class' => 'btn btn-default btn-file' ]
						)
						. Html::a(Yii::tr('Remove'), '#', [ 'class' => 'btn btn-default fileinput-exists', 'data-dismiss' => 'fileinput' ])
					),
					[ 'class' => 'fileinput fileinput-new', 'data-provides' => 'fileinput' ]
				),
				[ 'class' => 'image' ]
			);
			$this->enableLabel = false;

			return $this;
		}

		/**
		 * This function overrides error output. All errors are displayed.
		 *
		 * @inheritdoc
		 */
		public function render ($content = null)
		{
			// Custom error output.
			$errors = $this->model->getErrors($this->attribute);

			if (count($errors) == 0)
				$this->parts[ '{error}' ] = '';
			else
			{
				$class = isset($this->errorOptions[ 'class' ]) ? ' class="' . $this->errorOptions[ 'class' ] . '"' : '';
				$this->parts[ '{error}' ] = '<div' . $class . '><ul class="list-unstyled">';
				foreach ($errors as $e)
				{
					$this->parts[ '{error}' ] .= '<li>' . $e . '</li>';
				}
				$this->parts[ '{error}' ] .= '</ul></div>';
			}

			return parent::render($content);
		}

		/**
		 * Prepares data to be shown as [bootstrap select plugin](https://github.com/silviomoreto/bootstrap-select).
		 *
		 * @param array $models Array of models to be inserted in option tag.
		 * @param array $listOptions Options for 'option' tag. The following options are
		 * * value: string, attribute name to use for `<option>` value, default is 'id';
		 * * label: string, attribute name to put for `<option>` text;
		 * * data-content: string, attribute name to put in 'data-content' attribute for `<option>`, will overwrite label if set;
		 * * title: string, attribute name to put for title attribute for `<option>`.
		 * @param array $options
		 * @return $this
		 */
		public function select ($models, $listOptions, $options = [])
		{
			$value = isset($listOptions[ 'value' ]) ? $listOptions[ 'value' ] : 'id';
			$label = isset($listOptions[ 'label' ]) ? $listOptions[ 'label' ] : false;
			$content = isset($listOptions[ 'data-content' ]) ? $listOptions[ 'data-content' ] : false;
			$title = isset($listOptions[ 'title' ]) ? $listOptions[ 'title' ] : false;

			$items = [];
			foreach ($models as $model)
				$items[ $model->$value ] = $label ? $model->$label : '';

			$options[ 'options' ] = [];

			if ($title)
				foreach ($models as $model)
					$options[ 'options' ][ $model->$value ][ 'title' ] = $model->$title;

			if ($content)
			{
				preg_match_all('/\{([\w]+)\}/', $content, $matches);
				foreach ($models as $model)
				{
					$tr = [];
					foreach ($matches[ 0 ] as $i => $match)
					{
						$var = $matches[ 1 ][ $i ];
						$tr[ $match ] = $model->$var;
					}
					$options[ 'options' ][ $model->$value ][ 'data-content' ] = strtr($content, $tr);
				}
			}

			return $this->dropDownList($items, $options);
		}

		/**
		 * Renders [sortable lists](https://github.com/rubaxa/Sortable) for selecting multiple data with order. It
		 * contains left and right blocks with draggable items between them.
		 *
		 * @param array $left Items for left block (selected items).
		 * @param array $right Items for right block.
		 * @param array $options
		 * @return $this
		 * @throws \yii\base\InvalidConfigException
		 */
		public function sortable ($left, $right, $options = [])
		{
			$options = array_merge($this->inputOptions, $options);
			$this->adjustLabelFor($options);
			$options[ 'value' ] = '';

			$html = Html::activeHiddenInput($this->model, $this->attribute, $options);
			$leftBlock = Html::listGroupOrder($left, [
				'class'      => 'sortable list-group-sm' . ( count($left) > 0 ? '' : ' empty' ),
				'orderClass' => 'info'
			]);

			if (key($right) === 0)
			{
				$rightBlock = Html::listGroupOrder($right, [ 'class' => 'sortable list-group-sm', 'title' => 'guid' ]);
			}
			else
			{
				$collapse = new Collapse;
				$items = [];
				foreach ($right as $label => $data)
				{
					$items[] = [
						'label'   => $label,
						'content' => Html::listGroupOrder($data, [ 'class' => 'sortable list-group-sm', 'title' => 'guid' ]),
						'options' => [ 'class' => 'panel-sortable' ]
					];
				}
				$collapse->items = $items;
				$rightBlock = $collapse->renderItems();
			}

			$this->parts[ '{input}' ] = $html . '<div class="row sortable-' . $this->attribute . '"><div class="col-md-6 sortable-left">' . $leftBlock . '</div><div class="col-md-6 sortable-right">' . $rightBlock . '</div></div>';

			return $this;
		}

		/**
		 * Renders submit button.
		 *
		 * @param string $text
		 * @param array  $options
		 * @return $this
		 */
		public function submit ($text, $options = [])
		{
			$this->label(false);
			$this->parts[ '{input}' ] = Html::submitButton($text, [ 'class' => 'btn btn-lg btn-primary', 'name' => 's-' . $this->attribute ]);

			return $this;
		}

		/**
		 * Renders [tagsinput](https://github.com/bootstrap-tagsinput/bootstrap-tagsinput).
		 *
		 * @param array $options
		 * @return $this
		 */
		public function tags ($options = [])
		{
			$options[ 'data-role' ] = 'tagsinput';

			$this->adjustLabelFor($options);
			if (!isset($options[ 'id' ]))
				$options[ 'id' ] = $this->getInputId();

			$model = $this->model;
			$attribute = $this->attribute;
			$data = $model->$attribute;
			$value = '';
			if (is_array($data))
			{
				if (count($data) > 0)
				{
					if (is_object($data[ 0 ]))
						$value = implode(',', ArrayHelper::objectsAttribute($data, 'title'));
					else
						$value = implode(',', $data);
				}
			}
			else
				$value = $data;

			$this->parts[ '{input}' ] = Html::textInput(Html::getInputName($model, $attribute), $value, $options);

			return $this;
		}

		/**
		 * Added translation for label title.
		 *
		 * @inheritdoc
		 */
		protected function renderLabelParts ($label = null, $options = [])
		{
			parent::renderLabelParts($label, $options);
			if (!empty($this->parts[ '{labelTitle}' ]))
				$this->parts[ '{labelTitle}' ] = Yii::tr($this->parts[ '{labelTitle}' ]);
		}
	}
