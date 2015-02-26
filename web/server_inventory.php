<?php
session_start();
require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');
/*
 * Script:    DataTables server-side script for PHP and MySQL
 * Copyright: 2010 - Allan Jardine
 * License:   GPL v2 or BSD (3-point)
 */
 

//$siteUrl   = "http://localhost:4430/poscms/web/";
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array("inventory.item_id","shops.branch_number", "inventory.total", "inventory.sold", "inventory.book_out", "inventory.returned", "inventory.available","inventory.delivery_count","inventory.stock_in","inventory.stock_out");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "inventory.id";

/* DB table to use */
$sTable = "inventory";



$sJoin = 'LEFT JOIN shops   ON shops.id = inventory.shop_id';

 

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
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
				 	" . mysql_real_escape_string($_GET['sSortDir_' . $i]) . ", ";
        }
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

        if ($aColumns[$i] == "selling_price") {

            if (!is_numeric($_GET['sSearch'])) {

                $numpricve = itemsLib::currencyVersionConvertor($_GET['sSearch']);

                if (is_numeric($numpricve)) {
                    $sWhere .= $aColumns[$i] . " = '" . mysql_real_escape_string($numpricve) . "' OR ";
                }
            } else {
               // $sWhere .= $aColumns[$i] . " LIKE '" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
            }
        } else {

            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
        }
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}
//   echo $sWhere;

/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
    if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
    }
}


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
$rResult = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());

 //echo $sQuery;
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
	";
$rResultTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];

  $_SESSION['sQuery']=$sQuery;
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
 
    for ($i = 0; $i < count($aColumns); $i++) {
        $col = explode('.',$aColumns[$i]);
      
        
         if ($aColumns[$i] == "inventory.sold") {
        
        if($aRow[$col[1]]>0){
                 $action = "<a href=".$siteUrl."backend.php/inventory/inventorySoldDetail?id=" . $aRow["item_id"] . "&branch_number=".$aRow["branch_number"]." class='inevtory-link'>".$aRow[$col[1]]."</a>";
                 
              $row[] = $action;
        }else{
            $row[] = $aRow[$col[1]];     
        }
              
         }elseif($aColumns[$i] == "inventory.delivery_count"){
              if($aRow[$col[1]]>0){
              $action = "<a href=".$siteUrl."backend.php/inventory/deliveries/id/" . $aRow["item_id"] . "?branch_number=".$aRow["branch_number"]." class='inevtory-link'>".$aRow[$col[1]]."</a>";
                 
             
               $row[] = $action;
        }else{
            $row[] = $aRow[$col[1]];     
        }
              }elseif($aColumns[$i] == "inventory.stock_in"){
              if($aRow[$col[1]]>0){
              $action = "<a href=".$siteUrl."backend.php/inventory/stockIn/id/" . $aRow["item_id"] . "?branch_number=".$aRow["branch_number"]." class='inevtory-link'>".$aRow[$col[1]]."</a>";
                 
             
               $row[] = $action;
        }else{
            $row[] = $aRow[$col[1]];     
        }
              }elseif($aColumns[$i] == "inventory.stock_out"){
              if($aRow[$col[1]]>0){
              $action = "<a href=".$siteUrl."backend.php/inventory/stockOut/id/" . $aRow["item_id"] . "?branch_number=".$aRow["branch_number"]." class='inevtory-link'>".$aRow[$col[1]]."</a>";
                 
              $row[] = $action; 
              }else{
            $row[] = $aRow[$col[1]];     
        }
         }else{
           $row[] = $aRow[$col[1]];    
         }
        
    }
    $output['aaData'][] = $row;

 
}

 
$sQueryTotal = "
		SELECT sum(inventory.total) as inTotal,
                sum(inventory.sold) as soldTotal,
                sum(inventory.book_out) as bookTotal,
                sum(inventory.returned) as sreturnTotal,
                sum(inventory.available) as availableTotal,
                sum(inventory.delivery_count) as deliveryTotal, 
                sum(inventory.stock_in) as stockInTotal,
                sum(inventory.stock_out) as stockOutTotal 
		FROM   $sTable
                     $sJoin
                    $sWhere
	";

 
$rsTotal = mysql_query($sQueryTotal, $gaSql['link']) or die(mysql_error());
 $rowTotal = mysql_fetch_array($rsTotal); 
  $row = array();
 $row[] = "<b> Total </b>";
 $row[] = "";
  $row[] ="<b> ". $rowTotal['inTotal']." </b>";  
   $row[] ="<b> ". $rowTotal['soldTotal']." </b>";
    $row[] = "<b> ". $rowTotal['bookTotal']." </b>";
     $row[] ="<b> ". $rowTotal['sreturnTotal']." </b>";
      $row[] = "<b> ". $rowTotal['availableTotal']." </b>";
       $row[] = "<b> ". $rowTotal['deliveryTotal']." </b>";
         $row[] = "<b> ". $rowTotal['stockInTotal']." </b>";
           $row[] = "<b> ". $rowTotal['stockOutTotal']." </b>";
       
 $output['aaData'][] = $row;


echo json_encode($output);
?>