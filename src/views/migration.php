<?php
	/**
	 * This view is used by console migration controller.
	 * The following variables are available in this view:
	 */
	/* @var $className string the new migration class name without namespace */
	/* @var $namespace string the new migration class namespace */

	echo "<?php\n";
	if (!empty($namespace))
	{
		echo "\nnamespace {$namespace};\n";
	}
?>

	use vps\tools\db\Migration;

	class <?= $className ?> extends Migration
	{
		public function safeUp()
		{

		}

		public function safeDown()
		{
			echo "<?= $className ?> cannot be reverted.\n";

			return false;
		}
	}