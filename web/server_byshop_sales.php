<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
 require_once('connection.php');
/*
 * Script:    DataTables server-side script for PHP and MySQL
 * Copyright: 2010 - Allan Jardine
 * License:   GPL v2 or BSD (3-point)
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("orders.shop_receipt_number_id","shops.branch_number",    "user.name", "orders.total_amount",   "orders.created_at", "orders.id");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "orders.id";

/* DB table to use */
$sTable = "orders";



$sJoin = 'LEFT JOIN shops   ON shops.id = orders.shop_id ';
$sJoin .= ' LEFT JOIN user   ON user.id = orders.shop_user_id ';

 

 


/*
 * Paging
 */
$sLimit = "";
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $sLimit = "LIMIT " . mysql_real_escape_string($_GET['iDisplayStart']) . ", " .
            mysql_real_escape_string($_GET['iDisplayLength']);
}


/*
 * Ordering
 */
if (isset($_GET['iSortCol_0'])) {
    $sOrder = "ORDER BY  ";
    for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
            if(intval($_GET['iSortCol_' . $i])==6){
               $sOrder .= " status   " . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";  
            }else{
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
				 	" . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
        } }
    }
 
    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == "ORDER BY") {
        $sOrder = "";
    }
}


/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere = "";
if ($_GET['sSearch'] != "") {
    $sWhere = "WHERE (";
    for ($i = 0; $i < count($aColumns); $i++) {

       

            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
       
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}
//   echo $sWhere;

/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
    if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
        if ($_GET['sSearch_' . $i] != "~") {
            if ($sWhere == "") {
                $sWhere = "WHERE ";
            } else {
                $sWhere .= " AND ";
            }
        }

        if ($aColumns[$i] == "orders.created_at") {

            $col = explode('~', $_GET['sSearch_' . $i]);
            if ($col[0] != "") {
                $sWhere .= $aColumns[$i] . ">='" . mysql_real_escape_string($col[0]) . "'";
            }
            if ($col[1] != "") {
                if ($col[0] != "") {
                    $sWhere .= " AND ";
                }
                $sWhere .= $aColumns[$i] . "<='" . mysql_real_escape_string($col[1]) . "'";
            }
        } elseif ($aColumns[$i] == "shops.branch_number") {

            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
        } elseif ($aColumns[$i] == "user.name") {

            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
        } elseif ($aColumns[$i] == "orders.shop_receipt_number_id") {
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
           }else{
            
        }
    }
}
//
// if ($sWhere == "") {
//            $sWhere = "WHERE  transactions.transaction_type_id=2 AND transactions.parent_type='receipt_numbers' ";
//        } else {
//            $sWhere .= " AND  transactions.transaction_type_id=2  AND transactions.parent_type='receipt_numbers' ";
//        }
/*if ($sWhere == "") {
    $sWhere = "WHERE  transactions.transaction_type_id<>1 AND transactions.transaction_type_id<>4  AND transactions.transaction_type_id<6   AND transactions.status_id=3   AND transactions.shop_receipt_number_id<>0 ";
} else {
    $sWhere .= " AND transactions.transaction_type_id<>1  AND transactions.transaction_type_id<>4  AND transactions.transaction_type_id<6    AND transactions.status_id=3    AND transactions.shop_receipt_number_id<>0 ";
}
*/

 if(isset($_GET["item_id"]) && $_GET["item_id"]!=""){
     $sJoin .= ' LEFT JOIN transactions   ON transactions.order_id = orders.id ';
   $sWhere .= " AND transactions.item_id=".$_GET["item_id"]; 
   $sWhere .=" Group by transactions.order_id";
}elseif (isset($_GET["shop_id"]) && $_GET["shop_id"]!=""){
   $sWhere .= " AND orders.shop_id=".$_GET["shop_id"];  
}
/*
//AND transactions.status_id=3 
$beforeWhereGroupBy=$sWhere;


*/
/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
                $sJoin
		$sWhere
		$sOrder
		$sLimit
	";

 //echo $sQuery;
//echo $sQuery;
// die;
$rResult = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());


/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
	";
$rResultFilterTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
		SELECT COUNT(" . $sIndexColumn . ")
		FROM   $sTable
                     $sJoin
                    $sWhere
	";

$rResultTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];

 
$sQueryTotal = "
		SELECT sum(orders.total_amount) as totalPrice  
		FROM   $sTable
                     $sJoin
                   $sWhere
	";

 
$rsTotal = mysql_query($sQueryTotal, $gaSql['link']) or die(mysql_error());

 
/*
 * Output
 */
$output = array(
    "sEcho" => intval($_GET['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
//    var_dump($aColumns);
//    die;
    for ($i = 0; $i < count($aColumns); $i++) {
        $col = explode('.', $aColumns[$i]);
        
               if ($col[1] == "shop_receipt_number_id") {
            /* Special output formatting for 'version' column */
            //$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];  branch_number       
            $vartal = $aRow[$col[1]];
            $row[] = "<a href=".$siteUrl."backend.php/transactions/saleDetailView?id=" . $aRow['id'] . "&branch_number=".$aRow['branch_number'].">" . $vartal . " </a>";
               }elseif($col[1] == "id") {
        
         }else {
            $col = explode('.', $aColumns[$i]);
            $row[] = $aRow[$col[1]];
        }
    }
    $output['aaData'][] = $row;
}
 
 
$rowTotal = mysql_fetch_array($rsTotal);
$row = array();
$row[] = "<b> Total </b>";
$row[] = "";
$row[] = "";
$row[] = "<b> " . number_format($rowTotal['totalPrice'], 2) . " </b>";
  $row[] = "";
 
$output['aaData'][] = $row;
 

echo json_encode($output);
?>