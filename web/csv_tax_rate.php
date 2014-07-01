<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');

 
header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="reportPaymentMethodSale.csv"');
 
      $startDate=$_REQUEST['startDate']." 00:00:00";
      $endDate=$_REQUEST['endDate']." 23:59:59";
 $sQuery1 =' 
SELECT   "Tax" AS taxval, "19 %" AS vatval,    round(sum((sold_price)*19/100),2) as vatprice,round(sum(sold_price),2) as soldprice  FROM transactions     WHERE     transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2)   AND transactions.created_at>="' . $startDate . '"   AND transactions.created_at<="' . $endDate . '"';

 
 
$rResultTotal = mysql_query($sQuery1, $gaSql['link']) or die(mysql_error());
  

$fp = fopen('php://output', 'w');
 
        $headertext=";";
        
 $list = array (
    array('Tax','Rate','Vat Amount On Paid Order','Paid Order Amount'),
 
);
foreach ($list as $fields) {
    fputcsv($fp, $fields);
}
while($row = mysql_fetch_assoc($rResultTotal)){
    fputcsv($fp, $row);
}
fclose($fp);

echo $csvOutput;
exit();
?>