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
$aColumns = array("montname", "yearname",  "numberoforder", "soldprice" );

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "transactions.id";

/* DB table to use */
$sTable = "transactions";

$sJoin ="";

//$sJoin = ' LEFT JOIN USER ON user.id=transactions.user_id ';
//$sJoin .= ' LEFT JOIN user   ON user.id = transactions.user_id ';
//$sJoin .= ' RIGHT JOIN order_payments   ON order_payments.id = transactions.order_id ';
//$sJoin .= ' LEFT JOIN payment_types   ON payment_types.id = order_payments.payment_type_id ';
/*
 SELECT DATE(transactions.created_at) AS daat ,user.name,
 (SELECT COUNT(tr1.order_id)   FROM transactions  tr1 WHERE   tr1.status_id=3 AND (tr1.transaction_type_id=3 OR tr1.transaction_type_id=2)  AND DATE(tr1.created_at)=daat     AND tr1.user_id=user.id) AS numberoforder,
           (SELECT ROUND(SUM(tr2.sold_price),2)  FROM transactions  tr2    WHERE   tr2.status_id=3 AND (tr2.transaction_type_id=3 OR tr2.transaction_type_id=2)  AND DATE(tr2.created_at)=daat AND tr2.user_id=user.id) AS  soldprice

 FROM transactions  LEFT JOIN USER ON user.id=transactions.`user_id`  WHERE  transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2) AND transactions.created_at >='2012-04-29 16:10:49' AND  transactions.created_at <='2014-05-29 16:10:49'  GROUP BY user.name,daat ORDER BY daat;

 */
 
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
 
//
 if ($sWhere == "") {
     $startDate=$_REQUEST['startDate']." 00:00:00";
      $endDate=$_REQUEST['endDate']." 23:59:59";
      //AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2)
            $sWhere = "WHERE transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2) AND    transactions.created_at>='".mysql_real_escape_string($startDate)."'";
                    $sWhere .= " AND   transactions.created_at<='".$endDate."'" ;
        } else {
            //
            $sWhere .= " AND  transactions.status_id=3 AND (transactions.transaction_type_id=3 OR transactions.transaction_type_id=2) AND    transactions.created_at>='".$startDate."'" ;
             $sWhere .= " AND  transactions.created_at<='".$endDate."'" ;
        }
     
       
        
/*
 * SQL queries
 * Get data to display
 */
        $sGroup="  GROUP BY montname,yearname ";
$sQuery = " SELECT SQL_CALC_FOUND_ROWS MONTHNAME(transactions.created_at) AS montname , YEAR(transactions.created_at) AS yearname,
 (SELECT COUNT(tr1.order_id)   FROM transactions  tr1 WHERE   tr1.status_id=3 AND (tr1.transaction_type_id=3 OR tr1.transaction_type_id=2)  AND MONTHNAME(tr1.created_at)=montname     AND YEAR(tr1.created_at)=yearname) AS numberoforder,
           (SELECT ROUND(SUM(tr2.sold_price),2)  FROM transactions  tr2    WHERE   tr2.status_id=3 AND (tr2.transaction_type_id=3 OR tr2.transaction_type_id=2)   AND MONTHNAME(tr2.created_at)=montname     AND YEAR(tr2.created_at)=yearname) AS  soldprice

 FROM   $sTable
                $sJoin
		$sWhere
                    $sGroup
		$sOrder
		$sLimit
	";
 
 
  

$rResult = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());


/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
                
	";
$rResultFilterTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
 $num_rows = mysql_num_rows($rResult);
$iTotal = $num_rows;

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
       // $col = explode('.',$aColumns[$i]);
      
          if ($aColumns[$i] == "numberoforder") {
          $transactiontnumber=$transactiontnumber+$aRow[$aColumns[$i]]; 
           $row[] = $aRow[$aColumns[$i]];    
          }elseif($aColumns[$i] == "soldprice"){
               $amounttotal=$amounttotal+$aRow[$aColumns[$i]]; 
           $row[] = $aRow[$aColumns[$i]];    
         }else{
             $row[] = $aRow[$aColumns[$i]];     
         }
       
    }
    $output['aaData'][] = $row;

 
}

  $row = array();
 $row[] = "<b> Total </b>";
 $row[] = " ";
      
         $row[] ="<b> ". $transactiontnumber." </b>";
  $row[] ="<b> ". $amounttotal." </b>";

   
  
 $output['aaData'][] = $row; 
echo json_encode($output);
?>