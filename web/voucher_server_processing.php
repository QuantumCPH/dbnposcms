<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');
/*
 * Script:    DataTables server-side script for PHP and MySQL
 * Copyright: 2010 - Allan Jardine
 * License:   GPL v2 or BSD (3-point)
 */
//$siteUrl   = "http://poscms.zap-itsolutions.com/";
//$siteUrl   = "http://localhost:4430/poscms/web/";
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('voucher.id', 's1.branch_number','u1.name', 'voucher.amount', 'voucher.shop_created_at', 'voucher.is_used', 's2.branch_number as used_branch', 'u2.name as consumer_user' ,'voucher.used_amount','voucher.shop_used_at','voucher.created_at');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "voucher.id";

/* DB table to use */
$sTable = "voucher";


$sJoin = ' LEFT JOIN shops s1  ON voucher.created_shop_id = s1.id ';
$sJoin .= ' LEFT JOIN shops s2  ON voucher.used_shop_id = s2.id ';
$sJoin .= ' LEFT JOIN user u1  ON voucher.created_user_id = u1.id ';
$sJoin .= ' LEFT JOIN user u2  ON voucher.used_user_id = u2.id ';

/* Database connection information */
//$gaSql['user'] = "root";
//$gaSql['password'] = "@wsxzaQ1";
//$gaSql['db'] = "poscms";
//$gaSql['server'] = "184.107.218.122";
//$gaSql['user'] = "root";
//$gaSql['password'] = "";
//$gaSql['db'] = "poscms";
//$gaSql['server'] = "";
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */

/*
 * MySQL connection
 */
//$gaSql['link'] = mysql_pconnect($gaSql['server'], $gaSql['user'], $gaSql['password']) or
//        die('Could not open connection to server');
//
//mysql_select_db($gaSql['db'], $gaSql['link']) or
//        die('Could not select database ' . $gaSql['db']);


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
        
       $colaa = explode(" as ",$aColumns[$i]);
        $sWhere .= $colaa[0] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
    }

    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}
// echo $sWhere;
/* Individual column filtering */
for ($i = 0; $i < count($aColumns); $i++) {
    if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        $colaa = explode(" as ",$aColumns[$i]);
        $sWhere .= $colaa[0] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
    }
}


//$sWhere .=" Group by delivery_notes.note_id";
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

/* Data set length after filtering */
$sQuery = "
		SELECT FOUND_ROWS()
	";
$rResultFilterTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery1 = "
		SELECT COUNT(" . $sIndexColumn . ")
		FROM   $sTable 
                     $sJoin 
                    
	  ";
$rResultTotal = mysql_query($sQuery1, $gaSql['link']) or die(mysql_error());
$iTotal = mysql_num_rows($rResultTotal);



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
        $col = explode('.', $aColumns[$i]);
        if ($col[1] == "is_used") {
            $row[] = ($aRow[$col[1]]) ? "Yes" : "No";
        }else if ($col[1] == 'id') {
            $row[] = "<a href=".$siteUrl."backend.php/voucher/view?id=" . $aRow["id"]. " class='inevtory-link'>".$aRow["id"]."</a>";
        }
        else if ($col[1] == 'created_at') {
            $row[] = $aRow["amount"]-$aRow["used_amount"];
        }  else if ($aColumns[$i] == 's2.branch_number as used_branch') {
            $row[] = $aRow["used_branch"];
        } else if ($aColumns[$i] == 'u2.name as consumer_user') {
            $row[] = $aRow["consumer_user"];
        } 
        else if ($col[1] != ' ') {
            /* General output */
            $row[] = $aRow[$col[1]];
        }else {
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;

//     var_dump($output);
}

echo json_encode($output);
?>