<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');

 
header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="reportStaffSale.csv"');
 
      $startDate=$_REQUEST['startDate']." 00:00:00";
      $endDate=$_REQUEST['endDate']." 23:59:59";
  $sQueryTotal ='SELECT DATE(transactions.created_at) AS daat ,user.name,
 (SELECT COUNT(tr1.order_id)   FROM transactions  tr1 WHERE   tr1.status_id=3 AND (tr1.transaction_type_id=3 OR tr1.transaction_type_id=2)  AND DATE(tr1.created_at)=daat     AND tr1.user_id=user.id) AS numberoforder,
           (SELECT ROUND(SUM(tr2.sold_price),2)  FROM transactions  tr2    WHERE   tr2.status_id=3 AND (tr2.transaction_type_id=3 OR tr2.transaction_type_id=2)  AND DATE(tr2.created_at)=daat AND tr2.user_id=user.id) AS  soldprice

 FROM transactions  LEFT JOIN USER ON user.id=transactions.`user_id`  WHERE  transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2) AND transactions.created_at>="' . $startDate . '"   AND transactions.created_at<="' . $endDate . '"   GROUP BY user.name,daat ORDER BY daat
'; 
 
 
 
                       
 
 
$rResultTotal = mysql_query($sQueryTotal, $gaSql['link']) or die(mysql_error());
  

$fp = fopen('php://output', 'w');
 
        $headertext=";";
        
 $list = array (
    array('Date','Staff Member','Order Count','Total Sale'),
 
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