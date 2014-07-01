<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');

//$siteUrl   = "http://localhost:4430/poscms/web/";
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
$aColumns = array('name','pin', 'password', 'pin_status' , 'role_id','is_super_user','updated_by','id');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "user";

 

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

        if ($aColumns[$i] == "role_id") {

              $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
        }elseif($aColumns[$i] == "pos_user_role_id"){ 
            
            
        }else {
             
               $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";  
           
        }
    }
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

//  echo $sWhere;
/*
 * SQL queries
 * Get data to display
 */

 if ($sWhere == "") {
            $sWhere = "WHERE status_id != 5";
        } else {
            $sWhere .= " AND status_id != 5";
        }
 

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
	 Where status_id != 5 ";
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
      if ($aColumns[$i] == "id") {
            $vartal = $aRow['id'];
           $actions =  "<a href=".$siteUrl."backend.php/user/edit/id/" . $vartal. "><img src='".$siteUrl."sf/sf_admin/images/edit_icon.png' /></a>";
           $actions .=   "&nbsp;&nbsp;<a href=".$siteUrl."backend.php/user/delete/id/" . $vartal. "  onclick=\"return confirm('Are you sure to delete user?');\"><img src='".$siteUrl."sf/sf_admin/images/delete.png' /></a>";
        
            $row[] = $actions;
        }elseif ($aColumns[$i] == "role_id") {
             $rollId =$aRow[$aColumns[$i]];
            if($rollId>0){
             $queryroll="select name from role where id=".$rollId;
             $rRs = mysql_query($queryroll, $gaSql['link']) or die(mysql_error());
             $aRole = mysql_fetch_array($rRs);
 
             $row[] =$aRole['name'];
            }else{
                $row[] ="";  
            }
             
        }elseif ($aColumns[$i] == "is_super_user") {            
            if($aRow[$aColumns[$i]]){
              $row[] = "<img src='".$siteUrl."sf/sf_admin/images/toggle_check.png' />";
            }else{
                $row[] = "";
            }
        }elseif ($aColumns[$i] == "pin_status") {            
            if($aRow[$aColumns[$i]]==3){
              $row[] = "<img src='".$siteUrl."sf/sf_admin/images/toggle_check.png' />";
            }else{
                 $row[] = "<img src='".$siteUrl."sf/sf_admin/images/cancel.png' />";
                 $row["DT_RowClass"] = "clsRed";
            }
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
        }else if ($aColumns[$i] == 'name') {
            $row[] = "<a href=".$siteUrl."backend.php/user/view/id/" . $aRow["id"]. ">".$aRow[$aColumns[$i]]."</a>";
        }else if ($aColumns[$i] != ' ') {
           
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    $output['aaData'][] = $row;

 //var_dump($row);
    
}

echo json_encode($output);
?>