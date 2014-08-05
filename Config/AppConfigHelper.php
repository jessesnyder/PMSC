<?php
require_once 'NPowerSeattle/Types/Registry.php';
require_once 'SalesForce/AdminLoginManager.php';
/**
 * AppConfigHelper.php
 * 
 * Registry class for application configuration.
 * 
 * @since 7-26-2009
 * @author Jesse Snyder, NPower Seattle (www.npowerseattle.org)
 * 
 * @package NPowerSeattle_Config
 */
class PMSC_Config_AppConfigHelper extends NPowerSeattle_Types_Registry {
    private static $instance;

    /**
     * getInstance()
     * 
     * @return NPowerSeattle_Config_AppConfigHelper $helper - The single instance of this class
     */
    public static function getInstance() {
        if (empty (self :: $instance)) {
            self :: $instance = new PMSC_Config_AppConfigHelper();
        }
        return self :: $instance;
    }

    private function __construct() {
        // this value is read from php.ini, and should be the only change necessary
        // between hosting environments.
        $xml_config = get_cfg_var("pmsc_app_config");
        if (file_exists($xml_config)) {
            $xml = simplexml_load_file($xml_config);
            if (!($xml->username and $xml->password and $xml->wsdl)) {
                throw new Exception('config file ' . $xml_config . ' is not formatted correctly');
            }
            $this->setProperty('wsdl', (string) $xml->wsdl);
            if (!file_exists($this->getProperty('wsdl'))) {
                throw new Exception("wsdl file " . $this->getProperty('wsdl') . " not found.");
            }
            $this->setProperty('webserviceWsdl', (string) $xml->webserviceWsdl);
            if (!file_exists($this->getProperty('webserviceWsdl'))) {
                throw new Exception("webservice wsdl file " . $this->getProperty('webserviceWsdl') . " not found.");
            }            
            $this->setProperty('username', (string) $xml->username);
            $this->setProperty('passwordToken', (string) $xml->passwordToken);
            $this->setProperty('password', (string) $xml->password . (string) $xml->passwordToken);
            $this->setProperty('SFConnection', $this->initSFConnection());
        } else {
            throw new Exception('Failed to locate config file: ' . $xml_config);
        }
    }

    private function initSFConnection() {
        $adminLogin = new NPowerSeattle_SalesForce_AdminLoginManager($this->getProperty('wsdl'));
        $connection = $adminLogin->login($this->getProperty('username'), $this->getProperty('password'));
        if (!$connection) {
            throw new Exception('Failed to connect to SalesForce ' . $adminLogin->getError());
        }
        return $connection;
    }
}
?>