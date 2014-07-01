<?php
session_start();
set_time_limit(1000000000000);

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');

 
 


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */
 $sQuery = $_SESSION['sQuery'];
echo $filename = ExportData($sQuery);
//$output=Array(	'status'=>'success',
//'query'=>$sQuery,
//'filename'=>$filename);
//echo json_encode($output);
 
 public   function ExportData($sql) {
$out = '';
$header = '';
$results = $this->runQuery($sql);
$fields = $this->GetQueryFields($results);

foreach ($fields as $f) {
$header .= '"'.$f.'",';
}

$out .= rtrim($header,',')."\n";

while ($row = mysql_fetch_array($results))
{
$csv_row = '';
foreach ($fields as $f) {
$csv_row .= '"' . $row[$f] . '",';	
}
$out .= rtrim($csv_row,',')."\n";
}
$filename = $this->OutPutData($out);
return $filename;
}

private function OutPutData($out) {
$time = $this->current_time();
$length = strlen($out);
$filename = "tmp/".md5($time.$length).".csv";
$fd = fopen($filename, "w");
$bytes = fwrite($fd, $out . "\n");
fclose($fd);
return $filename;
}


?>