<?php

set_time_limit(10000000);
require_once(sfConfig::get('sf_lib_dir') . '/changeLanguageCulture.php');
require_once(sfConfig::get('sf_lib_dir') . '/emailLib.php');
require_once(sfConfig::get('sf_lib_dir') . '/ForumTel.php');
require_once(sfConfig::get('sf_lib_dir') . '/commissionLib.php');
require_once(sfConfig::get('sf_lib_dir') . '/curl_http_client.php');
require_once(sfConfig::get('sf_lib_dir') . '/smsCharacterReplacement.php');
require_once(sfConfig::get('sf_lib_dir') . '/zerocall_out_sms.php');
require_once(sfConfig::get('sf_lib_dir') . '/uploadfile.php');

/**
 * scripts actions.
 *
 * @package    Zapna
 * @subpackage scripts
 * @author     Khan Muhamamd
 * @version    actions.class.php,v 1.5 2012-01-16 22:20:12 KM Exp $
 */
class pScriptsActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    private $currentCulture;

    public function executeSendEmails(sfWebRequest $request) {

        require_once(sfConfig::get('sf_lib_dir') . '/swift/lib/swift_init.php');


        echo 'starting the debug';
        echo '<br/>';
        echo sfConfig::get('app_email_smtp_host');
        echo '<br/>';
        echo sfConfig::get('app_email_smtp_port');
        echo '<br/>';
        echo sfConfig::get('app_email_smtp_username');
        echo '<br/>';
        echo sfConfig::get('app_email_smtp_password');
        echo '<br/>';
        echo sfConfig::get('app_email_sender_email', 'support@kimarin.es');
        echo '<br/>';
        echo sfConfig::get('app_email_sender_name', 'Kimarin support');


        $connection = Swift_SmtpTransport::newInstance()
                ->setHost(sfConfig::get('app_email_smtp_host'))
                ->setPort(sfConfig::get('app_email_smtp_port'))
                ->setUsername(sfConfig::get('app_email_smtp_username'))
                ->setPassword(sfConfig::get('app_email_smtp_password'))
        ;




        $sender_email = sfConfig::get('app_email_support_email');
        $sender_name = sfConfig::get('app_email_support_name');

        echo '<br/>';
        echo $sender_email;
        echo '<br/>';
        echo $sender_name;


        $mailer = new Swift_Mailer($connection);

        $c = new Criteria();
        $c->add(EmailQueuePeer::EMAIL_STATUS_ID, sfConfig::get('app_status_completed'), Criteria::NOT_EQUAL);
        $emails = EmailQueuePeer::doSelect($c);
        try {
            foreach ($emails as $email) {


                $message = Swift_Message::newInstance($email->getSubject())
                        ->setFrom(array($sender_email => $sender_name))
                        ->setTo(array($email->getReceipientEmail() => $email->getReceipientName()))
                        ->setBody($email->getMessage(), 'text/html')
                ;

//                $message = Swift_Message::newInstance($email->getSubject())
//		         ->setFrom(array("support@landncall.com"))
//		         ->setTo(array("mohammadali110@gmail.com"=>"Mohammad Ali"))
//		         ->setBody($email->getMessage(), 'text/html')
//		         ;
                echo 'inside loop';
                echo '<br/>';

                echo $email->getId();
                echo '<br/>';
                echo '<br/>';

                //This Conditon Add Update Row Which Have the 
                if ($email->getReceipientEmail() != '') {
                    @$mailer->send($message);
                    $email->setEmailStatusId(sfConfig::get('app_status_completed'));
                    //TODO:: add sent_at too
                    $email->save();
                    echo sprintf("Send to %s<br />", $email->getReceipientEmail());
                }
            }
        } catch (Exception $e) {

            echo $e->getLine();
            echo $e->getMessage();
        }
        return sfView::NONE;
    }

    public function getEnableCountryId($calingcode) {
        // echo $full_mobile_number = $calingcode;
        $enableCountry = new Criteria();
        $enableCountry->add(EnableCountryPeer::STATUS, 1);
        $enableCountry->add(EnableCountryPeer::LANGUAGE_SYMBOL, 'en', Criteria::NOT_EQUAL);
        $enableCountry->add(EnableCountryPeer::CALLING_CODE, '%' . $calingcode . '%', Criteria::LIKE);
        $country_id = EnableCountryPeer::doSelectOne($enableCountry);
        $countryId = $country_id->getId();
        return $countryId;
    }

    private function setPreferredCulture(Customer $customer) {
        $this->currentCulture = $this->getUser()->getCulture();
        $preferredLang = PreferredLanguagesPeer::retrieveByPK($customer->getPreferredLanguageId());
        $this->getUser()->setCulture($preferredLang->getLanguageCode());
    }

    private function updatePreferredCulture() {
        $this->getUser()->setCulture($this->currentCulture);
    }

    public function executeViewItems($request) {
        $vcn = new Criteria();

        $allItems = ItemsPeer::doSelect($vcn);

        $this->Items = $allItems;
        $this->setLayout(false);
    }

    public function executeAddItems($request) {

        $this->setLayout(false);
    }

    public function executeFtpProcessDeliveryNotes($request) {


        $cronHistory = new CronJobHistory();
        $cronHistory->setStart(time());


        $cron_jobs = CronJobsPeer::retrieveByPk($request->getParameter('id'));

        $cronHistory->setCronJobId($cron_jobs->getId());
        $cronHistory->save();

        $xml_root_dir = '/home/dbnposcms/' . $cron_jobs->getDefinationFilePath();

        $csv_root_dir = '/home/dbnposcms/' . $cron_jobs->getDataFilePath();
        /* $xml_root_dir = '/home/dbnposcms/delivery_notes/xml';
          $csv_root_dir = '/home/dbnposcms/delivery_notes/csv';
         */
        $xml_error_dir = $xml_root_dir . '/error';
        $xml_backup_dir = $xml_root_dir . '/backup';

        $csv_error_dir = $csv_root_dir . '/error';
        $csv_backup_dir = $csv_root_dir . '/backup';

        $ignore = array('.', '..', 'backup_staging_error', 'backup_staging', 'backup', 'error');

        $files = scandir($xml_root_dir);
        $files = array_diff($files, $ignore);

        if (count($files) == 0) {
            echo 'No file to process in "' . $xml_root_dir . '"';
            $cronHistory->setEnd(time());
            $cronHistory->save();
            emailLib::sendCronJobHistory($cronHistory, $cron_jobs);
            exit(1);
        }
        echo "<hr/>";
        foreach ($files as $file) {
            $splits = explode('.', $file);
            $file_with_path = $xml_root_dir . '/' . $file;
            $file_extension = end($splits);
//            echo $file_extension;
//            echo "<br/>";
//            echo filesize($xml_root_dir.'/'.$file);
//            die;


            if ($file_extension == 'xml' && filesize($file_with_path) != 0) {
                $handle = fopen($file_with_path, "r");
                $xmlfile = fread($handle, filesize($file_with_path));
                try {
                    $xml_obj = new SimpleXMLElement($xmlfile);
                    //  var_dump($xml_obj);
                } catch (Exception $e) {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Process stoped as XML file is invalid fileName:" . $file;
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }

                $separater = $xml_obj->separator;
                if ($separater == "," || $separater == ";" || $separater == '\t') {
                    if ($separater == '\t') {
                        $separater = "\t";
                    }
                    //$this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                } else {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Unkown Seprator Found Please check XML File:" . $file;
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }

                if ($xml_obj->tableName == "") {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Empty Table Name Please check XML File:" . $file;
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }
                $tableName = $xml_obj->tableName;
                $os = array("items", "delivery_notes", "m6", "m16");
                if (!in_array($tableName, $os)) {
                    $tableName = FALSE;
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Unknown Table Name Please check XML File:" . $file;
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }

                if ($xml_obj->charactersQuotation) {
                    
                } else {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "characters Quotation does not exist in XML  file";
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }

                $enclosure = $xml_obj->charactersQuotation;


                if ($xml_obj->header) {
                    $start = 1;
                } else {
                    $start = 0;
                }
                if ($enclosure == "") {
                    $enclosure = '"';
                }

                $delimiter = $separater;





                $fieldsArray = array();
                $countdata = $xml_obj->columns[0]->count();
                for ($i = 0; $i < $countdata; $i++) {

                    $datacolumnName = $xml_obj->columns[0]->column[$i]->columnName;
                    $datacolumnIndex = $xml_obj->columns[0]->column[$i]->columnIndex;
                    $fieldsArray["" . $datacolumnIndex] = $datacolumnName;
                    //  echo "<hr>";
                }
                ksort($fieldsArray);


                if ($xml_obj->tableName == "delivery_notes") {

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
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "Mandatory Column '$keyPrintMandatory' not Found Please check XML File: " . $file;
                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);
                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();
                        continue;
                    }



                    $columnAray = $mandatoryItemFields;
                    $field_error = false;
                    $keyPrint = "";
                    foreach ($fieldsArray as $key) {
                        if (!in_array($key, $columnAray)) {
                            echo "<hr>";
                            $keyPrint = $key;
                            echo "<hr>";
                            $field_error = true;
                        }
                    }

                    if ($field_error) {
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "Unknown Column '$keyPrint' Found Please check XML File:" . $file;
                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);
                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();
                        continue;
                    }





                    $splits = explode('.', $xml_obj->file);
                    $file_extension = end($splits);

//               echo  $csv_root_dir . '/' . $xml_obj->file;
//               die;


                    if (file_exists($csv_root_dir . '/' . $xml_obj->file) && filesize($csv_root_dir . '/' . $xml_obj->file) != 0 && ($file_extension == 'csv' || $file_extension == 'txt' )) {


                        $separater = $xml_obj->separator;

                        $fileTmpName = $csv_root_dir . '/' . $xml_obj->file;

                        $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                        $reader->open();


                        //   $data = explode("\n", $csv);
                        $valid_csv = true;
                        $combine_valid_csv = true;
                        $startrow = 0;
                        if ($xml_obj->startRow == "") {
                            $startrow = 0;
                        } else {
                            $startrow = $xml_obj->startRow;
                        }



                        $startingRow = $startrow + $start - 1;

                        if ($startingRow < 0) {
                            $startingRow = 0;
                        }


                        // var_dump($data);die;
                        // This loop will check only for the errors in the CSV and the next loop will insert into the database;
                        $insert_new = false;
                        $i = 0;
                        while ($data = $reader->read()) {
                            $i++;
                            if ($i < $startingRow) {
                                continue;
                            }


                            $combine = array_combine($fieldsArray, $data);
                            if (!$combine) {
                                $combine_valid_csv = false;
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $message = "CSV columns does not match with the defination xml columns. At row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                                //$this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                            }
                            foreach ($combine as $key => $values) {
                                $combine[trim($key)] = trim($values);
                            }

                            $sc = new Criteria();
                            $sc->add(ShopsPeer::COMPANY_NUMBER, $combine['company_number']);
                            $sc->add(ShopsPeer::BRANCH_NUMBER, $combine['branch_number']);
                            if (ShopsPeer::doCount($sc) != 1 && $valid_csv) {
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $valid_csv = false;
                                $message = "Validation failed as shop not found while parsing CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                            }
                            $csc = new Criteria();
                            $csc->add(SystemConfigPeer::KEYS, "Check Double Delivery Notes", Criteria::EQUAL);
                            $csc->addOr(SystemConfigPeer::ID, 2);
                            $dnItem = SystemConfigPeer::doSelectOne($csc);
                            if ($dnItem->getValues() == "Yes") {
                                $dc = new Criteria();
                                $dc->add(DeliveryNotesPeer::NOTE_ID, $combine['delivery_number']);
                                if (DeliveryNotesPeer::doCount($dc) >= 1 && $valid_csv) {
                                    rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                    $valid_csv = false;
                                    $message = "Validation failed as previous delivery number found while parsing CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                    $cronHistoryInfo = new CronJobHistoryInfo();
                                    $cronHistoryInfo->setXml($file);
                                    $cronHistoryInfo->setCsv($xml_obj->file);
                                    $cronHistoryInfo->setStatus(0);
                                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                    $cronHistoryInfo->setMessage($message);
                                    $cronHistoryInfo->save();
                                }
                            }


                            $csc = new Criteria();
                            $csc->add(SystemConfigPeer::KEYS, "Check Delivery Items", Criteria::EQUAL);
                            $csc->addOr(SystemConfigPeer::ID, 3);
                            $dnItem = SystemConfigPeer::doSelectOne($csc);
                            if ($dnItem->getValues() == "Yes") {
                                $ic = new Criteria();
                                $ic->add(ItemsPeer::ITEM_ID, $combine['item_number']);
                                if (ItemsPeer::doCount($ic) != 1 && $valid_csv) {
                                    rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                    $valid_csv = false;
                                    $message = "Validation failed as as item not found while parsing CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                    $cronHistoryInfo = new CronJobHistoryInfo();
                                    $cronHistoryInfo->setXml($file);
                                    $cronHistoryInfo->setCsv($xml_obj->file);
                                    $cronHistoryInfo->setStatus(0);
                                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                    $cronHistoryInfo->setMessage($message);
                                    $cronHistoryInfo->save();
                                }
                            } else {
                                $cit = new Criteria();
                                $cit->add(ItemsPeer::ITEM_ID, $combine['item_number'], Criteria::EQUAL);
                                $chkitem = ItemsPeer::doCount($cit);
                                if ($chkitem == 0) {
                                    $item = array();
                                    $item["id"] = $combine['item_number'];
                                    $item["ean"] = $combine['item_number'];
                                    $item["updated_by"] = 1;
                                    itemsLib::populateItem($item);
                                }
                            }

                            if (!$valid_csv) {
                                
                            } elseif (!$combine_valid_csv) {
                                
                            } elseif (!is_numeric($combine['company_number']) || !is_numeric($combine['branch_number']) || !is_numeric($combine['item_number']) || !is_numeric($combine['quantity'])) {
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $valid_csv = false;
                                $message = "Validation failed in CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                            }
                        }
                        $reader->close();

                        if ($valid_csv && $combine_valid_csv) {
                            $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                            $reader->open();
                            $insert_new = false;
                            $i = 0;
                            while ($data = $reader->read()) {
                                $i++;
                                if ($i < $startingRow) {
                                    continue;
                                }


                                $combine = array_combine($fieldsArray, $data);
                                foreach ($combine as $key => $values) {
                                    $combine[trim($key)] = trim($values);
                                }
                                $combine["updated_by"] = 1; ///Ftp user id
                                $insert_new = itemsLib::populateDeliveryNotes($combine);
                            }
                            $reader->close();

                            $sp = new Criteria();
                            $sp->add(ShopsPeer::BRANCH_NUMBER, $combine['branch_number']);
                            if (ShopsPeer::doCount($sp) > 0) {
                                $shops = ShopsPeer::doSelectOne($sp);
                                if ($shops->getGcmKey() != "") {
                                    new GcmLib("delivery_note", array($shops->getGcmKey()));
                                }
                            } else {
                                $shops = 0;
                            }




                            // as CSV parsed so move it and move XML as well to the backup.
                            rename("$csv_root_dir/$xml_obj->file", "$csv_backup_dir/$xml_obj->file");
                            rename("$xml_root_dir/$file", "$xml_backup_dir/$file");
                            $cronHistoryInfo = new CronJobHistoryInfo();
                            $cronHistoryInfo->setXml($file);
                            $cronHistoryInfo->setCsv($xml_obj->file);
                            $cronHistoryInfo->setStatus(1);
                            $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                            $cronHistoryInfo->setMessage("Data Parsed Successfully with rows count:" . $i - 1);
                            $cronHistoryInfo->save();
                        }
                    } else {
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "unable to parse xml file as csv file not found/invalid:" . $file;
                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);

                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();

                        continue;
                    }
                } else {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "invalide table found in delivery_notes directory xml. unable to parse " . $file;

                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setCsv($xml_obj->file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }
            } else {
                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                $message = "invalid extension or xml is empty. unable to parse " . $file;
                $cronHistoryInfo = new CronJobHistoryInfo();
                $cronHistoryInfo->setXml($file);
                $cronHistoryInfo->setStatus(0);
                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                $cronHistoryInfo->setMessage($message);
                $cronHistoryInfo->save();
                continue;
            }
        }
        $cronHistory->setEnd(time());
        $cronHistory->save();
        emailLib::sendCronJobHistory($cronHistory, $cron_jobs);
        return sfView::NONE;
    }

    public function executeFtpProcessImport($request) {

        $cronHistory = new CronJobHistory();
        $cronHistory->setStart(time());

        $cron_jobs = CronJobsPeer::retrieveByPk($request->getParameter('id'));

        $cronHistory->setCronJobId($cron_jobs->getId());
        $cronHistory->save();

        $xml_root_dir = '/home/dbnposcms/' . $cron_jobs->getDefinationFilePath();

        $csv_root_dir = '/home/dbnposcms/' . $cron_jobs->getDataFilePath();


        /*  $xml_root_dir = '/home/dbnposcms/items/xml';

          $csv_root_dir = '/home/dbnposcms/items/csv'; */

        $xml_error_dir = $xml_root_dir . '/error';
        $xml_backup_dir = $xml_root_dir . '/backup';

        $csv_error_dir = $csv_root_dir . '/error';
        $csv_backup_dir = $csv_root_dir . '/backup';

        $ignore = array('.', '..', 'backup_staging_error', 'backup_staging', 'backup', 'error');

        $files = scandir($xml_root_dir);
        $files = array_diff($files, $ignore);

        if (count($files) == 0) {
            echo 'No file to process in "' . $xml_root_dir . '"';
            $cronHistory->setEnd(time());
            $cronHistory->save();
            emailLib::sendCronJobHistory($cronHistory, $cron_jobs);
            exit(1);
        }
        echo "<hr/>";
        foreach ($files as $file) {
            $splits = explode('.', $file);
            $file_with_path = $xml_root_dir . '/' . $file;
            $file_extension = end($splits);
//            echo $file_extension;
//            echo "<br/>";
//            echo filesize($xml_root_dir.'/'.$file);
//            die;


            if ($file_extension == 'xml' && filesize($file_with_path) != 0) {
                $handle = fopen($file_with_path, "r");
                $xmlfile = fread($handle, filesize($file_with_path));
                try {
                    $xml_obj = new SimpleXMLElement($xmlfile);
                    //  var_dump($xml_obj);
                } catch (Exception $e) {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Process stoped as XML file is invalid fileName:" . $file;
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }

                $separater = $xml_obj->separator;
                if ($separater == "," || $separater == ";" || $separater == '\t') {
                    if ($separater == '\t') {
                        $separater = "\t";
                    }
                    //$this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                } else {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Unkown Seprator Found Please check XML File:" . $file;
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }

                if ($xml_obj->tableName == "") {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Empty Table Name Please check XML File:" . $file;
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }
                $tableName = $xml_obj->tableName;
                $os = array("items", "delivery_notes", "m6", "m16");
                if (!in_array($tableName, $os)) {
                    $tableName = FALSE;
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Unknown Table Name Please check XML File:" . $file;
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }

                if ($xml_obj->charactersQuotation) {
                    
                } else {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "characters Quotation is not exist in XML  file";
                    $cronHistoryInfo = new CronJobHistoryInfo();
                    $cronHistoryInfo->setXml($file);
                    $cronHistoryInfo->setStatus(0);
                    $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                    $cronHistoryInfo->setMessage($message);
                    $cronHistoryInfo->save();
                    continue;
                }

                $enclosure = $xml_obj->charactersQuotation;


                if ($xml_obj->header) {
                    $start = 1;
                } else {
                    $start = 0;
                }
                if ($enclosure == "") {
                    $enclosure = '"';
                }

                $delimiter = $separater;





                $fieldsArray = array();
                $countdata = $xml_obj->columns[0]->count();
                for ($i = 0; $i < $countdata; $i++) {

                    $datacolumnName = $xml_obj->columns[0]->column[$i]->columnName;
                    $datacolumnIndex = $xml_obj->columns[0]->column[$i]->columnIndex;
                    $fieldsArray["" . $datacolumnIndex] = $datacolumnName;
                    //  echo "<hr>";
                }
                ksort($fieldsArray);


                if ($xml_obj->tableName == "items") {

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
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "Mandatory Column '$keyPrintMandatory' not Found Please check XML File:" . $file;
                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);
                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();
                        continue;
                    }



                    $columnAray = array("id", "description1", "description2", "description3", "supplier_number", "supplier_item_number", "ean", "color", "size", "buying_price", "selling_price", "taxation_code", "group");
                    $field_error = false;
                    $keyPrint = "";
                    foreach ($fieldsArray as $key) {
                        if (!in_array($key, $columnAray)) {
                            echo "<hr>";
                            $keyPrint = $key;
                            echo "<hr>";
                            $field_error = true;
                        }
                    }

                    if ($field_error) {
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "Unknown Column '$keyPrint' Found Please check XML File:" . $file;
                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);
                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();
                        continue;
                    }





                    $splits = explode('.', $xml_obj->file);
                    $file_extension = end($splits);

//               echo  $csv_root_dir . '/' . $xml_obj->file;
//               die;


                    if (file_exists($csv_root_dir . '/' . $xml_obj->file) && filesize($csv_root_dir . '/' . $xml_obj->file) != 0 && ($file_extension == 'csv' || $file_extension == 'txt' )) {


                        $separater = $xml_obj->separator;




                        /*   $csv_file = fopen($csv_root_dir . '/' . $xml_obj->file, "r");

                          $csv = fread($csv_file, filesize($csv_root_dir . '/' . $xml_obj->file));
                          $csv = str_replace('"', '', $csv);
                          fclose($csv_file);
                         */

                        $fileTmpName = $csv_root_dir . '/' . $xml_obj->file;

                        $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                        $reader->open();


                        //   $data = explode("\n", $csv);
                        $valid_csv = true;
                        $combine_valid_csv = true;
                        $startrow = 0;
                        if ($xml_obj->startRow == "") {
                            $startrow = 0;
                        } else {
                            $startrow = $xml_obj->startRow;
                        }



                        $startingRow = $startrow + $start - 1;

                        if ($startingRow < 0) {
                            $startingRow = 0;
                        }


                        // var_dump($data);die;
                        // This loop will check only for the errors in the CSV and the next loop will insert into the database;
                        $insert_new = false;
                        $i = 0;
                        while ($data = $reader->read() && $data != "") {
                            $i++;
                            if ($i < $startingRow) {
                                continue;
                            }


                            $combine = array_combine($fieldsArray, $data);
                            if (!$combine) {
                                $combine_valid_csv = false;
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $message = "CSV columns does not match with the defination xml columns. At row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                                //$this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                            }
                            foreach ($combine as $key => $values) {
                                $combine[trim($key)] = trim($values);
                            }
                            var_dump($combine);
                            echo "<hr/>";
                            $combine['selling_price'] = itemsLib::currencyVersionConvertor($combine['selling_price']);
                            $combine['buying_price'] = itemsLib::currencyVersionConvertor($combine['buying_price']);
                            if (!$combine_valid_csv) {
                                
                            } elseif (!is_numeric($combine['id']) || !is_numeric($combine['ean']) || !is_numeric($combine['buying_price']) || !is_numeric($combine['selling_price']) || !is_numeric($combine['taxation_code'])) {


                                $valid_csv = false;
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $message = $combine['id'] . ":Validation failed in CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                                //$this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                            }
                        }
                        $reader->close();


                        if ($valid_csv && $combine_valid_csv) {
                            $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                            $reader->open();
                            $insert_new = false;
                            $i = 0;
                            while ($data = $reader->read()) {
                                $i++;
                                if ($i < $startingRow) {
                                    continue;
                                }


                                $combine = array_combine($fieldsArray, $data);
                                foreach ($combine as $key => $values) {
                                    $combine[trim($key)] = trim($values);
                                }
                                $combine["updated_by"] = 1; ///Ftp user id
                                $insert_new = itemsLib::populateItem($combine);
                            }
                            $reader->close();
                            // as CSV parsed so move it and move XML as well to the backup.
                            rename("$csv_root_dir/$xml_obj->file", "$csv_backup_dir/$xml_obj->file");
                            rename("$xml_root_dir/$file", "$xml_backup_dir/$file");

                            $cronHistoryInfo = new CronJobHistoryInfo();
                            $cronHistoryInfo->setXml($file);
                            $cronHistoryInfo->setCsv($xml_obj->file);
                            $cronHistoryInfo->setStatus(1);
                            $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                            $message = "Successfully import " . ($i - 1) . " Rows";
                            $cronHistoryInfo->setMessage($message);
                            $cronHistoryInfo->save();
                        }
                    } else {
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "unable to parse xml file as csv file not found/invalid:" . $file;

                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);
                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();
                        continue;
                    }
                } elseif ($xml_obj->tableName == "delivery_notes") {

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
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "Mandatory Column '$keyPrintMandatory' not Found Please check XML File: " . $file;
                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);
                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();
                        continue;
                    }



                    $columnAray = $mandatoryItemFields;
                    $field_error = false;
                    $keyPrint = "";
                    foreach ($fieldsArray as $key) {
                        if (!in_array($key, $columnAray)) {
                            echo "<hr>";
                            $keyPrint = $key;
                            echo "<hr>";
                            $field_error = true;
                        }
                    }

                    if ($field_error) {
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "Unknown Column '$keyPrint' Found Please check XML File:" . $file;
                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);
                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();
                        continue;
                    }





                    $splits = explode('.', $xml_obj->file);
                    $file_extension = end($splits);

//               echo  $csv_root_dir . '/' . $xml_obj->file;
//               die;


                    if (file_exists($csv_root_dir . '/' . $xml_obj->file) && filesize($csv_root_dir . '/' . $xml_obj->file) != 0 && ($file_extension == 'csv' || $file_extension == 'txt' )) {


                        $separater = $xml_obj->separator;

                        $fileTmpName = $csv_root_dir . '/' . $xml_obj->file;

                        $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                        $reader->open();


                        //   $data = explode("\n", $csv);
                        $valid_csv = true;
                        $combine_valid_csv = true;
                        $startrow = 0;
                        if ($xml_obj->startRow == "") {
                            $startrow = 0;
                        } else {
                            $startrow = $xml_obj->startRow;
                        }



                        $startingRow = $startrow + $start - 1;

                        if ($startingRow < 0) {
                            $startingRow = 0;
                        }


                        // var_dump($data);die;
                        // This loop will check only for the errors in the CSV and the next loop will insert into the database;
                        $insert_new = false;
                        $i = 0;
                        while ($data = $reader->read()) {
                            $i++;
                            if ($i < $startingRow) {
                                continue;
                            }


                            $combine = array_combine($fieldsArray, $data);
                            if (!$combine) {
                                $combine_valid_csv = false;
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $message = "CSV columns does not match with the defination xml columns. At row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                                //$this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                            }
                            foreach ($combine as $key => $values) {
                                $combine[trim($key)] = trim($values);
                            }

                            $sc = new Criteria();
                            $sc->add(ShopsPeer::COMPANY_NUMBER, $combine['company_number']);
                            $sc->addAnd(ShopsPeer::BRANCH_NUMBER, $combine['branch_number']);
                            if (ShopsPeer::doCount($sc) != 1 && $valid_csv) {
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $valid_csv = false;
                                $message = "Validation failed as shop not found while parsing CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                            }

                            $dc = new Criteria();
                            $dc->add(DeliveryNotesPeer::NOTE_ID, $combine['delivery_number']);
                            if (DeliveryNotesPeer::doCount($dc) >= 1 && $valid_csv) {
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $valid_csv = false;
                                //$message = "Validation failed as previous delivery number found while parsing CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $message = "Error : Delivery No " . $combine['delivery_number'] . " already exist in Database.";
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                            }




                            $ic = new Criteria();
                            $ic->add(ItemsPeer::ITEM_ID, $combine['item_number']);
                            if (ItemsPeer::doCount($ic) != 1 && $valid_csv) {
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $valid_csv = false;
                                //$message = "Validation failed as as item not found while parsing CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $message = "Error : Item  No " . $combine['item_number'] . " not Found in Database.";
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                            }


                            if (!$valid_csv) {
                                
                            } elseif (!$combine_valid_csv) {
                                
                            } elseif (!is_numeric($combine['company_number']) || !is_numeric($combine['branch_number']) || !is_numeric($combine['item_number']) || !is_numeric($combine['quantity'])) {
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $valid_csv = false;
                                $message = "Validation failed in CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                $cronHistoryInfo = new CronJobHistoryInfo();
                                $cronHistoryInfo->setXml($file);
                                $cronHistoryInfo->setCsv($xml_obj->file);
                                $cronHistoryInfo->setStatus(0);
                                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                                $cronHistoryInfo->setMessage($message);
                                $cronHistoryInfo->save();
                            }
                        }
                        $reader->close();

                        if ($valid_csv && $combine_valid_csv) {
                            $reader = new sfCsvReader($fileTmpName, $delimiter, $enclosure);
                            $reader->open();
                            $insert_new = false;
                            $i = 0;
                            while ($data = $reader->read()) {
                                $i++;
                                if ($i < $startingRow) {
                                    continue;
                                }


                                $combine = array_combine($fieldsArray, $data);
                                foreach ($combine as $key => $values) {
                                    $combine[trim($key)] = trim($values);
                                }
                                $combine["updated_by"] = 1; ///Ftp user id
                                $insert_new = itemsLib::populateDeliveryNotes($combine);
                            }
                            $reader->close();
                            // as CSV parsed so move it and move XML as well to the backup.
                            rename("$csv_root_dir/$xml_obj->file", "$csv_backup_dir/$xml_obj->file");
                            rename("$xml_root_dir/$file", "$xml_backup_dir/$file");
                            $shop = ShopsPeer::doSelectOne($sc);
                            if ($shop->getGcmKey() != "") {
                                new GcmLib("delivery_note", array($shop->getGcmKey()));
                            }

                            $cronHistoryInfo = new CronJobHistoryInfo();
                            $cronHistoryInfo->setXml($file);
                            $cronHistoryInfo->setCsv($xml_obj->file);
                            $cronHistoryInfo->setStatus(1);
                            $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                            $message = "Successfully import " . ($i - 1) . " Rows";
                            $cronHistoryInfo->setMessage($message);
                            $cronHistoryInfo->save();
                        }
                    } else {
                        rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                        $message = "unable to parse xml file as csv file not found/invalid:" . $file;
                        $cronHistoryInfo = new CronJobHistoryInfo();
                        $cronHistoryInfo->setXml($file);

                        $cronHistoryInfo->setStatus(0);
                        $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                        $cronHistoryInfo->setMessage($message);
                        $cronHistoryInfo->save();

                        continue;
                    }
                }
            } else {
                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                $message = "invalid extension or xml is empty. unable to parse " . $file;
                $cronHistoryInfo = new CronJobHistoryInfo();
                $cronHistoryInfo->setXml($file);
                $cronHistoryInfo->setStatus(0);
                $cronHistoryInfo->setCronJobHistoryId($cronHistory->getId());
                $cronHistoryInfo->setMessage($message);
                $cronHistoryInfo->save();
                continue;
            }
        }
        $cronHistory->setEnd(time());
        $cronHistory->save();
        emailLib::sendCronJobHistory($cronHistory, $cron_jobs);
        return sfView::NONE;
    }

    public function executeSyncItems(sfWebRequest $request) {
        $urlval = "SyncItems-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_it=" . $request->getParameter("shop_id") . "&sync_type=" . $request->getParameter("sync_type"));
        $dibsCall->save();
        $sync_type = $request->getParameter("sync_type"); ///0 for all items. 1 for recent items
        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $syncItems = "";
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setItemSyncRequestedAt(time());
            $shop->save();
            $ic = new Criteria();
            if ($sync_type == 1) {
                if ($shop->getItemSyncSyncedAt() != "") {
                    $ic->add(ItemsPeer::ITEM_UPDATED_AT, $shop->getItemSyncSyncedAt(), Criteria::GREATER_EQUAL);
                }
            }
            $syncItems = ItemsPeer::doSelect($ic);
            $items = "";
            $i = 0;
            foreach ($syncItems as $syncItem) {
                $items[$i]['id'] = ($syncItem->getId() == "") ? "" : $syncItem->getId();
                $items[$i]['item_id'] = ($syncItem->getItemId() == "") ? "" : $syncItem->getItemId();
                $items[$i]['description1'] = ($syncItem->getDescription1() == "") ? "" : $syncItem->getDescription1();
                $items[$i]['description2'] = ($syncItem->getDescription2() == "") ? "" : $syncItem->getDescription2();
                $items[$i]['description3'] = ($syncItem->getDescription3() == "") ? "" : $syncItem->getDescription3();
                $items[$i]['supplier_number'] = ($syncItem->getSupplierNumber() == "") ? "" : $syncItem->getSupplierNumber();
                ;
                $items[$i]['supplier_item_number'] = ($syncItem->getSupplierItemNumber() == "") ? "" : $syncItem->getSupplierItemNumber();
                $items[$i]['ean'] = ($syncItem->getEan() == "") ? "" : $syncItem->getEan();
                $items[$i]['color'] = ($syncItem->getColor() == "") ? "" : $syncItem->getColor();
                $items[$i]['grop'] = ($syncItem->getGroup() == "") ? "" : $syncItem->getGroup();
                $items[$i]['size'] = ($syncItem->getSize() == "") ? "" : $syncItem->getSize();
                $items[$i]['image_status'] = ($syncItem->getImageStatus() == "") ? "" : $syncItem->getImageStatus();
                $items[$i]['buying_price'] = ($syncItem->getBuyingPrice() == "") ? "0" : $syncItem->getBuyingPrice();
                $items[$i]['selling_price'] = ($syncItem->getSellingPrice() == "") ? "0" : $syncItem->getSellingPrice();
                $items[$i]['taxation_code'] = ($syncItem->getTaxationCode() == "") ? "" : $syncItem->getTaxationCode();
                $items[$i]['created_at'] = ($syncItem->getItemUpdatedAt() == "") ? "" : $syncItem->getItemUpdatedAt();
                $i++;
            }
            echo json_encode($items);
        }
        return sfView::NONE;
    }

    public function executeSyncItemsUpdate(sfWebRequest $request) {
        $urlval = "SyncItemsUpdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("id") . "id=" . $request->getParameter("id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setItemSyncSyncedAt($shop->getItemSyncRequestedAt());
            if ($shop->save()) {
                echo "OK";
            } else {
                echo "ERROR";
            }
        }
        return sfView::NONE;
    }

    public function executeSyncDeliveryNotes(sfWebRequest $request) {
        $urlval = "SyncDeliveryNotes-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $i = new Criteria();
            $i->add(DeliveryNotesPeer:: SHOP_ID, (int) $request->getParameter("shop_id"));
            $i->add(DeliveryNotesPeer::IS_SYNCED, 0);
            $i->add(DeliveryNotesPeer::DELIVERY_DATE, date("Y-m-d 23:59:59"), Criteria::LESS_EQUAL);
            $syncNotes = DeliveryNotesPeer::doSelect($i);
            $notes = "";
            $i = 0;
            foreach ($syncNotes as $syncNote) {
                $notes[$i]['id'] = $syncNote->getId();
                $notes[$i]['item_id'] = $syncNote->getItemId();
                $notes[$i]['branch_number'] = $syncNote->getBranchNumber();
                $notes[$i]['company_number'] = $syncNote->getCompanyNumber();
                $notes[$i]['quantity'] = $syncNote->getQuantity();
                $notes[$i]['delivery_date'] = $syncNote->getDeliveryDate();
                $notes[$i]['note_id'] = $syncNote->getNoteId();
                $i++;
            }
            echo json_encode($notes);
        }
        return sfView::NONE;
    }

    public function executeSyncDeliveryNotesUpdate(sfWebRequest $request) {
        $urlval = "SyncDeliveryNotesUpdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("id=" . $request->getParameter("id"));
        $dibsCall->save();
        $ids = array_map('intval', explode(",", $request->getParameter("id")));
        $c = new Criteria();
        $c->add(DeliveryNotesPeer::ID, $ids, Criteria::IN);
        $c->add(DeliveryNotesPeer::IS_SYNCED, 0);
        $deliveryNotesFlag = false;
        if (DeliveryNotesPeer::doCount($c) > 0) {
            $delivery_notes = DeliveryNotesPeer::doSelect($c);
            foreach ($delivery_notes as $delivery_note) {
                $delivery_note->setIsSynced(1);
                $delivery_note->setSyncedAt(time());
                $delivery_note->setUpdatedBy(1); /// pos user on csm
                if ($delivery_note->save()) {
                    $deliveryNotesFlag = true;
                } else {
                    $deliveryNotesFlag = false;
                }
            }
            if ($deliveryNotesFlag) {
                echo "OK";
            }
        }

        return sfView::NONE;
    }

///////////////////////////////////////////////////////////////////////////////////////////// 

    public function executeSyncBookOutNotes($request) {

        $urlval = "SyncBookOutNotes-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_bookout=" . $request->getParameter("server_json_bookout"));
        $dibsCall->save();

        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $bookoutIds = "";
        $json_from_bookout = json_decode($request->getParameter("server_json_bookout"));
        $shop_id = $request->getParameter("shop_id");

        $cd = new Criteria();
        $cd->clearSelectColumns();
        $cd->addSelectColumn('MAX(' . DeliveryNotesPeer::GROUP_ID . ') as maxgroup');
        $stmt = DeliveryNotesPeer::doSelectStmt($cd);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $max_group = $row['maxgroup'] + 1;
        $bookoutReceived = 0;
        foreach ($json_from_bookout as $object) {


            $new_dn = new BookoutNotes();
            $new_dn->setId($object->id);
            $new_dn->setNoteId($object->note_id);
            $new_dn->setBranchNumber($object->branch_number);
            $new_dn->setCompanyNumber($object->company_number);
            $new_dn->setShopId($shop_id);
            $new_dn->setQuantity($object->quantity);
            $new_dn->setItemId($object->item_id);
            $new_dn->setDeliveryDate(date("Y-m-d H:i:s", strtotime($object->bookout_date)));
            $new_dn->setStatusId(1);
            $new_dn->setSyncedAt(date("Y-m-d H:i:s"));
            $new_dn->setComment($object->comments);
            $new_dn->setUpdatedBy($object->user_id);
            $new_dn->setGroupId($max_group);


            if ($new_dn->save()) {
                $bookoutIds[] = $new_dn->getId();
                $bookoutReceived = 1;
                $deliveryNote = $new_dn;
            }
        }

        if ($bookoutReceived) {
            emailLib::sendEmailBookoutReceived($deliveryNote);
        }

        //  $a = implode(', ', $bookoutIds);
        $a = implode(',', $bookoutIds);
        echo json_encode($a);

        return sfView::NONE;
    }

    public function executeSyncBookoutUpdated(sfWebRequest $request) {
        $urlval = "SyncbookoutItemsupdated-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $syncItems = "";
            $shop = ShopsPeer::doSelectOne($s);


            $ic = new Criteria();
            $ic->Add(BookoutNotesPeer::STATUS_ID, 3);

            $ic->add(BookoutNotesPeer::IS_SYNCED, 0);
            $ic->Add(BookoutNotesPeer::SHOP_ID, $shop->getId());
            $syncbookout = BookoutNotesPeer::doSelect($ic);
            $bookout = "";
            $i = 0;
            foreach ($syncbookout as $syncbookot) {
                $bookout[$i]['id'] = $syncbookot->getId();
                $bookout[$i]['item_id'] = $syncbookot->getItemId();
                $bookout[$i]['reply_comment'] = $syncbookot->getReplyComment();
                $bookout[$i]['received_quantity'] = $syncbookot->getReceivedQuantity();
                $i++;
            }
            echo json_encode($bookout);
        }
        return sfView::NONE;
    }

    public function executeSyncBookoutNotesReceived(sfWebRequest $request) {
        $urlval = "SyncBookoutNotesReceived-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_trans=" . $request->getParameter("server_json_trans") . "&shop_id=" . $request->getParameter("shop_id") . "&response_json=" . $request->getParameter("response_json") . "&response_json_new=" . $request->getParameter("response_json_new") . "&server_json_trans_new=" . $request->getParameter("server_json_trans_new"));
        $dibsCall->save();
        $shop_id = $request->getParameter("shop_id");
        $json_from_shop = json_decode($request->getParameter("response_json"));

        foreach ($json_from_shop as $object) {


            $c = new Criteria();
            $c->add(BookoutNotesPeer::ID, $object->cms_id);
            $c->addAnd(BookoutNotesPeer::IS_RECEIVED, 1);
            if (BookoutNotesPeer::doCount($c) == 1) {
                //    echo "khanna";
                $syncNote = BookoutNotesPeer::doSelectOne($c);
                //  $syncNote->setComment($object->comments);
                //  $syncNote->setReceivedAt($object->received_at);
                //  $syncNote->setReceivedQuantity($object->received_quantity);
                $syncNote->setUserId($object->user_id);
                $syncNote->setSyncedDayStartId($object->synced_day_start_id);
                $syncNote->setReceivedDayStartId($object->received_day_start_id);
                $syncNote->setStatusId(3);
                $syncNote->setIsReceived(1);

                //  $syncNote->setSyncedAt(date("Y-m-d H:i:s"));
                $syncNote->setIsSynced(1);
                $syncNote->setShopRespondedAt(date("Y-m-d H:i:s"));
                $syncNote->setUpdatedBy(1); /// pos user on csm
                $syncNote->save();

                $is_item_received = true;
            }
        }
        $saved_transactions = "";
        $json_of_transactions = json_decode($request->getParameter("server_json_trans"));
        foreach ($json_of_transactions as $object) {
            $c = new Criteria();
            $c->add(TransactionsPeer::SHOP_TRANSACTION_ID, $object->pos_id);
            $c->add(TransactionsPeer::SHOP_ID, $shop_id);
            if (TransactionsPeer::doCount($c) == 0) {
                $saved_transactions[] = itemsLib::createTransactionUsingObject($object, $shop_id);
            } else {
                $saved_transactions[] = $object->pos_id;
            }
        }

        $response_json_new = json_decode($request->getParameter("response_json_new"));
//        var_dump($response_json_new);
//        die;
//        $response_json_new[0]->branch_number;
//        var_dump($server_json_trans_new);

        if ($response_json_new) {
            $cd = new Criteria();
            $cd->clearSelectColumns();
            $cd->addSelectColumn('MAX(' . DeliveryNotesPeer::GROUP_ID . ') as maxgroup');
            $stmt = DeliveryNotesPeer::doSelectStmt($cd);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $max_group = $row['maxgroup'] + 1;
            foreach ($response_json_new as $object2) {
                if ($object2 == null)
                    continue;
                $csnew = new Criteria();
                $csnew->add(ShopsPeer::BRANCH_NUMBER, $object2->branch_number);
                $shop_new = ShopsPeer::doSelectOne($csnew);
                $synote = new DeliveryNotes();
                $synote->setId($object2->cms_id);
                $synote->setItemId($object2->item_id);
                $synote->setBranchNumber($object2->branch_number);
                $synote->setCompanyNumber($object2->company_number);
                $synote->setNoteId($object2->note_number);
                $synote->setQuantity(0);
                $synote->setComment($object2->comments);
                $synote->setReceivedAt($object2->received_at);
                $synote->setReceivedQuantity($object2->received_quantity);
                $synote->setUserId($object2->user_id);
                $synote->setDeliveryDate($object2->delivery_date);
                $synote->setSyncedDayStartId($object2->synced_day_start_id);
                $synote->setReceivedDayStartId($object2->received_day_start_id);
                $synote->setStatusId(3);
                $synote->setShopId($shop_new->getId());
                $synote->setIsReceived(1);
                $synote->setIsSynced(1);
                $synote->setSyncedAt(date("Y-m-d H:i:s"));
                $synote->setShopRespondedAt(date("Y-m-d H:i:s"));
                $synote->setUpdatedBy(1); /// pos user on csm
                $synote->setGroupId($max_group);
                $synote->save();
            }

            $server_json_trans_new = json_decode($request->getParameter("server_json_trans_new"));
            foreach ($server_json_trans_new as $objectnew) {
                if ($objectnew == null)
                    continue;
                $c = new Criteria();
                $c->add(TransactionsPeer::SHOP_TRANSACTION_ID, $objectnew->pos_id);
                $c->add(TransactionsPeer::SHOP_ID, $shop_new->getId());
                if (TransactionsPeer::doCount($c) == 0) {
                    $saved_transactions[] = itemsLib::createTransactionUsingObject($objectnew, $shop_new->getId());
                }
            }
        }

        echo implode(",", $saved_transactions);
        return sfView::NONE;
    }

    /////////////////////////////////////////////////////////////////////////////////

    public function executeSyncDeliveryNotesReceived(sfWebRequest $request) {
        $urlval = "SyncDeliveryNotesReceived-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_trans=" . $request->getParameter("server_json_trans") . "&shop_id=" . $request->getParameter("shop_id") . "&response_json=" . $request->getParameter("response_json") . "&response_json_new=" . $request->getParameter("response_json_new") . "&server_json_trans_new=" . $request->getParameter("server_json_trans_new"));
        $dibsCall->save();
        $shop_id = $request->getParameter("shop_id");
        $json_from_shop = json_decode($request->getParameter("response_json"));

        foreach ($json_from_shop as $object) {
            $c = new Criteria();
            $c->add(DeliveryNotesPeer::ID, $object->cms_id);
            $c->add(DeliveryNotesPeer::IS_RECEIVED, 0);
            if (DeliveryNotesPeer::doCount($c) == 1) {
                $syncNote = DeliveryNotesPeer::doSelectOne($c);
                $syncNote->setComment($object->comments);
                $syncNote->setReceivedAt($object->received_at);
                $syncNote->setReceivedQuantity($object->received_quantity);
                $syncNote->setUserId($object->user_id);
                $syncNote->setSyncedDayStartId($object->synced_day_start_id);
                $syncNote->setReceivedDayStartId($object->received_day_start_id);
                $syncNote->setStatusId(3);
                $syncNote->setIsReceived(1);
                $syncNote->setIsSynced(1);
                $syncNote->setShopRespondedAt(date("Y-m-d H:i:s"));
                $syncNote->setUpdatedBy($object->user_id); /// pos user on csm   1101392294394
                $syncNote->save();

                $is_item_received = true;
            }
        }
        $saved_transactions = "";
        $json_of_transactions = json_decode($request->getParameter("server_json_trans"));
        foreach ($json_of_transactions as $object) {
            $c = new Criteria();
            $c->add(TransactionsPeer::SHOP_TRANSACTION_ID, $object->pos_id);
            $c->add(TransactionsPeer::SHOP_ID, $shop_id);
            if (TransactionsPeer::doCount($c) == 0) {
                $saved_transactions[] = itemsLib::createTransactionUsingObject($object, $shop_id);
            } else {
                $saved_transactions[] = $object->pos_id;
            }
        }

        $response_json_new = json_decode($request->getParameter("response_json_new"));
//        var_dump($response_json_new);
//        die;
//        $response_json_new[0]->branch_number;
//        var_dump($server_json_trans_new);

        if ($response_json_new) {
            $cd = new Criteria();
            $cd->clearSelectColumns();
            $cd->addSelectColumn('MAX(' . DeliveryNotesPeer::GROUP_ID . ') as maxgroup');
            $stmt = DeliveryNotesPeer::doSelectStmt($cd);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $max_group = $row['maxgroup'] + 1;
            foreach ($response_json_new as $object2) {
                if ($object2 == null)
                    continue;
                $csnew = new Criteria();
                $csnew->add(ShopsPeer::BRANCH_NUMBER, $object2->branch_number);
                $shop_new = ShopsPeer::doSelectOne($csnew);
                $synote = new DeliveryNotes();
                $synote->setId($object2->cms_id);
                $synote->setItemId($object2->item_id);
                $synote->setBranchNumber($object2->branch_number);
                $synote->setCompanyNumber($object2->company_number);
                $synote->setNoteId($object2->note_number);
                $synote->setQuantity(0);
                $synote->setComment($object2->comments);
                $synote->setReceivedAt($object2->received_at);
                $synote->setReceivedQuantity($object2->received_quantity);
                $synote->setUserId($object2->user_id);
                $synote->setDeliveryDate($object2->delivery_date);
                $synote->setSyncedDayStartId($object2->synced_day_start_id);
                $synote->setReceivedDayStartId($object2->received_day_start_id);
                $synote->setStatusId(3);
                $synote->setShopId($shop_new->getId());
                $synote->setIsReceived(1);
                $synote->setIsSynced(1);
                $synote->setSyncedAt(date("Y-m-d H:i:s"));
                $synote->setShopRespondedAt(date("Y-m-d H:i:s"));
                $synote->setUpdatedBy(1101392294394); /// pos user on csm
                $synote->setGroupId($max_group);
                if ($synote->save()) {
                    $deliverNoteaddes = 1;
                }
            }

            $server_json_trans_new = json_decode($request->getParameter("server_json_trans_new"));
            foreach ($server_json_trans_new as $objectnew) {
                if ($objectnew == null)
                    continue;
                $c = new Criteria();
                $c->add(TransactionsPeer::SHOP_TRANSACTION_ID, $objectnew->pos_id);
                $c->add(TransactionsPeer::SHOP_ID, $shop_new->getId());
                if (TransactionsPeer::doCount($c) == 0) {
                    $saved_transactions[] = itemsLib::createTransactionUsingObject($objectnew, $shop_new->getId());
                }
            }
        }

        echo implode(",", $saved_transactions);
        return sfView::NONE;
    }

    public function executeCropImage($request) {

        $sshManager = new Ssh2_crontab_manager('184.107.168.18', '22', 'dbnposcms', 'zap#@!SAH');
        $images_root_dir = '/home/dbnposcms/images';
        $c3 = "chmod 777 /home/dbnposcms/images/  -R";
        $sshManager->exec($c3);
        //  chmod("/home/dbnposcms/images", 0777);

        $images_error_dir = $images_root_dir . '/error';
        $images_backup_dir = $images_root_dir . '/backup';



        $ignore = array('.', '..', 'backup_staging_error', 'backup_staging', 'backup', 'error');

        $files = scandir($images_root_dir);
        $files = array_diff($files, $ignore);

        if (count($files) == 0) {
            echo 'No file to process in "' . $images_root_dir . '"';
            exit(1);
        }
        echo "<hr/>";
        foreach ($files as $file) {
            $splits = explode('.', $file);
            echo "<br/>" . $file_with_path = $images_root_dir . '/' . $file;
            $file_extension = end($splits);
            $file_name = $splits[0];

            $today = time();

            /////////////////////////////////////////////////////////////////////////////////////////////////////// 
            $foo = new Upload($file_with_path);
            if ($foo->uploaded) {
                // save uploaded image with no changes

                gf::checkAndRenameImage($file_name, $updated_by = 1);

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



                $item = new Criteria();
                $item->add(ItemsPeer::ITEM_ID, $file_name);
                if (ItemsPeer::doCount($item) > 0) {
                    $item = ItemsPeer::doSelectOne($item);
                    $item->setImageUpdateDate($today);
                    $item->setImageStatus(1);
                    $item->save();
                } else {

                    $message = "unable to find item ID for given image name" . $file_name;
                    emailLib::sendItemsImageError($message);
                }
                $foo->file_new_name_body = $file_name . '_50';
                $foo->image_resize = true;
                $foo->image_x = 50;
                $foo->image_y = 50;
                //  $foo->image_ratio_x = true;
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
                    
                }

                $foo->file_new_name_body = $file_name . '_32';
                $foo->image_resize = true;
                $foo->image_x = 32;
                $foo->image_y = 32;
                // $foo->image_ratio_y = true;
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
                    
                }

                $foo->file_new_name_body = $file_name . '_187';
                $foo->image_resize = true;
                $foo->image_x = 187;
                $foo->image_y = 187;
                $foo->Process('/var/www/dbnposcms/web/uploads/images/thumbs/');
                if ($foo->processed) {
//                    echo 'image renamed, resized x=100
//          and converted to gif';
                    $foo->Clean();
                } else {
                    echo 'error : ' . $foo->error;
                }

                $cit = new Criteria();
                $cit->add(ItemsPeer::ITEM_ID, $file_name);
                if (ItemsPeer::doCount($cit)) {
                    $itemRec = ItemsPeer::doSelectOne($cit);
                    $item = array();
                    $item["description1"] = $itemRec->getDescription1();
                    $item["description2"] = $itemRec->getDescription2();
                    $item["description3"] = $itemRec->getDescription3();
                    $item["supplier_number"] = $itemRec->getSupplierNumber();
                    $item["supplier_item_number"] = $itemRec->getSupplierItemNumber();
                    $item["group"] = $itemRec->getGroup();
                    $item["color"] = $itemRec->getColor();
                    $item["size"] = $itemRec->getSize();
                    $item["buying_price"] = $itemRec->getBuyingPrice();
                    $item["selling_price"] = $itemRec->getSellingPrice();
                    $item["taxation_code"] = $itemRec->getTaxationCode();
                    $item["status_id"] = $itemRec->getStatusId();
                    $item["id"] = $itemRec->getItemId();
                    $item["updated_by"] = 1;
                    $item["is_image_update"] = true;
                    //  var_dump($item);die;
                    $insert_new = itemsLib::populateItem($item);
                }
            }


            ////////////////////////////////////////////////////////////////////////////////////////////////////////////     
            sleep(1);
        }
        return sfView::NONE;
    }

    public function executeSyncImages(sfWebRequest $request) {
        $urlval = "SyncImages-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $syncItems = "";
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setPicSyncRequestedAt(time());

            $shop->save();
            $ic = new Criteria();
            $ic->add(ItemsPeer::IMAGE_STATUS, 1);
            $ic->setLimit(50);

            if ($shop->getPicSyncSyncedAt() != "") {
                $ic->addAnd(ItemsPeer::IMAGE_UPDATE_DATE, $shop->getPicSyncSyncedAt(), Criteria::GREATER_EQUAL);
            }

            $syncItems = ItemsPeer::doSelect($ic);
            $items = "";
            $i = 0;
            // $zipfilename = 'file.zip';
            $destination = '/var/www/dbnposcms/web/uploads/tmp/piture.zip';
            $overwrite = false;
            $zip = new ZipArchive();
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                echo die("file not open");
            }
            $pathLifeway = '/var/www/dbnposcms/web/uploads/images/thumbs/';
            foreach ($syncItems as $syncItem) {
                $filePathName = "";

                $filePathName = $pathLifeway . $syncItem->getSmallPic();
                if (!file_exists($filePathName) && $syncItem->getSmallPic() == "") {
                    continue;
                }

                echo "<br/>pic=" . $pathLifeway . $syncItem->getSmallPic();
                if ($zip->addFile($pathLifeway . $syncItem->getSmallPic(), $syncItem->getSmallPic())) {
                    $zip->addFile($pathLifeway . $syncItem->getLargePic(), $syncItem->getLargePic());
                }
                sleep(.001);
            }

            $zip->close();

            sleep(.001);
        }

        ob_end_flush();

        $this->getResponse()->clearHttpHeaders();
        $this->getResponse()->setStatusCode(200);

        $this->getResponse()->setContentType('image/jpg');
        $this->getResponse()->setContentType('application/octet-stream');
        $this->getResponse()->setContentType('application/zip');


        $this->getResponse()->setHttpHeader('Pragma', 'public'); //optional cache header
        $this->getResponse()->setHttpHeader('Expires', 0); //optional cache header
        $this->getResponse()->setHttpHeader('Content-Disposition', "attachment; filename=piture.zip");
        $this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $this->getResponse()->setHttpHeader('Content-Length', filesize($destination));
        sleep(.001);
        flush();
        //  return $this->renderText(file_get_contents($destination));
        // return sfView::HEADERS_ONLY;
    }

    public function executeSyncImagesUpdate(sfWebRequest $request) {
        $urlval = "SyncImagesUpdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setPicSyncSyncedAt($shop->getPicSyncRequestedAt());

            echo "OK";
        } else {
            echo "ERROR";
        }
        return sfView::NONE;
    }

    public function executePosttest($request) {
        
    }

    public function executeForgot($request) {
        if ($request->getMethod() != 'post') {
            $pin = $request->getParameter("user[pin]");

            $cuc = new Criteria();
            $cuc->add(UserPeer::PIN, $pin);
            $cuc->add(UserPeer::PIN_STATUS, 3);
            $cuc->add(UserPeer::STATUS_ID, 3);
            if (UserPeer::doCount($cuc) > 0) {
                $fpToken = md5(uniqid(mt_rand(), true));
                $fpUser = UserPeer::doSelectOne($cuc);
                $fpUser->setResetPasswordToken($fpToken);
                $fpUser->save();
                $email_content = 'Hello, ' . $fpUser->getName() . '<br /><br />
===============================================<br /><br />

You have requested to reset your password.<br /><br />

To choose a new password, just follow this link: ' . sfConfig::get("app_customer_url") . 'pScripts/resetPassword/token/' . $fpUser->getResetPasswordToken() . '.
<br /><br />
Have a great day!';

                emailLib::sendAdminForgotPassworEmail($fpUser, $email_content);
                $this->getUser()->setFlash('reset_message', 'Email sent to your email address.');
            } else {
                
            }
        }
    }

    public function executeValidateEmailOnForgot($request) {
        $pin = $request->getParameter("user[pin]");

        $cuc = new Criteria();
        $cuc->add(UserPeer::PIN, $pin);
        $cuc->add(UserPeer::PIN_STATUS, 3);
        $cuc->add(UserPeer::STATUS_ID, 3);
        if (UserPeer::doCount($cuc) > 0) {
            echo "true";
        } else {
            echo "false";
        }
        return sfView::NONE;
    }

    public function executeResetPassword($request) {
        $token = $request->getParameter("token");
        $new_password = $request->getParameter("new_password");
        $confirm_password = $request->getParameter("confirm_password");
        if ($new_password != "" && $confirm_password != "") {
            $cuc = new Criteria();
            $cuc->add(UserPeer::RESET_PASSWORD_TOKEN, $token);
            $cuc->add(UserPeer::STATUS_ID, 3);
            if (UserPeer::doCount($cuc) > 0) {
                if ($new_password == $confirm_password) {
                    $user = UserPeer::doSelectOne($cuc);
                    $user->setPassword($new_password);
                    $user->save();
                    $this->getUser()->setFlash('reset_message', 'Password has been updated.');
                    $this->redirect(sfConfig::get("app_admin_url") . 'user/login');
                }
            }
        }
    }

    public function executeSyncSalesTransaction(sfWebRequest $request) {


        $urlval = "SyncSalesTransaction-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_orderpayment=" . $request->getParameter("server_json_orderpayment") . "&server_json_trans=" . $request->getParameter("server_json_trans") . "&shop_id=" . $request->getParameter("shop_id") . "&server_json_order=" . $request->getParameter("server_json_order"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");
        $json_from_orders = json_decode($request->getParameter("server_json_order"));

        $i = 0;
        $a = "";
        foreach ($json_from_orders as $json_form_order) {
            $co = new Criteria();
            $co->add(OrdersPeer::SHOP_ORDER_ID, $json_form_order->shop_order_id);
            $co->add(OrdersPeer::SHOP_ID, $shop_id);
            if (OrdersPeer::doCount($co) == 0) {
                $orderId = itemsLib::createOrderUsingObject($json_form_order, $shop_id);
            } else {
                $orderId = itemsLib::updateOrderUsingObject($json_form_order, $shop_id);
            }
            $orderIdArr = explode("~", $orderId);
            $orderPaymentId = "";
            foreach ($json_form_order->payments as $orderPaymentObject) {
                $cop = new Criteria();
                $cop->add(OrderPaymentsPeer::SHOP_ORDER_PAYMENT_ID, $orderPaymentObject->shop_order_payment_id);
                $cop->add(OrderPaymentsPeer::SHOP_ID, $shop_id);
                if (OrderPaymentsPeer::doCount($cop) == 0)
                    $orderPaymentId[] = itemsLib::createOrderPaymentUsingObject($orderPaymentObject, $shop_id, $orderIdArr[1]);
            }
            $saved_transactions = "";
            foreach ($json_form_order->transactions as $object) {
                $c = new Criteria();
                $c->add(TransactionsPeer::SHOP_TRANSACTION_ID, $object->pos_id);
                $c->add(TransactionsPeer::SHOP_ID, $shop_id);
                if (TransactionsPeer::doCount($c) == 0) {
                    $saved_transactions[] = itemsLib::createTransactionUsingObject($object, $shop_id, $orderIdArr[1]);
                }
            }

            $a[$i]["order_id"] = $orderIdArr[0];
            $a[$i]["order_payment_id"] = implode(",", $orderPaymentId);
            $a[$i]["order_transaction_id"] = implode(",", $saved_transactions);
            $i++;
        }

        echo json_encode($a);
        return sfView::NONE;
    }

    public function executeSyncBranchConfiguration(sfWebRequest $request) {
        $urlval = "SyncBranchConfiguration-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("branch_number=" . $request->getParameter("branch_number"));
        $dibsCall->save();
        $roles = array();
        $permissions = array();
        $s = new Criteria();
        $s->add(ShopsPeer::BRANCH_NUMBER, $request->getParameter("branch_number"));
        $s->addAnd(ShopsPeer::PASSWORD, $request->getParameter("password"));
        if (ShopsPeer::doCount($s) == 1) {
            $syncItems = "";
            $shopData = ShopsPeer::doSelectOne($s);
            $shopData->setConfiguredAt(time());
            $shopData->setIsConfigured(1);
            $shopData->setGcmKey($request->getParameter("gcm_key"));
            $shopData->save();

            $shop['id'] = $shopData->getId();
            $shop['name'] = $shopData->getName();
            $shop['branch_number'] = $shopData->getBranchNumber();
            $shop['company_number'] = $shopData->getCompanyNumber();
            $shop['password'] = $shopData->getPassword();
            $shop['address'] = $shopData->getAddress();
            $shop['zip'] = $shopData->getZip();
            $shop['place'] = $shopData->getPlace();
            $shop['country'] = $shopData->getCountry();
            $shop['tel'] = $shopData->getTel();

            $shop['negative_sale'] = $shopData->getNegativeSale();
            $shop['language_id'] = $shopData->getLanguageId();
            $shop['time_out'] = $shopData->getTimeOut();
            $shop['start_value_sale_receipt'] = $shopData->getStartValueSaleReceipt();
            $shop['start_value_return_receipt'] = $shopData->getStartValueReturnReceipt();
            $shop['sale_receipt_format_id'] = $shopData->getSaleReceiptFormatId();
            $shop['return_receipt_format_id'] = $shopData->getReturnReceiptFormatId();
            $shop['bookout_format_id'] = $shopData->getBookoutFormatId();
            $shop['start_value_bookout'] = $shopData->getStartValueBookout();
            $shop['discount_value'] = $shopData->getDiscountValue();
            $shop['discount_type_id'] = $shopData->getDiscountTypeId();

            $shop['receipt_header_position'] = $shopData->getReceiptHeaderPosition();
            $shop['receipt_tax_statement_one'] = $shopData->getReceiptTaxStatmentOne();
            $shop['receipt_tax_statement_two'] = $shopData->getReceiptTaxStatmentTwo();
            $shop['receipt_tax_statement_three'] = $shopData->getReceiptTaxStatmentThree();
            $shop['receipt_auto_print'] = ($shopData->getReceiptAutoPrint()) ? 1 : 0;
            $shop['vat_value'] = $shopData->getVatValue();
            $shop['currency_id'] = $shopData->getCurrencyId();
            $u = new Criteria();
            $u->addJoin(UserPeer::ID, ShopUsersPeer::USER_ID, Criteria::LEFT_JOIN);
            $u->add(ShopUsersPeer::SHOP_ID, $shopData->getId());
            $u->add(ShopUsersPeer::STATUS_ID, 3);
            $Susers = UserPeer::doSelect($u);
            $users = "";
            $i = 0;
            foreach ($Susers as $Suser) {


                $pos_role_id = "";
                $possupperuser = "";
                $pos_user_status_id = 5;
                $ct = new Criteria();
                $ct->add(ShopUsersPeer::USER_ID, $Suser->getId());
                $ct->add(ShopUsersPeer::SHOP_ID, $shopData->getId());
                $ct->add(ShopUsersPeer::STATUS_ID, 3);
                if (ShopUsersPeer::doCount($ct) == 1) {
                    $shopUser = ShopUsersPeer::doSelectOne($ct);
                    $pos_role_id = $shopUser->getPosRoleId();
                    $possupperuser = $shopUser->getPosSuperUser();
                    $pos_user_status_id = $shopUser->getStatusId();
                }



                $users[$i]['id'] = $Suser->getId();
                $users[$i]['name'] = $Suser->getName();
                $users[$i]['email'] = $Suser->getEmail();
                $users[$i]['password'] = $Suser->getPassword();
                $users[$i]['sur_name'] = $Suser->getSurName();
                $users[$i]['created_at'] = $Suser->getCreatedAt();

                $users[$i]['address'] = $Suser->getAddress();
                $users[$i]['zip'] = $Suser->getZip();
                $users[$i]['city'] = $Suser->getCity();
                $users[$i]['country'] = $Suser->getCountry();
                $users[$i]['tel'] = $Suser->getTel();
                $users[$i]['mobile'] = $Suser->getMobile();

                $users[$i]['pin'] = $Suser->getPin();
                $users[$i]['pin_status'] = $Suser->getPinStatus();

                $users[$i]['pos_user_role_id'] = $pos_role_id;
                $users[$i]['pos_super_user'] = ($possupperuser) ? 1 : 0;
                $users[$i]['status_id'] = $pos_user_status_id;

                $users[$i]['updated_by'] = $Suser->getUpdatedBy();
                $i++;
            }
            $psrc = new Criteria();
            $psrc->add(PosShopRolePeer::SHOP_ID, $shopData->getId());
            $psrc->addJoin(PosRolePeer::ID, PosShopRolePeer::POS_ROLE_ID, Criteria:: LEFT_JOIN);
            if (PosRolePeer::doCount($psrc) > 0) {
                $pos_shop_roles = PosRolePeer::doSelect($psrc);
                //  var_dump($pos_shop_roles);die;
                $r = 0;

                foreach ($pos_shop_roles as $pos_shop_role) {
                    $roles[$r]['id'] = $pos_shop_role->getId();
                    $roles[$r]['name'] = $pos_shop_role->getName();
                    $r++;
                    $cp = new Criteria();
                    $cp->add(PosRolePermissionRefPeer::POS_ROLE_ID, $pos_shop_role->getId());
                    $cp->addJoin(PosPermissionPeer::ID, PosRolePermissionRefPeer::POS_PERMISSION_ID, Criteria::LEFT_JOIN);
                    $p_cnt = PosPermissionPeer::doCount($cp);
                    if ($p_cnt > 0) {
                        $pospermissions = PosPermissionPeer::doSelect($cp);
                        $p = 0;
                        foreach ($pospermissions as $pospermission) {
                            $permissions[$p]["id"] = $pospermission->getId();
                            $permissions[$p]["action_name"] = $pospermission->getActionName();
                            $permissions[$p]["action_title"] = $pospermission->getActionTitle();
                            $permissions[$p]["pos_role_id"] = $pos_shop_role->getId();
                            $p++;
                        }
                    }
                }
            }



            $arayData = "";

            $arayData['branch']['detail'] = $shop;
            $arayData['branch']['user'] = $users;
            $arayData['branch']['roles'] = $roles;
            $arayData['branch']['permissions'] = $permissions;
            echo json_encode($arayData);
        } else {
            echo json_encode(array('status_code' => '0', 'msg' => 'Branch Number or password not correct'));
        }
        return sfView::NONE;
    }

    public function executeSyncBranchSettings(sfWebRequest $request) {
        $urlval = "SyncBranchSettings-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shopArray = "";
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setShopSettingSyncRequestedAt(time());
            $shop->save();
            $ic = new Criteria();
            $ic->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
            if ($shop->getShopSettingSyncSyncedAt() != "") {
                $ic->add(ShopsPeer::UPDATED_AT, $shop->getItemSyncSyncedAt(), Criteria::GREATER_EQUAL);
            }
            if (ShopsPeer::doCount($ic) > 0) {
                $shopData = ShopsPeer::doSelectOne($ic);
                $shopArray['negative_sale'] = $shopData->getNegativeSale();
                $shopArray['language_id'] = $shopData->getLanguageId();
                $shopArray['time_out'] = $shopData->getTimeOut();
                $shopArray['start_value_sale_receipt'] = $shopData->getStartValueSaleReceipt();
                $shopArray['start_value_return_receipt'] = $shopData->getStartValueReturnReceipt();
                $shopArray['sale_receipt_format_id'] = $shopData->getSaleReceiptFormatId();
                $shopArray['return_receipt_format_id'] = $shopData->getReturnReceiptFormatId();
                $shopArray['updated_at'] = $shopData->getUpdatedAt();

                $shopArray['receipt_header_position'] = $shopData->getReceiptHeaderPosition();
                $shopArray['receipt_tax_statement_one'] = $shopData->getReceiptTaxStatmentOne();
                $shopArray['receipt_tax_statement_two'] = $shopData->getReceiptTaxStatmentTwo();
                $shopArray['receipt_tax_statement_three'] = $shopData->getReceiptTaxStatmentThree();

                echo json_encode($shopArray);
            } else {
                $shopArray["status"] = "Update not found";
                echo json_encode($shopArray);
            }
        } else {
            $shopArray["status"] = "Shop not found";
            echo json_encode($shopArray);
        }
        return sfView::NONE;
    }

    public function executeSyncBranchSettingUpdate(sfWebRequest $request) {
        $urlval = "SyncSyncBranchSettingUpdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("id") . "id=" . $request->getParameter("id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setShopSettingSyncSyncedAt($shop->getShopSettingSyncRequestedAt());
            if ($shop->save()) {
                echo "OK";
            } else {
                echo "ERROR";
            }
        }
        return sfView::NONE;
    }

    public function executeUpdateBranchSettings(sfWebRequest $request) {
        $urlval = "UpdateBranchSettings-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("id") . "update_config=" . $request->getParameter("update_config"));
        $dibsCall->save();
        $jsonObj = json_decode($request->getParameter("update_config"));
        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setNegativeSale($jsonObj->negative_sale);
            $shop->setLanguageId($jsonObj->language_id);
            $shop->setTimeOut($jsonObj->time_out);
            $shop->setStartValueSaleReceipt($jsonObj->start_value_sale_receipt);
            $shop->setStartValueReturnReceipt($jsonObj->start_value_return_receipt);
            $shop->setMaxDayEndAttempts($jsonObj->max_day_end_attempts);
            $shop->setSaleReceiptFormatId($jsonObj->sale_receipt_format_id);
            $shop->setReturnReceiptFormatId($jsonObj->return_receipt_format_id);
            $shop->setDiscountValue($jsonObj->discount_value);
            $shop->setDiscountTypeId($jsonObj->discount_type_id);
            $shop->setReceiptHeaderPosition($jsonObj->receipt_header_position);
            $shop->setReceiptTaxStatmentOne($jsonObj->receipt_tax_statement_one);
            $shop->setReceiptTaxStatmentTwo($jsonObj->receipt_tax_statement_two);
            $shop->setReceiptTaxStatmentThree($jsonObj->receipt_tax_statement_three);
            $shop->setReceiptAutoPrint($jsonObj->receipt_auto_print);

            $shop->setBookoutFormatId($jsonObj->bookout_format_id);
            $shop->setStartValueBookout($jsonObj->start_value_bookout);

            $shop->save();
            echo "OK";
        } else {
            echo "ERROR";
        }
        return sfView::NONE;
    }

    public function executeSyncUser(sfWebRequest $request) {
        $urlval = "SyncUser-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $syncItems = "";
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setUserSyncRequestedAt(time());

            $shop->save();
            $u = new Criteria();
            $u->addJoin(UserPeer::ID, ShopUsersPeer::USER_ID, Criteria::LEFT_JOIN);
            $u->add(ShopUsersPeer::SHOP_ID, $shop->getId());
            if ($shop->getUserSyncSyncedAt() != "") {
                $u->addAnd(UserPeer::UPDATE_AT, $shop->getUserSyncSyncedAt(), Criteria::GREATER_EQUAL);
            } else {


                $Susers = UserPeer::doSelect($u);
                $users = "";
                $i = 0;
                foreach ($Susers as $Suser) {


                    $pos_role_id = "";
                    $possupperuser = "";
                    $pos_user_status_id = 5;

                    $ct = new Criteria();
                    $ct->add(ShopUsersPeer::USER_ID, $Suser->getId());
                    $ct->add(ShopUsersPeer::SHOP_ID, $shop->getId());
                    $ct->add(ShopUsersPeer::STATUS_ID, 3);
                    if (ShopUsersPeer::doCount($ct) == 1) {
                        $shopUser = ShopUsersPeer::doSelectOne($ct);
                        $pos_role_id = $shopUser->getPosRoleId();
                        $possupperuser = $shopUser->getPosSuperUser();
                        $pos_user_status_id = $shopUser->getStatusId();
                    }

                    $users[$i]['id'] = $Suser->getId();
                    $users[$i]['name'] = $Suser->getName();
                    $users[$i]['email'] = $Suser->getEmail();
                    $users[$i]['password'] = $Suser->getPassword();
                    $users[$i]['sur_name'] = $Suser->getSurName();
                    $users[$i]['created_at'] = $Suser->getCreatedAt();

                    $users[$i]['address'] = $Suser->getAddress();
                    $users[$i]['zip'] = $Suser->getZip();
                    $users[$i]['city'] = $Suser->getCity();
                    $users[$i]['country'] = $Suser->getCountry();
                    $users[$i]['tel'] = $Suser->getTel();
                    $users[$i]['mobile'] = $Suser->getMobile();

                    $users[$i]['pin'] = $Suser->getPin();
                    $users[$i]['pin_status'] = $Suser->getPinStatus();

                    $users[$i]['pos_user_role_id'] = $pos_role_id;
                    $users[$i]['pos_super_user'] = ($possupperuser) ? 1 : 0;
                    $users[$i]['updated_by'] = $Suser->getUpdatedBy();
                    $users[$i]['status_id'] = $pos_user_status_id;

                    $i++;
                }





                echo json_encode($users);
            }
        }
        return sfView::NONE;
    }

    public function executeSyncUserUpdate(sfWebRequest $request) {
        $urlval = "SyncUserUpdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("id") . "id=" . $request->getParameter("id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setUserSyncSyncedAt($shop->getUserSyncRequestedAt());
            if ($shop->save()) {
                echo "OK";
            } else {
                echo "ERROR";
            }
        }
        return sfView::NONE;
    }

    public function executeSyncPosCreatedUser(sfWebRequest $request) {
        $urlval = "SyncPosCreatedUser-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_creatuser=" . $request->getParameter("server_json_creatuser") . "shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");
        $json_from_Users = json_decode($request->getParameter("server_json_creatuser"));

        if ($shop = ShopsPeer::retrieveByPK($shop_id)) {
            
        } else {
            echo "ERROR";
            die;
        }

        foreach ($json_from_Users as $json_from_user) {


            $s = new Criteria();
            $s->add(UserPeer::ID, (int) $json_from_user->id);
            if (UserPeer::doCount($s) == 1) {
                $user = UserPeer::doSelectOne($s);
            } else {
                $user = new User();
            }

            $user->setId($json_from_user->id);
            $user->setName($json_from_user->name);
            $user->setEmail($json_from_user->email);
            $user->setPassword($json_from_user->password);
            $user->setStatusId($json_from_user->status_id);
            $user->setCreatedAt($json_from_user->created_at);
            $user->setSurName($json_from_user->sur_name);

            $user->setAddress($json_from_user->address);
            $user->setZip($json_from_user->zip);
            $user->setCity($json_from_user->city);
            $user->setCountry($json_from_user->country);
            $user->setTel($json_from_user->tel);
            $user->setMobile($json_from_user->mobile);
            $user->setUpdatedAt(time());

            if ($json_from_user->pin_status == 1) {
                $cuser = new Criteria();
                $cuser->add(UserPeer::PIN, $json_from_user->pin);
                $cuser->add(UserPeer::PIN_STATUS, 3);
                if (UserPeer::doCount($cuser) == 0) {
                    $user->setPinStatus(3);
                } else {
                    $user->setPinStatus(2);
                }
            }
            $user->setPin($json_from_user->pin);
//            $user->setPosUserRoleId($json_from_user->pos_user_role_id);
//            $user->setPosSuperUser($json_from_user->pos_super_user);


            $user->setUpdatedBy($json_from_user->updated_by);

            $user->save();


            $shop = new Criteria();
            $shop->add(ShopUsersPeer::USER_ID, (int) $user->getId());
            $shop->addAnd(ShopUsersPeer::SHOP_ID, (int) $shop_id);
            if (ShopUsersPeer::doCount($shop) == 1) {
                $shopuser = ShopUsersPeer::doSelectOne($shop);
            } else {
                $shopuser = new ShopUsers();
                $shopuser->setIsPrimary(1);
            }

            $shopuser->setUserId($user->getId());
            $shopuser->setShopId($shop_id);
            $shopuser->setStatusId(3);
            $shopuser->setPosRoleId($json_from_user->pos_user_role_id);
            $shopuser->setPosSuperUser($json_from_user->pos_super_user);
            $shopuser->save();
        }

        $cc = new Criteria();
        $cc->add(ShopUsersPeer::USER_ID, $user->getId());
        $shopUsers = ShopUsersPeer::doSelect($cc);
        $arrayShopIds = "";
        foreach ($shopUsers as $user) {
            $arrayShopIds[] = $user->getShopId();
        }


        $sc = new Criteria();
        $sc->add(ShopsPeer::ID, $arrayShopIds, Criteria::IN);
        $sc->addAnd(ShopsPeer::STATUS_ID, 3);
        $sc->addAnd(ShopsPeer::GCM_KEY, null, Criteria::ISNOTNULL);
        if (ShopsPeer::doCount($sc) > 0) {
            $shops = ShopsPeer::doSelect($sc);
            $gcmKeyArray = "";
            foreach ($shops as $shop) {
                $gcmKeyArray[] = $shop->getGcmKey();
            }
            new GcmLib("user_updated", $gcmKeyArray);
        }


        echo "OK";
        return sfView::NONE;
    }

    public function executeSyncPosUpdatedUser(sfWebRequest $request) {
        $urlval = "SyncPosUpdatedUser-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_updateuser=" . $request->getParameter("server_json_updateuser") . "&shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();
        $json_from_Users = json_decode($request->getParameter("server_json_updateuser"));



        foreach ($json_from_Users as $json_from_user) {


            $s = new Criteria();
            $s->add(UserPeer::ID, (int) $json_from_user->id);
            if (UserPeer::doCount($s) == 1) {
                $user = UserPeer::doSelectOne($s);

                $user->setName($json_from_user->name);
                $user->setEmail($json_from_user->email);
                $user->setPassword($json_from_user->password);

//                $user->setStatusId($json_from_user->status_id);
                $user->setCreatedAt($json_from_user->created_at);
                $user->setSurName($json_from_user->sur_name);

                $user->setAddress($json_from_user->address);
                $user->setZip($json_from_user->zip);
                $user->setCity($json_from_user->city);
                $user->setCountry($json_from_user->country);
                $user->setTel($json_from_user->tel);
                $user->setMobile($json_from_user->mobile);
                $user->setUpdatedAt(time());
                $user->setPin($json_from_user->pin);

                if ($json_from_user->pin_status == 1) {
                    $cuser = new Criteria();
                    $cuser->add(UserPeer::PIN, $json_from_user->pin);
                    $cuser->add(UserPeer::PIN_STATUS, 3);
                    if (UserPeer::doCount($cuser) == 0) {
                        $user->setPinStatus(3);
                    } else {
                        $user->setPinStatus(2);
                    }
                }




//                $user->setPosUserRoleId($json_from_user->pos_user_role_id);
//                $user->setPosSuperUser($json_from_user->pos_super_user);

                $user->setUpdatedBy($json_from_user->updated_by);

                $user->save();

                if ($request->getParameter("shop_id") != "") {
                    $shop = new Criteria();
                    $shop->add(ShopUsersPeer::USER_ID, (int) $user->getId());
                    $shop->addAnd(ShopUsersPeer::SHOP_ID, (int) $request->getParameter("shop_id"));
                    $shop->addAnd(ShopUsersPeer::STATUS_ID, 3);
                    if (ShopUsersPeer::doCount($shop) == 1) {
                        $shopuser = ShopUsersPeer::doSelectOne($shop);
                    } else {
                        $shopuser = new ShopUsers();
                    }

                    $shopuser->setUserId($user->getId());
                    $shopuser->setShopId($request->getParameter("shop_id"));
                    $shopuser->setStatusId($json_from_user->status_id);
                    $shopuser->setPosRoleId($json_from_user->pos_user_role_id);
                    $shopuser->setPosSuperUser($json_from_user->pos_super_user);
                    $shopuser->save();
                }

                $cc = new Criteria();
                $cc->add(ShopUsersPeer::USER_ID, $user->getId());
                $shopUsers = ShopUsersPeer::doSelect($cc);
                $arrayShopIds = "";
                foreach ($shopUsers as $user) {
                    $arrayShopIds[] = $user->getShopId();
                }


                $sc = new Criteria();
                $sc->add(ShopsPeer::ID, $arrayShopIds, Criteria::IN);
                $sc->addAnd(ShopsPeer::STATUS_ID, 3);
                $sc->addAnd(ShopsPeer::GCM_KEY, null, Criteria::ISNOTNULL);
                if (ShopsPeer::doCount($sc) > 0) {
                    $shops = ShopsPeer::doSelect($sc);
                    $gcmKeyArray = "";
                    foreach ($shops as $shop) {
                        $gcmKeyArray[] = $shop->getGcmKey();
                    }
                    new GcmLib("user_updated", $gcmKeyArray);
                }


                echo "OK";
            } else {
                echo "ERROR";
            }
        }
        return sfView::NONE;
    }

    public function executeSyncBranchInfo(sfWebRequest $request) {
        $urlval = "SyncBranchInfo-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("branch_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, $request->getParameter("branch_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shopData = ShopsPeer::doSelectOne($s);

            $shop['id'] = $shopData->getId();
            $shop['name'] = $shopData->getName();
            $shop['branch_number'] = $shopData->getBranchNumber();
            $shop['company_number'] = $shopData->getCompanyNumber();
            $shop['password'] = $shopData->getPassword();
            $shop['address'] = $shopData->getAddress();
            $shop['zip'] = $shopData->getZip();
            $shop['place'] = $shopData->getPlace();
            $shop['country'] = $shopData->getCountry();
            $shop['tel'] = $shopData->getTel();
            $shop['fax'] = $shopData->getFax();
            $shop['status_id'] = $shopData->getStatusId();
            $shop['max_day_end_attempts'] = $shopData->getMaxDayEndAttempts();
            $shop['negative_sale'] = $shopData->getNegativeSale();
            $shop['language_id'] = $shopData->getLanguageId();
            $shop['time_out'] = $shopData->getTimeOut();
            $shop['start_value_sale_receipt'] = $shopData->getStartValueSaleReceipt();
            $shop['start_value_return_receipt'] = $shopData->getStartValueReturnReceipt();
            $shop['sale_receipt_format_id'] = $shopData->getSaleReceiptFormatId();
            $shop['return_receipt_format_id'] = $shopData->getReturnReceiptFormatId();

            $shop['bookout_format_id'] = $shopData->getBookoutFormatId();
            $shop['start_value_bookout'] = $shopData->getStartValueBookout();

            $shop['updated_at'] = $shopData->getUpdatedAt();
            $shop['discount_value'] = $shopData->getDiscountValue();
            $shop['discount_type_id'] = $shopData->getDiscountTypeId();

            $shop['receipt_header_position'] = $shopData->getReceiptHeaderPosition();
            $shop['receipt_tax_statement_one'] = $shopData->getReceiptTaxStatmentOne();
            $shop['receipt_tax_statement_two'] = $shopData->getReceiptTaxStatmentTwo();
            $shop['receipt_tax_statement_three'] = $shopData->getReceiptTaxStatmentThree();
            $shop['receipt_auto_print'] = ($shopData->getReceiptAutoPrint()) ? 1 : 0;
            $shop['vat_value'] = $shopData->getVatValue();
            $shop['currency_id'] = $shopData->getCurrencyId();
            $u = new Criteria();
            $u->addJoin(UserPeer::ID, ShopUsersPeer::USER_ID, Criteria::LEFT_JOIN);
            $u->add(ShopUsersPeer::SHOP_ID, $shopData->getId());
            $u->add(ShopUsersPeer::STATUS_ID, 3);
            $Susers = UserPeer::doSelect($u);
            $users = "";
            $i = 0;
            foreach ($Susers as $Suser) {


                $pos_role_id = "";
                $possupperuser = "";
                $pos_user_status_id = 5;

                $ct = new Criteria();
                $ct->add(ShopUsersPeer::USER_ID, $Suser->getId());
                $ct->add(ShopUsersPeer::SHOP_ID, $shopData->getId());
                $ct->add(ShopUsersPeer::STATUS_ID, 3);   // Removed as it is needed on the POS to mark the deleted user from the web :)
                if (ShopUsersPeer::doCount($ct) == 1) {
                    $shopUser = ShopUsersPeer::doSelectOne($ct);
                    $pos_role_id = $shopUser->getPosRoleId();
                    $possupperuser = $shopUser->getPosSuperUser();
                    $pos_user_status_id = $shopUser->getStatusId();
                }



                $users[$i]['id'] = $Suser->getId();
                $users[$i]['name'] = $Suser->getName();
                $users[$i]['email'] = $Suser->getEmail();
                $users[$i]['password'] = $Suser->getPassword();
                $users[$i]['sur_name'] = $Suser->getSurName();
                $users[$i]['created_at'] = $Suser->getCreatedAt();

                $users[$i]['address'] = $Suser->getAddress();
                $users[$i]['zip'] = $Suser->getZip();
                $users[$i]['city'] = $Suser->getCity();
                $users[$i]['country'] = $Suser->getCountry();
                $users[$i]['tel'] = $Suser->getTel();
                $users[$i]['mobile'] = $Suser->getMobile();
                $users[$i]['pin'] = $Suser->getPin();
                $users[$i]['pin_status'] = $Suser->getPinStatus();


                $users[$i]['pos_user_role_id'] = $pos_role_id;
                $users[$i]['pos_super_user'] = ($possupperuser) ? 1 : 0;
                $users[$i]['status_id'] = $pos_user_status_id;


                $users[$i]['updated_by'] = $Suser->getUpdatedBy();
                $i++;
            }


            $arayData = "";

            $arayData['branch']['detail'] = $shop;
            $arayData['branch']['user'] = $users;

            echo json_encode($arayData);
        } else {
            
        }
        return sfView::NONE;
    }

    public function executeSyncAddRole(sfWebRequest $request) {
        $urlval = "SyncAddRole-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_posrole=" . $request->getParameter("server_json_posrole"));
        $dibsCall->save();
//        $json_from_addroles = json_encode(array('role_id'=>'56456489789','name'=>'testing','branch_number'=>'4046108000049'));
//        $json_from_addroles = json_decode($json_from_addroles);
        $json_from_addroles = json_decode($request->getParameter("server_json_posrole"));
        if ($json_from_addroles->role_id && $json_from_addroles->name && $json_from_addroles->shop_id) {
            $pos_role = PosRolePeer::retrieveByPK($json_from_addroles->role_id);
            if ($pos_role) {
                $pos_role->setId($json_from_addroles->role_id);
                $pos_role->setName($json_from_addroles->name);
                $pos_role->save();
            } else {
                $pos_role = new PosRole();
                $pos_role->setId($json_from_addroles->role_id);
                $pos_role->setName($json_from_addroles->name);
                $pos_role->save();
            }
            $ps = new Criteria();
            $ps->add(PosShopRolePeer::POS_ROLE_ID, $pos_role->getId());
            $ps->add(PosShopRolePeer::SHOP_ID, $json_from_addroles->shop_id);
            $pos_role_count = PosShopRolePeer::doCount($ps);
            if ($pos_role_count == 0) {
                $pos_role_shop = new PosShopRole();
                $pos_role_shop->setPosRoleId($pos_role->getId());
                $pos_role_shop->setShopId($json_from_addroles->shop_id);
                $pos_role_shop->setStatusId(3);
                $pos_role_shop->save();
            } else {
                
            }

            echo json_encode(array('status_code' => '1', 'msg' => 'Role Added'));
        } else {
            echo json_encode(array('status_code' => '0', 'msg' => 'Required info not provided'));
        }
        return sfView::NONE;
    }

    public function executeSyncAddPermissions(sfWebRequest $request) {
        $urlval = "SyncAddPermissions-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("role_id=" . $request->getParameter("role_id") . "&permission_id=" . $request->getParameter("permission_id"));
        $dibsCall->save();

        $role_id = $request->getParameter("role_id");
        $permission_ids = $request->getParameter("permission_id");
        $permission_ids = explode(",", $permission_ids);
        if ($role_id && $permission_ids) {
            $role = PosRolePeer::retrieveByPK($role_id);
            $same_permission = array();
            if ($role) {
                $cr = new Criteria();
                $cr->add(PosRolePermissionRefPeer::POS_ROLE_ID, $role_id);
                $current_count = PosRolePermissionRefPeer::doCount($cr);
                if ($current_count > 0) {
                    $current_permissions = PosRolePermissionRefPeer::doSelect($cr);
                    foreach ($current_permissions as $current_permission) {
                        $curr_perm_id = $current_permission->getPosPermissionId();
                        if (!in_array($curr_perm_id, $permission_ids)) {
//                            $current_permission->delete();
                        } else {
                            $same_permission[] = $curr_perm_id;
                        }
                    }
                }
                for ($i = 0; $i < count($permission_ids); $i++) {
                    if (!in_array($permission_ids[$i], $same_permission)) {
                        $pos_permission_ref = new PosRolePermissionRef();
                        $pos_permission_ref->setPosPermissionId($permission_ids[$i]);
                        $pos_permission_ref->setPosRoleId($role->getId());
                        $pos_permission_ref->save();
                    }
                }
                echo json_encode(array('status_code' => '1', 'msg' => 'Permissions set'));
            } else {
                echo json_encode(array('status_code' => '0', 'msg' => 'Role does not exist'));
            }
        } else {
            echo json_encode(array('status_code' => '0', 'msg' => 'Required info not provided'));
        }


        return sfView::NONE;
    }

    /*
     * Basically Modules and permissions are not being added or updated so We can't ask pos to sync it. 
     * POS will impliment module_permissions_updated and we need to send request to the server.
     */

    public function executeSyncModulesPermissions(sfWebRequest $request) {
        $urlval = "SyncModulesPermissions-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->save();

        $modules = array();
        $permissions = array();
        $pos_modules = PosModulesPeer::doSelect(new Criteria());
        $m = 0;
        foreach ($pos_modules as $pos_module) {
            $modules[$m]["id"] = $pos_module->getId();
            $modules[$m]["title"] = $pos_module->getTitle();
            $m++;
        }
        $pos_perms = PosPermissionPeer::doSelect(new Criteria());
        $p = 0;
        foreach ($pos_perms as $pos_perm) {
            $permissions[$p]["id"] = $pos_perm->getId();
            $permissions[$p]["pos_module_id"] = $pos_perm->getPosModuleId();
            $permissions[$p]["action_name"] = $pos_perm->getActionName();
            $permissions[$p]["action_title"] = $pos_perm->getActionTitle();
            $p++;
        }
        $arayData['user_rights']['modules'] = $modules;
        $arayData['user_rights']['permissions'] = $permissions;

        echo json_encode($arayData);
        return sfView::NONE;
    }

    public function executeSyncDeletePermissions(sfWebRequest $request) {
        $urlval = "SyncDeletePermissions-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("role_id=" . $request->getParameter("role_id") . "&permission_id=" . $request->getParameter("permission_id"));
        $dibsCall->save();

        $role_id = $request->getParameter("role_id");
        $permission_ids = $request->getParameter("permission_id");
        $permission_ids = explode(",", $permission_ids);
        if ($role_id && $permission_ids) {
            $role = PosRolePeer::retrieveByPK($role_id);
            if ($role) {
                $cr = new Criteria();
                $cr->add(PosRolePermissionRefPeer::POS_ROLE_ID, $role_id);
                $current_count = PosRolePermissionRefPeer::doCount($cr);
                if ($current_count > 0) {
                    $current_permissions = PosRolePermissionRefPeer::doSelect($cr);
                    foreach ($current_permissions as $current_permission) {
                        $curr_perm_id = $current_permission->getPosPermissionId();
                        if (in_array($curr_perm_id, $permission_ids)) {
                            $current_permission->delete();
                        } else {
                            
                        }
                    }
                }

                echo json_encode(array('status_code' => '1', 'msg' => 'Role permission updated'));
            } else {
                echo json_encode(array('status_code' => '0', 'msg' => 'Role does not exist'));
            }
        } else {
            echo json_encode(array('status_code' => '0', 'msg' => 'Required info not provided'));
        }


        return sfView::NONE;
    }

    public function executeSyncBranchRole(sfWebRequest $request) {
        $urlval = "SyncBranchRole-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();
        $roles = array();
        $permissions = array();
        $shop_id = $request->getParameter("shop_id");
        $s = new Criteria();
        $s->add(ShopsPeer::ID, $shop_id);
        if (ShopsPeer::doCount($s) == 1) {

//            $psrc = new Criteria();
//            $psrc->add(PosShopRolePeer::SHOP_ID, $shop_id);
//            $psrc->addAnd(PosShopRolePeer::STATUS_ID,3 ,Criteria::EQUAL);
//            $psrc->addJoin(PosRolePeer::ID, PosShopRolePeer::POS_ROLE_ID, Criteria:: LEFT_JOIN);
//            $cnt_role = PosRolePeer::doCount($psrc);
//            if ($cnt_role > 0) {
//                $pos_shop_roles = PosRolePeer::doSelect($psrc);
//                //  var_dump($pos_shop_roles);die;
//                $r = 0;
//
//                foreach ($pos_shop_roles as $pos_shop_role) {
//                    $roles[$r]['id'] = $pos_shop_role->getId();
//                    $roles[$r]['name'] = $pos_shop_role->getName();
//                    
//                    $cp = new Criteria();
//                    $cp->add(PosRolePermissionRefPeer::POS_ROLE_ID, $pos_shop_role->getId());
//                    $cp->addJoin(PosPermissionPeer::ID, PosRolePermissionRefPeer::POS_PERMISSION_ID, Criteria::LEFT_JOIN);
//                    $p_cnt = PosPermissionPeer::doCount($cp);
////                  echo "<br />";
//                    if ($p_cnt > 0) {
//                        $pospermissions = PosPermissionPeer::doSelect($cp);
//                        $p = 0;
//                        foreach ($pospermissions as $pospermission) {
//                            $permissions[$r][$p]["id"] = $pospermission->getId();
//                            $permissions[$r][$p]["action_name"] = $pospermission->getActionName();
//                            $permissions[$r][$p]["action_title"] = $pospermission->getActionTitle();
//                            $permissions[$r][$p]["pos_role_id"] = $pos_shop_role->getId();
//                            $p++;
////                            echo "<br />";
////                            echo $p;
////                            echo "<br />";
//                        }
//                        $r++;
//                    }
//                }
////                
//            }

            $psrc = new Criteria();
            $psrc->add(PosShopRolePeer::SHOP_ID, $shop_id);
            $psrc->addAnd(PosShopRolePeer::STATUS_ID, 3, Criteria::EQUAL);
            $psrc->addJoin(PosRolePeer::ID, PosShopRolePeer::POS_ROLE_ID, Criteria:: LEFT_JOIN);
            $cnt_role = PosRolePeer::doCount($psrc);
            if ($cnt_role > 0) {
                $pos_shop_roles = PosRolePeer::doSelect($psrc);
                //  var_dump($pos_shop_roles);die;
                $r = 0;
                $p = 0;
                foreach ($pos_shop_roles as $pos_shop_role) {
                    $roles[$r]['id'] = $pos_shop_role->getId();
                    $roles[$r]['name'] = $pos_shop_role->getName();

                    $cp = new Criteria();
                    $cp->add(PosRolePermissionRefPeer::POS_ROLE_ID, $pos_shop_role->getId());
                    $cp->addJoin(PosPermissionPeer::ID, PosRolePermissionRefPeer::POS_PERMISSION_ID, Criteria::LEFT_JOIN);
                    $p_cnt = PosPermissionPeer::doCount($cp);
//                  echo "<br />";
                    if ($p_cnt > 0) {
                        $pospermissions = PosPermissionPeer::doSelect($cp);

                        foreach ($pospermissions as $pospermission) {
                            $permissions[$p]["id"] = $pospermission->getId();
                            $permissions[$p]["action_name"] = $pospermission->getActionName();
                            $permissions[$p]["action_title"] = $pospermission->getActionTitle();
                            $permissions[$p]["pos_role_id"] = $pos_shop_role->getId();
                            $p++;
//                            echo "<br />";
//                            echo $p;
//                            echo "<br />";
                        }
                    }
                    $r++;
                }
//                
            }
            $arayData = "";

            $arayData['branch']['roles'] = $roles;
            $arayData['branch']['permissions'] = $permissions;
            echo json_encode($arayData);
        } else {
            echo json_encode(array('status_code' => '0', 'msg' => 'Branch not found'));
        }
        return sfView::NONE;
    }

    public function executeSyncVouchersPut(sfWebRequest $request) {
        $urlval = "SyncVouchersPut-" . $request->getURI();

        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "&vouchers_json=" . $request->getParameter("vouchers_json"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");
        $vouchers_json = json_decode($request->getParameter("vouchers_json"));
        $s = new Criteria();
        $s->add(ShopsPeer::ID, $shop_id);
        if (ShopsPeer::doCount($s) == 1) {
            $shopObj = ShopsPeer::doSelectOne($s);
            $voucher_ids = "";
            foreach ($vouchers_json as $voucher) {
                $c = new Criteria();
                $c->add(VoucherPeer::ID, $voucher->id);
                $voucher_ids[] = $voucher->id;
                if (VoucherPeer::doCount($c) == 1) {
                    //Existing/Update Voucher
                    $db_voucher = VoucherPeer::doSelectOne($c);
                    if ($db_voucher->getIsUsed()) {
                        $reShop = ShopsPeer::retrieveByPK($db_voucher->getUsedShopId());
                        $message = "Voucher " . $db_voucher->getId() . " already used at shop" . $reShop->getBranchNumber() . " but now again used at shop:" . $shopObj->getBranchNumber();

                        emailLib::sendErrorInVoucherSync($message);

                        //trigger error email
                        continue;
                    } else {
                        $db_voucher->setIsUsed($voucher->is_used);
                        $db_voucher->setUsedAmount($voucher->used_amount);
                        $db_voucher->setShopUpdatedAt($voucher->shop_updated_at);
                        $db_voucher->setUsedShopTransactionId($voucher->used_shop_transaction_id);
                        $db_voucher->setUsedShopId($shop_id);
                        $db_voucher->setShopUpdatedAt($voucher->shop_used_at);
                        $db_voucher->setShopUsedAt($voucher->shop_used_at);
                        $db_voucher->setCreatedDayStartId($voucher->created_day_start_id);
                        $db_voucher->setUsedUserId($voucher->used_user_id);
                        $db_voucher->save();
                    }
                } else {
                    //New Voucher
                    $db_voucher = new Voucher();
                    $db_voucher->setId($voucher->id);
                    $db_voucher->setAmount($voucher->amount);
                    $db_voucher->setShopCreatedAt($voucher->shop_created_at);
                    $db_voucher->setShopUpdatedAt($voucher->shop_updated_at);
                    $db_voucher->setParentId($voucher->parent_id);
                    $db_voucher->setCreatedShopId($shop_id);
                    $db_voucher->setCreatedShopTransactionId($voucher->created_shop_transaction_id);
                    $db_voucher->setCreatedUserId($voucher->created_user_id);
                    $db_voucher->setUsedDayStartId($voucher->used_day_start_id);
                    $db_voucher->save();
                }
            }
            $sc = new Criteria();
            $sc->add(ShopsPeer::ID, $shop_id, Criteria::NOT_EQUAL);
            $sc->addAnd(ShopsPeer::STATUS_ID, 3);
            $sc->addAnd(ShopsPeer::GCM_KEY, null, Criteria::ISNOTNULL);
            if (ShopsPeer::doCount($sc) > 0) {
                $shops = ShopsPeer::doSelect($sc);
                $gcmKeyArray = "";
                foreach ($shops as $shop) {
                    $gcmKeyArray[] = $shop->getGcmKey();
                }
                new GcmLib("voucher_updated", $gcmKeyArray);
            }
            echo implode(",", $voucher_ids);
        } else {
            echo json_encode(array('status_code' => '0', 'msg' => 'Branch not found'));
        }
        return sfView::NONE;
    }

    public function executeSyncVouchersGet(sfWebRequest $request) {
        $urlval = "SyncVouchersGet-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_it=" . $request->getParameter("shop_id") . "&sync_type=" . $request->getParameter("sync_type"));
        $dibsCall->save();
        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setVoucherSyncRequestedAt(time());
            $shop->save();
            $ic = new Criteria();
            $sync_type = $request->getParameter("sync_type"); //all will be sending all the parameters
            if ($sync_type != 'all') {
                if ($shop->getVoucherSyncSyncedAt() != "") {
                    $ic->add(VoucherPeer::UPDATED_AT, $shop->getVoucherSyncSyncedAt(), Criteria::GREATER_EQUAL);
                }
            }

            if (VoucherPeer::doCount($ic) > 0) {
                $vouchers = VoucherPeer::doSelect($ic);
                $i = 0;
                $voucher_array = "";
                foreach ($vouchers as $voucher) {
                    $voucher_array[$i]['id'] = $voucher->getId();
                    $voucher_array[$i]['amount'] = $voucher->getAmount();
                    $voucher_array[$i]['used_amount'] = $voucher->getUsedAmount();

                    $voucher_array[$i]['created_shop_id'] = $voucher->getCreatedShopId();
                    $voucher_array[$i]['used_shop_id'] = $voucher->getUsedShopId();

                    $voucher_array[$i]['created_shop_transaction_id'] = $voucher->getCreatedShopTransactionId();
                    $voucher_array[$i]['used_shop_transaction_id'] = $voucher->getUsedShopTransactionId();

                    $voucher_array[$i]['parent_id'] = $voucher->getParentId();

                    $voucher_array[$i]['shop_created_at'] = $voucher->getShopCreatedAt();
                    $voucher_array[$i]['shop_updated_at'] = $voucher->getShopUpdatedAt();
                    $voucher_array[$i]['shop_used_at'] = $voucher->getShopUsedAt();

                    $voucher_array[$i]['created_day_start_id'] = $voucher->getCreatedDayStartId();
                    $voucher_array[$i]['used_day_start_id'] = $voucher->setUsedDayStartId();

                    $voucher_array[$i]['created_user_id'] = $voucher->getCreatedUserId();
                    $voucher_array[$i]['used_user_id'] = $voucher->getUsedUserId();

                    $voucher_array[$i]['is_used'] = ($voucher->getIsUsed()) ? 1 : 0;

                    $i++;
                }
                echo json_encode($voucher_array);
            } else {
                echo 'No Data Found to Sync';
            }
        } else {
            echo 'Branch not found';
        }

        return sfView::NONE;
    }

    public function executeSyncVouchersGetUpdate(sfWebRequest $request) {
        $urlval = "SyncVouchersGetUpdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("id") . "id=" . $request->getParameter("id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setVoucherSyncSyncedAt($shop->getVoucherSyncRequestedAt());
            if ($shop->save()) {
                echo "OK";
            } else {
                echo "ERROR";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeSyncGetReceiptTransactions(sfWebRequest $request) {
        $urlval = "executeSyncGetReceiptTransactions-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "receipt_number=" . $request->getParameter("receipt_number"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();
            $c->add(TransactionsPeer::SHOP_RECEIPT_NUMBER_ID, $request->getParameter("receipt_number"));
            $c->add(TransactionsPeer::TRANSACTION_TYPE_ID, 3);
            $c->add(TransactionsPeer::STATUS_ID, 3);
            if (TransactionsPeer::doCount($c) > 0) {
                $transactions = TransactionsPeer::doSelect($c);
                $jsonTransaction = "";
                $i = 0;
                foreach ($transactions as $transaction) {
                    $jsonTransaction[$i]['transaction_type_id'] = $transaction->getTransactionTypeId();
                    $jsonTransaction[$i]['shop_id'] = $transaction->getShopId();
                    $jsonTransaction[$i]['pos_id'] = $transaction->getShopTransactionId();
                    $jsonTransaction[$i]['quantity'] = $transaction->getQuantity();
                    $jsonTransaction[$i]['item_id'] = $transaction->getItemId();
                    $jsonTransaction[$i]['order_number_id'] = $transaction->getShopOrderNumberId();
                    $jsonTransaction[$i]['shop_receipt_id'] = $transaction->getShopReceiptNumberId();
                    $jsonTransaction[$i]['status_id'] = $transaction->getStatusId();
                    $jsonTransaction[$i]['created_at'] = $transaction->getCreatedAt();
                    $jsonTransaction[$i]['updated_at'] = $transaction->getUpdatedAt();
                    $jsonTransaction[$i]['discount_type_id'] = $transaction->getDiscountTypeId();
                    $jsonTransaction[$i]['discount_value'] = $transaction->getDiscountValue();
                    $jsonTransaction[$i]['parent_type'] = $transaction->getParentType();
                    $jsonTransaction[$i]['item_cms_id'] = $transaction->getCmsItemId();
                    $jsonTransaction[$i]['parent_type_id'] = $transaction->getParentTypeId();
                    $jsonTransaction[$i]['sold_price'] = $transaction->getSoldPrice();
                    $jsonTransaction[$i]['description1'] = $transaction->getDescription1();
                    $jsonTransaction[$i]['description2'] = $transaction->getDescription2();
                    $jsonTransaction[$i]['description3'] = $transaction->getDescription3();
                    $jsonTransaction[$i]['supplier_item_number'] = $transaction->getSupplierItemNumber();
                    $jsonTransaction[$i]['supplier_number'] = $transaction->getSupplierNumber();
                    $jsonTransaction[$i]['ean'] = $transaction->getEan();
                    $jsonTransaction[$i]['color'] = $transaction->getColor();
                    $jsonTransaction[$i]['group'] = $transaction->getGroup();
                    $jsonTransaction[$i]['size'] = $transaction->getSize();
                    $jsonTransaction[$i]['selling_price'] = $transaction->getSellingPrice();
                    $jsonTransaction[$i]['buying_price'] = $transaction->getBuyingPrice();
                    $jsonTransaction[$i]['taxation_code'] = $transaction->getTaxationCode();
                    $jsonTransaction[$i]['user_id'] = $transaction->getUserId();
                    $i++;
                }
                echo json_encode($jsonTransaction);
            } else {
                echo "No Transactions Found";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeSyncDayStarts(sfWebRequest $request) {


        $urlval = "executeSyncDayStarts-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "&day_starts_json=" . $request->getParameter("day_starts_json"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");
        $day_starts_json = json_decode($request->getParameter("day_starts_json"));

        $i = 0;
        $a = "";
        $dayStartIds = "";
        $dayAttemptIds = "";
        $dayStartDenominationIds = "";
        foreach ($day_starts_json as $day_start_json) {
            $co = new Criteria();
            $co->add(DayStartsPeer::ID, $day_start_json->id);
            if (DayStartsPeer::doCount($co) == 0) {
                $daystart = new DayStarts();
                $daystart->setId($day_start_json->id);
            } else {
                $daystart = DayStartsPeer::doSelectOne($co);
            }

            $daystart->setDayStartedAt($day_start_json->day_started_at);
            $daystart->setDayStartedBy($day_start_json->day_started_by);
            $daystart->setIsDayClosed($day_start_json->is_day_closed);
            $daystart->setShopId($shop_id);
            $daystart->setTotalAmount($day_start_json->total_amount);
            $daystart->setExpectedAmount($day_start_json->expected_amount);
            //   $daystart->setSuccess($day_start_json->success);
            if ($daystart->save()) {
                $dayStartIds[] = $daystart->getId();
            }
            ////////////////////////////////////////day starta attempts ////////////////////////////////////
            foreach ($day_start_json->day_start_attempts as $day_start_attempt) {
                $dsa = new Criteria();
                $dsa->add(DayStartsAttemptsPeer::ID, $day_start_attempt->id);
                if (DayStartsAttemptsPeer::doCount($dsa) == 0) {
                    $daystartAttempts = new DayStartsAttempts();
                    $daystartAttempts->setId($day_start_attempt->id);
                } else {
                    $daystartAttempts = DayStartsAttemptsPeer::doSelectOne($dsa);
                }


                $daystartAttempts->setExpectedAmount($day_start_attempt->expected_amount);
                $daystartAttempts->setDayStartId($day_start_attempt->day_start_id);

                $daystartAttempts->setShopId($shop_id);

                $daystartAttempts->setTotalAmount($day_start_attempt->total_amount);

                if ($daystartAttempts->save()) {
                    $dayAttemptIds[] = $daystartAttempts->getId();
                }
            }

            ////////////////////////////////////////day starta attempts ////////////////////////////////////       


            foreach ($day_start_json->day_start_denominations as $day_start_denomination) {
                //   var_dump($day_start_denomination);
                //      echo "<hr/>";
                //   die;
                $cop = new Criteria();
                $cop->add(DayStartDenominationsPeer::ID, $day_start_denomination->id);
                if (DayStartDenominationsPeer::doCount($cop) == 0) {
                    $daystartDenomination = new DayStartDenominations();
                    $daystartDenomination->setId($day_start_denomination->id);
                } else {
                    $daystartDenomination = DayStartDenominationsPeer::doSelectOne($cop);
                }

                $daystartDenomination->setCount($day_start_denomination->count);
                $daystartDenomination->setDayAttemptId($day_start_denomination->day_start_attempt_id);
                $daystartDenomination->setDayStartId($day_start_denomination->day_start_id);
                $daystartDenomination->setAmount($day_start_denomination->amount);
                $daystartDenomination->setDenominationId($day_start_denomination->denomination_id);
                //  var_dump($daystartDenomination->save());
                if ($daystartDenomination->save()) {
                    $dayStartDenominationIds[] = $daystartDenomination->getId();
                    echo $daystartDenomination->getId();
                }
            }
            $i++;
        }
        $a = implode(",", $dayStartIds) . ":" . implode(",", $dayStartDenominationIds);
        echo json_encode($a);
        return sfView::NONE;
    }

    public function executeSyncDayEnds(sfWebRequest $request) {


        $urlval = "executeSyncDayEnds-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "&day_ends_json=" . $request->getParameter("day_ends_json"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");
        $day_ends_json = json_decode($request->getParameter("day_ends_json"));

        $i = 0;
        $a = "";
        $dayEndIds = "";
        $dayEndDenominationIds = "";
        foreach ($day_ends_json as $day_end_json) {
            $co = new Criteria();
            $co->add(DayEndsPeer::ID, $day_end_json->id);
            if (DayEndsPeer::doCount($co) == 0) {
                $dayend = new DayEnds();
                $dayend->setId($day_end_json->id);
            } else {
                $dayend = DayEndsPeer::doSelectOne($co);
            }

            $dayend->setDayEndedAt($day_end_json->day_ended_at);
            $dayend->setDayEndedBy($day_end_json->day_ended_by);
            $dayend->setExpectedAmount($day_end_json->expected_amount);
            $dayend->setDayStartId($day_end_json->day_start_id);
            $dayend->setSuccess($day_end_json->success);
            $dayend->setShopId($shop_id);
            $dayend->setTotalAmount($day_end_json->total_amount);

            if ($dayend->save()) {
                $dayEndIds[] = $dayend->getId();
            }

            foreach ($day_end_json->day_end_denominations as $day_end_denomination) {
                $cop = new Criteria();
                $cop->add(DayEndDenominationsPeer::ID, $day_end_denomination->id);
                if (DayEndDenominationsPeer::doCount($cop) == 0) {
                    $dayendDenomination = new DayEndDenominations();
                    $dayendDenomination->setId($day_end_denomination->id);
                } else {
                    $dayendDenomination = DayEndDenominationsPeer::doSelectOne($cop);
                }
                $dayendDenomination->setCount($day_end_denomination->count);
                $dayendDenomination->setDayEndId($day_end_denomination->day_end_id);
                $dayendDenomination->setAmount($day_end_denomination->amount);
                $dayendDenomination->setDenominationId($day_end_denomination->denomination_id);
                if ($dayendDenomination->save()) {
                    $dayEndDenominationIds[] = $dayendDenomination->getId();
                }
            }
            $i++;
        }
        $a = implode(",", $dayEndIds) . ":" . implode(",", $dayEndDenominationIds);
        echo json_encode($a);
        return sfView::NONE;
    }

    public function executeSyncGcmKey(sfWebRequest $request) {

        $urlval = "executeSyncGcmKey-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "&gcm_key=" . $request->getParameter("gcm_key"));
        $dibsCall->save();
        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            if ($request->getParameter("gcm_key") != "") {
                $shop = ShopsPeer::doSelectOne($s);
                $shop->setGcmKey($request->getParameter("gcm_key"));
                $shop->save();
                echo "OK";
            }
        }

        return sfView::NONE;
    }

    public function executeSyncPromotion(sfWebRequest $request) {
        $urlval = "SyncPromotions-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setPromotionSyncRequestedAt(time());

            $shop->save();

            $i = new Criteria();
            //  $i->add(PromotionPeer:: PROMOTION_STATUS, 3);
            if ($shop->getPromotionSyncRequestedAt() != "") {
                $i->addAnd(PromotionPeer::UPDATED_AT, $shop->getPromotionSyncSyncedAt(), Criteria::GREATER_EQUAL);
            }
            $i->addAnd(PromotionPeer::END_DATE, date("Y-m-d"), Criteria::GREATER_EQUAL);
            $syncPromotions = PromotionPeer::doSelect($i);

            $promotions = "";
            $i = 0;
            foreach ($syncPromotions as $syncPromotion) {

                $branchIds = explode(",", $syncPromotion->getBranchId());

                //   var_dump($branchIds);
                if (in_array($request->getParameter("shop_id"), $branchIds)) {


                    $promotions[$i]['id'] = $syncPromotion->getId();
                    $promotions[$i]['promotion_title'] = $syncPromotion->getPromotionTitle();
                    $promotions[$i]['start_date'] = $syncPromotion->getStartDate();
                    $promotions[$i]['end_date'] = $syncPromotion->getEndDate();
                    $promotions[$i]['on_all_item'] = $syncPromotion->getOnAllItem();
                    $promotions[$i]['promotion_value'] = $syncPromotion->getPromotionValue();
                    $promotions[$i]['promotion_type'] = $syncPromotion->getPromotionType();
                    $promotions[$i]['created_at'] = $syncPromotion->getCreatedAt();
                    $promotions[$i]['updated_by'] = $syncPromotion->getUpdatedBy();
                    $promotions[$i]['promotion_status'] = $syncPromotion->getPromotionStatus();
                    $promotions[$i]['updated_at'] = $syncPromotion->getUpdatedAt();
                    $promotions[$i]['item_id_type'] = $syncPromotion->getItemIdType();
                    $promotions[$i]['item_id'] = $syncPromotion->getItemId();
                    $promotions[$i]['item_id_to'] = $syncPromotion->getItemIdTo();
                    $promotions[$i]['item_id_from'] = $syncPromotion->getItemIdFrom();
                    $promotions[$i]['description1'] = $syncPromotion->getDescription1();
                    $promotions[$i]['description2'] = $syncPromotion->getDescription2();
                    $promotions[$i]['description3'] = $syncPromotion->getDescription3();
                    $promotions[$i]['size'] = $syncPromotion->getSize();
                    $promotions[$i]['color'] = $syncPromotion->getColor();
                    $promotions[$i]['group_type'] = $syncPromotion->getGroupType();
                    $promotions[$i]['group_name'] = $syncPromotion->getGroupName();
                    $promotions[$i]['group_to'] = $syncPromotion->getGroupTo();
                    $promotions[$i]['group_from'] = $syncPromotion->getGroupFrom();
                    $promotions[$i]['price_type'] = $syncPromotion->getPriceType();
                    $promotions[$i]['price_less'] = $syncPromotion->getPriceLess();
                    $promotions[$i]['price_greater'] = $syncPromotion->getPriceGreater();
                    $promotions[$i]['price_to'] = $syncPromotion->getPriceTo();
                    $promotions[$i]['price_from'] = $syncPromotion->getPriceFrom();
                    $promotions[$i]['supplier_number'] = $syncPromotion->getSupplierNumber();
                    $promotions[$i]['supplier_item_number'] = $syncPromotion->getSupplierItemNumber();
                    $i++;
                }
            }
            echo json_encode($promotions);
        }
        return sfView::NONE;
    }

    public function executeSyncPromotionUpdate(sfWebRequest $request) {
        $urlval = "SyncPromotionUpdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);
            $shop->setPromotionSyncSyncedAt($shop->getPromotionSyncRequestedAt());
            $shop->save();
            echo "OK";
        } else {
            echo "ERROR";
        }
        return sfView::NONE;
    }

    public function executeSyncStockUpdate(sfWebRequest $request) {
        $urlval = "SyncStockUpdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");
        $stock_id = $request->getParameter("stock_id");
        $stocks_taking_json = json_decode($request->getParameter("server_json_stock_taking"));

        $i = 0;
        $a = "";
        $dayEndIds = "";
        $dayEndVar = 0;
        $stockTakingIds = "";

        
          $s = new Criteria();
        $s->add(StocksPeer::STOCK_ID, (int) $stock_id);
        if (StocksPeer::doCount($s) == 1) {
         echo "stock Id already Exist";
        }else{
        $db_stocks = new Stocks();
        $db_stocks->setStockId($stock_id);
        $db_stocks->setShopId($shop_id);
        $db_stocks->setUpdatedBy($request->getParameter("updated_by"));
        if ($db_stocks->save()) {

            foreach ($stocks_taking_json as $stock_taking_json) {

                $db_stockItem = new StockItems();
                $db_stockItem->setCmsItemId($stock_taking_json->cms_item_id);
                $db_stockItem->setItemId($stock_taking_json->item_id);
                $db_stockItem->setTotalQty($stock_taking_json->total_qty);
                $db_stockItem->setSoldQty($stock_taking_json->sold_qty);
                $db_stockItem->setReturnQty($stock_taking_json->return_qty);
                $db_stockItem->setRemainingQty($stock_taking_json->remaining_qty);
                $db_stockItem->setBookoutQty($stock_taking_json->bookout_qty);
                $db_stockItem->setStockQty($stock_taking_json->stock_qty);
                $db_stockItem->setStockId($db_stocks->getId());
                $db_stockItem->save();
                
            }

            echo "OK";
        }

        }
              
        return sfView::NONE;
    }

}
