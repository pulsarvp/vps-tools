<?php

	namespace vps\tools\widgets;

	use vps\tools\helpers\ConfigurationHelper;
	use yii\base\Widget;
	use yii\helpers\Json;
	use yii\web\View;
	use Yii;

	/**
	 * Widget footer site
	 * Class FooterWidget
	 *
	 * @package vps\tools\widgets
	 */
	class FooterWidget extends Widget
	{
		/**
		 * Copyright from date
		 *
		 * ```php
		 * copyrightFrom = 2017
		 * ```
		 */
		public $copyrightFrom;

		/**
		 * Name company and url
		 *
		 * ```php
		 * company = [ 'title' => '', 'url' => '' ]
		 * ```
		 */
		public $company = [ 'title' => '', 'url' => '' ];

		/**
		 * Array links in center footer
		 *
		 * ```php
		 * links = [[ 'title' => '', 'url' => '' ],[ 'title' => '', 'url' => '' ]]
		 * ```
		 */
		public $links = [];

		/**
		 * Class "container-fluid" or "container"
		 *
		 * @var bool
		 */
		public $fluid = false;

		/**
		 * @var bool
		 * Whether to show right block with version information.
		 */
		public $showVersion;

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();
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

			ConfigurationHelper::addTranslation('widgets', [ 'widgets/footer' => 'footer.php' ], __DIR__ . '/messages');
			
			if (empty($this->company[ 'title' ]))
				$this->company[ 'title' ] = Yii::$app->settings->get('footer_copyright_org_title');
			if (empty($this->company[ 'url' ]))
				$this->company[ 'url' ] = Yii::$app->settings->get('footer_copyright_org_url');
			if (empty($this->copyrightFrom))
				$this->copyrightFrom = Yii::$app->settings->get('footer_copyright_from');
			if (empty($this->links))
				$this->links = Json::decode(Yii::$app->settings->get('footer_links'));
			if (empty($this->showVersion))
				$this->showVersion = (bool) Yii::$app->settings->get('footer_show_version', true);
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			return $this->renderFile(__DIR__ . '/views/footer.tpl', [
				'copyrightFrom' => $this->copyrightFrom,
				'company'       => $this->company,
				'links'         => $this->links,
				'fluid'         => $this->fluid,
				'showVersion'   => $this->showVersion
			]);
		}
	}