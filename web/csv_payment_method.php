<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');

 
header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="reportPaymentMethodSale.csv"');
 
      $startDate=$_REQUEST['startDate']." 00:00:00";
      $endDate=$_REQUEST['endDate']." 23:59:59";
 $sQuery1 ='select pt.title,
 (select COUNT(order_id)  FROM order_payments  as op    WHERE    op.payment_type_id=pt.id    AND op.created_at>="' . $startDate . '"   AND op.created_at<="' . $endDate . '") AS transactionquantity 

,(select round(sum(amount),2)    FROM order_payments as op  WHERE     op.payment_type_id=pt.id    AND op.created_at>="' . $startDate . '"   AND op.created_at<="' . $endDate . '") AS totalsale 

from payment_types pt where id<5';

 
 
$rResultTotal = mysql_query($sQuery1, $gaSql['link']) or die(mysql_error());
  

$fp = fopen('php://output', 'w');
 
        $headertext=";";
        
 $list = array (
    array('Payment Method','Transaction Count','Total Payment'),
     
  
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