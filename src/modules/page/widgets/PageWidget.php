<?php

	namespace vps\tools\modules\page\widgets;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-31
	 */

	use vps\tools\modules\page\models\Page;
	use Yii;
	use yii\base\Widget;
	use yii\data\ActiveDataProvider;
	use yii\web\View;

	class PageWidget extends Widget
	{
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
							'Url'  => '\vps\tools\helpers\Url',

						],
						'widgets' => [
							'blocks' => [
								'LinkPager' => 'yii\widgets\LinkPager'
							]
						]
					]
				]
			]);
			Yii::$app->view->registerCss('#page-list .value {font-family: Menlo, monospace; overflow-wrap: break-word; word-wrap: break-word; max-width: 500px}');
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			$query = Page::find();

			$provider = new ActiveDataProvider([
				'query'      => $query,
				'sort'       => [
					'attributes'   => [
						'id',
						'guid',
						'title',
						'active'
					],
					'defaultOrder' => [
						'title' => SORT_ASC
					]
				],
				'pagination' => [
					'pageSize'       => Yii::$app->settings->get('page_size_object', 20),
					'forcePageParam' => false,
					'pageSizeParam'  => false,
					'urlManager'     => new \yii\web\UrlManager([
						'enablePrettyUrl' => true,
						'showScriptName'  => false,
						'rules'           => [
							'page/page/<page>' => 'page/index'
						]
					])
				]
			]);

			return $this->renderFile('@pageViews/index.tpl', [
				'title'      => Yii::tr('Manage page', [], 'page'),
				'models'     => $provider->models,
				'pagination' => $provider->pagination,
				'sort'       => $provider->sort,
			]);
		}
	}