<?php

	namespace vps\tools\widgets;

	use Yii;
	use yii\base\Widget;
	use yii\web\View;
	use yii\base\InvalidConfigException;

	abstract class AnalyticWidgetAbstract extends Widget
	{
		const TYPE_APP_FRONTEND = 'frontend';
		const TYPE_APP_BACKEND  = 'backend';

		/** @var string Тип приложения */
		public $typeApp;

		//Должно быть поределено в конкретном виджете
		public $nameSettingAnalyticUseSuffix;
		public $nameSettingAnalyticKeySuffix;
		public $nameTemplate;
		public $userHash;

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();

			if (
				$this->nameSettingAnalyticKeySuffix === null
				||
				$this->nameSettingAnalyticUseSuffix === null
			)
			{
				throw new InvalidConfigException('"nameSettingAnalyticKeySuffix" and "nameSettingAnalyticUseSuffix" properties must be defined');
			}

			if ($this->typeApp === null)
			{
				//по умолчанию фронтенд
				$this->typeApp = static::TYPE_APP_FRONTEND;
			}

			$this->view = new View([
				'renderers' => [
					'tpl' => [
						'class'   => 'yii\smarty\ViewRenderer',
						'imports' => [
							'Html' => '\vps\tools\helpers\Html',
							'Url'  => '\vps\tools\helpers\Url'
						]
					]
				]
			]);
		}

		protected function getNameUseSetting ()
		{
			if ($this->typeApp === 'backend')
			{
				return 'analytics_admin_' . $this->nameSettingAnalyticUseSuffix;
			}

			return 'analytics_' . $this->nameSettingAnalyticUseSuffix;
		}

		protected function getNameKeySetting ()
		{
			if ($this->typeApp === 'backend')
			{
				return 'analytics_admin_' . $this->nameSettingAnalyticKeySuffix;
			}

			return 'analytics_' . $this->nameSettingAnalyticKeySuffix;
		}
	}