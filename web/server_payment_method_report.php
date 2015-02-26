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
$aColumns = array("payment_types.title", "payment_types.id",   "order_payments.amount" );

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "payment_types.id";

/* DB table to use */
$sTable = "payment_types";



$sJoin = 'LEFT JOIN order_payments   ON order_payments.payment_type_id =payment_types.id';
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
      //AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2)
            $sWhere = "WHERE payment_types.id<5 AND    order_payments.created_at>='".mysql_real_escape_string($startDate)."'";
                    $sWhere .= " AND  payment_types.id<5 AND  order_payments.created_at<='".$endDate."'" ;
        } else {
            //
            $sWhere .= " AND  payment_types.id<5 AND    order_payments.created_at>='".$startDate."'" ;
             $sWhere .= " AND  payment_types.id<5 AND  order_payments.created_at<='".$endDate."'" ;
        }
     
       
        
/*
 * SQL queries
 * Get data to display
 */
        $sGroup=" group by  order_payments.payment_type_id";
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
                $sJoin
		$sWhere
                    $sGroup
		$sOrder
		$sLimit
	";
 
  $sQuery;
 

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
// echo $sQuery;
$rResultTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];

$output = array(
    "sEcho" => intval($_GET['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

$amounttotal=0;
$transactiontnumber=0;
while ($aRow = mysql_fetch_array($rResult)) {
    $row = array();
  //var_dump($aRow);
    for ($i = 0; $i < count($aColumns); $i++) {
        $col = explode('.',$aColumns[$i]);
      
        
         if ($aColumns[$i] == "payment_types.id") {
          $idtypemethod= $aRow['id'];
 
		 $sQueryTotal ='select pt.title,
 (select COUNT(order_id)  FROM order_payments  as op    WHERE    op.payment_type_id=pt.id    AND op.created_at>="' . $startDate . '"   AND op.created_at<="' . $endDate . '") AS transactionquantity 

,(select round(sum(amount),2)    FROM order_payments as op  WHERE     op.payment_type_id=pt.id    AND op.created_at>="' . $startDate . '"   AND op.created_at<="' . $endDate . '") AS totalsale 

from payment_types pt where id="' .$idtypemethod. '"';
                                                        

//echo  $sQueryTotal;
//die; 
//         echo "kkkkkkkk".$aRow['item_id']; 
// die;
$rsTotal = mysql_query($sQueryTotal, $gaSql['link']) or die(mysql_error());
 $rowTotal = mysql_fetch_array($rsTotal); 
         
            $row[] =$rowTotal['transactionquantity'];     
    $transactiontnumber=$transactiontnumber+$rowTotal['transactionquantity']; 
         }elseif($aColumns[$i] == "order_payments.amount"){
            $row[] =$rowTotal['totalsale'];      
            $amounttotal =$amounttotal+ $rowTotal['totalsale'];
         }else{
           $row[] = $aRow[$col[1]];    
         }
       
    }
    $output['aaData'][] = $row;

 
}


  $row = array();
 $row[] = "<b> Total </b>";
 
      
         $row[] ="<b> ". $transactiontnumber." </b>";
  $row[] ="<b> ". $amounttotal." </b>";

   
  
 $output['aaData'][] = $row; 
echo json_encode($output);
?>