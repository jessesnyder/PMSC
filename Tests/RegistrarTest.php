<?php
require_once 'PHPUnit/Framework.php';
require_once 'PMSC/Domain/Registrar.php';
require_once 'PMSC/Config/AppConfigHelper.php';

/**
 * RegistrarTest.php
 * 
 * Unit tests
 * 
 * @since 7-26-2009
 * @author Jesse Snyder, NPower Seattle <jesses@npowerseattle.org>
 */


class TestRecordActivities extends PHPUnit_Framework_TestCase
{
    protected $toDelete;
    protected $fixture;
    protected $helper;
    protected $sfConnection;
    const INVALID_PERSON_ID = 'nobody'; // A signal to our Mock Webservice in Salesforce
 
    protected function setUp() {
        // Don't cache the wsdl, which php will do by default (in /tmp)
        ini_set("soap.wsdl_cache_enabled", "0");
        // trigger_error makes your tests fail, so suppress anything lower 
        // than E_ERROR
        ini_set("error_reporting", "E_ERROR");
        $this->helper = PMSC_Config_AppConfigHelper::getInstance();
        $cwd = getcwd() . '/';
        $this->helper->setProperty('webserviceWsdl', $cwd . 'PMSCWebServicesMock.wsdl.xml');
        $this->fixture = new PMSC_Domain_Registrar('PMSCWebServicesMOCK');
        $this->sfConnection = $this->helper->getProperty('SFConnection');
        // Store any test objects we create and need to delete from Salesforce
        $this->toDelete = array();
    }
    
    protected function tearDown() {
        // Nothing to do
    }
    
    
    public function testType() {
        $this->assertType('PMSC_Domain_Registrar', $this->fixture);
    }
    
    public function testActivities() {
        $records = array(
                'lunch,' . self::INVALID_PERSON_ID . ',2009-10-21 12:15:44',
                'lunch,person2,2009-10-21 12:15:44',
                'lunch,person3,2009-10-21 12:15:44',
                'lunch,' . self::INVALID_PERSON_ID . ',2009-10-21 12:15:44',
            );
        $result = $this->fixture->createActivities($records);
        $this->assertNotNull($result);
        $this->assertType('PMSC_Domain_Reporter', $result);
        $this->assertEquals(4, $result->totalAttempts(), "wrong number of attempts");
        $this->assertEquals(2, $result->totalErrors(), "wrong number of totalErrors");
        $reports = $result->listErrors();
        foreach($reports as $report) {
            $this->assertEquals(self::INVALID_PERSON_ID, $report['clientId']);
            $this->assertEquals('', $report['activityId']);
            $this->assertEquals('lunch', $report['activityType']);
            $this->assertEquals('2009-10-21 12:15:44', $report['time']);
        }                
    }
}
?>