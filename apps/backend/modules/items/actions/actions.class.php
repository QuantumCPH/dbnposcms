<?php

set_time_limit(10000000000000000);
 
require_once(sfConfig::get('sf_lib_dir') . '/uploadfile.php');
/**
 * items actions.
 *
 * @package    zapnacrm
 * @subpackage items
 * @author     Your name here
 */
class itemsActions extends sfActions {

    public function executeItemExport(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Item module id is 1.
        if (Access::checkPermissions(11, "itemExport",$user_id,$session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
//           $this->redirect($this->targetUrl().'user/dashboard');
            $this->redirect($request->getReferer());
        }
    }

    public function executeItemExportSubmit(sfWebRequest $request) {
        if ($request->isMethod('post')) {

            ///////////////////defination area/////////////////////
            $fileTmpNamef = $_FILES['defile']['tmp_name'];
            $fileNamef = $_FILES['defile']['name'];
            $path_infof = pathinfo($fileNamef);
            $extensionf = $path_infof['extension'];
            $fileSizef = $_FILES['defile']['size'];

            $filename = date('dmY-His') . ".csv";
            if (!$fileSizef) {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Defination s is Empty.'));
                $this->redirect('items/itemExport');
                exit;
            } elseif ($extensionf != 'xml') {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Defination file must be in xml format.'));
                $this->redirect('items/itemExport');
                exit;
            }

            $filef = fopen($fileTmpNamef, "r");
            $xmlfile = fread($filef, $fileSizef);
            //  $xml_obj = new SimpleXMLElement($xmlfile);

            try {
                $xml_obj = new SimpleXMLElement($xmlfile);
                //var_dump($xml_obj);
            } catch (Exception $e) {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Defination file format is wrong.'));
                $this->redirect('items/itemExport');
                exit;
            }


            //->attributes()->column
            //   $data = $xml_obj->articles[0]->value[0];
            //   echo $xml_obj->header;
            //   
            //   
///////////////////////////////////////////////////////
            if ($xml_obj->separator == "") {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('separator is not define in defination file'));
                $this->redirect('items/itemExport');
                exit;
            }
            /////////////////////////////////////////////////////////////////////////  
            $csv_terminated = "\n";

            //   
            $csv_separator = $xml_obj->separator;

            //  $csv_enclosed = '"';
            $csv_enclosed = $separator = $xml_obj->charactersQuotation;

            $csv_escaped = "\\";
            ///////////////////////////////////////////////////////
            $separater = $xml_obj->separator;

            if ($separater == "," || $separater == ";" || $separater == '\t') {
                if ($separater == '\t') {
                    $csv_separator = "\t";
                }
            } else {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Separator is not in define list.'));
                $this->redirect('items/itemExport');
                exit;
            }


//////////////////////////////////////////////////////
            if ($xml_obj->tableName == "") {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('table name is not set in defination file'));
                $this->redirect('items/itemExport');
                exit;
            } else {

                $tableName = $xml_obj->tableName;
                $os = array("items", "t5", "m6", "m16");
                if (in_array($tableName, $os)) {



                    $tableName = $tableName;
                } else {
                    $tableName = FALSE;
                    $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('table name not exist in database'));
                    $this->redirect('items/itemExport');
                    exit;
                }
            }



            //////////////////////////////////////////////////////////// 

            $startrow = $xml_obj->startRow;
            $startrow = (int) $startrow;
            $m = 0;
            $start = 0;
            if ($xml_obj->header == "true") {
                $start = 1;
            } else {
                $start = 0;
            }

            //  echo $start;
            //   die;
            foreach ($xml_obj->articles[0]->value as $articlevlaue) {


                $fieldsArray["" . $articlevlaue->key] = $articlevlaue->columnName;
                $fieldsArrayKey[$m] = $articlevlaue->key;
                $fieldsArrayName[$m] = $articlevlaue->columnName;
                $m++;
            }




            if ($tableName) {

                $columnAray = array("id", "description1", "description2", "description3", "supplier_number", "supplier_item_number", "ean", "color", "size", "group", "buying_price", "selling_price", "taxation_code");

                foreach ($fieldsArrayName as $key) {
                    if (in_array($key, $columnAray)) {
                        
                    } else {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('The following column is not exist in our system ' . $key));
                        $this->redirect('items/itemExport');
                        exit;
                    }
                }
            }
            //  var_dump($fieldsArrayKey);
            // die;
            $out = "";
            $columns_total =
                    $countItems = ItemsPeer::doCount(new Criteria());
// Get The Field Name
            $schema_insert = '';



            if ($start) {
                for ($i = 0; $i < $m; $i++) {
                    $heading = $fieldsArrayKey[$i];



                    $k = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, stripslashes($heading)) . $csv_enclosed;
                    $schema_insert .= $k;

                    if ($i < $m - 1) {
                        $schema_insert .= $csv_separator;
                    }
                }
                $out .=$schema_insert;
                $out .= $csv_terminated;
            }
            ///////////////////////////////////////////

            $finalPricetotal = 0;
            $conn = Propel::getConnection();
            $query = 'SELECT  * from items';

            $statement = $conn->prepare($query);
            $statement->execute();

            /////////////////////////////////////////
// Get Records from the table
            $items_lists = ItemsPeer::doSelect(new Criteria());
            $rtloop = (int) $m - 1;
            $zloop = 1;
            while ($rowObj = $statement->fetch(PDO::FETCH_OBJ)) {

                $schema_insert = '';
                if ($zloop < $startrow) {
                    $zloop++;
                    continue;
                }


                for ($i = 0; $i < $m; $i++) {
                    $objectsert = $fieldsArrayName[$i];

                    $schema_insert .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $rowObj->$objectsert) . $csv_enclosed;
                    ;

                    if ($i < $rtloop) {

                        $schema_insert .=$csv_separator;
                    }
                }
                $out .=$schema_insert;
                $out .= $csv_terminated;
            }

// Download the file

            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Length: " . strlen($out));
            // Output to browser with appropriate mime type, you choose ;)
            header("Content-type: text/x-csv");
            //header("Content-type: text/csv");
            //header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=$filename");
            echo $out;
            exit;
            //    $this->redirect('items/itemExport');
            // $this->items_list = ItemsPeer::doSelect(new Criteria());
            return sfView::NONE;
        }
    }

    public function executeIndex(sfWebRequest $request) {
         $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
         $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Item module id is 1.
        if (Access::checkPermissions(1, "index",$user_id,$session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        $searchFld = $request->getParameter('searchFld');

//        $c = new Criteria();
//        
//          if($searchFld){
//              $this->searchFld=$searchFld;
//              $criterion = $c->getNewCriterion(ItemsPeer::ITEM_ID, $searchFld."%", Criteria::LIKE);
//$criterion->addOr($c->getNewCriterion(ItemsPeer::DESCRIPTION1, $searchFld."%", Criteria::LIKE));
//$criterion->addOr($c->getNewCriterion(ItemsPeer::DESCRIPTION2, $searchFld."%", Criteria::LIKE));
//$criterion->addOr($c->getNewCriterion(ItemsPeer::DESCRIPTION3, $searchFld."%", Criteria::LIKE));
//$criterion->addOr($c->getNewCriterion(ItemsPeer::GROUP, $searchFld."%", Criteria::LIKE));
//$criterion->addOr($c->getNewCriterion(ItemsPeer::SIZE, $searchFld."%", Criteria::LIKE));
//$criterion->addOr($c->getNewCriterion(ItemsPeer::COLOR, $searchFld."%", Criteria::LIKE));
//$criterion->addOr($c->getNewCriterion(ItemsPeer::EAN, $searchFld."%", Criteria::LIKE));
//$criterion->addOr($c->getNewCriterion(ItemsPeer::SUPPLIER_NUMBER, $searchFld."%", Criteria::LIKE));
//$criterion->addOr($c->getNewCriterion(ItemsPeer::SUPPLIER_ITEM_NUMBER, $searchFld."%", Criteria::LIKE));
//  $numpricve = itemsLib::currencyVersionConvertor($searchFld);
// 
// $criterion->addOr($c->getNewCriterion(ItemsPeer::BUYING_PRICE, $numpricve."%", Criteria::LIKE));
// $criterion->addOr($c->getNewCriterion(ItemsPeer::SELLING_PRICE, $numpricve."%", Criteria::LIKE));
// $criterion->addOr($c->getNewCriterion(ItemsPeer::CREATED_AT, $searchFld."%", Criteria::LIKE));
// $criterion->addOr($c->getNewCriterion(ItemsPeer::UPDATED_AT, $searchFld."%", Criteria::LIKE));
// 
//$c->add($criterion);
//         
//          
//          }else{
//             $this->searchFld="";  
//          }
//       $c->setLimit(1000);
//   
//      $this->items_list = ItemsPeer::doSelect($c);
//       $this->itemsCount = ItemsPeer::doCount(new Criteria());
    }

    public function executeAddItems($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
//        $this->setLayout(true);
        //// Item module id is 1.
        if (Access::checkPermissions(10, "addItems",$user_id,$session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
    }

    public function executeAddItemsSubmit($request) {

        if ($request->isMethod('post')) {
            ///////////////////defination area/////////////////////
            $fileTmpNamef = $_FILES['defile']['tmp_name'];
            $fileNamef = $_FILES['defile']['name'];
            $path_infof = pathinfo($fileNamef);
            $extensionf = $path_infof['extension'];
            $fileSizef = $_FILES['defile']['size'];

            $fileTmpName = $_FILES['datafile']['tmp_name'];
            $fileName = $_FILES['datafile']['name'];
            $path_info = pathinfo($fileName);
            $extension = $path_info['extension'];
            $fileSize = $_FILES['datafile']['size'];

            if (!$fileSizef) {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Defination s is Empty.'));
                $this->redirect('items/addItems');
                exit;
            } elseif ($extensionf != 'xml') {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Defination file must be in xml format.'));
                $this->redirect('items/addItems');
                exit;
            }

            if (!$fileSize) {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Data file is empty.'));
                $this->redirect('items/addItems');
                exit;
            } elseif ($extension != 'csv') {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Data File must be in CSV format.'));
                $this->redirect('items/addItems');
                exit;
            }

            $filef = fopen($fileTmpNamef, "r");
            $xmlfile = fread($filef, $fileSizef);




            try {
                $xml_obj = new SimpleXMLElement($xmlfile);
                //var_dump($xml_obj);
            } catch (Exception $e) {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('XML is invalid .'));
                $this->redirect('items/addItems');
                exit;
            }


            $separater = $xml_obj->separator;


            if ($xml_obj->charactersQuotation) {
                
            } else {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('characters Quotation is not exist in file'));
                $this->redirect('items/addItems');
                exit;
            }

            $enclosure = $xml_obj->charactersQuotation;





            $startrow = 0;
            if ($xml_obj->startRow == "") {
                $startrow = 0;
            } else {
                $startrow = $xml_obj->startRow;
            }




            if ($xml_obj->header == "true") {
                $start = 1;
            } else {
                $start = 0;
            }

            $countdata = $xml_obj->columns[0]->count();

            $fieldsArray = array();
            for ($i = 0; $i < $countdata; $i++) {

                $datacolumnName = $xml_obj->columns[0]->column[$i]->columnName;
                $datacolumnIndex = $xml_obj->columns[0]->column[$i]->columnIndex;
                $fieldsArray["" . $datacolumnIndex] = $datacolumnName;
                //  echo "<hr>";
            }
            ksort($fieldsArray);

            if ($xml_obj->tableName == "") {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('table name is not set in defination file'));
                $this->redirect('items/addItems');
                exit;
            } else {

                $tableName = $xml_obj->tableName;
                $os = array("items", "delivery_notes", "m6", "m16");
                if (in_array($tableName, $os)) {



                    $tableName = $tableName;
                } else {
                    $tableName = FALSE;
                    $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('table name not exist in database'));
                    $this->redirect('items/addItems');
                    exit;
                }
            }

            $file = fopen($fileTmpName, "r");
            if (!$file) {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Error opening data file.'));
                $this->redirect('items/addItems');
                exit;
            }
            fclose($file);

            //////////////////////////////////////////////////////////////////////////////////


            if ($separater == "," || $separater == ";" || $separater == '\t') {
                if ($separater == '\t') {
                    $separater = "\t";
                }
            } else {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Separator is not in define list.'));
                $this->redirect('items/addItems');
                exit;
            }


            //////////////////////////////////////////////////////////////////


            if ($enclosure == "") {
                $enclosure = '"';
            }


            $delimiter = $separater;


            if ($xml_obj->tableName == "items") {
////////////////////////////////////////////////////////////////////////////////
                $mandatoryItemFields = array("id", "ean", "group", "buying_price", "selling_price", "taxation_code");
                $mandatory_field_error = false;
                $keyPrintMandatory = "";
                foreach ($mandatoryItemFields as $key) {
                    if (!in_array($key, $fieldsArray)) {
                        echo "<hr>";
                        $keyPrintMandatory = $key;
                        echo "<hr>";
                        $mandatory_field_error = true;
                    }
                }

                if ($mandatory_field_error) {
                    $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('mandatory field missing: ' . $keyPrintMandatory));
                    $this->redirect('items/addItems');
                }

//////////////////////////////old dta csv read code ///////////////////////

                /*    $csv = fread($file, $fileSize);
                  $csv = str_replace('"', '', $csv);
                  fclose($file);

                  $data = explode("\n", $csv); */

/////////////////////////////new csv read code ///////////////////////////////////////////////////////////////
                ///    optional parameters
                //      $path, $delimiter = ',', $enclosure = '"'
                $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                $reader->open();

                /////////////////////////////////////////////////////////////////////////

                $startingRow = $startrow + $start - 1;

                if ($startingRow < 0) {
                    $startingRow = 0;
                }

                ///////////////////////////////////////////////
                // }
                /////////////////////////////////////////////////////
                // var_dump($data);die;
                // This loop will check only for the errors in the CSV and the next loop will insert into the database;
                $i = 0;
                while ($data = $reader->read()) {
                    $i++;
                    if ($i < $startingRow) {
                        continue;
                    }


                    $combine = array_combine($fieldsArray, $data);


                    if (!$combine) {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                        $this->redirect('items/addItems');
                    }
                    foreach ($combine as $key => $values) {
                        $combine[trim($key)] = trim($values);
                    }

                    //setlocale(LC_MONETARY,"en_US");
                    $combine['selling_price'] = itemsLib::currencyVersionConvertor($combine['selling_price']);
                    $combine['buying_price'] = itemsLib::currencyVersionConvertor($combine['buying_price']);

                    if (!is_numeric($combine['id']) || !is_numeric($combine['ean']) || !is_numeric($combine['buying_price']) || !is_numeric($combine['selling_price']) || !is_numeric($combine['taxation_code'])) {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                        $this->redirect('items/addItems');
                    }
                    $csc = new Criteria();
                    $csc->add(SystemConfigPeer::KEYS,"Check Barcode/EAN No", Criteria::EQUAL);
                    $csc->addOr(SystemConfigPeer::ID,4);
                    $dnItem = SystemConfigPeer::doSelectOne($csc);
                    if($dnItem->getValues()=="Yes"){
                        $itc = new Criteria();
                        $itc->add(ItemsPeer::EAN, $combine['ean']);
                        if (ItemsPeer::doCount($itc) > 0) {
                            $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Validation failed as previous ean found while parsing CSV at row number: ' . $i));
                            $this->redirect('items/addItems');
                        }   
                    }
                }


                if ($tableName) {

                    $columnAray = array("id", "description1", "description2", "description3", "supplier_number", "supplier_item_number", "ean", "color", "size", "group", "buying_price", "selling_price", "taxation_code");

                    foreach ($fieldsArray as $key) {
                        if (in_array($key, $columnAray)) {
                            
                        } else {
                            $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('The following column does not exist in our system ' . $key));
                            $this->redirect('items/addItems');
                            exit;
                        }
                    }
                }
                $reader->close();
                $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                $reader->open();


                $i = 0;
                $insert_new = false;
                while ($data = $reader->read()) {
                    $i++;
                    if ($i < $startingRow) {
                        continue;
                    }
 
                    $combine = array_combine($fieldsArray, $data);
                    foreach ($combine as $key => $values) {
                        $combine[trim($key)] = trim($values);
                    }
                    $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
                    $combine["updated_by"] = $user_id;
                    // var_dump($combine);
                    //  die;
                    $insert_new = itemsLib::populateItem($combine);
                }

                if ($insert_new) {
                    $this->getUser()->setFlash('file_done', $this->getContext()->getI18N()->__('Items imported successfully'));
                    $this->redirect('items/addItems');
                    exit;
                }
                $reader->close();
            } elseif ($xml_obj->tableName == "delivery_notes") {

                // Mandatory Fields
                $mandatoryItemFields = array("company_number", "branch_number", "delivery_number", "delivery_date", "item_number", "quantity");
                $mandatory_field_error = false;
                $keyPrintMandatory = "";
                foreach ($mandatoryItemFields as $key) {
                    if (!in_array($key, $fieldsArray)) {
                        echo "<hr>";
                        $keyPrintMandatory = $key;
                        echo "<hr>";
                        $mandatory_field_error = true;
                    }
                }

                if ($mandatory_field_error) {
                    $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('mandatory field missing: ' . $keyPrintMandatory));
                    $this->redirect('items/addItems');
                }
                // Mandatory Fields

                $columnAray = $mandatoryItemFields;

                foreach ($fieldsArray as $key) {
                    if (in_array($key, $columnAray)) {
                        
                    } else {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('The following column does not exist in our system: ' . $key));
                        $this->redirect('items/addItems');
                        exit;
                    }
                }

                $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                $reader->open();
                // Validation Loopthorugh of the CSV
                $i = 0;
                while ($data = $reader->read()) {
                    $i++;
                    if ($i < $startingRow) {
                        continue;
                    }

                    $combine = array_combine($fieldsArray, $data);


                    if (!$combine) {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                        $this->redirect('items/addItems');
                    }
                    foreach ($combine as $key => $values) {
                        $combine[trim($key)] = trim($values);
                    }

                    if (!is_numeric($combine['company_number']) || !is_numeric($combine['branch_number']) || !is_numeric($combine['item_number']) || !is_numeric($combine['quantity'])) {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                        $this->redirect('items/addItems');
                    }

                    $sc = new Criteria();
                    $sc->add(ShopsPeer::COMPANY_NUMBER, $combine['company_number']);
                    $sc->add(ShopsPeer::BRANCH_NUMBER, $combine['branch_number']);
                    if (ShopsPeer::doCount($sc) != 1) {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as branch not found while parsing CSV on line number ' . $i));
                        $this->redirect('items/addItems');
                    }
                    $csc = new Criteria();
                    $csc->add(SystemConfigPeer::KEYS,"Check Double Delivery Notes", Criteria::EQUAL);
                    $csc->addOr(SystemConfigPeer::ID,2);
                    $dnItem = SystemConfigPeer::doSelectOne($csc);
                    if($dnItem->getValues()=="Yes"){
                        $dc = new Criteria();
                        $dc->add(DeliveryNotesPeer::NOTE_ID, $combine['delivery_number']);
                        if (DeliveryNotesPeer::doCount($dc) >= 1) {

                            $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Validation failed as previous delivery number found while parsing CSV at row number: ' . $i));
                            $this->redirect('items/addItems');
                        }
                    }
                    $csc = new Criteria();
                    $csc->add(SystemConfigPeer::KEYS,"Check Delivery Items", Criteria::EQUAL);
                    $csc->addOr(SystemConfigPeer::ID,3);
                    $dnItem = SystemConfigPeer::doSelectOne($csc);
                    if($dnItem->getValues()=="Yes"){
                        $ic = new Criteria();
                        $ic->add(ItemsPeer::ITEM_ID, $combine['item_number']);
                        if (ItemsPeer::doCount($ic) != 1) {
                            $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as item not found while parsing CSV on line number ' . $i));
                            $this->redirect('items/addItems');
                        }  
                    }else{
                        $cit = new Criteria();
                        $cit->add(ItemsPeer::ITEM_ID,$combine['item_number'], Criteria::EQUAL);
                        $chkitem = ItemsPeer::doCount($cit);
                        if($chkitem==0){
                            $item = array();
                            $item["id"] = $combine['item_number'];
                            $item["ean"] = $combine['item_number'];
                            $item["updated_by"] = $this->getUser()->getAttribute('user_id', '', 'backendsession');
                            itemsLib::populateWebItem($item);
                        }     
                    }
                }


                // Insertion Loopthrough

                $reader->close();
                $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                $reader->open();

                $i = 0;
                $y = 0;
                $insert_new = false;
                $dnCount = DeliveryNotesPeer::doCount(new Criteria());
                $dnid = "1".$dnCount.time();
                while ($data = $reader->read()) {
                    $i++;
                    if ($i < $startingRow) {
                        continue;
                    }

                    $combine = array_combine($fieldsArray, $data);
                    foreach ($combine as $key => $values) {
                        $combine[trim($key)] = trim($values);
                    }
                    $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
                    $dnid = "1".$dnCount+$y.time();
                    $combine["updated_by"] = $user_id;
                    $combine["id"] = $dnid;
                    // var_dump($combine);
                    //  die;
                    $insert_new = itemsLib::populateDeliveryNotes($combine);
                    $y++;
                }

                if ($insert_new) {
                      $sp = new Criteria();
                            $sp->add(ShopsPeer::BRANCH_NUMBER, $combine['branch_number']);  
                            if(ShopsPeer::doCount($sp)>0){
                            $shops=ShopsPeer::doSelectOne($sp);
                              if ($shops->getGcmKey() != "") {
                                new GcmLib("delivery_note", array($shops->getGcmKey()));
                            }
                            }else{
                             $shops=0;   
                            }
                            
                            
                    $this->getUser()->setFlash('file_done', $this->getContext()->getI18N()->__('Delivery Notes imported successfully'));
                    $this->redirect('items/addItems');
                    exit;
                }
                $reader->close();
            }
        }
        return sfView::NONE;
    }

    public function executeView($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Item module id is 1.
        if (Access::checkPermissions(1, "view",$user_id,$session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $id = $request->getParameter("id");


        $item = new Criteria();
        $item->add(ItemsPeer::ITEM_ID, $id);
        $this->item = ItemsPeer::doSelectOne($item);
        $item_id = $this->item->getItemId();

        $itemlg = new Criteria();
        $itemlg->add(ItemsLogPeer::ITEM_ID, $item_id);
        $itemlg->addDescendingOrderByColumn(ItemsLogPeer::ID);
        $this->log_items = ItemsLogPeer::doSelect($itemlg);

        $imagHistory = new Criteria();
        $imagHistory->add(ImageHistoryPeer::ITEM_ID, $item_id);
        $countImage = ImageHistoryPeer::doCount($imagHistory);
        $this->imCount = 0;
        $this->imCount = $countImage;
        if ($countImage > 0) {
            $imagHistory->addDescendingOrderByColumn(ImageHistoryPeer::CREATED_AT);
            $this->imHistorys = ImageHistoryPeer::doSelect($imagHistory);
        }
        
            $conn = Propel::getConnection();
            $query="SELECT  item_id 
,(SELECT item_id FROM items    WHERE  item_id<ii.item_id  order by item_id DESC  limit 1 )  AS previousid
,(SELECT item_id FROM items    WHERE  item_id>ii.item_id  order by item_id ASC  limit 1 )  AS nextid
FROM items as ii where item_id=". $id;
           $statement = $conn->prepare($query);
            $statement->execute();
         $rowObj = $statement->fetch(PDO::FETCH_OBJ);
                        $this->nextid=$rowObj->nextid; 
                          $this->previousid=$rowObj->previousid; 
                          
//        $bc = new barCode('png');
//        $this->barcode = $bc->build($this->item->getEan());
        
    }

    public function targetUrl() {
        return sfConfig::get("app_admin_url");
    }
    public function executeEdit($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Item module id is 1.
        if (Access::checkPermissions(1, "edit",$user_id,$session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $itemid = $request->getParameter("id");  
        $ci = new Criteria();
        $ci->add(ItemsPeer::ITEM_ID,$itemid);
        $item = ItemsPeer::doSelectOne($ci);
        $this->item = $item;
        $this->cancel_url = $request->getReferer();
                
    }
    public function executeUpdate($request){
      
     if ($request->isMethod('post')) {   
                
        $desc1 = $request->getParameter("desc1");
        $desc2 = $request->getParameter("desc2");
        $desc3 = $request->getParameter("desc3");
        $supNum = $request->getParameter("supplier_number");
        $supitemno = $request->getParameter("supitemno");
//        $ean = $request->getParameter("ean");
        $group = $request->getParameter("group");
        $color = $request->getParameter("color");
        $size = $request->getParameter("size");
        $buyingPrice = $request->getParameter("buying_price");
        $sellingPrice = $request->getParameter("selling_price");
        $taxcode = $request->getParameter("taxation_code");
        $status_id = $request->getParameter("status_id");
        $id = $request->getParameter("itemId");
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $user = UserPeer::retrieveByPK($user_id); 
        $imgUpdate = false;
       
          if($_FILES["product_image"]["name"]){
 ////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        
        $file_name=$id;
        
     // echo "file=path=". $_FILES["product_image"]["name"];
    echo  $file_with_path=$_FILES["product_image"]["tmp_name"];
    
  $allowedExts = array("jpeg", "jpg", "JPG", "JPEG");
$temp = explode(".", $_FILES["product_image"]["name"]);
$extension = end($temp);
 if ((($_FILES["product_image"]["type"] == "image/jpeg")
  || ($_FILES["product_image"]["type"] == "image/jpg")
  || ($_FILES["product_image"]["type"] == "image/JPG")
  || ($_FILES["product_image"]["type"] == "image/JPEG"))
 
&& ($_FILES["product_image"]["size"] < 25000000)
&& in_array($extension, $allowedExts))
  {  
     
     
 
     $today = time();

            /////////////////////////////////////////////////////////////////////////////////////////////////////// 
            $foo = new Upload($file_with_path);
            if ($foo->uploaded) {
                // save uploaded image with no changes
                
                gf::checkAndRenameImage($file_name,$user->getId());
                $imgUpdate = true;
                $foo->file_new_name_body = $file_name;
                $foo->image_convert = 'jpg';
                $foo->Process('/var/www/dbnposcms/web/uploads/images/');
                if ($foo->processed) {
                    echo 'original image copied';
                } else {
                    echo 'error : ' . $foo->error;
                }

                // save uploaded image with a new name
                $foo->file_new_name_body = $file_name;
                $foo->Process('/home/dbnposcms/images/backup/');
                if ($foo->processed) {
                    echo 'image renamed "foo" copied';
                } else {
                    echo 'error : ' . $foo->error;
                }



                $itemw = new Criteria();
                $itemw->add(ItemsPeer::ITEM_ID, $file_name);
                if (ItemsPeer::doCount($itemw) > 0) {
                    $itemw = ItemsPeer::doSelectOne($itemw);
                    $itemw->setImageUpdateDate($today);
                    $itemw->setImageStatus(1);
                    $itemw->save();
                    
                } else {

                    $message = "unable to find item ID for given image name" . $file_name;
                    emailLib::sendItemsImageError($message);
                }
                $foo->file_new_name_body = $file_name . '_50';
                $foo->image_resize = true;
                $foo->image_x = 50;
                $foo->image_y = 50;
               $foo->image_convert = 'jpg';
                //  $foo->image_ratio_x = true;
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
                    
                }

                $foo->file_new_name_body = $file_name . '_32';
                $foo->image_resize = true;
                $foo->image_x = 32;
                $foo->image_y = 32;
                $foo->image_convert = 'jpg';
                // $foo->image_ratio_y = true;
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
                    
                }


                $foo->file_new_name_body = $file_name . '_187';
                $foo->image_resize = true;
                $foo->image_x = 187;
                $foo->image_y = 187;
                $foo->image_convert = 'jpg';
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
//                    echo 'image renamed, resized x=100
//          and converted to gif';
                    $foo->Clean();
                } else {
                    echo 'error : ' . $foo->error;
                }
            }      
        
        
         }else{
   if($_FILES["product_image"]["size"] < 25000000){   
    $this->getUser()->setFlash('update_error', $this->getContext()->getI18N()->__('Image format is not jpg format. Use jpg format image'));   
          }else{
               $this->getUser()->setFlash('update_error', $this->getContext()->getI18N()->__('Image Size is greater then 20Mb. Please use image size less then 20Mb'));     
          }
  }
        
        
        
        
///////////////////////////////////////////////////////////////////////////////////////////////////////////   
     }
        
        
        $item = array();
        $item["description1"] = $desc1;
        $item["description2"] = $desc2;
        $item["description3"] = $desc3;
        $item["supplier_number"] = $supNum;
        $item["supplier_item_number"] = $supitemno;
//        $item["ean"] = $ean;
        $item["group"] = $group;
        $item["color"] = $color;
        $item["size"] = $size;
        $item["buying_price"] = $buyingPrice;
        $item["selling_price"] = $sellingPrice;
        $item["taxation_code"] = $taxcode;
        $item["status_id"] = $status_id;
        $item["id"] = $id;        
        $item["updated_by"] = $user->getId();
        $item["is_image_update"] = $imgUpdate;
        //  var_dump($item);die;
        $insert_new = itemsLib::populateWebItem($item);
        if($insert_new){
            $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Item Updated.'));
            $this->redirect('items/view?id='.$id);
        }else{
            $this->getUser()->setFlash('update_error', $this->getContext()->getI18N()->__('Error in updation.'));
            $this->redirect('items/view?id='.$id);
        }

      }
    }
    
    public function executeAdd($request){
     $this->cancel_url = $request->getReferer(); 
     $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
     $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Item module id is 1.
        if (Access::checkPermissions(1, "add",$user_id,$session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
     if ($request->isMethod('post')) {   
        $desc1 = $request->getParameter("desc1");
        $desc2 = $request->getParameter("desc2");
        $desc3 = $request->getParameter("desc3");
        $supNum = $request->getParameter("supplier_number");
        $supitemno = $request->getParameter("supitemno");
        $ean = $request->getParameter("ean");
        $group = $request->getParameter("group");
        $color = $request->getParameter("color");
        $size = $request->getParameter("size");
        $buyingPrice = $request->getParameter("buying_price");
        $sellingPrice = $request->getParameter("selling_price");
        $taxcode = $request->getParameter("taxation_code");
        $item_id = $request->getParameter("item_id");
        
        $ct = new Criteria();
        $ct->add(ItemsPeer::ITEM_ID,$item_id);
        $ct->add(ItemsPeer::STATUS_ID,3);
        $idcount = ItemsPeer::doCount($ct);
        if($idcount > 0){
            $this->getUser()->setFlash('add_error', $this->getContext()->getI18N()->__('Item No already exist.'));
            $this->redirect('items/add');
        }
        $csc = new Criteria();
        $csc->add(SystemConfigPeer::KEYS,"Check Barcode/EAN No", Criteria::EQUAL);
        $csc->addOr(SystemConfigPeer::ID,4);
        $dnItem = SystemConfigPeer::doSelectOne($csc);
            if($dnItem->getValues()=="Yes"){
            $ce = new Criteria();
            $ce->add(ItemsPeer::EAN,$ean);
            $ct->add(ItemsPeer::STATUS_ID,3);
            $encount = ItemsPeer::doCount($ce);
            if($encount > 0){
                $this->getUser()->setFlash('add_error', $this->getContext()->getI18N()->__('EAN already exist.'));
                $this->redirect('items/add');
            }
        }
        $statusId=3;
        $item = array();
        $item["description1"] = $desc1;
        $item["description2"] = $desc2;
        $item["description3"] = $desc3;
        $item["supplier_number"] = $supNum;
        $item["supplier_item_number"] = $supitemno;
        $item["ean"] = $ean;
        $item["group"] = $group;
        $item["color"] = $color;
        $item["size"] = $size;
        $item["buying_price"] = $buyingPrice;
        $item["selling_price"] = $sellingPrice;
        $item["taxation_code"] = $taxcode;
        $item["id"] = $item_id;  
        $item["status_id"] = $statusId;    
        $item["updated_by"] = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        
      
        //  var_dump($item);die;
        $insert_new = itemsLib::populateWebItem($item);
          if($_FILES["product_image"]["name"]){
         ////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        
        $file_name = $item_id;
        
     // echo "file=path=". $_FILES["product_image"]["name"];
      $file_with_path=$_FILES["product_image"]["tmp_name"];
    
  $allowedExts = array("jpeg", "jpg", "JPG", "JPEG");
$temp = explode(".", $_FILES["product_image"]["name"]);
$extension = end($temp);
 if ((($_FILES["product_image"]["type"] == "image/jpeg")
  || ($_FILES["product_image"]["type"] == "image/jpg")
         || ($_FILES["product_image"]["type"] == "image/JPG")
         || ($_FILES["product_image"]["type"] == "image/JPEG"))
 
&& ($_FILES["product_image"]["size"] < 25000000)
&& in_array($extension, $allowedExts))
  {  
     
     
 
     $today = time();

            /////////////////////////////////////////////////////////////////////////////////////////////////////// 
            $foo = new Upload($file_with_path);
            if ($foo->uploaded) {
                // save uploaded image with no changes

                gf::checkAndRenameImage($file_name,$this->getUser()->getAttribute('user_id', '', 'backendsession'));
                
                $foo->file_new_name_body = $file_name;
                $foo->image_convert = 'jpg';
                $foo->Process('/var/www/dbnposcms/web/uploads/images/');
                if ($foo->processed) {
                    echo 'original image copied';
                } else {
                    echo 'error : ' . $foo->error;
                }

                // save uploaded image with a new name
                $foo->file_new_name_body = $file_name;
                $foo->Process('/home/dbnposcms/images/backup/');
                if ($foo->processed) {
                    echo 'image renamed "foo" copied';
                } else {
                    echo 'error : ' . $foo->error;
                }



                $itemt = new Criteria();
                $itemt->add(ItemsPeer::ITEM_ID, $file_name);
                if (ItemsPeer::doCount($itemt) > 0) {
                    $itemt = ItemsPeer::doSelectOne($itemt);
                    $itemt->setImageUpdateDate($today);
                    $itemt->setImageStatus(1);
                    $itemt->save();
                } else {

                    $message = "unable to find item ID for given image name" . $file_name;
                    emailLib::sendItemsImageError($message);
                }
                $foo->file_new_name_body = $file_name . '_50';
                $foo->image_resize = true;
                $foo->image_x = 50;
                $foo->image_y = 50;
               $foo->image_convert = 'jpg';
                //  $foo->image_ratio_x = true;
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
                    
                }

                $foo->file_new_name_body = $file_name . '_32';
                $foo->image_resize = true;
                $foo->image_x = 32;
                $foo->image_y = 32;
                $foo->image_convert = 'jpg';
                // $foo->image_ratio_y = true;
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
                    
                }


                $foo->file_new_name_body = $file_name . '_187';
                $foo->image_resize = true;
                $foo->image_x = 187;
                $foo->image_y = 187;
                $foo->image_convert = 'jpg';
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
//                    echo 'image renamed, resized x=100
//          and converted to gif';
                    $foo->Clean();
                } else {
                    echo 'error : ' . $foo->error;
                }
            }      
        
        
         }else{
          if($_FILES["product_image"]["size"] < 25000000){   
    $this->getUser()->setFlash('update_error', $this->getContext()->getI18N()->__('Image format is not jpg format. Use jpg format image'));   
          }else{
               $this->getUser()->setFlash('update_error', $this->getContext()->getI18N()->__('Image Size is greater then 20Mb. Please use image size less then 20Mb'));     
          }
    
  }
        
        
        
        
///////////////////////////////////////////////////////////////////////////////////////////////////////////  
        
        }
        
        if($insert_new){
            $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Item added successfully.'));
            $this->redirect('items/view?id='.$item_id);
        }else{
            $this->getUser()->setFlash('update_error', $this->getContext()->getI18N()->__('Error in adding item.'));
            $this->redirect('items/add');
        }
        
       }
    }
    
    public function executeValidateItemId($request){
        $item_id = $request->getParameter("item_id");
//        $csc = new Criteria();
//        $csc->add(SystemConfigPeer::KEYS,"Check Double Item", Criteria::EQUAL);
//        $csc->addOr(SystemConfigPeer::ID,1);
//        $dnItem = SystemConfigPeer::doSelectOne($csc);
//        if($dnItem->getValues()=="Yes"){
            $ct = new Criteria();
            $ct->add(ItemsPeer::ITEM_ID,$item_id);
    //        $ct->add(ItemsPeer::STATUS_ID,3);
            $idcount = ItemsPeer::doCount($ct);
            if($idcount > 0){
                echo "false";
            }else{
                echo "true";
            }
//        }
//        else{
//            
//        }
        return sfView::NONE;
    }
    
    public function executeValidateEan($request){
        $ean = $request->getParameter("ean");
        $csc = new Criteria();
        $csc->add(SystemConfigPeer::KEYS,"Check Barcode/EAN No", Criteria::EQUAL);
        $csc->addOr(SystemConfigPeer::ID,4);
        $dnItem = SystemConfigPeer::doSelectOne($csc);
        if($dnItem->getValues()=="Yes"){
        $ct = new Criteria();
            $ct->add(ItemsPeer::EAN,$ean);
    //        $ct->add(ItemsPeer::STATUS_ID,3);
            $idcount = ItemsPeer::doCount($ct);
            if($idcount > 0){
                echo "false";
            }else{
                echo "true";
            }
        }else{
            echo "true";
        }
        return sfView::NONE;
    }
    
    public function executeDelItem($request){
        $item_id = $request->getParameter("id");
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Item module id is 1.
        if (Access::checkPermissions(1, "delItem",$user_id,$session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        
        $cit = new Criteria();
        $cit->add(ItemsPeer::ITEM_ID,$item_id);
        $__Item = ItemsPeer::doSelectOne($cit);
        if($__Item->getStatusId()==5){
            $status_id = 3;
            $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Item activated successfully.'));
        }else{
            $status_id = 5;
            $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Item deactivated successfully.'));
        }
        
        
        $item["id"]   = $item_id;
        $item["status_id"] = $status_id;
        
        $insert_new = itemsLib::populateWebItem($item);
        
        $this->redirect('items/index');
        return sfView::NONE;
    }
    
    public function executeDeleteItem($request){
        $item_id = $request->getParameter("id");
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Item module id is 1.
        if (Access::checkPermissions(1, "delItem",$user_id,$session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $ct = new Criteria();
        $ct->add(ItemsPeer::ITEM_ID,$item_id);
        $item = ItemsPeer::doSelectOne($ct);
        $item->delete();
        
        //$insert_new = itemsLib::populateWebItem($item);
        $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Item deleted successfully.'));
        $this->redirect('items/index');
        return sfView::NONE;
    }
}
