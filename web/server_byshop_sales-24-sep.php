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
$aColumns = array("transactions.shop_receipt_number_id","shops.branch_number",    "user.name", "transactions.order_id",   "transactions.created_at");

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "transactions.id";

/* DB table to use */
$sTable = "transactions";



$sJoin = 'LEFT JOIN shops   ON shops.id = transactions.shop_id ';
$sJoin .= ' LEFT JOIN user   ON user.id = transactions.user_id ';
//$sJoin .= ' LEFT JOIN statuses   ON statuses.id = transactions.status_id ';
$sJoin .= ' LEFT JOIN transaction_types   ON transaction_types.id = transactions.transaction_type_id ';
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

        if ($aColumns[$i] == "transactions.created_at") {

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
        } elseif ($aColumns[$i] == "transactions.item_id") {
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
        } elseif ($aColumns[$i] == "transactions.shop_receipt_number_id") {
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
        }elseif ($aColumns[$i] == "transactions.description1") {
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
        } elseif ($aColumns[$i] == "statuses.title as status") {
            $sWhere .= "statuses.title" . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
        } elseif ($aColumns[$i] == "transaction_types.title") {
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
if ($sWhere == "") {
    $sWhere = "WHERE  transactions.transaction_type_id<>1 AND transactions.transaction_type_id<>4  AND transactions.transaction_type_id<6   AND transactions.status_id=3   AND transactions.shop_receipt_number_id<>0 ";
} else {
    $sWhere .= " AND transactions.transaction_type_id<>1  AND transactions.transaction_type_id<>4  AND transactions.transaction_type_id<6    AND transactions.status_id=3    AND transactions.shop_receipt_number_id<>0 ";
}


if(isset($_GET["item_id"]) && $_GET["item_id"]!=""){
   $sWhere .= " AND transactions.item_id=".$_GET["item_id"]; 
}elseif (isset($_GET["shop_id"]) && $_GET["shop_id"]!=""){
   $sWhere .= " AND transactions.shop_id=".$_GET["shop_id"];  
}

//AND transactions.status_id=3 
$beforeWhereGroupBy=$sWhere;

$sWhere .=" Group by transactions.shop_receipt_number_id";

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
		SELECT sum(transactions.sold_price) as totalPrice  
		FROM   $sTable
                     $sJoin
                    $beforeWhereGroupBy
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
            $row[] = "<a href=".$siteUrl."backend.php/transactions/saleDetailView?id=" . $aRow['order_id'] . "&branch_number=".$aRow['branch_number'].">" . $vartal . " </a>";
        
        }elseif($col[1] == "order_id") {
            
            $queryrolin = "select sum(amount) as totalInvoicePrice  from  order_payments   where  order_id=" . $aRow['order_id'];
                $rRsin = mysql_query($queryrolin, $gaSql['link']) or die("3nd query" . mysql_error()); 
            $rowTotalinvoice = mysql_fetch_array($rRsin);
             $row[] =  number_format($rowTotalinvoice['totalInvoicePrice'], 2);
//        }elseif($col[1] == "payment") {
////            var_dump($aRow);
////            echo "<hr/>";
////            var_dump($col);
//            $abc = "";
//            if ($aRow[$col[1]] != "") {
//                $queryroll = "select payment_types.title from  order_payments left join payment_types on  payment_types.id=order_payments.payment_type_id where order_payments.order_id=" . $aRow[$col[1]];
//                $rRs = mysql_query($queryroll, $gaSql['link']) or die("2nd query" . mysql_error());
//                while ($aRowP = mysql_fetch_array($rRs)) {
//
//
//                    if ($abc == "") {
//                        $abc = $aRowP['title'];
//                    } else {
//                        $abc = $abc . " , " . $aRowP['title'];
//                    }
//                }
//            }
//            $row[] = $abc;
//            
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