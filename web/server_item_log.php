<?php

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
$aColumns = array('items_log.id','items_log.image_name','items_log.description1', 'items_log.description2', 'items_log.description3', 'items_log.supplier_number', 'items_log.supplier_item_number', 'items_log.group', 'items_log.color', 'items_log.size', 'items_log.buying_price', 'items_log.selling_price','items_log.taxation_code','items_log.item_status_id','items_log.updated_at','user.name');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "items_log.id";

/* DB table to use */
$sTable = "items_log";
$sJoin  = ' LEFT JOIN user   ON user.id = items_log.updated_by ';
/* Database connection information */
 

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

if($sOrder==""){
    $sOrder = "ORDER BY items_log.id DESC";
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

//if ($sWhere == "") {
//        $sWhere = "WHERE status_id != 5";
//    } else {
//        $sWhere .= " AND status_id != 5";
//    }
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


 if ($sWhere == "") {
            $sWhere = "WHERE  items_log.item_id='".$_GET['item_id']."'" ;
        } else {
            $sWhere .= " AND  items_log.item_id='".$_GET['item_id']."'" ;
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

// echo $sQuery;
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
                    $sWhere
        ";
$rResultTotal = mysql_query($sQuery, $gaSql['link']) or die(mysql_error());
$aResultTotal = mysql_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];


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
//          if ($aColumns[$i] == "items_log.image_name") {
//            $aRow[$col[1]];
//               
//              $row[] ="<img src='".$siteUrl."uploads/images/thumbs/".$aRow[$col[1]]."_50.jpg'  >";
////              $row[]=$status_id;
//          } else {
//            /* General output */
//            $row[] = $aRow[$col[1]];
//          }
              $row[] = $aRow[$col[1]];
          
    }
    $output['aaData'][] = $row;

//    var_dump($row);
}

echo json_encode($output);
?>