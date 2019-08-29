<?php
	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 * @date      2018-05-23
	 * @package   vps\tools\widgets
	 */

	namespace vps\tools\widgets;

	use vps\tools\helpers\ArrayHelper;
	use vps\tools\helpers\ConfigurationHelper;
	use Yii;
	use yii\helpers\Html;
	use yii\widgets\LinkPager as BaseLinkPager;

	class LinkPager extends BaseLinkPager
	{
		/**
		 * Надо ли оборачивать в контейнер
		 * Если $this->isCountAllPages true, то будет автоматически присвоено true
		 *
		 * @var bool
		 */
		public $isWrapper = false;

		/**
		 * Тег и опции для wrapper
		 * @var array
		 */
		public $wrapperOptions = [
			'tag'   => 'div',
			'class' => 'pagination-container',
		];

		/**
		 * Надо ли отображать ссылку "Всего"
		 * @var bool
		 */
		public $isCountAllPages = true;

		/**
		 * @inheritdoc
		 */
		public function init ()
		{
			parent::init();

			if ($this->isCountAllPages)
			{
				$this->isWrapper = true;
			}

			// In Bootstrap 4 no div's "next" and "prev", so you need to overwrite the default values
			$this->prevPageCssClass = 'page-item';
			$this->nextPageCssClass = 'page-item';

			// Change the location and size of block
			// https://v4-alpha.getbootstrap.com/components/pagination/#alignment
			// https://v4-alpha.getbootstrap.com/components/pagination/#sizing
			$this->options[ 'class' ] = 'pagination';

			// Default div for links
			$this->linkOptions[ 'class' ] = 'page-link';
			$this->disabledListItemSubTagOptions[ 'class' ] = 'page-link';

			ConfigurationHelper::addTranslation('widgets', [ 'widgets/link-pager' => 'link-pager.php' ], __DIR__ . '/messages');
		}

		/**
		 * @inheritdoc
		 */
		public function run ()
		{
			if ($this->registerLinkTags)
			{
				$this->registerLinkTags();
			}

			if ($this->pagination->getPageCount() > 1)
			{
				if ($this->isWrapper)
				{
					$tagWrapper = ArrayHelper::remove($this->wrapperOptions, 'tag', 'div');

					return Html::tag(
						$tagWrapper,
						Html::tag('nav', $this->renderPageButtons(), [ 'class' => 'd-inline-flex' ])
						. ( $this->isCountAllPages ? $this->renderCountAllPages() : '' ),
						$this->wrapperOptions
					);
				}

				return Html::tag('nav', $this->renderPageButtons(), [ 'class' => 'd-inline-flex' ]);
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function renderPageButton ($label, $page, $class, $disabled, $active)
		{
			$options = [ 'class' => empty($class) ? 'page-item' : $class ];
			$linkOptions = $this->linkOptions;

			if ($active)
			{
				Html::addCssClass($options, $this->activePageCssClass);
			}

			if ($disabled)
			{
				Html::addCssClass($options, $this->disabledPageCssClass);
				$linkOptions[ 'tabindex' ] = '-1';
			}

			return Html::tag('li', Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
		}

		/**
		 * @return string
		 */
		protected function renderCountAllPages ()
		{
			return $this->render('count-all-pages', [
				'totalCount' => $this->pagination->totalCount,
			]);
		}
	}