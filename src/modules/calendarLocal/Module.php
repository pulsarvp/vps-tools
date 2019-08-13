<?php
	namespace vps\tools\modules\calendarLocal;

	use vps\tools\helpers\ConfigurationHelper;
	use yii\base\BootstrapInterface;
	use Yii;

	/**
	 * Class Module ApiApp
	 *
	 * @package vps\tools\modules\apiapp
	 */
	class Module extends \yii\base\Module implements BootstrapInterface
	{
		/**
		 * @inheritdoc
		 */
		public function bootstrap ($app)
		{
			// Add module I18N category.
			ConfigurationHelper::addTranslation(
				'calendar',
				[ 'calendar' => 'calendar.php' ],
				__DIR__ . '/messages'
			);
		}
	}