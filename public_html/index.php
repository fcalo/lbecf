<?php


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

if ( APPLICATION_ENV != 'development' && $_SERVER['REQUEST_URI']!="/apoyo/ipn/" && $_SERVER['REQUEST_URI']!="/apoyo/payipn/")
    require('sas.php');


if ( APPLICATION_ENV == 'development' )
    defined('STATIC_PATH') ||  define('STATIC_PATH',  'http://lbe.dev');
else 
    defined('STATIC_PATH') || define('STATIC_PATH',  'http://labutacaescarlata.com');
   
// Ensure library/ is on include_path


$includePath = array();
$includePath[] = realpath(APPLICATION_PATH.'/forms/');
$includePath[] = realpath(APPLICATION_PATH.'/lib/');
$includePath[] = realpath(APPLICATION_PATH . '/../library');
$includePath = implode(PATH_SEPARATOR,$includePath);
set_include_path($includePath);


/*set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));*/

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();