<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Onoffice_Gui
 */

use onOffice\WPlugin\Installer\DatabaseChangesInterface;
use function DI\autowire;

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	$pDI = require dirname( dirname( __FILE__ ) ) . '/plugin.php';
	$pDI->set(DatabaseChangesInterface::class, autowire(\onOffice\tests\Mocks\DatabaseChangesDummy::class));
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
