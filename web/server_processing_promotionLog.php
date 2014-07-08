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
$aColumns = array('promotion_log.promotion_title', 'promotion_log.start_date', 'promotion_log.end_date', 'promotion_type.promotion_type_title','promotion_log.promotion_value','promotion_statuses.promotion_status_title','promotion_log.updated_at','promotion_log.updated_by','promotion_log.id');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "promotion_log.id";

/* DB table to use */
$sTable = "promotion_log";


 $sJoin = ' LEFT JOIN promotion_type   ON promotion_type.id = promotion_log.promotion_type ';
 $sJoin .= ' LEFT JOIN promotion_statuses   ON promotion_statuses.id = promotion_log.promotion_status ';
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
   
        $sWhere .=  "  promotion_log.description1 LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
         $sWhere .=  "  promotion_log.description2 LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
          $sWhere .=  "  promotion_log.description3 LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
          
             $sWhere .=  "  promotion_log.color LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
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


 if ($sWhere == "") {
            $sWhere = "WHERE  promotion_log.promotion_id='".$_GET['promotion_id']."'" ;
        } else {
            $sWhere .= " AND   promotion_log.promotion_id='".$_GET['promotion_id']."'" ;
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
          $col = explode('.',$aColumns[$i]);
    if ($col[1] == 'id') {
//            $row[] = "here";
            
            
                                
                    $actions = "<a href=".$siteUrl."backend.php/promotion/viewLog?id=" . $aRow['id'] . " ><img src='".$siteUrl."sf/sf_admin/images/view_icon.png' /></a>";
                   
                
                    $row[] = $actions;
                
            
        }else if ($col[1] == 'created_at') {
            $row[] = date("Y-m-d",strtotime($aRow['created_at']));
            
        }    elseif ($col[1] == "updated_by") {  
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