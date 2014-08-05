==============================
Installation and Configuration
==============================

1. Unpack the PMSC.tgz tarball and move the four enclosed directories into you
   php search path:
   
   - soapclient
   - NPowerSeattle
   - Salesforce
   - PMSC
   
2. Add a single directive to the PHP configuration environment, either in php.ini, or in a .htaccess
   file, with the following definition::
   
        twb_app_config = [/Full/path/to/]PMSC/Config/twbconfig.xml
        
3. In the Config directory, copy twbconfig.xml.example to twbconfig.xml
4. Open the file in your text editor of choice, and fill in the values for username, password,
   password token, and also the full path to the current directory::
   
       <appData>
         <username>[username]</username>
         <password>[password]</password>
         <passwordToken>[token]</passwordToken>
         <wsdl>[/path/to/this/directory/]soapclient/partner.wsdl.xml]</wsdl>
       </appData>
       
5. To run the unit tests, you will need to be able to run your php installations cli executable. 
   Move into the Tests directory, then run::
   
    /path/to/cli/php -d error_reporting=E_ERROR testSuite.php
    
   or, if phpunit is callable, you can filter to run a single file or test::
   
    phpunit --filter testType TestRecordActivities RegistrarTest.php
    

=====================
PMSC_Domain_Registrar
=====================

------------------
createActivities()
------------------
In your application, first import the Registrar module and instantiate an object::

    require_once 'PMSC/Domain/Registrar.php';
    $registrar = new PMSC_Domain_Registrar();

    $result = $registrar->createActivities($listOfDicts);




