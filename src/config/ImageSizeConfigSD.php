<?php

	namespace vps\tools\config;

	/**
	 * @author    Evgenii Kuteiko <kuteiko@mail.ru>
	 * @copyright Copyright (c) 2018
	 */
	class ImageSizeConfigSD
	{
		const QUALITY = 80;
		const WIDTH   = 150;
		const HEIGHT  = 150;

		public static $fit = ImageSizeFit::NONE;
	}