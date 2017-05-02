<?php
	namespace vps\tools\web;

	/**
	 * @inheritdoc
	 */
	class UrlRule extends \yii\web\UrlRule
	{
		public $ajax = false;

		/**
		 * @inheritdoc
		 */
		public function parseRequest ($manager, $request)
		{
			if ($this->ajax and isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) and $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] !== 'XMLHttpRequest')
				return false;

			return parent::parseRequest($manager, $request);
		}

	}