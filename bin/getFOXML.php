<?php

header("Content-type: text/xml;");

require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Stream.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Exception.php';
require_once 'Zend/Json.php';

require_once 'FedoraObject.php';

date_default_timezone_set('America/New_York');

$now = date('Y-m-d_His');

$vudl_home = getenv('VUDL_HOME');
$vufind_local_dir = getenv('VUFIND_LOCAL_DIR');


$xmldoc = new DOMDocument();        
$xmldoc->formatOutput = true;
$xmldoc->encoding="utf-8";
$root = $xmldoc->createElement("members");
$xmldoc->appendChild( $root );

$PID = $_GET['PID'];
if (!strlen($PID)) {
  
} else {
  
  $apiBase = "http://localhost:8088/fedora";

  //this is required by the fedoraAPI
  $logger = new Zend_Log();
  
  $exportObj = new FedoraObject($logger);
  $exportObj->setPID($PID);
  $exportObj->setAPiBase($apiBase);
  $export = $exportObj->export("info:fedora/fedora-system:FOXML-1.1", "public", "UTF-8");

  echo $export->getBody();

  
}



?>
