<?php
// Define path to inpdex directory
define('INDEX_PATH', realpath(dirname(__FILE__)));

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

defined('CACHE_PATH')
|| define('CACHE_PATH', INDEX_PATH . '/cache');
 
// Ensure libraries/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
realpath(APPLICATION_PATH . '/../../libraries'),
realpath(APPLICATION_PATH . '/../libraries'),
get_include_path(),
)));

/** Constants */
require_once APPLICATION_PATH . '/include/constants.php';

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
APPLICATION_ENV,
array(
    	'config' => APPLICATION_PATH . '/configs/application.ini',
    	'autoloadernamespaces' => array('HTS_')
)
);

$application
->bootstrap()
->run();
