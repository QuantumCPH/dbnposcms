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
$aColumns = array("items.item_id", "items.description1", "items.group", "items.supplier_number", "items.selling_price", "items.id" );

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "items.id";

/* DB table to use */
$sTable = "items";



$sJoin = 'LEFT JOIN transactions   ON transactions.item_id =items.item_id';
//$sJoin .= ' LEFT JOIN user   ON user.id = transactions.user_id ';
//$sJoin .= ' RIGHT JOIN order_payments   ON order_payments.id = transactions.order_id ';
//$sJoin .= ' LEFT JOIN payment_types   ON payment_types.id = order_payments.payment_type_id ';

 
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
        if($_GET['sSearch_' . $i]!="~"){
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        }
         
 
         $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";    
         
         
    }
}
//
 if ($sWhere == "") {
     $startDate=$_REQUEST['startDate']." 00:00:00";
      $endDate=$_REQUEST['endDate']." 23:59:59";
            $sWhere = "WHERE   transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2) AND transactions.created_at>='".mysql_real_escape_string($startDate)."'";
                    $sWhere .= " AND transactions.created_at<='".$endDate."'" ;
        } else {
            $sWhere .= "  AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2) AND transactions.status_id=3 AND transactions.created_at>='".$startDate."'" ;
             $sWhere .= " AND transactions.created_at<='".$endDate."'" ;
        }
     
       
        
/*
 * SQL queries
 * Get data to display
 */
        $sGroup=" group by items.item_id ";
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
                $sJoin
		$sWhere
                    $sGroup
		$sOrder
		$sLimit
	";
 
 //echo $sQuery;
 

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
                         $sGroup
	";
 
$rResultTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];


//$sQuery = "
//		SELECT *
//		FROM   $sTable
//                     $sJoin
//                    $sWhere
//	";
//
//echo $sQuery;
//die;
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
      
        
         if ($aColumns[$i] == "items.selling_price") {
             $strCond=" AND transactions.item_id='".$aRow['item_id']."'";        
        $sQueryTotal = "
		SELECT sum(transactions.quantity) as inTotal 
               
FROM   $sTable
                     $sJoin
                    $sWhere
               $strCond
	";

// echo $sQueryTotal;
// 
//         echo "kkkkkkkk".$aRow['item_id']; 
// die;
$rsTotal = mysql_query($sQueryTotal, $gaSql['link']) or die(mysql_error());
 $rowTotal = mysql_fetch_array($rsTotal); 
         
            $row[] =$rowTotal['inTotal'];     
    
         }elseif($aColumns[$i] == "items.id"){
             
              $strCond=" AND transactions.item_id='".$aRow['item_id']."'";        
        $sQueryTotal = "
		SELECT sum(transactions.sold_price) as inTotal 
               
FROM   $sTable
                     $sJoin
                    $sWhere
               $strCond
	";

// echo $sQueryTotal;
// 
//         echo "kkkkkkkk".$aRow['item_id']; 
// die;
$rsTotal = mysql_query($sQueryTotal, $gaSql['link']) or die(mysql_error());
 $rowTotal = mysql_fetch_array($rsTotal); 
         
            $row[] = number_format($rowTotal['inTotal'],2);     
         }else{
           $row[] = $aRow[$col[1]];    
         }
       
    }
    $output['aaData'][] = $row;

 
}

   $sQueryTotal = "
		SELECT round(sum(transactions.sold_price),2) as totalPrice ,sum(transactions.quantity) as totalQuantity
               
FROM   $sTable
                     $sJoin
                    $sWhere";
$rsTotal = mysql_query($sQueryTotal, $gaSql['link']) or die(mysql_error());
$rowTotal = mysql_fetch_array($rsTotal); 
  $row = array();
 $row[] = "<b> Total </b>";
  $row[] = "";
     $row[] = "";
      $row[] = "";
         $row[] ="<b> ". $rowTotal['totalQuantity']." </b>";
  $row[] ="<b> ". $rowTotal['totalPrice']." </b>";

   
  
 $output['aaData'][] = $row;
echo json_encode($output);
?>