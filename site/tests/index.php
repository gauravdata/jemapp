<?php

// define these global path constants here
define ( 'ROOT_PATH', dirname ( dirname ( __FILE__ ) ) );
define ( 'LIB_PATH', ROOT_PATH . '/inc/library' );
define ( 'APPLICATION_PATH', ROOT_PATH . '/inc/application' );
define ( 'MODULE_PATH', ROOT_PATH . '/inc/application/modules' );

// define the path for config.ini
define ( 'CONFIG_PATH', ROOT_PATH . '/inc/application/config' );

// Define application environment
defined ( 'APPLICATION_ENV' ) || define ( 'APPLICATION_ENV', (getenv ( 'APPLICATION_ENV' ) ? getenv ( 'APPLICATION_ENV' ) : 'production') );

// set the php include path
set_include_path ( LIB_PATH . PATH_SEPARATOR . get_include_path () );

require_once './application/controllers/ControllerTestCase.php';