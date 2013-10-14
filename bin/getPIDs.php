<?php

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

if (!$vudl_home) {
  die('VUDL_HOME environment variable must be set\n');
}

if (count($argv) < 7) {
  die('
Missing parameters!
       Usage:
       php getPIDs.php host port verbose(CSV or just PIDs) collection modelType include_collection_pid
       
       Example:
       php getPIDs.php localhost 8080 true "vudl:3" "ResourceCollection" true
       
');
}

$host = $argv[1];
$port = $argv[2];
$verbose = $argv[3];
$collection = $argv[4];
$modelType_str = $argv[5];
$include_collection_pid = $argv[6];

$modelTypes = array();
if (strlen($modelType_str)) {
  $modelTypes = explode(',', $modelType_str);
}

$apiBase = "http://{$host}:{$port}/fedora";

//this is required by the fedoraAPI
$logger = new Zend_Log();

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime; 

if (strlen($collection)) {
  $exportCSV = getAllChildren($collection, $modelTypes);
} else {
  $exportCSV = getAllObjects($modelTypes);
}

if ($verbose == 'true') {

  echo $exportCSV;
  if ($include_collection_pid == 'true') {
    echo $collection . "\n";
  }
} else {
  //add header line to match verbose CSV
  echo "PID\n";
  
  if ($include_collection_pid == 'true') {
    echo $collection . "\n";
  }
  
  $rows = explode("\n", $exportCSV);
  unset($rows[0]);
  foreach($rows as $row) {
    $row_arr = str_getcsv($row, ",");
    echo $row_arr[0] . "\n";
  }
  
}

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = (($endtime - $starttime)/60);

function getAllObjects($modelTypes) {
  global $logger;
  global $apiBase;

  $query = '
            select $pid $object $date $state $label
                from <#ri>
                where
                    $object <dc:identifier> $pid
                    and
                    ';
  if (count($modelTypes)) {
      $query .= '(';
      foreach($modelTypes as $key=>$objectType) {
        $query .= '
                  $object <info:fedora/fedora-system:def/model#hasModel> <info:fedora/vudl-system:' . $objectType . '>
                  ';
                  if ($key < (count($modelTypes)-1)) {
                  $query .= ' 
                            or 
                            ';
                  }
      }
      $query .= ')';
      $query .= '
                and
                ';
  }
    $query .= '
                    $object <fedora-view:lastModifiedDate> $date
                  and 
                    $object <fedora-model:state> $state
                  and 
                    $object <fedora-model:label> $label
';

  //echo $query;
  
  $obj = new FedoraObject($logger);
  $obj->setAPiBase($apiBase);
  
  $returnFormat = 'CSV';
  
  $result = $obj->risearch('tuples', '', 'itql', $returnFormat, '', '', '', $query);
  
  return $result->getBody();
  
}

function getAllChildren($pid, $modelTypes) {

  global $logger;
  global $apiBase;
  
  $obj = new FedoraObject($logger);
  $obj->setPID($pid);
  $obj->setAPiBase($apiBase);
  
  $query = ' 
            select $pid $child $parent $date $state $label
              from <#ri>
              where  $child <fedora-rels-ext:isMemberOf> $item
              and ';
  if (count($modelTypes)) {
      $query .= '(';
      foreach($modelTypes as $key=>$objectType) {
        $query .= '
                  $child <info:fedora/fedora-system:def/model#hasModel> <info:fedora/vudl-system:' . $objectType . '>
                  ';
                  if ($key < (count($modelTypes)-1)) {
                  $query .= ' 
                            or 
                            ';
                  }
      }
      $query .= ')';
      $query .= '
                and
                ';
  }
  $query .= '
              walk (
                    $child
                    <fedora-rels-ext:isMemberOf>
                    <info:fedora/XXX:XXX>
                  and
                    $child
                    <fedora-rels-ext:isMemberOf>
                    $parent
                ) 
                and 
                  $child <fedora-model:label> $label
                and 
                  $child <dc:identifier> $pid
                and 
                  $child <fedora-view:lastModifiedDate> $date
                and 
                  $child <fedora-model:state> $state
';
  
  $query = str_replace('XXX:XXX', $pid, $query);

  //echo $query;
  
  $returnFormat = 'CSV';
  
  $result = $obj->risearch('tuples', '', 'itql', $returnFormat, '', '', '', $query);
  
  return $result->getBody();

}

?>
