<?php

	namespace vps\tools\widgets;

	use yii\bootstrap\Modal;
	use yii\web\NotFoundHttpException;
	use Yii;
	use yii\helpers\Html;
	use yii\helpers\Json;

	class ModalAjax extends Modal
	{
		/**
		 * Наименование класса для размещения основного текста возвращаемого AJAXом
		 * @var string
		 */
		public $classBodyBlock = 'modal-ajax-body';
		/**
		 * @var string url для генерации текста, который помещается в диалоговое окно;
		 */
		public $dataAjaxRoute;
		/**
		 * @var array параметры запроса
		 */
		public $dataAjaxParams = [];

		public function init ()
		{
			/** Подключение событий перед открытием и после зарытия окна */
			$this->clientEvents[ 'show.bs.modal' ] = $this->getShowEvent();
			$this->clientEvents['hidden.bs.modal'] = $this->hiddenEvent;

			parent::init();
		}

		public function run ()
		{
			echo $this->renderBody();

			parent::run();
		}

		/**
		 * @return string
		 * @throws NotFoundHttpException
		 */
		public function renderBody ()
		{
			if ($this->dataAjaxRoute == null)
			{
				throw new NotFoundHttpException(Yii::t('app', 'The url is not defined.'));
			}
			else
			{
				return Html::tag('div', '', [ 'class' => $this->classBodyBlock ]
				);
			}
		}

		/**
		 * Возвращает текст события перед открытием
		 * @return string
		 */
		protected function getShowEvent(){
			return 'function (event) {'
				. 'var modal = $(this);'
				. 'var bodyBlock = modal.find(".' . $this->classBodyBlock . '");'
				. 'bodyBlock.addClass("loading");'
				. '$.ajax({'
					. 'type: "get",'
					. 'url: "'. $this->dataAjaxRoute . '",'
					. 'data: '. Json::encode($this->dataAjaxParams) . ','
					. 'success: function(data){'
						. 'if(bodyBlock.hasClass("loading")) {bodyBlock.removeClass("loading")};'
						. 'bodyBlock.html(data);'
					. '}'. "\n"
				. '});'. "\n"
				. '}';
		}

		/**
		 * Возвращает текст события после закрытия
		 * @return string
		 */
		protected function getHiddenEvent(){
			return 'function (event){'
				. '$(this).find(".' . $this->classBodyBlock . '").html("");'
				. '}';
		}
	}