<?php
require_once 'PMSC/Config/AppConfigHelper.php';
require_once 'PMSC/Domain/Reporter.php';
/**
 * Registrar.php
 * 
 * Call a webservice in Salesforce to create various activity records.
 *
 * The objects returned by the webservice look like this:
 *
 * stdClass Object ( 
 *    [result] => Array 
 *        ( [0] => ActivityType,BarcodeID,Time,Error 
 *          [1] => ActivityType,BarcodeID,Time,Error )
 * )
 * 
 * @since 7-26-2009
 * @author Jesse Snyder at NPower Seattle <jesses@npowerseattle.org>
 * @package PMSC_Domain
 */

class PMSC_Domain_Registrar {
    private $helper;
    private $sfConnection; 
    private $webservice;
    private $errors;
    const DEFAULT_WEB_SERVICE_NAME = "PMSCWebServices";
    
    public function __construct($serviceName = Null) {
        // Since we "borrow" the session from a regular API call, we don't want to cache it
        ini_set('soap.wsdl_cache_enabled', 0);
        $this->errors = array();
        $serviceName ? $this->webserviceName = $serviceName : $this->webserviceName = self::DEFAULT_WEB_SERVICE_NAME;
        try {
            $this->helper = PMSC_Config_AppConfigHelper::getInstance();
            $this->sfConnection = $this->helper->getProperty('SFConnection');
            // setup the SOAP client modify the headers
            $this->webservice = $this->initializeWebservice();
        } 
        catch (Exception $e) {
            array_push($this->errors, $e->getMessage());
            trigger_error($e->getMessage(), E_USER_WARNING);
        }
    }
      
    /**
     * createActivities()
     *
     *  @param array $activityData An array of associative arrays describing Activities to create
     *  @return array $newActivityIds An array of created Activity IDs, or null on error
     **/
    public function createActivities($activityData) {

        if (!isset($this->sfConnection)) {
            $error = "No connection to Salesforce";
            array_push($this->errors, $error);
            trigger_error($error, E_USER_WARNING);
            return null;
        }
        try {
            $response = $this->webservice->createPMSCActivity($activityData);
        }
        catch (Exception $e) {
            $error = "Webservice call " . $this->webserviceName . " failed: " . 
                            $e->getMessage() . $e->getTraceAsString();
            trigger_error($error, E_USER_WARNING);
            array_push($this->errors, $error);
            return null;
        }
        isset($response->result) ? $result = $response->result: $result = Null;
        if ($result and !is_array($result)){
            $result = array($result);
        }
        $feedback = new PMSC_Domain_WebserviceErrorReporter();
        $feedback->setAttempts(count($activityData));
        if ($result) {
            foreach ($result as $rec) {
                $feedback->addError($rec);
            }
        }
        return $feedback;
    }
    
    public function errors() {
        return $this->errors;
    }
    
    /*
     *  End public API
     */
    private function initializeWebservice() {
        // setup the SOAP client modify the headers
        $parsedURL = parse_url($this->sfConnection->getLocation());
        define ("_SFDC_SERVER_", substr($parsedURL['host'],0,strpos($parsedURL['host'], '.')));
        define ("_WS_WSDL_", $this->helper->getProperty('webserviceWsdl'));
        define ("_WS_ENDPOINT_", 'https://' . _SFDC_SERVER_ . '.salesforce.com/services/wsdl/class/' . $this->webserviceName);
        define ("_WS_NAMESPACE_", 'http://soap.sforce.com/schemas/class/' . $this->webserviceName);

        $client = new SoapClient($this->helper->getProperty('webserviceWsdl'));
        $sforce_header = new SoapHeader(_WS_NAMESPACE_, "SessionHeader", array("sessionId" => $this->sfConnection->getSessionId()));
        $client->__setSoapHeaders(array($sforce_header));
        
        return $client;
    }

}
?>