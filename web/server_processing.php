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
$aColumns = array('small_pic', 'item_id', 'description1', 'description2', 'description3', '`group`', 'color', 'supplier_number', 'selling_price', 'item_updated_at','status_id');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "items";

 
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


/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $aColumns)) . "
		FROM   $sTable
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
        if ($aColumns[$i] == "small_pic") {
            /* Special output formatting for 'version' column */
            //$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
            $vartal = $aRow[$aColumns[$i]];
            $row[] = "<img src=" . gf::checkImage1($vartal) . " />";
        } elseif ($aColumns[$i] == "selling_price") {
            $vartal = $aRow[$aColumns[$i]];
            $row[] = number_format($vartal, 2, ',', ',');
        } elseif ($aColumns[$i] == "item_id") {
            $vartal = $aRow[$aColumns[$i]];
            $row[] = "<a href=".$siteUrl."backend.php/items/view/id/" . $aRow['item_id'] . ">" . $vartal . " </a>";
        } elseif ($aColumns[$i] == "status_id") {
            $actions = "";$nosync=FALSE;$nodn = false;$notrans=false;
            $actions .=  "<a href=".$siteUrl."backend.php/items/edit/id/" . $aRow['item_id']. " title='Edit'><img src='".$siteUrl."/sf/sf_admin/images/edit_icon.png' /></a>";
            if($aRow['status_id']!=5){
                $actions .=  "&nbsp;<a href=".$siteUrl."backend.php/items/delItem/id/" . $aRow['item_id']. " onclick=\"return confirm('Are you sure to inactivate item?');\" title='Inactive'><img src='".$siteUrl."/sf/sf_admin/images/inactive.png' /></a>";
            }else{
                $actions .=  "&nbsp;<a href=".$siteUrl."backend.php/items/delItem/id/" . $aRow['item_id']. " onclick=\"return confirm('Are you sure to activate item?');\" title='Activate'><img src='".$siteUrl."/sf/sf_admin/images/active.png' /></a>";
            }
            $qry = "SELECT count(it.created_at) as it_count FROM shops sh , items it WHERE it.updated_at <= sh.item_sync_synced_at and it.item_id=".$aRow['item_id'];
            $rRs = mysql_query($qry, $gaSql['link']) or $a= "";
            $aIt = mysql_fetch_array($rRs);
            if($aIt["it_count"]==0){
                $nosync = true;
            }
            $qry = "SELECT count(id) as dn_count FROM delivery_notes WHERE item_id=".$aRow['item_id'];
            $rRs = mysql_query($qry, $gaSql['link']) or $a= "";
            $aIt = mysql_fetch_array($rRs);
            if($aIt["dn_count"]==0){
                $nodn = true;
            } 
            $qry = "SELECT count(id) as tr_count FROM transactions WHERE item_id=".$aRow['item_id'];
            $rRs = mysql_query($qry, $gaSql['link']) or $a= "";
            $aIt = mysql_fetch_array($rRs);
            if($aIt["tr_count"]==0){
                $notrans = true;
            }
            if($nosync && $nodn && $notrans) $actions .=  "&nbsp;<a href=".$siteUrl."backend.php/items/deleteItem/id/" . $aRow['item_id']. " onclick=\"return confirm('Are you sure to delete item?');\" title='Delete'><img src='".$siteUrl."/sf/sf_admin/images/delete.png' /></a>";
            $row["DT_RowClass"] = "";
            if($aRow['status_id']==5)$row["DT_RowClass"] = "clsRed";
            $row[] = $actions;
        } elseif ($aColumns[$i] == "`group`") {

            $row[] = $aRow["group"];
        } else if ($aColumns[$i] != ' ') {
            /* General output */
        
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = array_map('utf8_encode',$row);

//    var_dump($output);
}
 
//  $encoded_rows = array_map('utf8_encode', $rows);
//    echo json_encode($encoded_rows);
    
 echo json_encode($output);
 
?>