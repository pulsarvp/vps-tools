<?php
	namespace tests\helpers;

	use vps\tools\helpers\ConfigurationHelper;
	use Yii;
	use yii\base\InvalidConfigException;

	class ConfigurationHelperTest extends \PHPUnit\Framework\TestCase
	{
		public function init ()
		{
			Yii::$app->translations = [];
		}

		public function testAddTranslation ()
		{
			$this->expectException(InvalidConfigException::class);
			ConfigurationHelper::addTranslation('setting', [ 'setting' => 'setting.php' ]);

			ConfigurationHelper::addTranslation('setting', [ 'setting' => 'setting.php' ], realpath(__DIR__ . '/../../src/modules/setting/messages'));
			$this->assertEquals("Название", Yii::tr("Name", [], "setting"));
			$this->assertEquals("w3ern8op", Yii::tr("w3ern8op", [], "setting"));

			ConfigurationHelper::addTranslation('widgets', [ 'widgets/footer' => 'footer.php' ], realpath(__DIR__ . '/../../src/widgets/messages'));
			$this->assertEquals("Версия 123", Yii::tr("Version {version}", [ 'version' => 123 ], "widgets/footer"));

			$this->assertEquals("Remove?", Yii::tr("Remove?", [], "apiapp"));
			ConfigurationHelper::addTranslation('apiapp', [ 'apiapp' => 'apiapp.php' ], realpath(__DIR__ . '/../../src/modules/apiapp/messages'));
			$this->assertEquals("Удалить?", Yii::tr("Remove?", [], "apiapp"));
		}
	}