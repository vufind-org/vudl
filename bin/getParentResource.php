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

/*
if (!$vudl_home) {
  die('VUDL_HOME environment variable must be set\n');
}
*/

$xmldoc = new DOMDocument();        
$xmldoc->formatOutput = true;
$xmldoc->encoding="utf-8";
$root = $xmldoc->createElement("parentResource");
$xmldoc->appendChild( $root );

$PID_node = $xmldoc->createElement('PID');
$PID_node = $root->appendChild($PID_node);

$label_node = $xmldoc->createElement('label');
$label_node = $root->appendChild($label_node);




$PID = $_GET['PID'];
if (!strlen($PID)) {
  
} else {

  $apiBase = "http://localhost:8088/fedora";

  //this is required by the fedoraAPI
  $logger = new Zend_Log();
  
  $parentResource_raw = 
  
  $parentResourceCSV = getParentResource($PID);

  $rows = explode("\n", $parentResourceCSV);
  unset($rows[0]);
  
  foreach($rows as $row) {
    $row_arr = str_getcsv($row, ",");
    //echo "<parentResource><PID>" . $row_arr[0] . "</PID><label>" . $row_arr[3] . "</label></parentResource>";
    if (strlen($row_arr[0])) {
    $PID_node->nodeValue = $row_arr[0];
    $label_node->nodeValue = $row_arr[3];
    }
    //break;
    //return the last line instead of breaking
  }
}

echo $xmldoc->saveXML();

function getParentResource($pid) {

  global $logger;
  global $apiBase;
  
  $obj = new FedoraObject($logger);
  $obj->setPID($pid);
  $obj->setAPiBase($apiBase);
  
  $query = ' 
            select $pid $child $parent $parentTitle from <#ri>
            where walk (
                <info:fedora/XXX:XXX>
                <fedora-rels-ext:isMemberOf>
                $parent
              and
                $child
                <fedora-rels-ext:isMemberOf>
                $parent
            ) 
            and 
              $parent 
              <fedora-model:label>
              $parentTitle
            and
              $parent 
              <info:fedora/fedora-system:def/model#hasModel> 
              <info:fedora/vudl-system:ResourceCollection>
            and 
              $parent <dc:identifier> $pid
';
  
  $query = str_replace('XXX:XXX', $pid, $query);

  //echo $query;
  
  $returnFormat = 'CSV';
  
  $result = $obj->risearch('tuples', '', 'itql', $returnFormat, '', '', '', $query);
  
  return $result->getBody();

}

?>
