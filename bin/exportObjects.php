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
$proc_dir = $vudl_home . "/bin/proc";

if (!$vudl_home) {
  die('VUDL_HOME environment variable must be set\n');
}

if (count($argv) < 5) {
  die('
Missing parameters!
       Usage:
       php exportObjects.php host port f|p /path/to/pids.csv|pid:xxx
       
       Example:
       php exportObjects.php localhost 8080 f /mnt/data/digital/local/vudl/log/proc/pids.csv
       or
       php exportObjects.php localhost 8080 p vudl:12345
');
}

$host = $argv[1];
$port = $argv[2];
$pid_type = $argv[3];
$single_pid = $argv[4];
$pid_path = $argv[4];


$apiBase = "http://{$host}:{$port}/fedora";

$writer = new Zend_Log_Writer_Stream($vudl_home . '/bin/log/exportObjects_' .  $now . '.txt');
$logger = new Zend_Log($writer);

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime; 

$logger->log('host: ' . $host, Zend_Log::INFO);
$logger->log('port: ' . $port, Zend_Log::INFO);
$logger->log('pid type: ' . $pid_type, Zend_Log::INFO);
$logger->log('pid data: ' . $single_pid, Zend_Log::INFO);

$PIDS = array();

if ($pid_type == 'p') {
  $PIDS[] = $single_pid;
} else if ($pid_type == 'f') {
  if (($handle = fopen($pid_path, "r")) !== FALSE) {
      $column_headers = fgetcsv($handle);
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          if (strlen(trim($data[0]))) {
            $PIDS[] = $data[0];
          }
      }
  }
} else {
  die('Incorrect PID type. Must be "d" or "f"');
}


$dest_dir = $vudl_home . "/bin/exportObjects/" . str_replace(":", "_", $now);
if (!is_dir($dest_dir)) {
  mkdir($dest_dir, 0777, true);
}

foreach($PIDS as $PID) {
    // get current object
    $exportObj = new FedoraObject($logger);
    $exportObj->setPID($PID);
    $exportObj->setAPiBase($apiBase);
    
    $export = $exportObj->export("info:fedora/fedora-system:FOXML-1.1", "migrate", "UTF-8");

    $exportXML = simplexml_load_string($export->getBody());
    file_put_contents($dest_dir . "/" . $now . "_" . str_replace(":", "_", $PID) . ".xml", $exportXML->asXML());

    $logger->log('Modified PID: ' . $PID, Zend_Log::INFO);
}


$logger->log('Total exported Objects: ' . count($PIDS), Zend_Log::INFO);
echo date('Y-m-d_His') . " Total exported Objects: " . count($PIDS) . "\n";

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = (($endtime - $starttime)/60);

echo date('Y-m-d_His') . " Total Time: " . $totaltime . " mins\n";
$logger->log('Total Time: ' . $totaltime . " mins", Zend_Log::INFO);

?>
