<?php
ini_set('display_errors', 1);

// define these global path constants here
define ( 'ROOT_PATH', dirname ( dirname ( __FILE__ ) ) );
define ( 'LIB_PATH', ROOT_PATH . '/inc/library' );
define ( 'APPLICATION_PATH', ROOT_PATH . '/inc/application' );
define ( 'MODULE_PATH', ROOT_PATH . '/inc/application/modules' );

// define the path for config.ini
define ( 'CONFIG_PATH', ROOT_PATH . '/inc/application/config' );

$dev = array ('adam', 'dev', 'hq.thewebmen.com');
if (in_array( $_SERVER ['HTTP_HOST'], $dev )) {
	ini_set('display_errors', 1);
	set_include_path ( LIB_PATH . PATH_SEPARATOR . get_include_path () );
	define ( 'APPLICATION_ENV', 'development' );
} else {
	set_include_path ( LIB_PATH . PATH_SEPARATOR . '/home/sites/general' );
	define ( 'APPLICATION_ENV', 'production' );	
}

require_once 'Zend/Application.php';
$application = new Zend_Application ( APPLICATION_ENV, CONFIG_PATH . '/config.ini' );
$application->bootstrap ()->run();