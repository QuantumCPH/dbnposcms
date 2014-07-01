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
$aColumns = array('id','name', 'branch_number', 'company_number', 'tel','updated_by','status_id');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "shops";

 
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
                 if (ctype_alpha($_GET['sSearch'])) {
                 }else{
                $numpricve = itemsLib::currencyVersionConvertor($_GET['sSearch']);
                 
                    $sWhere .= $aColumns[$i] . " LIKE '" . mysql_real_escape_string($numpricve) . "%' OR ";
                 }
            } else {
              //    $sWhere .= $aColumns[$i]." LIKE '".mysql_real_escape_string($_GET['sSearch'] )."%' OR ";   
            }
        } else {
             
               $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
           
        }
    }
      $sWhere .= " tel  LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
        $sWhere .= "  country  LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
          $sWhere .= "  place LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
            $sWhere .= " zip LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
              $sWhere .="  address LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
                $sWhere .="  fax LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}
   //  echo $sWhere;

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

//if ($sWhere == "") {
//            $sWhere = "WHERE status_id != 5 OR (status_id IS NULL OR status_id=3)";
//        } else {
//            $sWhere .= " AND status_id != 5 OR (status_id IS NULL OR status_id=3)";
//        }
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
      if ($aColumns[$i] == "branch_number") {
            $vartal = $aRow['id'];
            $row[] = "<a href=".$siteUrl."backend.php/shops/view/id/" . $vartal. ">" . $aRow[$aColumns[$i]] . " </a>";
        }elseif ($aColumns[$i] == "`group`") {
            
            $row[] = $aRow["group"];
                    
                    
        }elseif ($aColumns[$i] == "status_id") {
            $noconfigre = false;$notrans = false;
            $action = "<a href=".$siteUrl."backend.php/shops/edit/id/" . $aRow["id"] . " class='shop-edit' title='Edit'><img src='".$siteUrl."sf/sf_admin/images/edit_icon.png' /></a>";
            if($aRow['status_id']!=5){
                $action .= "&nbsp;<a href=".$siteUrl."backend.php/shops/delete/id/" . $aRow["id"]. " class='shop-delete' onclick=\"return confirm('Are you sure to inactivate branch?');\" title='Inactive'><img src='".$siteUrl."sf/sf_admin/images/inactive.png' /></a>";
            }else{
                $action .= "&nbsp;<a href=".$siteUrl."backend.php/shops/delete/id/" . $aRow["id"]. " class='shop-delete' onclick=\"return confirm('Are you sure to activate branch?');\" title='Active'><img src='".$siteUrl."sf/sf_admin/images/active.png' /></a>";
            }
            $qry = "SELECT is_configured FROM shops WHERE id=".$aRow['id'];
            $rRs = mysql_query($qry, $gaSql['link']) or $a= "";
            $ash = mysql_fetch_array($rRs);
            if($ash["is_configured"]==0){
                $noconfigre = true;
            } 
            $qry = "SELECT count(id) as tranCont FROM transactions WHERE shop_id=".$aRow['id'];
            $rRs = mysql_query($qry, $gaSql['link']) or $a= "";
            $ash = mysql_fetch_array($rRs);
            if($ash["tranCont"]==0){
                $notrans = true;
            }
            if($noconfigre && $notrans) $action .= "&nbsp;<a href=".$siteUrl."backend.php/shops/deleteShop/id/" . $aRow["id"]. " class='shop-delete' onclick=\"return confirm('Are you sure to delete branch?');\" title='Delete'><img src='".$siteUrl."sf/sf_admin/images/delete.png' /></a>";
            $row[] = $action;
            
            $row["DT_RowClass"] = "";
            if($aRow['status_id']==5)$row["DT_RowClass"] = "clsRed";
        } elseif ($aColumns[$i] == "updated_by") {  
            $name = "";
            if($aRow['updated_by']!=""){
              $sq = "Select * from user where id=".$aRow['updated_by'];
              $rRs = mysql_query($sq, $gaSql['link']) or $name= "";
              $aUser = mysql_fetch_array($rRs);
              $name =$aUser['name'];  
            }else{
              $name = "";
            }
            $row[] = $name;
        } else if ($aColumns[$i] != ' ') {
            /* General output */
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;

 //var_dump($row);
    
}

echo json_encode($output);
?>