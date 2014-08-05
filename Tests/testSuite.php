<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'PMSCTests::main');
}
/**
 * testSuite.php
 * 
 * Runs unit tests
 * 
 *  @author Jesse Snyder at NPowerSeattle <jesses@npowerseattle.org>
 *  @package Test
 *  @since 7-26-2009
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'RegistrarTest.php';
require_once 'ReporterTest.php';

class PMSCTests {
    
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite('PMSC');
        $suite->addTestSuite('TestRecordActivities');
        $suite->addTestSuite('TestInputErrorReporter');
        $suite->addTestSuite('TestWebserviceErrorReporter');        
        
        return $suite;
    }
}
if (PHPUnit_MAIN_METHOD == 'PMSCTests::main') {
    PMSCTests::main();
}
?>
