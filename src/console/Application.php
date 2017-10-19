<?php

	namespace vps\tools\console;
	
	/**
	 * @author    Anna Manaenkova <witzawitz@gmail.com>
	 * @copyright Copyright (c) 2017
	 * @date      2017-10-20
	 */
	class Application extends \yii\console\Application
	{
		protected $_nullComponents = [ 'settings' ];

		public function __construct ($config = [])
		{
			$config = $this->filterConfig($config);
			parent::__construct($config);
		}

		private function filterConfig ($config)
		{
			$setNullComponents = false;

			// Remove unnecessary components for migrations.
			$argv = $_SERVER[ 'argv' ];
			$argc = $_SERVER[ 'argc' ];
			if ($argc == 1)
				$setNullComponents = true;
			elseif ($argc > 1)
			{
				list($controller,) = explode("/", $argv[ 1 ]);
				if (
					$controller == "migrate"
					or !isset($config[ 'controllerMap' ][ $controller ])
				)
					$setNullComponents = true;
			}

			if ($setNullComponents)
			{
				foreach ($this->_nullComponents as $nc)
					if (isset($config[ 'components' ][ $nc ]))
						$config[ 'components' ][ $nc ] = null;
			}

			return $config;
		}

	}