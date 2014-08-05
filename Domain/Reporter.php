<?php
/**
 * Reporter.php
 * 
 * Encapsulate results reporting from the webservice call
 * 
 * @since 7-26-2009
 * @author Jesse Snyder at NPower Seattle <jesses@npowerseattle.org>
 * @package PMSC_Domain
 */

class PMSC_Domain_InputErrorReporter {
    private $attemptCount;
    private $errors;
    
    public function __construct() {
        $this->attemptCount = 0;
        $this->errors = array();
    }
    
    public function setAttempts($attempts) {
        $this->attemptCount = $attempts;
    }
    
    public function totalAttempts() {
        return $this->attemptCount;
    }
    
    public function addError($error) {
        array_push($this->errors, $error);
    }
    
    public function listErrors() {
        $copy = array();
        foreach ($this->errors as $rec) {
            array_push($copy, $rec);
        }
        return $copy;
    }
    
    public function totalErrors() {
        return count($this->errors);
    }
}
class PMSC_Domain_WebserviceErrorReporter {
    private $webserviceFailures;
    private $inputFailures;
    private $attemptCount;
    
    public function __construct() {
        $this->webserviceFailures = array();
        $this->inputFailures = array();
        $this->attemptCount = 0;
    }
    
    public function addError($resultString) {
        $record = array();
        list($id, $activityType, $person, $time, $error) = split(',', $resultString);
        $record['activityId'] = $id;
        $record['clientId'] = $person;
        $record['activityType'] = $activityType;
        $record['time'] = $time;
        $record['error'] = $error;
        
        array_push($this->webserviceFailures, $record);
    }
    
    public function listErrors() {
        $copy = array();
        foreach ($this->webserviceFailures as $rec) {
            array_push($copy, $rec);
        }
        return $copy;
    }
    
    public function totalErrors() {
        return count($this->webserviceFailures);
    }
    
    public function totalSuccesses() {
        return $this->attemptCount - $this->totalErrors();
    }
    public function totalAttempts() {
        return $this->attemptCount;
    }
    
    public function setAttempts($count) {
        $this->attemptCount = $count;
    }
}
?>