<?php
count($_SERVER['argv']) == 3 or die("\nPlease insert params: logAccessId APPLICATION_ENV\n");

$logAccessId = $_SERVER["argv"][1];
defined('APPLICATION_ENV')
        || define("APPLICATION_ENV", $_SERVER["argv"][2]);

        
defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));
    
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));


require_once APPLICATION_PATH . '/configs/constants.php';
//echo APPLICATION_PATH;die;
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    ZF_DEBUG_LIBRARY_PATH,
    get_include_path(),
)));


/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


///////////////////////////////////////////////////////////////////////////////

echo "start hook\n";

$logAccessesModel = new Application_Model_DbTable_Log_LogAccesses();
//Zend_Debug::dump($logAccessesModel);die;
$logErrorsModel = new Application_Model_DbTable_Log_LogErrors();

$logAccessData = $logAccessesModel->find($logAccessId)->current();

if($logAccessData && $logAccessData->Controller == 'error' && $logAccessData->Action == 'error'){
    $logErrorsModel->add(array(
        'Domain' => $logAccessData->Domain,
        'Url' => $logAccessData->Url,
        'RemoteAddress' => $logAccessData->RemoteAddress,
        'GET' => $logAccessData->GET,
        'POST' => $logAccessData->POST,
        'SERVER' => $logAccessData->SERVER,
        'CreatedDate' => $logAccessData->CreatedDate,
    ));

}

echo "end hook\n";