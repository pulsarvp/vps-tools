<?php

	use vps\tools\db\Migration;

	class m180101_000001_export_query extends Migration
	{
		public function safeUp ()
		{

			$this->createTable('export', [
				'id'          => $this->primaryKey(),
				'title'       => $this->string(255)->notNull(),
				'description' => $this->text()->null(),
				'query'       => $this->text()->null(),
				'prefix'      => $this->string(255)->null(),
				'createDT'    => $this->dateTime()->null(),
				'dt'          => $this->dateTime()->null(),
			]);
			$this->batchInsert('auth_item', [ 'name', 'type', 'description', 'created_at', 'fixed' ], [
				[ 'createExport', 2, 'Создания экспорта.', time(), 1 ],
				[ 'editExport', 2, 'Редактирования экспорта.', time(), 1 ],
				[ 'viewExport', 2, 'Просмотр экспорта.', time(), 1 ],
				[ 'viewExportList', 2, 'Просмотр списка экспортов.', time(), 1 ],
				[ 'deleteExport', 2, 'Удаление экспорта.', time(), 1 ],
				[ 'generateExport   ', 2, 'Генерация экспорта.', time(), 1 ],
				[ 'exportManager', 1, 'Export manager.', time(), 1 ],
				[ 'exportViewer', 1, 'Export viewer.', time(), 1 ]
			]);
			$this->batchInsert('auth_item_child', [ 'parent', 'child' ], [
				[ 'exportManager', 'createExport' ],
				[ 'exportManager', 'editExport' ],
				[ 'exportManager', 'viewExport' ],
				[ 'exportManager', 'viewExportList' ],
				[ 'exportManager', 'deleteExport' ],
				[ 'exportManager', 'generateExport' ],
				[ 'exportViewer', 'viewExport' ],
				[ 'exportViewer', 'viewExportList' ],
				[ 'exportViewer', 'generateExport' ]
			]);
		}

		public function safeDown ()
		{
			$this->dropTable('export');
			$this->delete('auth_item', [ 'name' => [
				'createExport',
				'editExport',
				'viewExport',
				'viewExportList',
				'deleteExport',
				'generateExport',
				'exportManager',
				'exportViewer'
			] ]);
		}
	}