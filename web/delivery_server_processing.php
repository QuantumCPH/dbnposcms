<?php

require_once('../lib/customFunction.class.php');
require_once('../lib/itemsLib.class.php');
require_once('connection.php');

//$siteUrl   = "http://localhost:4430/poscms/web/";
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
$aColumns = array('delivery_notes.note_id', 'delivery_notes.delivery_date', 'delivery_notes.branch_number','delivery_notes.status_id', 'delivery_notes.created_at','delivery_notes.synced_at','delivery_notes.shop_responded_at','delivery_notes.updated_by', 'delivery_notes.id');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "delivery_notes.id";

/* DB table to use */
$sTable = "delivery_notes";


$sJoin = ' LEFT JOIN items   ON items.item_id = delivery_notes.item_id ';
/* Database connection information */

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
$gaSql['link'] = mysql_pconnect($gaSql['server'], $gaSql['user'], $gaSql['password']) or
        die('Could not open connection to server');

mysql_select_db($gaSql['db'], $gaSql['link']) or
        die('Could not select database ' . $gaSql['db']);


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


        $numpricve = itemsLib::currencyVersionConvertor($_GET['sSearch']);
        if ($aColumns[$i] == "selling_price") {
            $sWhere .= $aColumns[$i] . " LIKE '" . mysql_real_escape_string($numpricve) . "%' OR ";
        } else {
            $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
        }
    }
     $sWhere .=  "  delivery_notes.item_id LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
        $sWhere .=  "  items.description1 LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
         $sWhere .=  "  items.description2 LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
          $sWhere .=  "  items.description3 LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
           $sWhere .=  "  items.group LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
             $sWhere .=  "  items.color LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
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
        $sWhere .= $aColumns[$i] . " LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
    }
}


$sWhere .=" Group by delivery_notes.note_id";
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
                    
	  Group by delivery_notes.note_id";
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
          $col = explode('.',$aColumns[$i]);
        if ($col[1] == "note_id") {
            /* Special output formatting for 'version' column */
            //$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];         
            $vartal = $aRow[$col[1]];
            $row[] = "<a href=".$siteUrl."backend.php/delivery_notes/view?id=" . $aRow['note_id'] . ">" . $vartal . " </a>";
        } else if ($col[1] == 'id') {
//            $row[] = "here";
            $sq = "Select sum(is_synced) from " . $sTable . " where note_id = '" . $aRow['note_id'] . "'";
            $rs = mysql_query($sq, $gaSql['link']);
            $actions ="";
            while ($arr = mysql_fetch_array($rs)) {
                if ($arr[0] == 0) {                    
                    $actions .= "<a href=".$siteUrl."backend.php/delivery_notes/edit?id=" . $aRow['note_id'] . " ><img src='".$siteUrl."sf/sf_admin/images/edit_icon.png' /></a>";
                    $actions .= "<a href=".$siteUrl."backend.php/delivery_notes/deleteNotes?id=" . $aRow['note_id'] . "  onclick=\"return confirm('Are you sure to delete delivery notes?');\"> <img src='".$siteUrl."sf/sf_admin/images/delete.png' /> </a>";
                
                    $row[] = $actions;
                } else {
                    $row[] = "";
                }
            }
        } else if ($col[1] == 'status_id') {
            $img = "";  
            $row["DT_RowClass"] = "clsWhite";
            
            $sq = "Select count(*) from " . $sTable . " where note_id = '" . $aRow['note_id'] . "' and is_synced = 0";
            $rs = mysql_query($sq, $gaSql['link']);
            $arr = mysql_fetch_array($rs);
            if ($arr && $arr[0] > 0) {
                    $row["DT_RowClass"] = "clsRed";
                    $img = "<img src='".$siteUrl."images/red-cauation.png' />";
            }
            
            $sq = "Select count(*) from " . $sTable . " where note_id = '" . $aRow['note_id'] . "' and is_synced = 1 and is_received = 0";
            $rs = mysql_query($sq, $gaSql['link']);
            $arr = mysql_fetch_array($rs);
            if ($arr && $arr[0] > 0) {
                    $row["DT_RowClass"] = "clsRed";
                    $img = "<img src='".$siteUrl."images/yellow-cauation.png' />";
            }
            
            $sq = "Select sum(received_quantity) , sum(quantity) from " . $sTable . " where note_id = '" . $aRow['note_id'] . "'";
            $rs = mysql_query($sq, $gaSql['link']);
            $arr = mysql_fetch_array($rs);
            if ($arr && $arr[0] > 0) {                
                if ($arr[0] == $arr[1]) {
                    $row["DT_RowClass"] = "clsGreen";
                    $img = "<img src='".$siteUrl."images/green.png' />";
                } else {
                    $row["DT_RowClass"] = "clsYellow";
                    $img = "<img src='".$siteUrl."images/yellow.png' />";
                }
            }
           
           $row[] = $img;
           
        }else if ($col[1] == 'created_at') {
            $row[] = date("Y-m-d",strtotime($aRow['created_at']));
            
        }else if ($col[1] == 'synced_at') {
            $sdate = $aRow['synced_at'];
            if($sdate !="")date("Y-m-d",strtotime($aRow['synced_at']));
            $row[] = $sdate;
            
        }else if ($col[1] == 'shop_responded_at') {
            $recdate = $aRow['shop_responded_at'];
            if($recdate !="")date("Y-m-d",strtotime($aRow['shop_responded_at']));
            $row[] = $recdate;            
            
        }else if ($col[1] == 'delivery_date') {
            $row[] = date("Y-m-d",strtotime($aRow['delivery_date']));
            
        } elseif ($col[1] == "updated_by") {  
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
        } else if ($col[1] != ' ') {
            /* General output */
            $row[] = $aRow[$col[1]];
        }
    }
    $output['aaData'][] = $row;

//     var_dump($output);
}

echo json_encode($output);
?>