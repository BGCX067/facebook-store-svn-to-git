<?php

class Bootstrap extends HTS_Bootstrap
{

	public function __initPath()
	{
		// Define path to image directory
		define('IMG_PATH', STATIC_PATH . '/img');
        define('UPLOAD_PATH', STATIC_PATH . '/upload');
        define('CSS_PATH', STATIC_PATH . '/css');
        define('JS_PATH', STATIC_PATH . '/js');
        define('UPLOAD_THUMBAIL_PATH', UPLOAD_PATH . '/thumbnails');
        // Define url to image directory
        define('IMG_URL', STATIC_URL . '/img');
        define('UPLOAD_URL', STATIC_URL . '/upload');
        define('CSS_URL', STATIC_URL . '/css');
        define('JS_URL', STATIC_URL . '/js');
        define('UPLOAD_THUMBAIL_URL', UPLOAD_URL . '/thumbnails');
	}

	public function __initPaymentManager()
	{
		parent::__initPaymentManager();

		$manager = Payment_Manager::getInstance();
		$manager->setOptions($this->getOptions());
		$manager->registerHandler(new Payment_Item_Footer());
	}

}

