<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');

 
header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="reportStaffSale.csv"');
 
      $startDate=$_REQUEST['startDate']." 00:00:00";
      $endDate=$_REQUEST['endDate']." 23:59:59";
  $sQueryTotal ='Select user.name,
        (select COUNT(transactions.order_id) FROM transactions WHERE transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2)  AND transactions.created_at>="' . $startDate . '"   AND transactions.created_at<="' . $endDate . '"  AND transactions.user_id=user.id) as numberoforder,
           (select round(sum(transactions.sold_price),2)  FROM transactions WHERE transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2)  AND transactions.created_at>="' . $startDate . '"   AND transactions.created_at<="' . $endDate . '"  AND transactions.user_id=user.id) as  soldprice
         from  user  left join transactions on  transactions.user_id=user.id WHERE transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2)  AND transactions.created_at>="' . $startDate . '"   AND transactions.created_at<="' . $endDate . '"   group by transactions.user_id 
         '; 
 
 
 
                       
 
 
$rResultTotal = mysql_query($sQueryTotal, $gaSql['link']) or die(mysql_error());
  

$fp = fopen('php://output', 'w');
 
        $headertext=";";
        
 $list = array (
    array('Staff Member','Order Count','Total Sale'),
 
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