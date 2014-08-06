<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');


header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="reportStockDetail.csv"');

 
$stockId = $_REQUEST['stock_id'];
$sQuery1 = ' 
SELECT stock_items.item_id,items.description1,stock_items.total_qty,stock_items.sold_qty,stock_items.return_qty,stock_items.remaining_qty,stock_items.bookout_qty,stock_items.stock_qty from stock_items left join items on items.id=stock_items.cms_item_id  where stock_items.stock_id="' . $stockId . '"';

$rResultTotal = mysql_query($sQuery1, $gaSql['link']) or die(mysql_error());


$fp = fopen('php://output', 'w');

$headertext = ";";

$list = array(
    array('Item Id', 'Item Name', 'Total Qty', 'Sold Qty', 'Return Qty', 'Remaining Qty', 'Bookout Qty', 'Stock Qty'),
);
foreach ($list as $fields) {
    fputcsv($fp, $fields);
}
while ($row = mysql_fetch_assoc($rResultTotal)) {
    fputcsv($fp, $row);
}
fclose($fp);

echo $csvOutput;
exit();
?>