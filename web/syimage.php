<?php

set_time_limit(1000000000000);

$shopId = $_REQUEST['shop_id'];
$sync_all = $_REQUEST['sync_all'];

if (isset($shopId) && $shopId != "") {
     unlink('/var/www/dbnposcms/web/uploads/tmp/'.$shopId.'.zip');
} else {
    return false;
    die;
}
$urlval = "SyncImages- http://dbnposcms.zap-itsolutions.com/syimage.php?shop_id=" . $shopId."&sync_all".$sync_all;
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
$username = "root";
$password = "U&4fewnT7&R7";
$hostname = "184.107.168.18";

//connection to the database
$dbhandle = mysql_connect($hostname, $username, $password) or die("Unable to connect to MySQL");
$selected = mysql_select_db("dbnposcms", $dbhandle) or die("Could not select items");


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */
$shopvar = "shop_id=" . $shopId."&sync_all".$sync_all;
$InsertQuery = "insert into dibs_call set callurl='" . $urlval . "' , decrypted_data= '" . $shopvar . "'";
mysql_query($InsertQuery) or die('could not insertQuery  connect to database');
 
  $updateQuery = "update shops set pic_sync_requested_at='" . date("Y-m-d H:i:s") . "' where id=" . $shopId;

mysql_query($updateQuery) or die('could not connect to database');


  $querySelect = "select * from shops where id=".$shopId;
$rsults = mysql_query($querySelect) or die('could not connect to database');
$rowshop = mysql_fetch_array($rsults);

 

$shop_id = $shopId;


Zip('/var/www/poscms/web/uploads/images/thumbs/', '/var/www/poscms/web/uploads/tmp/'.$shop_id.'.zip', $shop_id,$rowshop);

if (file_exists('/var/www/poscms/web/uploads/tmp/'.$shop_id.'.zip')) {
    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=test.zip");
    header('Content-Length: ' . filesize('/var/www/poscms/web/uploads/tmp/'.$shop_id.'.zip'));
    header("Pragma: no-cache");
    header("Expires: 0");
    header('Connection: close');
    readfile('/var/www/poscms/web/uploads/tmp/'.$shop_id.'.zip');
}

if (file_exists('/var/www/poscms/web/uploads/tmp/'.$shop_id.'.zip')) {
    unlink('/var/www/poscms/web/uploads/tmp/'.$shop_id.'.zip');
}

function Zip($source, $destination, $shopid,$rowshop,$sync_all) {
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true) { 
        //  $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        $query = " SELECT * FROM items WHERE image_status=1 ";
        if(!$sync_all){ 
            if ($rowshop["pic_sync_synced_at"] != "") {
                $query .="and image_update_date>='". $rowshop["pic_sync_synced_at"]."'";
            }
        }
       
        $rResult = mysql_query($query) or die('could not connect to database');


        while ($row = mysql_fetch_array($rResult)) {
            //  foreach ($files as $file)

//            $file = "/var/www/poscms/web/uploads/images/thumbs/" . $row['small_pic'];
//
//            //   echo "filename=".  $file = str_replace('\\', '/', realpath($file));
//
//            if (is_dir($file) === true) {
//                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
//            } else if (is_file($file) === true) {
//                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
//            }
            //////////////////////////////////////////////////////////////////////////////////////////////
            $filea = "/var/www/poscms/web/uploads/images/thumbs/" . $row['large_pic'];

            //   echo "filename=".  $file = str_replace('\\', '/', realpath($file));

            if (is_dir($filea) === true) {
                $zip->addEmptyDir(str_replace($source . '/', '', $filea . '/'));
            } else if (is_file($filea) === true) {
                $zip->addFromString(str_replace($source . '/', '', $filea), file_get_contents($filea));
            }

            //////////////////////////////////////////////////////////////////////////////////////////////     
        }
    } else if (is_file($source) === true) {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

?>
