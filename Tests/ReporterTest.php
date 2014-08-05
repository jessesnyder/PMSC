<?php
require_once 'PHPUnit/Framework.php';
require_once 'PMSC/Domain/Reporter.php';

/**
 * ReporterTest.php
 * 
 * Unit tests
 * 
 * @since 8-1-2009
 * @author Jesse Snyder, NPower Seattle <jesses@npowerseattle.org>
 */


class TestInputErrorReporter extends PHPUnit_Framework_TestCase
{
    protected $fixture;
    
    protected function setUp() {
        // trigger_error makes your tests fail, so suppress anything lower 
        // than E_ERROR
        ini_set("error_reporting", "E_ERROR");
        $this->fixture = new PMSC_Domain_InputErrorReporter();
    }
    
    protected function tearDown() {
        // Nothing to do
    }
    
    
    public function testType() {
        $this->assertType('PMSC_Domain_InputErrorReporter', $this->fixture);
    }
    
    public function testTotalAttempts() {
        $this->fixture->setAttempts(6);
        $this->assertEquals(6, $this->fixture->totalAttempts());
    }
    
    public function testInputFailures() {
        $failure1 = array('lineNum'=>'13', 'content'=>'some bad line');
        $failure2 = array('lineNum'=>'23', 'content'=>'some other bad line');
        
        $this->fixture->addError($failure1);
        $this->fixture->addError($failure2);
        $this->assertEquals(2, $this->fixture->totalErrors());
        $inputReport = $this->fixture->listErrors();
        $this->assertNotNull($inputReport);
        $this->assertEquals(2, count($inputReport));
        $this->assertEquals('13', $inputReport[0]['lineNum']);
        $this->assertEquals('23', $inputReport[1]['lineNum']);
        $this->assertEquals('some bad line', $inputReport[0]['content']);
        $this->assertEquals('some other bad line', $inputReport[1]['content']);
    }
}

class TestWebserviceErrorReporter extends PHPUnit_Framework_TestCase
{
    protected $fixture;
    
    protected function setUp() {
        // trigger_error makes your tests fail, so suppress anything lower 
        // than E_ERROR
        ini_set("error_reporting", "E_ERROR");
        $this->fixture = new PMSC_Domain_WebserviceErrorReporter();
    }

    protected function tearDown() {
        // Nothing to do
    }
    
    public function testType() {
        $this->assertType('PMSC_Domain_WebserviceErrorReporter', $this->fixture);
    }
    public function testWebserviceFailures() {
        // $id, $activityType, $person, $time, $error
        $failure1 = 'Null,Lunch,CLIENT12345,2009-12-31 12:45:06,some error';
        $failure2 = 'Null,Dinner,CLIENT54321,2009-12-31 12:45:08,some error';
        $this->fixture->addError($failure1);
        $this->fixture->addError($failure2);
        
        $this->assertEquals(2, $this->fixture->totalErrors());
        $wsReport = $this->fixture->listErrors();

        $this->assertEquals(2, count($wsReport));
        $this->assertEquals('Null', $wsReport[0]['activityId']);
        $this->assertEquals('Null', $wsReport[1]['activityId']);
        $this->assertEquals('Lunch', $wsReport[0]['activityType']);
        $this->assertEquals('Dinner', $wsReport[1]['activityType']);
        $this->assertEquals('CLIENT12345', $wsReport[0]['clientId']);        
        $this->assertEquals('CLIENT54321', $wsReport[1]['clientId']); 
        $this->assertEquals('some error', $wsReport[0]['error']); 
        $this->assertEquals('some error', $wsReport[1]['error']); 
    }
}
?>