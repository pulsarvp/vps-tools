<?php

	namespace vps\tools\html;

	use vps\tools\helpers\Html;

	/**
	 * @inheritdoc
	 */
	class FieldB4 extends Field
	{
		/**
		 * Generates hidden input inside hidden form-group.
		 *
		 * @param array $options
		 * @return $this
		 */
		public function hidden ($options = [])
		{
			$this->options[ 'class' ] = ( isset($this->options[ 'class' ]) ? $this->options[ 'class' ] . ' ' : '' ) . 'invisible';
			$this->parts[ '{input}' ] = Html::activeHiddenInput($this->model, $this->attribute, $options);

			return $this;
		}

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
			$this->parts[ '{input}' ] .= Html::tag(
				'div',
				'<span class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></span>'
					. Html::textInput('', '', [ 'class' => 'form-control', 'tabindex' => '-1' ]),
				[ 'class' => 'input-group date' . ( $dateOnly ? '' : 'time' ) . 'picker', 'id' => $this->attribute . '-picker' ]
			);

			return $this;
		}
	}
