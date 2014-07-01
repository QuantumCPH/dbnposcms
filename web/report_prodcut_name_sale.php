<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');

 
header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="reportProductNameSale.csv"');
 
      $startDate=$_REQUEST['startDate']." 00:00:00";
      $endDate=$_REQUEST['endDate']." 23:59:59";
$sWhere="  transactions.created_at>='".$startDate."'";
         $sWhere .=" AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2) AND  transactions.created_at<='".$endDate."'";
         $sWhere .=" AND transactions.status_id=3 GROUP BY items.item_id";
     $sQuery1 = "SELECT items.description1,items.group,items.supplier_number,SUM(transactions.quantity) AS totalQuantity,   ROUND(SUM(transactions.sold_price),2) AS totalPrice  
		,items.item_id FROM  items LEFT JOIN transactions ON transactions.item_id=items.item_id WHERE  $sWhere ";

 
 
$rResultTotal = mysql_query($sQuery1, $gaSql['link']) or die(mysql_error());
  

$fp = fopen('php://output', 'w');
 
        $headertext=";";
        
 $list = array (
    array('Name','Group','SupplierNumber','UnitSold','GrossSale','itemId'),
  
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