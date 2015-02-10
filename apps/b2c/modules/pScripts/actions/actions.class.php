<?php

set_time_limit(100000000000000000000);
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
                                    new GcmLib("delivery_note", array($shops->getGcmKey()), $shops);
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
                            if ($insert_new) {
                                $sc = new Criteria();

                                $sc->addAnd(ShopsPeer::STATUS_ID, 3);
                                $sc->addAnd(ShopsPeer::GCM_KEY, null, Criteria::ISNOTNULL);
                                if (ShopsPeer::doCount($sc) > 0) {
                                    $shops = ShopsPeer::doSelect($sc);
                                    $gcmKeyArray = "";
                                    foreach ($shops as $shop) {
                                        $gcmKeyArray[] = $shop->getGcmKey();
                                    }
                                    new GcmLib("item_updated", $gcmKeyArray);
                                }
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
                                new GcmLib("delivery_note", array($shop->getGcmKey()), $shop);
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
        $bookoutIds = '';
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
        //$book_string = '[{"branch_number":"3344980288","note_id":"400001\/104","item_id":"2002241600","user_id":"1311422864954","quantity":"10","comments":"book out send","bookout_date":"2015-02-05 17:32:46","company_number":"123654789"}]';
=======
        // $book_string = '[{"branch_number":"3344980288","note_id":"400001\/104","item_id":"2002241600","user_id":"1311422864954","quantity":"10","comments":"book out send","bookout_date":"2015-02-05 17:32:46","company_number":"123654789"}]';
>>>>>>> b4f0e7733c8041b9ca5acf46c8fb1c69c6d52c18
        $book_string = $request->getParameter("server_json_bookout");
=======
        $book_string = '[{"branch_number":"3344980288","note_id":"400001\/104","item_id":"2002241600","user_id":"1311422864954","quantity":"10","comments":"book out send","bookout_date":"2015-02-05 17:32:46","company_number":"123654789"}]';
<<<<<<< HEAD
        //$book_string = $request->getParameter("server_json_bookout");
>>>>>>> aca7f56813d756ed287ec5114b8683530bc6e53e
=======
        // $book_string = $request->getParameter("server_json_bookout");
>>>>>>> bd75e2853ffaeb0f08341b790a791e1a3ce3c787
=======
        //$book_string = '[{"branch_number":"3344980288","note_id":"400001\/104","item_id":"2002241600","user_id":"1311422864954","quantity":"10","comments":"book out send","bookout_date":"2015-02-05 17:32:46","company_number":"123654789"}]';
        $book_string = $request->getParameter("server_json_bookout");
>>>>>>> f54c73d756b35cef2342573891d578ffafe422b3
        $json_from_bookout = json_decode($book_string);
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

        $a = implode(', ', (array)$bookoutIds);
        $a = implode(',', (array)$bookoutIds);
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
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "&server_json_order=" . $request->getParameter("server_json_order"));
        $dibsCall->save();

      //  $data_string ='[{"total_amount":"-1557.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":1262,"description2":"","order_number_id":"584","parent_type_id":"1371415037722261","transaction_type_id":"9","discount_value":"","buying_price":"","parent_type":"Day End","selling_price":"","created_at":"2014-11-03 19:02:02","quantity":"","user_id":"1111414414689","item_cms_id":"","day_start_id":"1241414861356131","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-11-03 19:02:02","sold_price":"-1557.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1241414861356131","shop_user_id":"1111414414689","sold_total_amount":"-1557.5","payments":[{"total_amount":"-1557.5","cc_type_id":"","day_start_id":"1241414861356131","shop_receipt_id":"0","change_value":"","created_at":"2014-11-03 19:02:02","shop_order_payment_id":596,"shop_order_user_id":"1111414414689","change_type":"","shop_order_id":"584","payment_type_id":"8"}],"status_id":"3","created_at":"2014-11-03 19:02:02","order_discount_value":"","employee_id":"","shop_order_id":"584","order_discount_type":""},{"total_amount":"1557.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":1263,"description2":"","order_number_id":"585","parent_type_id":"1251415037722424","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2014-11-04 10:31:57","quantity":"","user_id":"131409402138","item_cms_id":"","day_start_id":"1251415037722424","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-11-04 10:31:57","sold_price":"1557.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1251415037722424","shop_user_id":"131409402138","sold_total_amount":"1557.5","payments":[{"total_amount":"1557.5","cc_type_id":"","day_start_id":"1251415037722424","shop_receipt_id":"0","change_value":"","created_at":"2014-11-04 10:31:57","shop_order_payment_id":597,"shop_order_user_id":"131409402138","change_type":"","shop_order_id":"585","payment_type_id":"7"}],"status_id":"3","created_at":"2014-11-04 10:31:57","order_discount_value":"","employee_id":"","shop_order_id":"585","order_discount_type":""},{"total_amount":"49.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Kylling 1.stk","description3":"null","pos_id":1265,"description2":"null","order_number_id":"583","parent_type_id":"1-6126","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-11-04 11:44:01","quantity":"2","user_id":"131409402138","item_cms_id":"490","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7004","shop_receipt_id":"1-6126","updated_at":"2014-11-04 11:44:01","sold_price":"49.00","color":"","item_id":"7004","group":"Sandwich"}],"shop_receipt_id":"1-6126","day_start_id":"1251415037722424","shop_user_id":"131409402138","sold_total_amount":"49.00","payments":[{"total_amount":"49.00","cc_type_id":"1","day_start_id":"1251415037722424","shop_receipt_id":"1-6126","change_value":"0.00","created_at":"2014-11-04 11:44:01","shop_order_payment_id":598,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"583","payment_type_id":"2"}],"status_id":"3","created_at":"2014-11-04 11:44:01","order_discount_value":"0","employee_id":"0","shop_order_id":"583","order_discount_type":"1"},{"total_amount":"30.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Kylling 1.stk","description3":"null","pos_id":1266,"description2":"null","order_number_id":"586","parent_type_id":"1-6127","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-11-04 12:01:25","quantity":"1","user_id":"131409402138","item_cms_id":"490","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7004","shop_receipt_id":"1-6127","updated_at":"2014-11-04 12:01:25","sold_price":"24.50","color":"","item_id":"7004","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Tun 1.stk","description3":"null","pos_id":1267,"description2":"null","order_number_id":"586","parent_type_id":"1-6127","transaction_type_id":"3","discount_value":"19","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-11-04 12:01:25","quantity":"1","user_id":"131409402138","item_cms_id":"494","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7008","shop_receipt_id":"1-6127","updated_at":"2014-11-04 12:01:25","sold_price":"5.50","color":"","item_id":"7008","group":"Sandwich"}],"shop_receipt_id":"1-6127","day_start_id":"1251415037722424","shop_user_id":"131409402138","sold_total_amount":"30.00","payments":[{"total_amount":"30.00","cc_type_id":"1","day_start_id":"1251415037722424","shop_receipt_id":"1-6127","change_value":"0.00","created_at":"2014-11-04 12:01:25","shop_order_payment_id":599,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"586","payment_type_id":"2"}],"status_id":"3","created_at":"2014-11-04 12:01:25","order_discount_value":"0","employee_id":"0","shop_order_id":"586","order_discount_type":"1"},{"total_amount":"30.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":1268,"description2":"null","order_number_id":"587","parent_type_id":"1-6128","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-11-04 12:02:25","quantity":"1","user_id":"131409402138","item_cms_id":"487","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7001","shop_receipt_id":"1-6128","updated_at":"2014-11-04 12:02:25","sold_price":"24.50","color":"","item_id":"7001","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Pastrami 1.stk","description3":"null","pos_id":1269,"description2":"null","order_number_id":"587","parent_type_id":"1-6128","transaction_type_id":"3","discount_value":"19","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-11-04 12:02:25","quantity":"1","user_id":"131409402138","item_cms_id":"491","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7005","shop_receipt_id":"1-6128","updated_at":"2014-11-04 12:02:25","sold_price":"5.50","color":"","item_id":"7005","group":"Sandwich"}],"shop_receipt_id":"1-6128","day_start_id":"1251415037722424","shop_user_id":"131409402138","sold_total_amount":"30.00","payments":[{"total_amount":"30.00","cc_type_id":"1","day_start_id":"1251415037722424","shop_receipt_id":"1-6128","change_value":"0.00","created_at":"2014-11-04 12:02:25","shop_order_payment_id":600,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"587","payment_type_id":"2"}],"status_id":"3","created_at":"2014-11-04 12:02:25","order_discount_value":"0","employee_id":"0","shop_order_id":"587","order_discount_type":"1"},{"total_amount":"33.00","transactions":[{"supplier_number":"1111111","description1":"choko","description3":"Custom Sale","pos_id":1270,"description2":"Custom Sale","order_number_id":"588","parent_type_id":"1-6129","transaction_type_id":"3","discount_value":"25","buying_price":"0","selling_price":"44.00","parent_type":"receipt_numbers","created_at":"2014-11-04 13:06:15","quantity":"1","user_id":"131409402138","item_cms_id":"485","day_start_id":"1251415037722424","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-6129","updated_at":"2014-11-04 13:06:15","sold_price":"33.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-6129","day_start_id":"1251415037722424","shop_user_id":"131409402138","sold_total_amount":"33.00","payments":[{"total_amount":"33.00","cc_type_id":"0","day_start_id":"1251415037722424","shop_receipt_id":"1-6129","change_value":"70.00","created_at":"2014-11-04 13:06:15","shop_order_payment_id":601,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"588","payment_type_id":"1"}],"status_id":"3","created_at":"2014-11-04 13:06:15","order_discount_value":"0","employee_id":"0","shop_order_id":"588","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":1271,"description2":"null","order_number_id":"589","parent_type_id":"1-6130","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-11-04 15:29:28","quantity":"1","user_id":"1","item_cms_id":"540","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-6130","updated_at":"2014-11-04 15:29:28","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-6130","day_start_id":"1251415037722424","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1251415037722424","shop_receipt_id":"1-6130","change_value":"0.00","created_at":"2014-11-04 15:29:28","shop_order_payment_id":602,"shop_order_user_id":"1","change_type":"0","shop_order_id":"589","payment_type_id":"1"}],"status_id":"3","created_at":"2014-11-04 15:29:28","order_discount_value":"0","employee_id":"0","shop_order_id":"589","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":1272,"description2":"null","order_number_id":"589","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-11-04 15:41:55","quantity":"1","user_id":"1","receipt_number_id":"0","item_cms_id":"540","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7300","shop_receipt_id":"1-6130","updated_at":"2014-11-04 15:41:55","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-6130","day_start_id":"1251415037722424","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"day_start_id":"1251415037722424","shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-11-04 15:41:55","order_discount_value":"0","employee_id":"0","shop_order_id":"589","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":1273,"description2":"null","order_number_id":"590","parent_type_id":"1-6131","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-11-04 15:42:01","quantity":"1","user_id":"141409402634","item_cms_id":"540","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-6131","updated_at":"2014-11-04 15:42:01","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-6131","day_start_id":"1251415037722424","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1251415037722424","shop_receipt_id":"1-6131","change_value":"0.00","created_at":"2014-11-04 15:42:01","shop_order_payment_id":603,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"590","payment_type_id":"1"}],"status_id":"3","created_at":"2014-11-04 15:42:01","order_discount_value":"0","employee_id":"0","shop_order_id":"590","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":1274,"description2":"null","order_number_id":"591","parent_type_id":"1-6132","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-11-04 15:43:02","quantity":"1","user_id":"141409402634","item_cms_id":"540","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-6132","updated_at":"2014-11-04 15:43:02","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-6132","day_start_id":"1251415037722424","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1251415037722424","shop_receipt_id":"1-6132","change_value":"0.00","created_at":"2014-11-04 15:43:02","shop_order_payment_id":604,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"591","payment_type_id":"1"}],"status_id":"3","created_at":"2014-11-04 15:43:02","order_discount_value":"0","employee_id":"0","shop_order_id":"591","order_discount_type":"1"},{"total_amount":"36.00","transactions":[{"supplier_number":"1111111","description1":"drik","description3":"Custom Sale","pos_id":1275,"description2":"Custom Sale","order_number_id":"592","parent_type_id":"1-6133","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"19.00","parent_type":"receipt_numbers","created_at":"2014-11-04 18:12:35","quantity":"1","user_id":"141409402634","item_cms_id":"485","day_start_id":"1251415037722424","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-6133","updated_at":"2014-11-04 18:12:35","sold_price":"19.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"1111111","description1":"chokolade bar","description3":"Custom Sale","pos_id":1276,"description2":"Custom Sale","order_number_id":"592","parent_type_id":"1-6133","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-11-04 18:12:35","quantity":"1","user_id":"141409402634","item_cms_id":"485","day_start_id":"1251415037722424","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-6133","updated_at":"2014-11-04 18:12:35","sold_price":"17.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-6133","day_start_id":"1251415037722424","shop_user_id":"141409402634","sold_total_amount":"36.00","payments":[{"total_amount":"36.00","cc_type_id":"1","day_start_id":"1251415037722424","shop_receipt_id":"1-6133","change_value":"0.00","created_at":"2014-11-04 18:12:35","shop_order_payment_id":605,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"592","payment_type_id":"2"}],"status_id":"3","created_at":"2014-11-04 18:12:35","order_discount_value":"0","employee_id":"0","shop_order_id":"592","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":1277,"description2":"null","order_number_id":"593","parent_type_id":"1-6134","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-11-04 18:32:12","quantity":"1","user_id":"141409402634","item_cms_id":"540","day_start_id":"1251415037722424","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-6134","updated_at":"2014-11-04 18:32:12","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-6134","day_start_id":"1251415037722424","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1251415037722424","shop_receipt_id":"1-6134","change_value":"0.00","created_at":"2014-11-04 18:32:12","shop_order_payment_id":606,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"593","payment_type_id":"1"}],"status_id":"3","created_at":"2014-11-04 18:32:12","order_discount_value":"0","employee_id":"0","shop_order_id":"593","order_discount_type":"1"},{"total_amount":"-1569.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":1278,"description2":"","order_number_id":"594","parent_type_id":"1391415124219348","transaction_type_id":"9","discount_value":"","buying_price":"","parent_type":"Day End","selling_price":"","created_at":"2014-11-04 19:03:39","quantity":"","user_id":"141409402634","item_cms_id":"","day_start_id":"1251415037722424","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-11-04 19:03:39","sold_price":"-1569.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1251415037722424","shop_user_id":"141409402634","sold_total_amount":"-1569.5","payments":[{"total_amount":"-1569.5","cc_type_id":"","day_start_id":"1251415037722424","shop_receipt_id":"0","change_value":"","created_at":"2014-11-04 19:03:39","shop_order_payment_id":607,"shop_order_user_id":"141409402634","change_type":"","shop_order_id":"594","payment_type_id":"8"}],"status_id":"3","created_at":"2014-11-04 19:03:39","order_discount_value":"","employee_id":"","shop_order_id":"594","order_discount_type":""}]';
      //  $shop_id =1;
      //  $orderjson=str_replace("?","0",$data_string);
      //  $json_from_orders = json_decode($data_string);
      //  var_dump($json_from_orders);
        
        
       // $data_string ='[shop_id=104, server_json_order=[{"total_amount":"0.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":1,"description2":"","order_number_id":"2","parent_type_id":"10401423048037717","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2015-02-04 16:07:18","quantity":"","user_id":"1311422864954","item_cms_id":"","day_start_id":"10401423048037717","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2015-02-04 16:07:18","sold_price":"0.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"10401423048037717","shop_user_id":"1311422864954","sold_total_amount":"0.5","payments":[{"total_amount":"0.5","cc_type_id":"","day_start_id":"10401423048037717","shop_receipt_id":"0","change_value":"","created_at":"2015-02-04 16:07:18","shop_order_payment_id":1,"shop_order_user_id":"1311422864954","change_type":"","shop_order_id":"2","payment_type_id":"7"}],"status_id":"3","created_at":"2015-02-04 16:07:18","order_discount_value":"","employee_id":"","shop_order_id":"2","order_discount_type":""}]]';
       
       // $data_string ='[{"total_amount":"0.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":1,"description2":"","order_number_id":"2","parent_type_id":"10401423048037717","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2015-02-04 16:07:18","quantity":"","user_id":"1311422864954","item_cms_id":"","day_start_id":"10401423048037717","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2015-02-04 16:07:18","sold_price":"0.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"10401423048037717","shop_user_id":"1311422864954","sold_total_amount":"0.5","payments":[{"total_amount":"0.5","cc_type_id":"","day_start_id":"10401423048037717","shop_receipt_id":"0","change_value":"","created_at":"2015-02-04 16:07:18","shop_order_payment_id":1,"shop_order_user_id":"1311422864954","change_type":"","shop_order_id":"2","payment_type_id":"7"}],"status_id":"3","created_at":"2015-02-04 16:07:18","order_discount_value":"","employee_id":"","shop_order_id":"2","order_discount_type":""}]';
       
       $data_string = $request->getParameter("server_json_order");
       // var_dump($data_string);
       $orderjson = str_replace("?", "0", $data_string);
       $shop_id = $request->getParameter("shop_id");
       $json_from_orders = json_decode($orderjson);
        
//        $data_string ='[{"total_amount":"0.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":1,"description2":"","order_number_id":"2","parent_type_id":"10401423048037717","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2015-02-04 16:07:18","quantity":"","user_id":"1311422864954","item_cms_id":"","day_start_id":"10401423048037717","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2015-02-04 16:07:18","sold_price":"0.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"10401423048037717","shop_user_id":"1311422864954","sold_total_amount":"0.5","payments":[{"total_amount":"0.5","cc_type_id":"","day_start_id":"10401423048037717","shop_receipt_id":"0","change_value":"","created_at":"2015-02-04 16:07:18","shop_order_payment_id":1,"shop_order_user_id":"1311422864954","change_type":"","shop_order_id":"2","payment_type_id":"7"}],"status_id":"3","created_at":"2015-02-04 16:07:18","order_discount_value":"","employee_id":"","shop_order_id":"2","order_discount_type":""}]';
//        $shop_id = 104;
//        $orderjson=str_replace("?","0",$data_string);
//        $json_from_orders = json_decode($data_string);
//        var_dump($json_from_orders);

        $i = 0;
        $a = "";
        $orderIdArr = "";
//         foreach ($json_from_orders as $json_form_order) {
//            echo  "<br/>".$json_form_order->shop_order_id;
//         }
        //   die('no execution');
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
                if ($orderPaymentObject->shop_order_payment_id) {
                    $cop = new Criteria();
                    $cop->add(OrderPaymentsPeer::SHOP_ORDER_PAYMENT_ID, $orderPaymentObject->shop_order_payment_id);
                    $cop->add(OrderPaymentsPeer::SHOP_ID, $shop_id);
                    if (OrderPaymentsPeer::doCount($cop) == 0) {
                        $orderpyid = itemsLib::createOrderPaymentUsingObject($orderPaymentObject, $shop_id, $orderIdArr[1]);
                    } else {
                        $orderpay = OrderPaymentsPeer::doSelectOne($cop);
                        $orderpyid = $orderpay->getShopOrderPaymentId();
                    }
                    $orderPaymentId[] = $orderpyid;
                }
            }

            $saved_transactions = "";
            foreach ($json_form_order->transactions as $transactionobject) {
                $c = new Criteria();
                $c->add(TransactionsPeer::SHOP_TRANSACTION_ID, $transactionobject->pos_id);
                $c->add(TransactionsPeer::SHOP_ID, $shop_id);
                if (TransactionsPeer::doCount($c) == 0) {
                    $saved_transid = itemsLib::createTransactionUsingObject($transactionobject, $shop_id, $orderIdArr[1]);
                } else {
                    $transactionss = TransactionsPeer::doSelectOne($c);
                    $saved_transid = $transactionss->getShopTransactionId();
                }
                $saved_transactions[] = $saved_transid;
            }


            emailLib::sendEmailSale($saved_transactions, $shop_id);


            $a[$i]["order_id"] = $orderIdArr[0];
            $a[$i]["order_payment_id"] = implode(",", $orderPaymentId);
            $a[$i]["order_transaction_id"] = implode(",", $saved_transactions);
            $i++;
        }
        $dibsCall->setCallResponse(json_encode($a));
        $dibsCall->save();
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

            if ($shopData->getIsConfigured()) {
                $shop['is_configure'] = 1;
            } else {
                $shop['is_configure'] = 0;
            }
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
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "&day_starts_json=" . $request->getParameter("day_starts_json") . "&day_starts_journal=" . $request->getParameter("day_starts_journal"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");
        $day_starts_json = json_decode($request->getParameter("day_starts_json"));
        $day_start_journal = json_decode($request->getParameter("day_starts_journal"));

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
            $dayStartVar = 0;
            $daystart->setDayStartedAt($day_start_json->day_started_at);
            $daystart->setDayStartedBy($day_start_json->day_started_by);
            $daystart->setIsDayClosed($day_start_json->is_day_closed);
            $daystart->setShopId($shop_id);
            $daystart->setTotalAmount($day_start_json->total_amount);
            $daystart->setExpectedAmount($day_start_json->expected_amount);
            $daystart->setJournalId($day_start_json->journal_id);
            $daystart->setSuccess($day_start_json->success);
            if ($daystart->save()) {
                $dayStartIds[] = $daystart->getId();
                $dayStartVar = 1;
            }

            //////////////////////////////general ID///////////////////////////
            $dayjournals = "";
            //  foreach ($day_start_json->day_starts_journal as $day_start_journal) {
            $ja = new Criteria();
            $ja->add(JournalPeer::JOURNAL_ID, $day_start_journal->id);
            $ja->addAnd(JournalPeer::SHOP_ID, $shop_id);
            if (JournalPeer::doCount($ja) == 0) {
                $journals = new Journal();
                $journals->setJournalId($day_start_journal->id);
            } else {
                $journals = JournalPeer::doSelectOne($ja);
            }


            $journals->setCreatedDate($day_start_journal->date);
            $journals->setCreatedAt(time());

            $journals->setShopId($shop_id);



            if ($journals->save()) {
                $dayjournals[] = $journals->getId();
            }
            // }
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

//                if ($daystartAttempts->save()) {
//                    $dayAttemptIds[] = $daystartAttempts->getId();
//                }
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
//                if ($daystartDenomination->save()) {
//                    $dayStartDenominationIds[] = $daystartDenomination->getId();
//                    echo $daystartDenomination->getId();
//                }
            }
            $i++;


            if ($dayStartVar) {
                emailLib::sendEmailDayStartDenomination($daystart->getId());
            }
        }


        @$a = implode(",", $dayStartIds) . ":" . implode(",", $dayStartDenominationIds);
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
            $dayend->setCash($day_end_json->cash);
            $dayend->setCard($day_end_json->card);
            $dayend->setVoucher($day_end_json->voucher);
            $dayend->setSale($day_end_json->sale);

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
            if ($shop->getPromotionSyncSyncedAt() != "") {
                $i->add(PromotionPeer::UPDATED_AT, $shop->getPromotionSyncSyncedAt(), Criteria::GREATER_EQUAL);
            }
            //   $i->addAnd(PromotionPeer::END_DATE, date("Y-m-d"), Criteria::GREATER_EQUAL);
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
        $stock_type = $request->getParameter("stock_type");
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
        } else {
            $db_stocks = new Stocks();
            $db_stocks->setStockId($stock_id);
            $db_stocks->setStockType($stock_type);

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
                    $db_stockItem->setShopId($shop_id);
                    $db_stockItem->setStockType($stock_taking_json->stock_type);
                    $db_stockItem->setStockValue($stock_taking_json->stock_value);
                    $db_stockItem->setProcessStatus($stock_taking_json->process_status);
                    $db_stockItem->save();
                }

                echo "OK";
            }
        }

        return sfView::NONE;
    }

////////////////////////////resync area ////////////////////////////////////////////////////////
    public function executeResyncTransactions(sfWebRequest $request) {
        $urlval = "executeResyncTransactions-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(TransactionsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

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
                    $jsonTransaction[$i]['promotion_ids'] = $transaction->getPromotionIds();
                    $jsonTransaction[$i]['day_start_id'] = $transaction->getDayStartId();
                    $i++;
                }
                echo json_encode($jsonTransaction);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncOrders(sfWebRequest $request) {
        $urlval = "executeResyncOrders-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(OrdersPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (OrdersPeer::doCount($c) > 0) {
                $orders = OrdersPeer::doSelect($c);
                $jsonOrder = "";
                $i = 0;
                foreach ($orders as $order) {
                    $jsonOrder[$i]['shop_order_id'] = $order->getShopOrderId();
                    $jsonOrder[$i]['shop_user_id'] = $order->getShopUserId();
                    $jsonOrder[$i]['discount_type_id'] = $order->getDiscountTypeId();
                    $jsonOrder[$i]['discount_value'] = $order->getDiscountValue();
                    $jsonOrder[$i]['shop_receipt_number_id'] = $order->getShopReceiptNumberId();
                    $jsonOrder[$i]['total_amount'] = $order->getTotalAmount();
                    $jsonOrder[$i]['total_sold_amount'] = $order->getTotalSoldAmount();
                    $jsonOrder[$i]['status_id'] = $order->getStatusId();
                    $jsonOrder[$i]['created_at'] = $order->getCreatedAt();
                    $jsonOrder[$i]['updated_at'] = $order->getUpdatedAt();
                    $jsonOrder[$i]['day_start_id'] = $order->getDayStartId();
                    $jsonOrder[$i]['employee_id'] = $order->getEmployeeId();

                    $i++;
                }
                echo json_encode($jsonOrder);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncOrderPayments(sfWebRequest $request) {
        $urlval = "executeResyncOrderPayments-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(OrderPaymentsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (OrderPaymentsPeer::doCount($c) > 0) {
                $orderPayments = OrderPaymentsPeer::doSelect($c);
                $jsonOrderPayments = "";
                $i = 0;
                foreach ($orderPayments as $orderPayment) {
                    $jsonOrderPayments[$i]['order_id'] = $orderPayment->getOrderId();
                    $jsonOrderPayments[$i]['payment_type_id'] = $orderPayment->getPaymentTypeId();
                    $jsonOrderPayments[$i]['amount'] = $orderPayment->getAmount();
                    $jsonOrderPayments[$i]['shop_order_payment_id'] = $orderPayment->getShopOrderPaymentId();
                    $jsonOrderPayments[$i]['dyn_syn'] = $orderPayment->getDynSyn();
                    $jsonOrderPayments[$i]['shop_order_user_id'] = $orderPayment->getShopOrderUserId();
                    $jsonOrderPayments[$i]['cc_type_id'] = $orderPayment->getCcTypeId();
                    $jsonOrderPayments[$i]['change_value'] = $orderPayment->getChangeValue();
                    $jsonOrderPayments[$i]['change_type'] = $orderPayment->getChangeType();
                    $jsonOrderPayments[$i]['created_at'] = $orderPayment->getCreatedAt();
                    $jsonOrderPayments[$i]['updated_at'] = $orderPayment->getUpdatedAt();
                    $jsonOrderPayments[$i]['day_start_id'] = $orderPayment->getDayStartId();
                    $jsonOrderPayments[$i]['shop_order_id'] = $orderPayment->getShopOrderId();

                    $i++;
                }
                echo json_encode($jsonOrderPayments);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeSyncDeliveryNotesAll(sfWebRequest $request) {
        $urlval = "SyncDeliveryNotesAll-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $i = new Criteria();
            $i->add(DeliveryNotesPeer:: SHOP_ID, (int) $request->getParameter("shop_id"));


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
                $notes[$i]['user_id'] = $syncNote->getUserId();
                $notes[$i]['created_at'] = $syncNote->getCreatedAt();
                $notes[$i]['received_at'] = $syncNote->getReceivedAt();
                $notes[$i]['received_quantity'] = $syncNote->getReceivedQuantity();
                $notes[$i]['comment'] = $syncNote->getComment();
                $notes[$i]['status_id'] = $syncNote->getStatusId();
                $notes[$i]['synced_day_start_id'] = $syncNote->getSyncedDayStartId();
                $notes[$i]['received_day_start_id'] = $syncNote->getReceivedDayStartId();
                $notes[$i]['delivery_note_type_id'] = $syncNote->getDeliveryNoteTypeId();
                $notes[$i]['is_synced'] = $syncNote->getIsSynced();

                $i++;
            }
            echo json_encode($notes);
        }
        return sfView::NONE;
    }

    public function executeResyncBookoutNotes(sfWebRequest $request) {
        $urlval = "executeResyncBookoutNotes-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(BookoutNotesPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (BookoutNotesPeer::doCount($c) > 0) {
                $bookoutNotes = BookoutNotesPeer::doSelect($c);
                $jsonBookoutNote = "";
                $i = 0;
                foreach ($bookoutNotes as $bookoutNote) {
                    $jsonBookoutNote[$i]['id'] = $bookoutNote->getId();
                    $jsonBookoutNote[$i]['item_id'] = $bookoutNote->getItemId();
                    $jsonBookoutNote[$i]['branch_number'] = $bookoutNote->getBranchNumber();
                    $jsonBookoutNote[$i]['company_number'] = $bookoutNote->getCompanyNumber();
                    $jsonBookoutNote[$i]['quantity'] = $bookoutNote->getQuantity();
                    $jsonBookoutNote[$i]['delivery_date'] = $bookoutNote->getDeliveryDate();
                    $jsonBookoutNote[$i]['user_id'] = $bookoutNote->getUserId();
                    $jsonBookoutNote[$i]['note_id'] = $bookoutNote->getNoteId();
                    $jsonBookoutNote[$i]['comment'] = $bookoutNote->getComment();
                    $jsonBookoutNote[$i]['reply_comment'] = $bookoutNote->getReplyComment();
                    $jsonBookoutNote[$i]['created_at'] = $bookoutNote->getCreatedAt();
                    $jsonBookoutNote[$i]['updated_at'] = $bookoutNote->getUpdatedAt();
                    $jsonBookoutNote[$i]['received_at'] = $bookoutNote->getReceivedAt();
                    $jsonBookoutNote[$i]['status_id'] = $bookoutNote->getStatusId();
                    $jsonBookoutNote[$i]['synced_day_start_id'] = $bookoutNote->getSyncedDayStartId();
                    $jsonBookoutNote[$i]['received_day_start_id'] = $bookoutNote->getReceivedDayStartId();
                    $jsonBookoutNote[$i]['received_quantity'] = $bookoutNote->getReceivedQuantity();

                    $i++;
                }
                echo json_encode($jsonBookoutNote);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncDayStartAttempts(sfWebRequest $request) {
        $urlval = "executeResyncDayStartAttempts-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(DayStartsAttemptsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (DayStartsAttemptsPeer::doCount($c) > 0) {
                $dayStartsAttempts = DayStartsAttemptsPeer::doSelect($c);
                $jsonStartsAttempt = "";
                $i = 0;
                foreach ($dayStartsAttempts as $dayStartsAttempt) {
                    $jsonStartsAttempt[$i]['id'] = $dayStartsAttempt->getId();

                    $jsonStartsAttempt[$i]['day_start_id'] = $dayStartsAttempt->getDayStartId();
                    $jsonStartsAttempt[$i]['shop_id'] = $dayStartsAttempt->getShopId();
                    $jsonStartsAttempt[$i]['updated_by'] = $dayStartsAttempt->getUpdatedBy();
                    $jsonStartsAttempt[$i]['created_at'] = $dayStartsAttempt->getCreatedAt();
                    $jsonStartsAttempt[$i]['total_amount'] = $dayStartsAttempt->getTotalAmount();
                    $jsonStartsAttempt[$i]['is_synce'] = $dayStartsAttempt->getIsSynce();
                    $jsonStartsAttempt[$i]['expected_amount'] = $dayStartsAttempt->getExpectedAmount();

                    $i++;
                }
                echo json_encode($jsonStartsAttempt);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncDayStart(sfWebRequest $request) {
        $urlval = "executeResyncDayStart-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(DayStartsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (DayStartsPeer::doCount($c) > 0) {
                $dayStarts = DayStartsPeer::doSelect($c);
                $jsonDayStart = "";
                $i = 0;
                foreach ($dayStarts as $dayStart) {
                    $jsonDayStart[$i]['id'] = $dayStart->getId();
                    $jsonDayStart[$i]['day_started_at'] = $dayStart->getDayStartedAt();
                    $jsonDayStart[$i]['day_started_by'] = $dayStart->getDayStartedBy();
                    $jsonDayStart[$i]['shop_id'] = $dayStart->getShopId();
                    $jsonDayStart[$i]['is_day_closed'] = $dayStart->getIsDayClosed();
                    $jsonDayStart[$i]['created_at'] = $dayStart->getCreatedAt();
                    $jsonDayStart[$i]['total_amount'] = $dayStart->getTotalAmount();
                    $jsonDayStart[$i]['success'] = $dayStart->getSuccess();
                    $jsonDayStart[$i]['expected_amount'] = $dayStart->getExpectedAmount();
                    $jsonDayStart[$i]['journal_id'] = $dayStart->getJournalId();

                    $i++;
                }
                echo json_encode($jsonDayStart);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncDayEnd(sfWebRequest $request) {
        $urlval = "executeResyncDayEnd-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(DayEndsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (DayEndsPeer::doCount($c) > 0) {
                $dayEnds = DayEndsPeer::doSelect($c);
                $jsonDayEnd = "";
                $i = 0;
                foreach ($dayEnds as $dayEnd) {
                    $jsonDayEnd[$i]['id'] = $dayEnd->getId();
                    $jsonDayEnd[$i]['day_ended_at'] = $dayEnd->getDayEndedAt();
                    $jsonDayEnd[$i]['day_ended_by'] = $dayEnd->getDayEndedBy();
                    $jsonDayEnd[$i]['shop_id'] = $dayEnd->getShopId();
                    $jsonDayEnd[$i]['day_start_id'] = $dayEnd->getDayStartId();
                    $jsonDayEnd[$i]['created_at'] = $dayEnd->getCreatedAt();
                    $jsonDayEnd[$i]['total_amount'] = $dayEnd->getTotalAmount();
                    $jsonDayEnd[$i]['success'] = $dayEnd->getSuccess();
                    $jsonDayEnd[$i]['expected_amount'] = $dayEnd->getExpectedAmount();
                    $jsonDayEnd[$i]['cash'] = $dayEnd->getCash();
                    $jsonDayEnd[$i]['card'] = $dayEnd->getCard();
                    $jsonDayEnd[$i]['voucher'] = $dayEnd->getVoucher();
                    $jsonDayEnd[$i]['sale'] = $dayEnd->getSale();

                    $i++;
                }
                echo json_encode($jsonDayEnd);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncDayStartDenominations(sfWebRequest $request) {
        $urlval = "executeResyncDayStartdenominations-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();
            $c->addJoin(DayStartDenominationsPeer::DAY_START_ID, DayStartsPeer::ID, Criteria::LEFT_JOIN);
            $c->add(DayStartsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (DayStartDenominationsPeer::doCount($c) > 0) {
                $dayStartDenominations = DayStartDenominationsPeer::doSelect($c);
                $jsonDayStartDenomination = "";
                $i = 0;
                foreach ($dayStartDenominations as $dayStartDenomination) {
                    $jsonDayStartDenomination[$i]['id'] = $dayStartDenomination->getId();
                    $jsonDayStartDenomination[$i]['denomination_id'] = $dayStartDenomination->getDenominationId();
                    $jsonDayStartDenomination[$i]['day_start_id'] = $dayStartDenomination->getDayStartId();
                    $jsonDayStartDenomination[$i]['count'] = $dayStartDenomination->getCount();
                    $jsonDayStartDenomination[$i]['amount'] = $dayStartDenomination->getAmount();
                    $jsonDayStartDenomination[$i]['created_at'] = $dayStartDenomination->getCreatedAt();
                    $jsonDayStartDenomination[$i]['day_attempt_id'] = $dayStartDenomination->getDayAttemptId();


                    $i++;
                }
                echo json_encode($jsonDayStartDenomination);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncDayEndDenominations(sfWebRequest $request) {
        $urlval = "executeResyncDayEnddenominations-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();
            $c->addJoin(DayEndDenominationsPeer::DAY_END_ID, DayEndsPeer::ID, Criteria::LEFT_JOIN);
            $c->add(DayEndsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (DayEndDenominationsPeer::doCount($c) > 0) {
                $dayEndDenominations = DayEndDenominationsPeer::doSelect($c);
                $jsonDayEndDenomination = "";
                $i = 0;
                foreach ($dayEndDenominations as $dayEndDenomination) {
                    $jsonDayEndDenomination[$i]['id'] = $dayEndDenomination->getId();
                    $jsonDayEndDenomination[$i]['denomination_id'] = $dayEndDenomination->getDenominationId();
                    $jsonDayEndDenomination[$i]['day_end_id'] = $dayEndDenomination->getDayEndId();
                    $jsonDayEndDenomination[$i]['count'] = $dayEndDenomination->getCount();
                    $jsonDayEndDenomination[$i]['amount'] = $dayEndDenomination->getAmount();



                    $i++;
                }
                echo json_encode($jsonDayEndDenomination);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncCashInOut(sfWebRequest $request) {
        $urlval = "executeResyncCashInOut-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(CashInOutPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (CashInOutPeer::doCount($c) > 0) {
                $cashInOuts = CashInOutPeer::doSelect($c);
                $jsonCashInOut = "";
                $i = 0;
                foreach ($cashInOuts as $cashInOut) {
                    $jsonCashInOut[$i]['id'] = $cashInOut->getId();
                    $jsonCashInOut[$i]['cash_inout_id'] = $cashInOut->getCashInoutId();
                    $jsonCashInOut[$i]['day_start_id'] = $cashInOut->getDayStartId();
                    $jsonCashInOut[$i]['is_synced'] = $cashInOut->getIsSynced();
                    $jsonCashInOut[$i]['description'] = $cashInOut->getDescription();
                    $jsonCashInOut[$i]['amount'] = $cashInOut->getAmount();
                    $jsonCashInOut[$i]['created_at'] = $cashInOut->getCreatedAt();
                    $jsonCashInOut[$i]['updated_at'] = $cashInOut->getUpdatedAt();
                    $jsonCashInOut[$i]['updated_by'] = $cashInOut->getUpdatedBy();
                    $jsonCashInOut[$i]['shop_id'] = $cashInOut->getShopId();

                    $i++;
                }
                echo json_encode($jsonCashInOut);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeSyncCashInOut($request) {

        $urlval = "SyncCashInOut-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_cashinout=" . $request->getParameter("server_json_cashinout"));
        $dibsCall->save();

        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $bookoutIds = "";
        $object = json_decode($request->getParameter("server_json_cashinout"));
        $shop_id = $request->getParameter("shop_id");

        $cd = new Criteria();
        $cd->add(CashInOutPeer::SHOP_ID, (int) $request->getParameter("shop_id"));
        $cd->addAnd(CashInOutPeer::CASH_INOUT_ID, $object->id);

        if (CashInOutPeer::doCount($cd) > 0) {
            $new_cin = CashInOutPeer::doSelectOne($cd);
        } else {
            $new_cin = new CashInOut();
        }


        $new_cin->setCashInoutId($object->id);
        $new_cin->setDayStartId($object->day_start_id);
        $new_cin->setAmount($object->amount);
        $new_cin->setDescription($object->description);
        $new_cin->setIsSynced($object->status_id);
        $new_cin->setShopId($request->getParameter("shop_id"));


        $new_cin->setCreatedAt($object->created_at);

        $new_cin->setUpdatedBy($object->created_by);



        if ($new_cin->save()) {
            $bookoutIds[] = $new_cin->getCashInoutId();
        }




        //  $a = implode(', ', $bookoutIds);
        $a = implode(',', $bookoutIds);
        echo json_encode($a);

        return sfView::NONE;
    }

    public function executeResyncInventory(sfWebRequest $request) {
        $urlval = "executeResyncInventory-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(InventoryPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (InventoryPeer::doCount($c) > 0) {
                $inventories = InventoryPeer::doSelect($c);
                $jsonInventory = "";
                $i = 0;
                foreach ($inventories as $inventory) {
                    $jsonInventory[$i]['cms_item_id'] = $inventory->getCmsItemId();
                    $jsonInventory[$i]['total'] = $inventory->getTotal();
                    $jsonInventory[$i]['sold'] = $inventory->getSold();
                    $jsonInventory[$i]['book_out'] = $inventory->getBookOut();
                    $jsonInventory[$i]['returned'] = $inventory->getReturned();
                    $jsonInventory[$i]['created_at'] = $inventory->getCreatedAt();
                    $jsonInventory[$i]['available'] = $inventory->getAvailable();
                    $jsonInventory[$i]['item_id'] = $inventory->getItemId();
                    $jsonInventory[$i]['delivery_count'] = $inventory->getDeliveryCount();
                    $jsonInventory[$i]['stock_in'] = $inventory->getStockIn();
                    $jsonInventory[$i]['stock_out'] = $inventory->getStockOut();

                    $i++;
                }
                echo json_encode($jsonInventory);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeResyncJournal(sfWebRequest $request) {
        $urlval = "executeResyncJournal-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(JournalPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (JournalPeer::doCount($c) > 0) {
                $journals = JournalPeer::doSelect($c);
                $jsonJournal = "";
                $i = 0;
                foreach ($journals as $journal) {
                    $jsonJournal[$i]['id'] = $journal->getId();
                    $jsonJournal[$i]['journal_id'] = $journal->getJournalId();
                    $jsonJournal[$i]['created_date'] = $journal->getCreatedDate();
                    $jsonJournal[$i]['created_at'] = $journal->getCreatedAt();


                    $i++;
                }
                echo json_encode($jsonJournal);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeSyncReceipts($request) {

        $urlval = "SyncReceipts-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_receipt=" . $request->getParameter("server_json_receipt"));
        $dibsCall->save();

        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $bookoutIds = "";
        $object = json_decode($request->getParameter("server_json_receipt"));
        $shop_id = $request->getParameter("shop_id");

        $cd = new Criteria();
        $cd->add(ReceiptsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));
        $cd->addAnd(ReceiptsPeer::RECEIPT_ID, $object->id);

        if (ReceiptsPeer::doCount($cd) > 0) {
            $new_cin = ReceiptsPeer::doSelectOne($cd);
        } else {
            $new_cin = new Receipts();
        }


        $new_cin->setReceiptId($object->id);


        $new_cin->setShopId($request->getParameter("shop_id"));


        $new_cin->setCreatedAt($object->created_at);

        //   $new_cin->setUpdatedBy($object->created_by);



        if ($new_cin->save()) {
            $bookoutIds[] = $new_cin->getReceiptId();
        }




        //  $a = implode(', ', $bookoutIds);
        $a = implode(',', $bookoutIds);
        echo json_encode($a);

        return sfView::NONE;
    }

    public function executeResyncReceipts(sfWebRequest $request) {
        $urlval = "executeResyncReceipts-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(ReceiptsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (ReceiptsPeer::doCount($c) > 0) {
                $receipts = ReceiptsPeer::doSelect($c);
                $jsonReceipts = "";
                $i = 0;
                foreach ($receipts as $receipt) {
                    $jsonReceipts[$i]['id'] = $receipt->getId();
                    $jsonReceipts[$i]['receipt_id'] = $receipt->getReceiptId();
                    $jsonReceipts[$i]['updated_at'] = $receipt->getUpdatedAt();
                    $jsonReceipts[$i]['created_at'] = $receipt->getCreatedAt();


                    $i++;
                }
                echo json_encode($jsonReceipts);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeSyncReturnReceipts($request) {

        $urlval = "SyncReturnReceipts-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_return_receipts=" . $request->getParameter("server_json_return_receipts"));
        $dibsCall->save();

        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $bookoutIds = "";
        $object = json_decode($request->getParameter("server_json_return_receipts"));
        $shop_id = $request->getParameter("shop_id");

        $cd = new Criteria();
        $cd->add(ReturnReceiptsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));
        $cd->addAnd(ReturnReceiptsPeer::RECEIPT_ID, $object->id);

        if (ReturnReceiptsPeer::doCount($cd) > 0) {
            $new_cin = ReturnReceiptsPeer::doSelectOne($cd);
        } else {
            $new_cin = new ReturnReceipts();
        }


        $new_cin->setReceiptId($object->id);


        $new_cin->setShopId($request->getParameter("shop_id"));


        $new_cin->setCreatedAt($object->created_at);

        //   $new_cin->setUpdatedBy($object->created_by);



        if ($new_cin->save()) {
            $bookoutIds[] = $new_cin->getReceiptId();
        }




        //  $a = implode(', ', $bookoutIds);
        $a = implode(',', $bookoutIds);
        echo json_encode($a);

        return sfView::NONE;
    }

    public function executeResyncReturnReceipts(sfWebRequest $request) {
        $urlval = "executeResyncReturnReceipts-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(ReturnReceiptsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));

            if (ReturnReceiptsPeer::doCount($c) > 0) {
                $receipts = ReturnReceiptsPeer::doSelect($c);
                $jsonReceipts = "";
                $i = 0;
                foreach ($receipts as $receipt) {
                    $jsonReceipts[$i]['id'] = $receipt->getId();
                    $jsonReceipts[$i]['receipt_id'] = $receipt->getReceiptId();
                    $jsonReceipts[$i]['updated_at'] = $receipt->getUpdatedAt();
                    $jsonReceipts[$i]['created_at'] = $receipt->getCreatedAt();


                    $i++;
                }
                echo json_encode($jsonReceipts);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeSyncDayStartsUpdate(sfWebRequest $request) {


        $urlval = "executeSyncDayStartsupdate-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id") . "&day_starts_json=" . $request->getParameter("day_starts_json"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");
        $day_start_json = json_decode($request->getParameter("day_starts_json"));

        $i = 0;
        $a = "";
        $dayStartIds = "";

        $co = new Criteria();
        $co->add(DayStartsPeer::ID, $day_start_json->id);
        if (DayStartsPeer::doCount($co) == 0) {
            //  $daystart = new DayStarts();
            //   $daystart->setId($day_start_json->id);
        } else {
            $daystart = DayStartsPeer::doSelectOne($co);
            $daystart->setIsDayClosed($day_start_json->is_day_closed);

            if ($daystart->save()) {
                $dayStartIds[] = $daystart->getId();
            }
        }







        $a = implode(",", $dayStartIds);
        echo json_encode($a);
        return sfView::NONE;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////   

    public function executeSyncTransaction(sfWebRequest $request) {
        $urlval = "SyncTransaction-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_trans=" . $request->getParameter("server_json_trans") . "&shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();
        $shop_id = $request->getParameter("shop_id");


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


        echo implode(",", $saved_transactions);
        return sfView::NONE;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////
    public function executeSynGcm(sfWebRequest $request) {


        $urlval = "executeSynGcm-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $shop_id = $request->getParameter("shop_id");


        $i = 0;
        $gcms = "";


        $co = new Criteria();
        $co->add(GcmRequestPeer::SHOP_ID, $shop_id);
        $co->addAnd(GcmRequestPeer::ACTION_NAME, $request->getParameter("message"));
        if (GcmRequestPeer::doCount($co) == 0) {
            $gcm = new GcmRequest();
            $gcm->setShopId($shop_id);
            $gcm->setActionName($request->getParameter("message"));
            $gcm->setReceiveCount(1);
        } else {
            $gcm = GcmRequestPeer::doSelectOne($co);
            $rccount = (int) $gcm->getReceiveCount();
            $rccount = $rccount + 1;
            $gcm->setReceiveCount($rccount);
        }

        $gcm->setUpdatedAt(time());
        $gcm->setRequestStatus(3);
        $gcm->setUserId($request->getParameter("user_id"));
        $gcm->setReceivedAt($request->getParameter("created_at"));


        if ($gcm->save()) {
            $gcms[] = $gcm->getId();
        }
        $dibsCall->setCallResponse(json_encode($gcms));
        $dibsCall->save();
        echo implode(",", $gcms);
        return sfView::NONE;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////

    public function executeSyncStockTransactions(sfWebRequest $request) {
        $urlval = "executeSyncStockTransactions-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $c = new Criteria();

            $c->add(TransactionsPeer::SHOP_ID, (int) $request->getParameter("shop_id"));
            $c->addAnd(TransactionsPeer::DOWN_SYNC, 0);
            if (TransactionsPeer::doCount($c) > 0) {
                $transactions = TransactionsPeer::doSelect($c);
                $jsonTransaction = "";
                $i = 0;
                foreach ($transactions as $transaction) {
                    $jsonTransaction[$i]['id'] = $transaction->getId();
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
                    $jsonTransaction[$i]['promotion_ids'] = $transaction->getPromotionIds();
                    $jsonTransaction[$i]['day_start_id'] = $transaction->getDayStartId();
                    $i++;
                }
                echo json_encode($jsonTransaction);
            } else {
                echo "No Data Found to Sync";
            }
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeReSyncPromotion(sfWebRequest $request) {
        $urlval = "executeReSyncPromotion-" . $request->getURI();
        $dibsCall = new DibsCall();

        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();

        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {
            $shop = ShopsPeer::doSelectOne($s);


            $i = new Criteria();
            //  $i->add(PromotionPeer:: PROMOTION_STATUS, 3);
            //  $i->addAnd(PromotionPeer::END_DATE, date("Y-m-d"), Criteria::GREATER_EQUAL);
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

    public function executeUpdateStockTransactions(sfWebRequest $request) {


        $urlval = "UpdateStockTransactions-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setDecryptedData("server_json_trans=" . $request->getParameter("server_json_trans") . "&shop_id=" . $request->getParameter("shop_id"));
        $dibsCall->save();
        $shop_id = $request->getParameter("shop_id");


        $s = new Criteria();
        $s->add(ShopsPeer::ID, (int) $request->getParameter("shop_id"));
        if (ShopsPeer::doCount($s) == 1) {

            $i = 0;
            $gcms = "";
            $saved_transactions = "";
            $json_of_transactions = json_decode($request->getParameter("server_json_trans"));
            foreach ($json_of_transactions as $object) {

                $co = new Criteria();
                $co->add(TransactionsPeer::SHOP_ID, $shop_id);
                $co->addAnd(TransactionsPeer::ID, $object->id);
                if (TransactionsPeer::doCount($co) > 0) {


                    $transaction = TransactionsPeer::doSelectOne($co);

                    $transaction->setUpdatedAt(time());
                    $transaction->setDownSync(1);
                    $transaction->setUserId($object->user_id);
                    $transaction->setShopTransactionId($object->shop_transaction_id);

                    if ($transaction->save()) {
                        $gcms[] = $transaction->getId();
                    }
                }
            }

            $dibsCall->setCallResponse(json_encode($gcms));
            $dibsCall->save();
            echo implode(",", $gcms);
        } else {
            echo "Shop not found";
        }
        return sfView::NONE;
    }

    public function executeUpdateTransactionsManual(sfWebRequest $request) {




        //  $dibsdata=DibsCallPeer::retrieveByPK(606);
        $data_string = '[{"total_amount":"-300","transactions":[{"promotion_ids":"","supplier_number":"","description1":"indkab okh","description3":"indkab okh","pos_id":499,"description2":"indkab okh","order_number_id":"267","parent_type_id":"5","transaction_type_id":"7","discount_value":"","buying_price":"","parent_type":"Cash Out","selling_price":"","created_at":"2014-10-15 10:02:34","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1131413306490979","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-15 10:02:34","sold_price":"-300","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"-300","payments":[{"total_amount":"-300","cc_type_id":"","day_start_id":"1131413306490979","shop_receipt_id":"0","change_value":"","created_at":"2014-10-15 10:02:34","shop_order_payment_id":278,"shop_order_user_id":"1","change_type":"","shop_order_id":"267","payment_type_id":"6"}],"status_id":"3","created_at":"2014-10-15 10:02:34","order_discount_value":"","employee_id":"","shop_order_id":"267","order_discount_type":""},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"&AElig;blemost drikkeklar","description3":"null","pos_id":500,"description2":"500","order_number_id":"264","parent_type_id":"1-5850","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 11:11:37","quantity":"1","user_id":"1","item_cms_id":"550","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7310","shop_receipt_id":"1-5850","updated_at":"2014-10-15 11:11:37","sold_price":"0.00","color":"","item_id":"7310","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":501,"description2":"null","order_number_id":"264","parent_type_id":"1-5850","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 11:11:37","quantity":"2","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5850","updated_at":"2014-10-15 11:11:37","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"}],"shop_receipt_id":"1-5850","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5850","change_value":"0.00","created_at":"2014-10-15 11:11:37","shop_order_payment_id":279,"shop_order_user_id":"1","change_type":"0","shop_order_id":"264","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 11:11:37","order_discount_value":"0","employee_id":"0","shop_order_id":"264","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":502,"description2":"500","order_number_id":"264","parent_type_id":"1-5851","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 11:43:19","quantity":"2","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5851","updated_at":"2014-10-15 11:43:19","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":503,"description2":"null","order_number_id":"264","parent_type_id":"1-5851","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 11:43:19","quantity":"2","user_id":"1","item_cms_id":"542","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5851","updated_at":"2014-10-15 11:43:19","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":504,"description2":"null","order_number_id":"264","parent_type_id":"1-5851","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 11:43:19","quantity":"2","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5851","updated_at":"2014-10-15 11:43:19","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":505,"description2":"null","order_number_id":"264","parent_type_id":"1-5851","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 11:43:19","quantity":"2","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5851","updated_at":"2014-10-15 11:43:19","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - &AElig;ggesalat 1.stk","description3":"null","pos_id":506,"description2":"500","order_number_id":"264","parent_type_id":"1-5851","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 11:43:19","quantity":"1","user_id":"1","item_cms_id":"548","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7308","shop_receipt_id":"1-5851","updated_at":"2014-10-15 11:43:19","sold_price":"0.00","color":"","item_id":"7308","group":"Sandwich"}],"shop_receipt_id":"1-5851","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5851","change_value":"0.00","created_at":"2014-10-15 11:43:19","shop_order_payment_id":280,"shop_order_user_id":"1","change_type":"0","shop_order_id":"264","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 11:43:19","order_discount_value":"0","employee_id":"0","shop_order_id":"264","order_discount_type":"1"},{"total_amount":"49.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Tun 1.stk","description3":"null","pos_id":507,"description2":"null","order_number_id":"268","parent_type_id":"1-5852","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-15 11:47:27","quantity":"1","user_id":"1","item_cms_id":"489","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7003","shop_receipt_id":"1-5852","updated_at":"2014-10-15 11:47:27","sold_price":"24.50","color":"","item_id":"7003","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":508,"description2":"null","order_number_id":"268","parent_type_id":"1-5852","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-15 11:47:27","quantity":"1","user_id":"1","item_cms_id":"487","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7001","shop_receipt_id":"1-5852","updated_at":"2014-10-15 11:47:27","sold_price":"24.50","color":"","item_id":"7001","group":"Sandwich"}],"shop_receipt_id":"1-5852","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"30.00","payments":[{"total_amount":"30.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5852","change_value":"10.00","created_at":"2014-10-15 11:47:27","shop_order_payment_id":281,"shop_order_user_id":"1","change_type":"0","shop_order_id":"268","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 11:47:27","order_discount_value":"19.0","employee_id":"0","shop_order_id":"268","order_discount_type":"2"},{"total_amount":"30.00","transactions":[{"supplier_number":"1111111","description1":"sandwich","description3":"Custom Sale","pos_id":511,"description2":"Custom Sale","order_number_id":"269","parent_type_id":"1-5853","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"30.00","parent_type":"receipt_numbers","created_at":"2014-10-15 12:31:02","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1131413306490979","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5853","updated_at":"2014-10-15 12:31:02","sold_price":"30.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5853","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"30.00","payments":[{"total_amount":"30.00","cc_type_id":"1","day_start_id":"1131413306490979","shop_receipt_id":"1-5853","change_value":"0.00","created_at":"2014-10-15 12:31:02","shop_order_payment_id":282,"shop_order_user_id":"1","change_type":"0","shop_order_id":"269","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-15 12:31:02","order_discount_value":"0","employee_id":"0","shop_order_id":"269","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":512,"description2":"500","order_number_id":"270","parent_type_id":"1-5854","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 12:39:58","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5854","updated_at":"2014-10-15 12:39:58","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":513,"description2":"500","order_number_id":"270","parent_type_id":"1-5854","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 12:39:58","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5854","updated_at":"2014-10-15 12:39:58","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":514,"description2":"null","order_number_id":"270","parent_type_id":"1-5854","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 12:39:58","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5854","updated_at":"2014-10-15 12:39:58","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":515,"description2":"null","order_number_id":"270","parent_type_id":"1-5854","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 12:39:58","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5854","updated_at":"2014-10-15 12:39:58","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":516,"description2":"null","order_number_id":"270","parent_type_id":"1-5854","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 12:39:58","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5854","updated_at":"2014-10-15 12:39:58","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":517,"description2":"null","order_number_id":"270","parent_type_id":"1-5854","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 12:39:58","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5854","updated_at":"2014-10-15 12:39:58","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"}],"shop_receipt_id":"1-5854","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5854","change_value":"0.00","created_at":"2014-10-15 12:39:58","shop_order_payment_id":283,"shop_order_user_id":"1","change_type":"0","shop_order_id":"270","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 12:39:58","order_discount_value":"0","employee_id":"0","shop_order_id":"270","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - &AElig;ggesalat 1.stk","description3":"null","pos_id":518,"description2":"500","order_number_id":"271","parent_type_id":"1-5855","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:14:36","quantity":"1","user_id":"1","item_cms_id":"548","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7308","shop_receipt_id":"1-5855","updated_at":"2014-10-15 13:14:36","sold_price":"0.00","color":"","item_id":"7308","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":519,"description2":"null","order_number_id":"271","parent_type_id":"1-5855","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:14:36","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5855","updated_at":"2014-10-15 13:14:36","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":520,"description2":"null","order_number_id":"271","parent_type_id":"1-5855","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:14:36","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5855","updated_at":"2014-10-15 13:14:36","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"}],"shop_receipt_id":"1-5855","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5855","change_value":"0.00","created_at":"2014-10-15 13:14:36","shop_order_payment_id":284,"shop_order_user_id":"1","change_type":"0","shop_order_id":"271","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 13:14:36","order_discount_value":"0","employee_id":"0","shop_order_id":"271","order_discount_type":"1"},{"total_amount":"239.60","transactions":[{"supplier_number":"1111111","description1":"sandwich","description3":"Custom Sale","pos_id":521,"description2":"Custom Sale","order_number_id":"272","parent_type_id":"1-5856","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"59.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:29:32","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1131413306490979","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5856","updated_at":"2014-10-15 13:29:32","sold_price":"59.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"NORDISK","description1":"Sennep - Eneb&aelig;r","description3":"null","pos_id":522,"description2":"275","order_number_id":"272","parent_type_id":"1-5856","transaction_type_id":"3","discount_value":"20","buying_price":"28","selling_price":"59.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:29:32","quantity":"1","user_id":"1","item_cms_id":"607","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5711558111012","shop_receipt_id":"1-5856","updated_at":"2014-10-15 13:29:32","sold_price":"47.20","color":"","item_id":"3421","group":"Sennep"},{"supplier_number":"1111111","description1":"solb&aelig;reddike nordisk","description3":"Custom Sale","pos_id":523,"description2":"Custom Sale","order_number_id":"272","parent_type_id":"1-5856","transaction_type_id":"3","discount_value":"20","buying_price":"0","selling_price":"59.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:29:32","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1131413306490979","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5856","updated_at":"2014-10-15 13:29:32","sold_price":"47.20","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"NORDISK","description1":"Sauce - Havtorn","description3":"250","pos_id":524,"description2":"250","order_number_id":"272","parent_type_id":"1-5856","transaction_type_id":"3","discount_value":"20","buying_price":"32","selling_price":"64.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:29:32","quantity":"1","user_id":"1","item_cms_id":"220","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5711558115027","shop_receipt_id":"1-5856","updated_at":"2014-10-15 13:29:32","sold_price":"51.20","color":"","item_id":"3405","group":"Sauce"},{"supplier_number":"1111111","description1":"hyldesirup","description3":"Custom Sale","pos_id":525,"description2":"Custom Sale","order_number_id":"272","parent_type_id":"1-5856","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"35.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:29:32","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1131413306490979","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5856","updated_at":"2014-10-15 13:29:32","sold_price":"35.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5856","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"239.60","payments":[{"total_amount":"239.60","cc_type_id":"1","day_start_id":"1131413306490979","shop_receipt_id":"1-5856","change_value":"0.00","created_at":"2014-10-15 13:29:32","shop_order_payment_id":285,"shop_order_user_id":"1","change_type":"0","shop_order_id":"272","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-15 13:29:32","order_discount_value":"0","employee_id":"0","shop_order_id":"272","order_discount_type":"1"},{"total_amount":"89.00","transactions":[{"supplier_number":"MILL &amp;amp; MORTAR","description1":"Happy Roots","description3":"45","pos_id":526,"description2":"45","order_number_id":"273","parent_type_id":"1-5857","transaction_type_id":"3","discount_value":"0","buying_price":"22.5","selling_price":"45.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:34:24","quantity":"1","user_id":"1","item_cms_id":"141","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5710175131151","shop_receipt_id":"1-5857","updated_at":"2014-10-15 13:34:24","sold_price":"45.00","color":"","item_id":"2602","group":"Krydderier"},{"supplier_number":"MORTEN HEIBERG","description1":"Pulled Pork","description3":"100","pos_id":527,"description2":"100","order_number_id":"273","parent_type_id":"1-5857","transaction_type_id":"3","discount_value":"0","buying_price":"22","selling_price":"44.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:34:24","quantity":"1","user_id":"1","item_cms_id":"528","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5704920001110","shop_receipt_id":"1-5857","updated_at":"2014-10-15 13:34:24","sold_price":"44.00","color":"","item_id":"1815","group":"Krydderier"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":528,"description2":"null","order_number_id":"273","parent_type_id":"1-5857","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:34:24","quantity":"2","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5857","updated_at":"2014-10-15 13:34:24","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":529,"description2":"null","order_number_id":"273","parent_type_id":"1-5857","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:34:24","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5857","updated_at":"2014-10-15 13:34:24","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":530,"description2":"null","order_number_id":"273","parent_type_id":"1-5857","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:34:24","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5857","updated_at":"2014-10-15 13:34:24","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - &AElig;ggesalat 1.stk","description3":"null","pos_id":531,"description2":"500","order_number_id":"273","parent_type_id":"1-5857","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:34:24","quantity":"1","user_id":"1","item_cms_id":"548","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7308","shop_receipt_id":"1-5857","updated_at":"2014-10-15 13:34:24","sold_price":"0.00","color":"","item_id":"7308","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":532,"description2":"500","order_number_id":"273","parent_type_id":"1-5857","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 13:34:24","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5857","updated_at":"2014-10-15 13:34:24","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5857","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"89.00","payments":[{"total_amount":"89.00","cc_type_id":"1","day_start_id":"1131413306490979","shop_receipt_id":"1-5857","change_value":"0.00","created_at":"2014-10-15 13:34:24","shop_order_payment_id":286,"shop_order_user_id":"1","change_type":"0","shop_order_id":"273","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-15 13:34:24","order_discount_value":"0","employee_id":"0","shop_order_id":"273","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":533,"description2":"500","order_number_id":"274","parent_type_id":"1-5858","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:13:08","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5858","updated_at":"2014-10-15 14:13:08","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":534,"description2":"500","order_number_id":"274","parent_type_id":"1-5858","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:13:08","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5858","updated_at":"2014-10-15 14:13:08","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":535,"description2":"null","order_number_id":"274","parent_type_id":"1-5858","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:13:08","quantity":"3","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5858","updated_at":"2014-10-15 14:13:08","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":536,"description2":"null","order_number_id":"274","parent_type_id":"1-5858","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:13:08","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5858","updated_at":"2014-10-15 14:13:08","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"}],"shop_receipt_id":"1-5858","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5858","change_value":"0.00","created_at":"2014-10-15 14:13:08","shop_order_payment_id":287,"shop_order_user_id":"1","change_type":"0","shop_order_id":"274","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 14:13:08","order_discount_value":"0","employee_id":"0","shop_order_id":"274","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":537,"description2":"null","order_number_id":"275","parent_type_id":"1-5859","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:19:32","quantity":"2","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5859","updated_at":"2014-10-15 14:19:32","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":538,"description2":"null","order_number_id":"275","parent_type_id":"1-5859","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:19:32","quantity":"2","user_id":"1","item_cms_id":"542","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5859","updated_at":"2014-10-15 14:19:32","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":539,"description2":"null","order_number_id":"275","parent_type_id":"1-5859","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:19:32","quantity":"2","user_id":"1","item_cms_id":"544","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5859","updated_at":"2014-10-15 14:19:32","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":540,"description2":"500","order_number_id":"275","parent_type_id":"1-5859","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:19:32","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5859","updated_at":"2014-10-15 14:19:32","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - &AElig;ggesalat 1.stk","description3":"null","pos_id":541,"description2":"500","order_number_id":"275","parent_type_id":"1-5859","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:19:32","quantity":"2","user_id":"1","item_cms_id":"548","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7308","shop_receipt_id":"1-5859","updated_at":"2014-10-15 14:19:32","sold_price":"0.00","color":"","item_id":"7308","group":"Sandwich"}],"shop_receipt_id":"1-5859","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5859","change_value":"0.00","created_at":"2014-10-15 14:19:32","shop_order_payment_id":288,"shop_order_user_id":"1","change_type":"0","shop_order_id":"275","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 14:19:32","order_discount_value":"0","employee_id":"0","shop_order_id":"275","order_discount_type":"1"},{"total_amount":"52.00","transactions":[{"supplier_number":"1111111","description1":"sandwich","description3":"Custom Sale","pos_id":542,"description2":"Custom Sale","order_number_id":"276","parent_type_id":"1-5860","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"30.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:58:02","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1131413306490979","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5860","updated_at":"2014-10-15 14:58:02","sold_price":"30.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"1111111","description1":"saft","description3":"Custom Sale","pos_id":543,"description2":"Custom Sale","order_number_id":"276","parent_type_id":"1-5860","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"22.00","parent_type":"receipt_numbers","created_at":"2014-10-15 14:58:02","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1131413306490979","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5860","updated_at":"2014-10-15 14:58:02","sold_price":"22.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5860","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"52.00","payments":[{"total_amount":"52.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5860","change_value":"48.00","created_at":"2014-10-15 14:58:02","shop_order_payment_id":289,"shop_order_user_id":"1","change_type":"0","shop_order_id":"276","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 14:58:02","order_discount_value":"0","employee_id":"0","shop_order_id":"276","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":544,"description2":"null","order_number_id":"277","parent_type_id":"1-5861","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 15:52:35","quantity":"1","user_id":"1","item_cms_id":"540","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5861","updated_at":"2014-10-15 15:52:35","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5861","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5861","change_value":"0.00","created_at":"2014-10-15 15:52:35","shop_order_payment_id":290,"shop_order_user_id":"1","change_type":"0","shop_order_id":"277","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 15:52:35","order_discount_value":"0","employee_id":"0","shop_order_id":"277","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":545,"description2":"500","order_number_id":"278","parent_type_id":"1-5862","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 16:51:01","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5862","updated_at":"2014-10-15 16:51:01","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":546,"description2":"500","order_number_id":"278","parent_type_id":"1-5862","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 16:51:01","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5862","updated_at":"2014-10-15 16:51:01","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":547,"description2":"null","order_number_id":"278","parent_type_id":"1-5862","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 16:51:01","quantity":"2","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5862","updated_at":"2014-10-15 16:51:01","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":548,"description2":"null","order_number_id":"278","parent_type_id":"1-5862","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 16:51:01","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5862","updated_at":"2014-10-15 16:51:01","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":549,"description2":"null","order_number_id":"278","parent_type_id":"1-5862","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 16:51:01","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5862","updated_at":"2014-10-15 16:51:01","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"}],"shop_receipt_id":"1-5862","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5862","change_value":"0.00","created_at":"2014-10-15 16:51:01","shop_order_payment_id":291,"shop_order_user_id":"1","change_type":"0","shop_order_id":"278","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 16:51:01","order_discount_value":"0","employee_id":"0","shop_order_id":"278","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":550,"description2":"null","order_number_id":"279","parent_type_id":"1-5863","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:12:15","quantity":"1","user_id":"1","item_cms_id":"540","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5863","updated_at":"2014-10-15 17:12:15","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5863","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5863","change_value":"0.00","created_at":"2014-10-15 17:12:15","shop_order_payment_id":292,"shop_order_user_id":"1","change_type":"0","shop_order_id":"279","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 17:12:15","order_discount_value":"0","employee_id":"0","shop_order_id":"279","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":551,"description2":"null","order_number_id":"280","parent_type_id":"1-5864","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:35:00","quantity":"4","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5864","updated_at":"2014-10-15 17:35:00","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":552,"description2":"null","order_number_id":"280","parent_type_id":"1-5864","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:35:00","quantity":"3","user_id":"1","item_cms_id":"542","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5864","updated_at":"2014-10-15 17:35:00","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":553,"description2":"null","order_number_id":"280","parent_type_id":"1-5864","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:35:00","quantity":"3","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5864","updated_at":"2014-10-15 17:35:00","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":554,"description2":"null","order_number_id":"280","parent_type_id":"1-5864","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:35:00","quantity":"2","user_id":"1","item_cms_id":"544","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5864","updated_at":"2014-10-15 17:35:00","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":555,"description2":"null","order_number_id":"280","parent_type_id":"1-5864","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:35:00","quantity":"2","user_id":"1","item_cms_id":"541","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5864","updated_at":"2014-10-15 17:35:00","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":556,"description2":"500","order_number_id":"280","parent_type_id":"1-5864","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:35:00","quantity":"2","user_id":"1","item_cms_id":"552","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5864","updated_at":"2014-10-15 17:35:00","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"&AElig;blemost drikkeklar","description3":"null","pos_id":557,"description2":"500","order_number_id":"280","parent_type_id":"1-5864","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:35:00","quantity":"3","user_id":"1","item_cms_id":"550","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7310","shop_receipt_id":"1-5864","updated_at":"2014-10-15 17:35:00","sold_price":"0.00","color":"","item_id":"7310","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":558,"description2":"500","order_number_id":"280","parent_type_id":"1-5864","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:35:00","quantity":"2","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5864","updated_at":"2014-10-15 17:35:00","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5864","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5864","change_value":"0.00","created_at":"2014-10-15 17:35:00","shop_order_payment_id":293,"shop_order_user_id":"1","change_type":"0","shop_order_id":"280","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 17:35:00","order_discount_value":"0","employee_id":"0","shop_order_id":"280","order_discount_type":"1"},{"total_amount":"34.00","transactions":[{"supplier_number":"Premium drinks","description1":"Birk Original","description3":"330","pos_id":559,"description2":"330","order_number_id":"281","parent_type_id":"1-5865","transaction_type_id":"3","discount_value":"0","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:36:43","quantity":"2","user_id":"1","item_cms_id":"262","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5710416101134","shop_receipt_id":"1-5865","updated_at":"2014-10-15 17:36:43","sold_price":"34.00","color":"","item_id":"1500","group":"Drikkevarer"}],"shop_receipt_id":"1-5865","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"34.00","payments":[{"total_amount":"34.00","cc_type_id":"1","day_start_id":"1131413306490979","shop_receipt_id":"1-5865","change_value":"0.00","created_at":"2014-10-15 17:36:43","shop_order_payment_id":294,"shop_order_user_id":"1","change_type":"0","shop_order_id":"281","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-15 17:36:43","order_discount_value":"0","employee_id":"0","shop_order_id":"281","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":560,"description2":"null","order_number_id":"282","parent_type_id":"1-5866","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:48:55","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5866","updated_at":"2014-10-15 17:48:55","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":561,"description2":"null","order_number_id":"282","parent_type_id":"1-5866","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:48:55","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5866","updated_at":"2014-10-15 17:48:55","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":562,"description2":"null","order_number_id":"282","parent_type_id":"1-5866","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:48:55","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5866","updated_at":"2014-10-15 17:48:55","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":563,"description2":"null","order_number_id":"282","parent_type_id":"1-5866","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:48:55","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5866","updated_at":"2014-10-15 17:48:55","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":564,"description2":"500","order_number_id":"282","parent_type_id":"1-5866","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:48:55","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5866","updated_at":"2014-10-15 17:48:55","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":565,"description2":"500","order_number_id":"282","parent_type_id":"1-5866","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 17:48:55","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5866","updated_at":"2014-10-15 17:48:55","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5866","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5866","change_value":"0.00","created_at":"2014-10-15 17:48:55","shop_order_payment_id":295,"shop_order_user_id":"1","change_type":"0","shop_order_id":"282","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 17:48:55","order_discount_value":"0","employee_id":"0","shop_order_id":"282","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":566,"description2":"500","order_number_id":"283","parent_type_id":"1-5867","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:24:26","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5867","updated_at":"2014-10-15 18:24:26","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"&AElig;blemost drikkeklar","description3":"null","pos_id":567,"description2":"500","order_number_id":"283","parent_type_id":"1-5867","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:24:26","quantity":"1","user_id":"1","item_cms_id":"550","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7310","shop_receipt_id":"1-5867","updated_at":"2014-10-15 18:24:26","sold_price":"0.00","color":"","item_id":"7310","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":568,"description2":"null","order_number_id":"283","parent_type_id":"1-5867","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:24:26","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5867","updated_at":"2014-10-15 18:24:26","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":569,"description2":"null","order_number_id":"283","parent_type_id":"1-5867","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:24:26","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5867","updated_at":"2014-10-15 18:24:26","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":570,"description2":"null","order_number_id":"283","parent_type_id":"1-5867","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:24:26","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5867","updated_at":"2014-10-15 18:24:26","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":571,"description2":"null","order_number_id":"283","parent_type_id":"1-5867","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:24:26","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5867","updated_at":"2014-10-15 18:24:26","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"}],"shop_receipt_id":"1-5867","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5867","change_value":"0.00","created_at":"2014-10-15 18:24:26","shop_order_payment_id":296,"shop_order_user_id":"1","change_type":"0","shop_order_id":"283","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 18:24:26","order_discount_value":"0","employee_id":"0","shop_order_id":"283","order_discount_type":"1"},{"total_amount":"59.40","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":572,"description2":"null","order_number_id":"284","parent_type_id":"1-5868","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:31:40","quantity":"1","user_id":"1","item_cms_id":"540","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5868","updated_at":"2014-10-15 18:31:40","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"},{"supplier_number":"1111111","description1":"br&oslash;d","description3":"Custom Sale","pos_id":573,"description2":"Custom Sale","order_number_id":"284","parent_type_id":"1-5868","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"30.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:31:40","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1131413306490979","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5868","updated_at":"2014-10-15 18:31:40","sold_price":"30.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"Stefan P&amp;aring;lsson","description1":"Romsill","description3":"300g","pos_id":574,"description2":"300g","order_number_id":"284","parent_type_id":"1-5868","transaction_type_id":"3","discount_value":"40","buying_price":"27132","selling_price":"49.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:31:40","quantity":"1","user_id":"1","item_cms_id":"367","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"7350007031433","shop_receipt_id":"1-5868","updated_at":"2014-10-15 18:31:40","sold_price":"29.40","color":"","item_id":"4308","group":"Fisk &amp; Skalddyr"}],"shop_receipt_id":"1-5868","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"59.40","payments":[{"total_amount":"59.40","cc_type_id":"1","day_start_id":"1131413306490979","shop_receipt_id":"1-5868","change_value":"0.00","created_at":"2014-10-15 18:31:40","shop_order_payment_id":297,"shop_order_user_id":"1","change_type":"0","shop_order_id":"284","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-15 18:31:40","order_discount_value":"0","employee_id":"0","shop_order_id":"284","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":575,"description2":"null","order_number_id":"285","parent_type_id":"1-5869","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:34:45","quantity":"2","user_id":"1","item_cms_id":"542","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5869","updated_at":"2014-10-15 18:34:45","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":576,"description2":"null","order_number_id":"285","parent_type_id":"1-5869","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:34:45","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5869","updated_at":"2014-10-15 18:34:45","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":577,"description2":"null","order_number_id":"285","parent_type_id":"1-5869","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:34:45","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5869","updated_at":"2014-10-15 18:34:45","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":578,"description2":"500","order_number_id":"285","parent_type_id":"1-5869","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:34:45","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5869","updated_at":"2014-10-15 18:34:45","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":579,"description2":"500","order_number_id":"285","parent_type_id":"1-5869","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-15 18:34:45","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1131413306490979","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5869","updated_at":"2014-10-15 18:34:45","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"}],"shop_receipt_id":"1-5869","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1131413306490979","shop_receipt_id":"1-5869","change_value":"0.00","created_at":"2014-10-15 18:34:45","shop_order_payment_id":298,"shop_order_user_id":"1","change_type":"0","shop_order_id":"285","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-15 18:34:45","order_discount_value":"0","employee_id":"0","shop_order_id":"285","order_discount_type":"1"},{"total_amount":"-622.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":580,"description2":"","order_number_id":"287","parent_type_id":"1191413392564977","transaction_type_id":"9","discount_value":"","buying_price":"","parent_type":"Day End","selling_price":"","created_at":"2014-10-15 19:02:45","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1131413306490979","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-15 19:02:45","sold_price":"-622.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1131413306490979","shop_user_id":"1","sold_total_amount":"-622.5","payments":[{"total_amount":"-622.5","cc_type_id":"","day_start_id":"1131413306490979","shop_receipt_id":"0","change_value":"","created_at":"2014-10-15 19:02:45","shop_order_payment_id":299,"shop_order_user_id":"1","change_type":"","shop_order_id":"287","payment_type_id":"8"}],"status_id":"3","created_at":"2014-10-15 19:02:45","order_discount_value":"","employee_id":"","shop_order_id":"287","order_discount_type":""},{"total_amount":"621.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":581,"description2":"","order_number_id":"288","parent_type_id":"1141413392565103","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2014-10-16 11:04:23","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1141413392565103","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-16 11:04:23","sold_price":"621.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"621.5","payments":[{"total_amount":"621.5","cc_type_id":"","day_start_id":"1141413392565103","shop_receipt_id":"0","change_value":"","created_at":"2014-10-16 11:04:23","shop_order_payment_id":300,"shop_order_user_id":"1","change_type":"","shop_order_id":"288","payment_type_id":"7"}],"status_id":"3","created_at":"2014-10-16 11:04:23","order_discount_value":"","employee_id":"","shop_order_id":"288","order_discount_type":""},{"total_amount":"30.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":583,"description2":"null","order_number_id":"286","parent_type_id":"1-5870","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-16 11:54:00","quantity":"1","user_id":"131409402138","item_cms_id":"487","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7001","shop_receipt_id":"1-5870","updated_at":"2014-10-16 11:54:00","sold_price":"24.50","color":"","item_id":"7001","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":584,"description2":"null","order_number_id":"286","parent_type_id":"1-5870","transaction_type_id":"3","discount_value":"19","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-16 11:54:00","quantity":"1","user_id":"131409402138","item_cms_id":"493","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7007","shop_receipt_id":"1-5870","updated_at":"2014-10-16 11:54:00","sold_price":"5.50","color":"","item_id":"7007","group":"Sandwich"}],"shop_receipt_id":"1-5870","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"30.00","payments":[{"total_amount":"30.00","cc_type_id":"1","day_start_id":"1141413392565103","shop_receipt_id":"1-5870","change_value":"0.00","created_at":"2014-10-16 11:54:00","shop_order_payment_id":301,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"286","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-16 11:54:00","order_discount_value":"0","employee_id":"0","shop_order_id":"286","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Pastrami 1.stk","description3":"null","pos_id":585,"description2":"null","order_number_id":"286","parent_type_id":"1-5871","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 11:56:58","quantity":"1","user_id":"131409402138","item_cms_id":"545","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7305","shop_receipt_id":"1-5871","updated_at":"2014-10-16 11:56:58","sold_price":"0.00","color":"","item_id":"7305","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":586,"description2":"null","order_number_id":"286","parent_type_id":"1-5871","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 11:56:58","quantity":"1","user_id":"131409402138","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5871","updated_at":"2014-10-16 11:56:58","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"}],"shop_receipt_id":"1-5871","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5871","change_value":"0.00","created_at":"2014-10-16 11:56:58","shop_order_payment_id":302,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"286","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 11:56:58","order_discount_value":"0","employee_id":"0","shop_order_id":"286","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":587,"description2":"null","order_number_id":"286","parent_type_id":"1-5872","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 12:26:58","quantity":"2","user_id":"131409402138","item_cms_id":"547","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5872","updated_at":"2014-10-16 12:26:58","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Bl&aring;b&aelig;r","description3":"330","pos_id":588,"description2":"330","order_number_id":"286","parent_type_id":"1-5872","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 12:26:58","quantity":"1","user_id":"131409402138","item_cms_id":"266","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416103138","shop_receipt_id":"1-5872","updated_at":"2014-10-16 12:26:58","sold_price":"0.00","color":"","item_id":"1504","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Hindb&aelig;r","description3":"330","pos_id":589,"description2":"330","order_number_id":"286","parent_type_id":"1-5872","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 12:26:58","quantity":"1","user_id":"131409402138","item_cms_id":"265","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416104135","shop_receipt_id":"1-5872","updated_at":"2014-10-16 12:26:58","sold_price":"0.00","color":"","item_id":"1503","group":"Drikkevarer"}],"shop_receipt_id":"1-5872","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5872","change_value":"0.00","created_at":"2014-10-16 12:26:58","shop_order_payment_id":303,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"286","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 12:26:58","order_discount_value":"0","employee_id":"0","shop_order_id":"286","order_discount_type":"1"},{"total_amount":"49.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Tun 1.stk","description3":"null","pos_id":590,"description2":"null","order_number_id":"289","parent_type_id":"1-5873","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-16 12:38:05","quantity":"2","user_id":"131409402138","item_cms_id":"494","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7008","shop_receipt_id":"1-5873","updated_at":"2014-10-16 12:38:05","sold_price":"49.00","color":"","item_id":"7008","group":"Sandwich"}],"shop_receipt_id":"1-5873","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"49.00","payments":[{"total_amount":"49.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5873","change_value":"51.00","created_at":"2014-10-16 12:38:05","shop_order_payment_id":304,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"289","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 12:38:05","order_discount_value":"0","employee_id":"0","shop_order_id":"289","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":591,"description2":"null","order_number_id":"289","parent_type_id":"1-5874","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 12:52:53","quantity":"1","user_id":"131409402138","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5874","updated_at":"2014-10-16 12:52:53","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Hyldeblomst","description3":"330","pos_id":592,"description2":"330","order_number_id":"289","parent_type_id":"1-5874","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 12:52:53","quantity":"2","user_id":"131409402138","item_cms_id":"264","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416102131","shop_receipt_id":"1-5874","updated_at":"2014-10-16 12:52:53","sold_price":"0.00","color":"","item_id":"1502","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":593,"description2":"null","order_number_id":"289","parent_type_id":"1-5874","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 12:52:53","quantity":"1","user_id":"131409402138","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5874","updated_at":"2014-10-16 12:52:53","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"}],"shop_receipt_id":"1-5874","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5874","change_value":"0.00","created_at":"2014-10-16 12:52:53","shop_order_payment_id":305,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"289","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 12:52:53","order_discount_value":"0","employee_id":"0","shop_order_id":"289","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":594,"description2":"null","order_number_id":"290","parent_type_id":"1-5875","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 13:30:42","quantity":"3","user_id":"131409402138","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5875","updated_at":"2014-10-16 13:30:42","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":595,"description2":"null","order_number_id":"290","parent_type_id":"1-5875","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 13:30:42","quantity":"1","user_id":"131409402138","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5875","updated_at":"2014-10-16 13:30:42","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Hyldeblomst","description3":"330","pos_id":596,"description2":"330","order_number_id":"290","parent_type_id":"1-5875","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 13:30:42","quantity":"2","user_id":"131409402138","item_cms_id":"264","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416102131","shop_receipt_id":"1-5875","updated_at":"2014-10-16 13:30:42","sold_price":"0.00","color":"","item_id":"1502","group":"Drikkevarer"}],"shop_receipt_id":"1-5875","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5875","change_value":"0.00","created_at":"2014-10-16 13:30:42","shop_order_payment_id":306,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"290","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 13:30:42","order_discount_value":"0","employee_id":"0","shop_order_id":"290","order_discount_type":"1"},{"total_amount":"73.50","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Tun 1.stk","description3":"null","pos_id":597,"description2":"null","order_number_id":"291","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"parked","created_at":"2014-10-16 14:05:32","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"494","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7008","shop_receipt_id":"0","updated_at":"2014-10-16 14:05:32","sold_price":"24.50","color":"","item_id":"7008","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Pastrami 1.stk","description3":"null","pos_id":598,"description2":"null","order_number_id":"291","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"parked","created_at":"2014-10-16 14:05:32","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"491","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7005","shop_receipt_id":"0","updated_at":"2014-10-16 14:05:32","sold_price":"24.50","color":"","item_id":"7005","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Kylling 1.stk","description3":"null","pos_id":599,"description2":"null","order_number_id":"291","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"parked","created_at":"2014-10-16 14:05:32","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"490","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7004","shop_receipt_id":"0","updated_at":"2014-10-16 14:05:32","sold_price":"24.50","color":"","item_id":"7004","group":"Sandwich"}],"shop_receipt_id":"0","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"73.50","payments":[{"day_start_id":"1141413392565103","shop_order_payment_id":"0","payment_type_id":""},{"shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-16 14:05:32","order_discount_value":"0","employee_id":"0","shop_order_id":"291","order_discount_type":"1"},{"total_amount":"-30","transactions":[{"promotion_ids":"","supplier_number":"","description1":"dansk handicap forening","description3":"dansk handicap forening","pos_id":601,"description2":"dansk handicap forening","order_number_id":"293","parent_type_id":"6","transaction_type_id":"7","discount_value":"","buying_price":"","parent_type":"Cash Out","selling_price":"","created_at":"2014-10-16 14:07:11","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1141413392565103","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-16 14:07:11","sold_price":"-30","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"-30","payments":[{"total_amount":"-30","cc_type_id":"","day_start_id":"1141413392565103","shop_receipt_id":"0","change_value":"","created_at":"2014-10-16 14:07:11","shop_order_payment_id":307,"shop_order_user_id":"1","change_type":"","shop_order_id":"293","payment_type_id":"6"}],"status_id":"3","created_at":"2014-10-16 14:07:11","order_discount_value":"","employee_id":"","shop_order_id":"293","order_discount_type":""},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":602,"description2":"null","order_number_id":"292","parent_type_id":"1-5876","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 14:46:59","quantity":"1","user_id":"131409402138","item_cms_id":"540","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5876","updated_at":"2014-10-16 14:46:59","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5876","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5876","change_value":"0.00","created_at":"2014-10-16 14:46:59","shop_order_payment_id":308,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"292","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 14:46:59","order_discount_value":"0","employee_id":"0","shop_order_id":"292","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":603,"description2":"null","order_number_id":"292","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-16 15:23:30","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7302","shop_receipt_id":"1-5876","updated_at":"2014-10-16 15:23:30","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":604,"description2":"null","order_number_id":"292","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-16 15:23:30","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7304","shop_receipt_id":"1-5876","updated_at":"2014-10-16 15:23:30","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":605,"description2":"null","order_number_id":"292","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-16 15:23:30","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"547","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7307","shop_receipt_id":"1-5876","updated_at":"2014-10-16 15:23:30","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":606,"description2":"null","order_number_id":"292","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-16 15:23:30","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7301","shop_receipt_id":"1-5876","updated_at":"2014-10-16 15:23:30","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"MY goodness","description1":"Aronia &amp; Bluebrry","description3":"37 cl","pos_id":607,"description2":"37 cl","order_number_id":"292","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"19.00","parent_type":"parked","created_at":"2014-10-16 15:23:30","quantity":"4","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"193","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"2","size":"","ean":"5707535001094","shop_receipt_id":"1-5876","updated_at":"2014-10-16 15:23:30","sold_price":"0.00","color":"","item_id":"2405","group":"Drikkevarer"}],"shop_receipt_id":"1-5876","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"day_start_id":"1141413392565103","shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-16 15:23:30","order_discount_value":"0","employee_id":"0","shop_order_id":"292","order_discount_type":"1"},{"total_amount":"-100","transactions":[{"promotion_ids":"","supplier_number":"","description1":"indkab","description3":"indkab","pos_id":608,"description2":"indkab","order_number_id":"295","parent_type_id":"7","transaction_type_id":"7","discount_value":"","buying_price":"","parent_type":"Cash Out","selling_price":"","created_at":"2014-10-16 15:23:48","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1141413392565103","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-16 15:23:48","sold_price":"-100","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"-100","payments":[{"total_amount":"-100","cc_type_id":"","day_start_id":"1141413392565103","shop_receipt_id":"0","change_value":"","created_at":"2014-10-16 15:23:48","shop_order_payment_id":309,"shop_order_user_id":"1","change_type":"","shop_order_id":"295","payment_type_id":"6"}],"status_id":"3","created_at":"2014-10-16 15:23:48","order_discount_value":"","employee_id":"","shop_order_id":"295","order_discount_type":""},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get 1.stk","description3":"null","pos_id":609,"description2":"null","order_number_id":"294","parent_type_id":"1-5877","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 15:46:16","quantity":"6","user_id":"131409402138","item_cms_id":"546","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7306","shop_receipt_id":"1-5877","updated_at":"2014-10-16 15:46:16","sold_price":"0.00","color":"","item_id":"7306","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":610,"description2":"null","order_number_id":"294","parent_type_id":"1-5877","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 15:46:16","quantity":"5","user_id":"131409402138","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5877","updated_at":"2014-10-16 15:46:16","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":611,"description2":"null","order_number_id":"294","parent_type_id":"1-5877","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 15:46:16","quantity":"1","user_id":"131409402138","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5877","updated_at":"2014-10-16 15:46:16","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"}],"shop_receipt_id":"1-5877","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5877","change_value":"0.00","created_at":"2014-10-16 15:46:16","shop_order_payment_id":310,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"294","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 15:46:16","order_discount_value":"0","employee_id":"0","shop_order_id":"294","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get 1.stk","description3":"null","pos_id":612,"description2":"null","order_number_id":"294","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-16 16:11:26","quantity":"6","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"546","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7306","shop_receipt_id":"1-5877","updated_at":"2014-10-16 16:11:26","sold_price":"0.00","color":"","item_id":"7306","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":613,"description2":"null","order_number_id":"294","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-16 16:11:26","quantity":"5","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7301","shop_receipt_id":"1-5877","updated_at":"2014-10-16 16:11:26","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":614,"description2":"null","order_number_id":"294","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-16 16:11:26","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7304","shop_receipt_id":"1-5877","updated_at":"2014-10-16 16:11:26","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"}],"shop_receipt_id":"1-5877","day_start_id":"1141413392565103","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"day_start_id":"1141413392565103","shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-16 16:11:26","order_discount_value":"0","employee_id":"0","shop_order_id":"294","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":615,"description2":"null","order_number_id":"296","parent_type_id":"1-5878","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:13:15","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5878","updated_at":"2014-10-16 16:13:15","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":616,"description2":"null","order_number_id":"296","parent_type_id":"1-5878","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:13:15","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5878","updated_at":"2014-10-16 16:13:15","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"}],"shop_receipt_id":"1-5878","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5878","change_value":"0.00","created_at":"2014-10-16 16:13:15","shop_order_payment_id":311,"shop_order_user_id":"1","change_type":"0","shop_order_id":"296","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 16:13:15","order_discount_value":"0","employee_id":"0","shop_order_id":"296","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":617,"description2":"null","order_number_id":"297","parent_type_id":"1-5879","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:24:43","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5879","updated_at":"2014-10-16 16:24:43","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":618,"description2":"null","order_number_id":"297","parent_type_id":"1-5879","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:24:43","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5879","updated_at":"2014-10-16 16:24:43","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":619,"description2":"500","order_number_id":"297","parent_type_id":"1-5879","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:24:43","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5879","updated_at":"2014-10-16 16:24:43","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"}],"shop_receipt_id":"1-5879","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5879","change_value":"0.00","created_at":"2014-10-16 16:24:43","shop_order_payment_id":312,"shop_order_user_id":"1","change_type":"0","shop_order_id":"297","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 16:24:43","order_discount_value":"0","employee_id":"0","shop_order_id":"297","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":620,"description2":"null","order_number_id":"298","parent_type_id":"1-5880","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:31:50","quantity":"2","user_id":"1","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5880","updated_at":"2014-10-16 16:31:50","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":621,"description2":"null","order_number_id":"298","parent_type_id":"1-5880","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:31:50","quantity":"2","user_id":"1","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5880","updated_at":"2014-10-16 16:31:50","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Bl&aring;b&aelig;r","description3":"330","pos_id":622,"description2":"330","order_number_id":"298","parent_type_id":"1-5880","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:31:50","quantity":"2","user_id":"1","item_cms_id":"266","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416103138","shop_receipt_id":"1-5880","updated_at":"2014-10-16 16:31:50","sold_price":"0.00","color":"","item_id":"1504","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Ginger Lime","description3":"330","pos_id":623,"description2":"330","order_number_id":"298","parent_type_id":"1-5880","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 16:31:50","quantity":"2","user_id":"1","item_cms_id":"263","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416105200","shop_receipt_id":"1-5880","updated_at":"2014-10-16 16:31:50","sold_price":"0.00","color":"","item_id":"1501","group":"Drikkevarer"}],"shop_receipt_id":"1-5880","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5880","change_value":"0.00","created_at":"2014-10-16 16:31:50","shop_order_payment_id":313,"shop_order_user_id":"1","change_type":"0","shop_order_id":"298","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 16:31:50","order_discount_value":"0","employee_id":"0","shop_order_id":"298","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":624,"description2":"null","order_number_id":"299","parent_type_id":"1-5881","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:01:14","quantity":"2","user_id":"1","item_cms_id":"540","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5881","updated_at":"2014-10-16 17:01:14","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5881","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5881","change_value":"0.00","created_at":"2014-10-16 17:01:14","shop_order_payment_id":314,"shop_order_user_id":"1","change_type":"0","shop_order_id":"299","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 17:01:14","order_discount_value":"0","employee_id":"0","shop_order_id":"299","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":625,"description2":"null","order_number_id":"300","parent_type_id":"1-5882","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:05:52","quantity":"2","user_id":"1","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5882","updated_at":"2014-10-16 17:05:52","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":626,"description2":"null","order_number_id":"300","parent_type_id":"1-5882","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:05:52","quantity":"2","user_id":"1","item_cms_id":"543","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5882","updated_at":"2014-10-16 17:05:52","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":627,"description2":"null","order_number_id":"300","parent_type_id":"1-5882","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:05:52","quantity":"2","user_id":"1","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5882","updated_at":"2014-10-16 17:05:52","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":628,"description2":"null","order_number_id":"300","parent_type_id":"1-5882","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:05:52","quantity":"2","user_id":"1","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5882","updated_at":"2014-10-16 17:05:52","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"&AElig;blemost drikkeklar","description3":"null","pos_id":629,"description2":"500","order_number_id":"300","parent_type_id":"1-5882","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:05:52","quantity":"2","user_id":"1","item_cms_id":"550","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7310","shop_receipt_id":"1-5882","updated_at":"2014-10-16 17:05:52","sold_price":"0.00","color":"","item_id":"7310","group":"Drikkevarer"}],"shop_receipt_id":"1-5882","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5882","change_value":"0.00","created_at":"2014-10-16 17:05:52","shop_order_payment_id":315,"shop_order_user_id":"1","change_type":"0","shop_order_id":"300","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 17:05:52","order_discount_value":"0","employee_id":"0","shop_order_id":"300","order_discount_type":"1"},{"total_amount":"99.00","transactions":[{"supplier_number":"&amp;Oslash;sterlandsk Thehus","description1":"Kiss me Monkey","description3":"null","pos_id":630,"description2":"398","order_number_id":"301","parent_type_id":"1-5883","transaction_type_id":"3","discount_value":"0","buying_price":"50","selling_price":"99.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:06:17","quantity":"1","user_id":"1","item_cms_id":"448","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5711738000044","shop_receipt_id":"1-5883","updated_at":"2014-10-16 17:06:17","sold_price":"99.00","color":"","item_id":"1012","group":"Gourmet The"}],"shop_receipt_id":"1-5883","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"75.00","payments":[{"total_amount":"75.00","cc_type_id":"1","day_start_id":"1141413392565103","shop_receipt_id":"1-5883","change_value":"0.00","created_at":"2014-10-16 17:06:17","shop_order_payment_id":316,"shop_order_user_id":"1","change_type":"0","shop_order_id":"301","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-16 17:06:17","order_discount_value":"24.0","employee_id":"0","shop_order_id":"301","order_discount_type":"2"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":631,"description2":"null","order_number_id":"302","parent_type_id":"1-5884","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:43:19","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5884","updated_at":"2014-10-16 17:43:19","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":632,"description2":"null","order_number_id":"302","parent_type_id":"1-5884","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:43:19","quantity":"2","user_id":"1","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5884","updated_at":"2014-10-16 17:43:19","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":633,"description2":"null","order_number_id":"302","parent_type_id":"1-5884","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:43:19","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5884","updated_at":"2014-10-16 17:43:19","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Hyldeblomst","description3":"330","pos_id":634,"description2":"330","order_number_id":"302","parent_type_id":"1-5884","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:43:19","quantity":"2","user_id":"1","item_cms_id":"264","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416102131","shop_receipt_id":"1-5884","updated_at":"2014-10-16 17:43:19","sold_price":"0.00","color":"","item_id":"1502","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Bl&aring;b&aelig;r","description3":"330","pos_id":635,"description2":"330","order_number_id":"302","parent_type_id":"1-5884","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:43:19","quantity":"1","user_id":"1","item_cms_id":"266","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416103138","shop_receipt_id":"1-5884","updated_at":"2014-10-16 17:43:19","sold_price":"0.00","color":"","item_id":"1504","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Ginger Lime","description3":"330","pos_id":636,"description2":"330","order_number_id":"302","parent_type_id":"1-5884","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:43:19","quantity":"1","user_id":"1","item_cms_id":"263","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416105200","shop_receipt_id":"1-5884","updated_at":"2014-10-16 17:43:19","sold_price":"0.00","color":"","item_id":"1501","group":"Drikkevarer"}],"shop_receipt_id":"1-5884","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5884","change_value":"0.00","created_at":"2014-10-16 17:43:19","shop_order_payment_id":317,"shop_order_user_id":"1","change_type":"0","shop_order_id":"302","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 17:43:19","order_discount_value":"0","employee_id":"0","shop_order_id":"302","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Premium drinks","description1":"Birk Hyldeblomst","description3":"330","pos_id":637,"description2":"330","order_number_id":"303","parent_type_id":"1-5885","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:56:12","quantity":"1","user_id":"1","item_cms_id":"264","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416102131","shop_receipt_id":"1-5885","updated_at":"2014-10-16 17:56:12","sold_price":"0.00","color":"","item_id":"1502","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Ginger Lime","description3":"330","pos_id":638,"description2":"330","order_number_id":"303","parent_type_id":"1-5885","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:56:12","quantity":"1","user_id":"1","item_cms_id":"263","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416105200","shop_receipt_id":"1-5885","updated_at":"2014-10-16 17:56:12","sold_price":"0.00","color":"","item_id":"1501","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":639,"description2":"null","order_number_id":"303","parent_type_id":"1-5885","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:56:12","quantity":"2","user_id":"1","item_cms_id":"547","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5885","updated_at":"2014-10-16 17:56:12","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":640,"description2":"null","order_number_id":"303","parent_type_id":"1-5885","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 17:56:12","quantity":"2","user_id":"1","item_cms_id":"543","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5885","updated_at":"2014-10-16 17:56:12","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"}],"shop_receipt_id":"1-5885","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5885","change_value":"0.00","created_at":"2014-10-16 17:56:12","shop_order_payment_id":318,"shop_order_user_id":"1","change_type":"0","shop_order_id":"303","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 17:56:12","order_discount_value":"0","employee_id":"0","shop_order_id":"303","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Premium drinks","description1":"Birk Hindb&aelig;r","description3":"330","pos_id":641,"description2":"330","order_number_id":"304","parent_type_id":"1-5886","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:06:46","quantity":"2","user_id":"1","item_cms_id":"265","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416104135","shop_receipt_id":"1-5886","updated_at":"2014-10-16 18:06:46","sold_price":"0.00","color":"","item_id":"1503","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":642,"description2":"null","order_number_id":"304","parent_type_id":"1-5886","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:06:46","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5886","updated_at":"2014-10-16 18:06:46","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":643,"description2":"null","order_number_id":"304","parent_type_id":"1-5886","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:06:46","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5886","updated_at":"2014-10-16 18:06:46","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":644,"description2":"null","order_number_id":"304","parent_type_id":"1-5886","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:06:46","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5886","updated_at":"2014-10-16 18:06:46","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"}],"shop_receipt_id":"1-5886","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5886","change_value":"0.00","created_at":"2014-10-16 18:06:46","shop_order_payment_id":319,"shop_order_user_id":"1","change_type":"0","shop_order_id":"304","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 18:06:46","order_discount_value":"0","employee_id":"0","shop_order_id":"304","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Premium drinks","description1":"Birk Bl&aring;b&aelig;r","description3":"330","pos_id":645,"description2":"330","order_number_id":"305","parent_type_id":"1-5887","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:16:44","quantity":"1","user_id":"1","item_cms_id":"266","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416103138","shop_receipt_id":"1-5887","updated_at":"2014-10-16 18:16:44","sold_price":"0.00","color":"","item_id":"1504","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Hindb&aelig;r","description3":"330","pos_id":646,"description2":"330","order_number_id":"305","parent_type_id":"1-5887","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:16:44","quantity":"1","user_id":"1","item_cms_id":"265","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416104135","shop_receipt_id":"1-5887","updated_at":"2014-10-16 18:16:44","sold_price":"0.00","color":"","item_id":"1503","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":647,"description2":"null","order_number_id":"305","parent_type_id":"1-5887","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:16:44","quantity":"2","user_id":"1","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5887","updated_at":"2014-10-16 18:16:44","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":648,"description2":"null","order_number_id":"305","parent_type_id":"1-5887","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:16:44","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5887","updated_at":"2014-10-16 18:16:44","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":649,"description2":"null","order_number_id":"305","parent_type_id":"1-5887","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:16:44","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5887","updated_at":"2014-10-16 18:16:44","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"}],"shop_receipt_id":"1-5887","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5887","change_value":"0.00","created_at":"2014-10-16 18:16:44","shop_order_payment_id":320,"shop_order_user_id":"1","change_type":"0","shop_order_id":"305","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 18:16:44","order_discount_value":"0","employee_id":"0","shop_order_id":"305","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Premium drinks","description1":"Birk Hyldeblomst","description3":"330","pos_id":650,"description2":"330","order_number_id":"306","parent_type_id":"1-5888","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:17:37","quantity":"3","user_id":"1","item_cms_id":"264","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416102131","shop_receipt_id":"1-5888","updated_at":"2014-10-16 18:17:37","sold_price":"0.00","color":"","item_id":"1502","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - &AElig;ggesalat 1.stk","description3":"null","pos_id":651,"description2":"500","order_number_id":"306","parent_type_id":"1-5888","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:17:37","quantity":"2","user_id":"1","item_cms_id":"548","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7308","shop_receipt_id":"1-5888","updated_at":"2014-10-16 18:17:37","sold_price":"0.00","color":"","item_id":"7308","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":652,"description2":"null","order_number_id":"306","parent_type_id":"1-5888","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:17:37","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5888","updated_at":"2014-10-16 18:17:37","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":653,"description2":"null","order_number_id":"306","parent_type_id":"1-5888","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:17:37","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5888","updated_at":"2014-10-16 18:17:37","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":654,"description2":"null","order_number_id":"306","parent_type_id":"1-5888","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:17:37","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5888","updated_at":"2014-10-16 18:17:37","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"}],"shop_receipt_id":"1-5888","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5888","change_value":"0.00","created_at":"2014-10-16 18:17:37","shop_order_payment_id":321,"shop_order_user_id":"1","change_type":"0","shop_order_id":"306","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 18:17:37","order_discount_value":"0","employee_id":"0","shop_order_id":"306","order_discount_type":"1"},{"total_amount":"65.00","transactions":[{"supplier_number":"ToGo","description1":"Pasta - Varmr&oslash;get","description3":"null","pos_id":655,"description2":"null","order_number_id":"307","parent_type_id":"1-5889","transaction_type_id":"3","discount_value":"0","buying_price":"25","selling_price":"49.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:28:16","quantity":"1","user_id":"1","item_cms_id":"496","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7101","shop_receipt_id":"1-5889","updated_at":"2014-10-16 18:28:16","sold_price":"49.00","color":"","item_id":"7101","group":"Pasta"},{"supplier_number":"Naturfrisk","description1":"Pink grape","description3":"275","pos_id":656,"description2":"275","order_number_id":"307","parent_type_id":"1-5889","transaction_type_id":"3","discount_value":"0","buying_price":"9.5","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:28:16","quantity":"1","user_id":"1","item_cms_id":"202","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5708636499735","shop_receipt_id":"1-5889","updated_at":"2014-10-16 18:28:16","sold_price":"16.00","color":"","item_id":"2309","group":"Drikkevarer"}],"shop_receipt_id":"1-5889","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"65.00","payments":[{"total_amount":"65.00","cc_type_id":"1","day_start_id":"1141413392565103","shop_receipt_id":"1-5889","change_value":"0.00","created_at":"2014-10-16 18:28:16","shop_order_payment_id":322,"shop_order_user_id":"1","change_type":"0","shop_order_id":"307","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-16 18:28:16","order_discount_value":"0","employee_id":"0","shop_order_id":"307","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":657,"description2":"null","order_number_id":"308","parent_type_id":"1-5890","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:34:50","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5890","updated_at":"2014-10-16 18:34:50","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":658,"description2":"null","order_number_id":"308","parent_type_id":"1-5890","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:34:50","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5890","updated_at":"2014-10-16 18:34:50","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":659,"description2":"null","order_number_id":"308","parent_type_id":"1-5890","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:34:50","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5890","updated_at":"2014-10-16 18:34:50","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":660,"description2":"null","order_number_id":"308","parent_type_id":"1-5890","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:34:50","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5890","updated_at":"2014-10-16 18:34:50","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"&AElig;blemost drikkeklar","description3":"null","pos_id":661,"description2":"500","order_number_id":"308","parent_type_id":"1-5890","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:34:50","quantity":"1","user_id":"1","item_cms_id":"550","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7310","shop_receipt_id":"1-5890","updated_at":"2014-10-16 18:34:50","sold_price":"0.00","color":"","item_id":"7310","group":"Drikkevarer"}],"shop_receipt_id":"1-5890","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5890","change_value":"0.00","created_at":"2014-10-16 18:34:50","shop_order_payment_id":323,"shop_order_user_id":"1","change_type":"0","shop_order_id":"308","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 18:34:50","order_discount_value":"0","employee_id":"0","shop_order_id":"308","order_discount_type":"1"},{"total_amount":"36.75","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":662,"description2":"null","order_number_id":"309","parent_type_id":"1-5891","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:44:29","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5891","updated_at":"2014-10-16 18:44:29","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":663,"description2":"null","order_number_id":"309","parent_type_id":"1-5891","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:44:29","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5891","updated_at":"2014-10-16 18:44:29","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - &AElig;ggesalat 1.stk","description3":"null","pos_id":664,"description2":"500","order_number_id":"309","parent_type_id":"1-5891","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:44:29","quantity":"1","user_id":"1","item_cms_id":"548","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7308","shop_receipt_id":"1-5891","updated_at":"2014-10-16 18:44:29","sold_price":"0.00","color":"","item_id":"7308","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Ginger Lime","description3":"330","pos_id":665,"description2":"330","order_number_id":"309","parent_type_id":"1-5891","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:44:29","quantity":"1","user_id":"1","item_cms_id":"263","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416105200","shop_receipt_id":"1-5891","updated_at":"2014-10-16 18:44:29","sold_price":"0.00","color":"","item_id":"1501","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Hyldeblomst","description3":"330","pos_id":666,"description2":"330","order_number_id":"309","parent_type_id":"1-5891","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:44:29","quantity":"1","user_id":"1","item_cms_id":"264","day_start_id":"1141413392565103","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416102131","shop_receipt_id":"1-5891","updated_at":"2014-10-16 18:44:29","sold_price":"0.00","color":"","item_id":"1502","group":"Drikkevarer"},{"supplier_number":"1111111","description1":"chokolade","description3":"Custom Sale","pos_id":667,"description2":"Custom Sale","order_number_id":"309","parent_type_id":"1-5891","transaction_type_id":"3","discount_value":"25","buying_price":"0","selling_price":"49.00","parent_type":"receipt_numbers","created_at":"2014-10-16 18:44:29","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1141413392565103","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5891","updated_at":"2014-10-16 18:44:29","sold_price":"36.75","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5891","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"36.75","payments":[{"total_amount":"36.75","cc_type_id":"0","day_start_id":"1141413392565103","shop_receipt_id":"1-5891","change_value":"3.25","created_at":"2014-10-16 18:44:29","shop_order_payment_id":324,"shop_order_user_id":"1","change_type":"0","shop_order_id":"309","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-16 18:44:29","order_discount_value":"0","employee_id":"0","shop_order_id":"309","order_discount_type":"1"},{"total_amount":"-578.0","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":668,"description2":"","order_number_id":"311","parent_type_id":"1211413481097482","transaction_type_id":"9","discount_value":"","buying_price":"","parent_type":"Day End","selling_price":"","created_at":"2014-10-16 19:38:17","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1141413392565103","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-16 19:38:17","sold_price":"-578.0","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1141413392565103","shop_user_id":"1","sold_total_amount":"-578.0","payments":[{"total_amount":"-578.0","cc_type_id":"","day_start_id":"1141413392565103","shop_receipt_id":"0","change_value":"","created_at":"2014-10-16 19:38:17","shop_order_payment_id":325,"shop_order_user_id":"1","change_type":"","shop_order_id":"311","payment_type_id":"8"}],"status_id":"3","created_at":"2014-10-16 19:38:17","order_discount_value":"","employee_id":"","shop_order_id":"311","order_discount_type":""},{"total_amount":"578.0","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":669,"description2":"","order_number_id":"312","parent_type_id":"1151413481097634","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2014-10-17 11:18:40","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1151413481097634","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-17 11:18:40","sold_price":"578.0","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"578.0","payments":[{"total_amount":"578.0","cc_type_id":"","day_start_id":"1151413481097634","shop_receipt_id":"0","change_value":"","created_at":"2014-10-17 11:18:40","shop_order_payment_id":326,"shop_order_user_id":"1","change_type":"","shop_order_id":"312","payment_type_id":"7"}],"status_id":"3","created_at":"2014-10-17 11:18:40","order_discount_value":"","employee_id":"","shop_order_id":"312","order_discount_type":""},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":670,"description2":"null","order_number_id":"310","parent_type_id":"1-5892","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:18:51","quantity":"2","user_id":"1","item_cms_id":"544","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5892","updated_at":"2014-10-17 11:18:51","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":671,"description2":"null","order_number_id":"310","parent_type_id":"1-5892","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:18:51","quantity":"2","user_id":"1","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5892","updated_at":"2014-10-17 11:18:51","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":672,"description2":"500","order_number_id":"310","parent_type_id":"1-5892","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:18:51","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5892","updated_at":"2014-10-17 11:18:51","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":673,"description2":"500","order_number_id":"310","parent_type_id":"1-5892","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:18:51","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5892","updated_at":"2014-10-17 11:18:51","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5892","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5892","change_value":"0.00","created_at":"2014-10-17 11:18:51","shop_order_payment_id":327,"shop_order_user_id":"1","change_type":"0","shop_order_id":"310","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 11:18:51","order_discount_value":"0","employee_id":"0","shop_order_id":"310","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":674,"description2":"null","order_number_id":"313","parent_type_id":"1-5893","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:30:10","quantity":"3","user_id":"1","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5893","updated_at":"2014-10-17 11:30:10","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5893","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5893","change_value":"0.00","created_at":"2014-10-17 11:30:10","shop_order_payment_id":328,"shop_order_user_id":"1","change_type":"0","shop_order_id":"313","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 11:30:10","order_discount_value":"0","employee_id":"0","shop_order_id":"313","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":675,"description2":"null","order_number_id":"314","parent_type_id":"1-5894","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:34:10","quantity":"2","user_id":"1","item_cms_id":"544","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5894","updated_at":"2014-10-17 11:34:10","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":676,"description2":"null","order_number_id":"314","parent_type_id":"1-5894","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:34:10","quantity":"2","user_id":"1","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5894","updated_at":"2014-10-17 11:34:10","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Bl&aring;b&aelig;r","description3":"330","pos_id":677,"description2":"330","order_number_id":"314","parent_type_id":"1-5894","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:34:10","quantity":"2","user_id":"1","item_cms_id":"266","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416103138","shop_receipt_id":"1-5894","updated_at":"2014-10-17 11:34:10","sold_price":"0.00","color":"","item_id":"1504","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Ginger Lime","description3":"330","pos_id":678,"description2":"330","order_number_id":"314","parent_type_id":"1-5894","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:34:10","quantity":"1","user_id":"1","item_cms_id":"263","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416105200","shop_receipt_id":"1-5894","updated_at":"2014-10-17 11:34:10","sold_price":"0.00","color":"","item_id":"1501","group":"Drikkevarer"},{"supplier_number":"Premium drinks","description1":"Birk Hyldeblomst","description3":"330","pos_id":679,"description2":"330","order_number_id":"314","parent_type_id":"1-5894","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:34:10","quantity":"1","user_id":"1","item_cms_id":"264","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416102131","shop_receipt_id":"1-5894","updated_at":"2014-10-17 11:34:10","sold_price":"0.00","color":"","item_id":"1502","group":"Drikkevarer"}],"shop_receipt_id":"1-5894","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5894","change_value":"0.00","created_at":"2014-10-17 11:34:10","shop_order_payment_id":329,"shop_order_user_id":"1","change_type":"0","shop_order_id":"314","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 11:34:10","order_discount_value":"0","employee_id":"0","shop_order_id":"314","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":680,"description2":"null","order_number_id":"315","parent_type_id":"1-5895","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:40:02","quantity":"2","user_id":"1","item_cms_id":"544","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5895","updated_at":"2014-10-17 11:40:02","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":681,"description2":"null","order_number_id":"315","parent_type_id":"1-5895","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:40:02","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5895","updated_at":"2014-10-17 11:40:02","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":682,"description2":"null","order_number_id":"315","parent_type_id":"1-5895","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:40:02","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5895","updated_at":"2014-10-17 11:40:02","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":683,"description2":"500","order_number_id":"315","parent_type_id":"1-5895","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:40:02","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5895","updated_at":"2014-10-17 11:40:02","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5895","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5895","change_value":"0.00","created_at":"2014-10-17 11:40:02","shop_order_payment_id":330,"shop_order_user_id":"1","change_type":"0","shop_order_id":"315","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 11:40:02","order_discount_value":"0","employee_id":"0","shop_order_id":"315","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":684,"description2":"null","order_number_id":"316","parent_type_id":"1-5896","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:43:10","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5896","updated_at":"2014-10-17 11:43:10","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":685,"description2":"null","order_number_id":"316","parent_type_id":"1-5896","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:43:10","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5896","updated_at":"2014-10-17 11:43:10","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":686,"description2":"500","order_number_id":"316","parent_type_id":"1-5896","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 11:43:10","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5896","updated_at":"2014-10-17 11:43:10","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5896","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5896","change_value":"0.00","created_at":"2014-10-17 11:43:10","shop_order_payment_id":331,"shop_order_user_id":"1","change_type":"0","shop_order_id":"316","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 11:43:10","order_discount_value":"0","employee_id":"0","shop_order_id":"316","order_discount_type":"1"},{"total_amount":"30.00","transactions":[{"supplier_number":"1111111","description1":"laks citron","description3":"Custom Sale","pos_id":687,"description2":"Custom Sale","order_number_id":"317","parent_type_id":"1-5897","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"30.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:13:34","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5897","updated_at":"2014-10-17 12:13:34","sold_price":"30.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5897","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"30.00","payments":[{"total_amount":"30.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5897","change_value":"20.00","created_at":"2014-10-17 12:13:34","shop_order_payment_id":332,"shop_order_user_id":"1","change_type":"0","shop_order_id":"317","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 12:13:34","order_discount_value":"0","employee_id":"0","shop_order_id":"317","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":689,"description2":"null","order_number_id":"318","parent_type_id":"1-5898","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:17:46","quantity":"2","user_id":"1","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5898","updated_at":"2014-10-17 12:17:46","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":690,"description2":"null","order_number_id":"318","parent_type_id":"1-5898","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:17:46","quantity":"2","user_id":"1","item_cms_id":"547","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5898","updated_at":"2014-10-17 12:17:46","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":691,"description2":"500","order_number_id":"318","parent_type_id":"1-5898","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:17:46","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5898","updated_at":"2014-10-17 12:17:46","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"}],"shop_receipt_id":"1-5898","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5898","change_value":"0.00","created_at":"2014-10-17 12:17:46","shop_order_payment_id":333,"shop_order_user_id":"1","change_type":"0","shop_order_id":"318","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 12:17:46","order_discount_value":"0","employee_id":"0","shop_order_id":"318","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":692,"description2":"null","order_number_id":"319","parent_type_id":"1-5899","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:26:08","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5899","updated_at":"2014-10-17 12:26:08","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":693,"description2":"null","order_number_id":"319","parent_type_id":"1-5899","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:26:08","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5899","updated_at":"2014-10-17 12:26:08","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":694,"description2":"null","order_number_id":"319","parent_type_id":"1-5899","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:26:08","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5899","updated_at":"2014-10-17 12:26:08","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":695,"description2":"null","order_number_id":"319","parent_type_id":"1-5899","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:26:08","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5899","updated_at":"2014-10-17 12:26:08","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Hyldeblomst","description3":"330","pos_id":696,"description2":"330","order_number_id":"319","parent_type_id":"1-5899","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:26:08","quantity":"2","user_id":"1","item_cms_id":"264","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416102131","shop_receipt_id":"1-5899","updated_at":"2014-10-17 12:26:08","sold_price":"0.00","color":"","item_id":"1502","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":697,"description2":"500","order_number_id":"319","parent_type_id":"1-5899","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:26:08","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5899","updated_at":"2014-10-17 12:26:08","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5899","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5899","change_value":"0.00","created_at":"2014-10-17 12:26:08","shop_order_payment_id":334,"shop_order_user_id":"1","change_type":"0","shop_order_id":"319","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 12:26:08","order_discount_value":"0","employee_id":"0","shop_order_id":"319","order_discount_type":"1"},{"total_amount":"150.00","transactions":[{"supplier_number":"Chaplon Tea","description1":"Ceylon UVA","description3":"190","pos_id":698,"description2":"190","order_number_id":"320","parent_type_id":"1-5900","transaction_type_id":"3","discount_value":"0","buying_price":"49.55","selling_price":"99.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:43:17","quantity":"1","user_id":"1","item_cms_id":"52","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5704120330027","shop_receipt_id":"1-5900","updated_at":"2014-10-17 12:43:17","sold_price":"99.00","color":"","item_id":"1107","group":"Gourmet The"},{"supplier_number":"Borngros","description1":"Spelt snacks m\/ramsl&oslash;g","description3":"150","pos_id":699,"description2":"150","order_number_id":"320","parent_type_id":"1-5900","transaction_type_id":"3","discount_value":"0","buying_price":"10","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:43:17","quantity":"1","user_id":"1","item_cms_id":"21","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5701155358354","shop_receipt_id":"1-5900","updated_at":"2014-10-17 12:43:17","sold_price":"17.00","color":"","item_id":"3103","group":"Kiks"},{"supplier_number":"1111111","description1":"siddinge","description3":"Custom Sale","pos_id":700,"description2":"Custom Sale","order_number_id":"320","parent_type_id":"1-5900","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"34.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:43:17","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5900","updated_at":"2014-10-17 12:43:17","sold_price":"34.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":701,"description2":"null","order_number_id":"320","parent_type_id":"1-5900","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:43:17","quantity":"2","user_id":"1","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5900","updated_at":"2014-10-17 12:43:17","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5900","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"150.00","payments":[{"total_amount":"150.00","cc_type_id":"1","day_start_id":"1151413481097634","shop_receipt_id":"1-5900","change_value":"0.00","created_at":"2014-10-17 12:43:17","shop_order_payment_id":335,"shop_order_user_id":"1","change_type":"0","shop_order_id":"320","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-17 12:43:17","order_discount_value":"0","employee_id":"0","shop_order_id":"320","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":702,"description2":"null","order_number_id":"321","parent_type_id":"1-5901","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:46:48","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5901","updated_at":"2014-10-17 12:46:48","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":703,"description2":"null","order_number_id":"321","parent_type_id":"1-5901","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:46:48","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5901","updated_at":"2014-10-17 12:46:48","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":704,"description2":"500","order_number_id":"321","parent_type_id":"1-5901","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:46:48","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5901","updated_at":"2014-10-17 12:46:48","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5901","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5901","change_value":"0.00","created_at":"2014-10-17 12:46:48","shop_order_payment_id":336,"shop_order_user_id":"1","change_type":"0","shop_order_id":"321","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 12:46:48","order_discount_value":"0","employee_id":"0","shop_order_id":"321","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":705,"description2":"null","order_number_id":"322","parent_type_id":"1-5902","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:47:12","quantity":"2","user_id":"1","item_cms_id":"547","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5902","updated_at":"2014-10-17 12:47:12","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":706,"description2":"null","order_number_id":"322","parent_type_id":"1-5902","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:47:12","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5902","updated_at":"2014-10-17 12:47:12","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":707,"description2":"null","order_number_id":"322","parent_type_id":"1-5902","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:47:12","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5902","updated_at":"2014-10-17 12:47:12","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":708,"description2":"500","order_number_id":"322","parent_type_id":"1-5902","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:47:12","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5902","updated_at":"2014-10-17 12:47:12","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":709,"description2":"500","order_number_id":"322","parent_type_id":"1-5902","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 12:47:12","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5902","updated_at":"2014-10-17 12:47:12","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"}],"shop_receipt_id":"1-5902","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5902","change_value":"0.00","created_at":"2014-10-17 12:47:12","shop_order_payment_id":337,"shop_order_user_id":"1","change_type":"0","shop_order_id":"322","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 12:47:12","order_discount_value":"0","employee_id":"0","shop_order_id":"322","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":710,"description2":"null","order_number_id":"322","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-17 13:01:30","quantity":"2","user_id":"1","receipt_number_id":"0","item_cms_id":"547","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7307","shop_receipt_id":"1-5902","updated_at":"2014-10-17 13:01:30","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":711,"description2":"null","order_number_id":"322","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-17 13:01:30","quantity":"1","user_id":"1","receipt_number_id":"0","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7301","shop_receipt_id":"1-5902","updated_at":"2014-10-17 13:01:30","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":712,"description2":"null","order_number_id":"322","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-17 13:01:30","quantity":"1","user_id":"1","receipt_number_id":"0","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7302","shop_receipt_id":"1-5902","updated_at":"2014-10-17 13:01:30","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":713,"description2":"500","order_number_id":"322","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-17 13:01:30","quantity":"1","user_id":"1","receipt_number_id":"0","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7311","shop_receipt_id":"1-5902","updated_at":"2014-10-17 13:01:30","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":714,"description2":"500","order_number_id":"322","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-17 13:01:30","quantity":"1","user_id":"1","receipt_number_id":"0","item_cms_id":"552","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7312","shop_receipt_id":"1-5902","updated_at":"2014-10-17 13:01:30","sold_price":"0.00","color":"","item_id":"7312","group":"Drikkevarer"}],"shop_receipt_id":"1-5902","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"day_start_id":"1151413481097634","shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-17 13:01:30","order_discount_value":"0","employee_id":"0","shop_order_id":"322","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":715,"description2":"null","order_number_id":"323","parent_type_id":"1-5903","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 13:01:44","quantity":"1","user_id":"141409402634","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5903","updated_at":"2014-10-17 13:01:44","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5903","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5903","change_value":"0.00","created_at":"2014-10-17 13:01:44","shop_order_payment_id":338,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"323","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 13:01:44","order_discount_value":"0","employee_id":"0","shop_order_id":"323","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":716,"description2":"null","order_number_id":"324","parent_type_id":"1-5904","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 13:10:31","quantity":"1","user_id":"141409402634","item_cms_id":"544","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5904","updated_at":"2014-10-17 13:10:31","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - &AElig;ggesalat 1.stk","description3":"null","pos_id":717,"description2":"500","order_number_id":"324","parent_type_id":"1-5904","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 13:10:31","quantity":"1","user_id":"141409402634","item_cms_id":"548","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7308","shop_receipt_id":"1-5904","updated_at":"2014-10-17 13:10:31","sold_price":"0.00","color":"","item_id":"7308","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":718,"description2":"500","order_number_id":"324","parent_type_id":"1-5904","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 13:10:31","quantity":"1","user_id":"141409402634","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5904","updated_at":"2014-10-17 13:10:31","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5904","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5904","change_value":"0.00","created_at":"2014-10-17 13:10:31","shop_order_payment_id":339,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"324","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 13:10:31","order_discount_value":"0","employee_id":"0","shop_order_id":"324","order_discount_type":"1"},{"total_amount":"-100","transactions":[{"promotion_ids":"","supplier_number":"","description1":"indkab okh","description3":"indkab okh","pos_id":719,"description2":"indkab okh","order_number_id":"326","parent_type_id":"8","transaction_type_id":"7","discount_value":"","buying_price":"","parent_type":"Cash Out","selling_price":"","created_at":"2014-10-17 13:45:03","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1151413481097634","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-17 13:45:03","sold_price":"-100","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"-100","payments":[{"total_amount":"-100","cc_type_id":"","day_start_id":"1151413481097634","shop_receipt_id":"0","change_value":"","created_at":"2014-10-17 13:45:03","shop_order_payment_id":340,"shop_order_user_id":"1","change_type":"","shop_order_id":"326","payment_type_id":"6"}],"status_id":"3","created_at":"2014-10-17 13:45:03","order_discount_value":"","employee_id":"","shop_order_id":"326","order_discount_type":""},{"total_amount":"104.00","transactions":[{"supplier_number":"1111111","description1":"chokolade","description3":"Custom Sale","pos_id":720,"description2":"Custom Sale","order_number_id":"325","parent_type_id":"1-5905","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 13:51:58","quantity":"2","user_id":"141409402634","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5905","updated_at":"2014-10-17 13:51:58","sold_price":"34.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":721,"description2":"null","order_number_id":"325","parent_type_id":"1-5905","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 13:51:58","quantity":"1","user_id":"141409402634","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5905","updated_at":"2014-10-17 13:51:58","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Premium drinks","description1":"Birk Hindb&aelig;r","description3":"330","pos_id":722,"description2":"330","order_number_id":"325","parent_type_id":"1-5905","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 13:51:58","quantity":"1","user_id":"141409402634","item_cms_id":"265","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5710416104135","shop_receipt_id":"1-5905","updated_at":"2014-10-17 13:51:58","sold_price":"0.00","color":"","item_id":"1503","group":"Drikkevarer"},{"supplier_number":"1111111","description1":"krebsesalat","description3":"Custom Sale","pos_id":723,"description2":"Custom Sale","order_number_id":"325","parent_type_id":"1-5905","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"70.00","parent_type":"receipt_numbers","created_at":"2014-10-17 13:51:58","quantity":"1","user_id":"141409402634","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5905","updated_at":"2014-10-17 13:51:58","sold_price":"70.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5905","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"104.00","payments":[{"total_amount":"104.00","cc_type_id":"1","day_start_id":"1151413481097634","shop_receipt_id":"1-5905","change_value":"0.00","created_at":"2014-10-17 13:51:58","shop_order_payment_id":341,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"325","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-17 13:51:58","order_discount_value":"0","employee_id":"0","shop_order_id":"325","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":724,"description2":"null","order_number_id":"327","parent_type_id":"1-5906","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 14:06:53","quantity":"1","user_id":"141409402634","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5906","updated_at":"2014-10-17 14:06:53","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":725,"description2":"null","order_number_id":"327","parent_type_id":"1-5906","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 14:06:53","quantity":"1","user_id":"141409402634","item_cms_id":"543","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5906","updated_at":"2014-10-17 14:06:53","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get 1.stk","description3":"null","pos_id":726,"description2":"null","order_number_id":"327","parent_type_id":"1-5906","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 14:06:53","quantity":"1","user_id":"141409402634","item_cms_id":"546","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7306","shop_receipt_id":"1-5906","updated_at":"2014-10-17 14:06:53","sold_price":"0.00","color":"","item_id":"7306","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":727,"description2":"null","order_number_id":"327","parent_type_id":"1-5906","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 14:06:53","quantity":"1","user_id":"141409402634","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5906","updated_at":"2014-10-17 14:06:53","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"1111111","description1":"birk","description3":"Custom Sale","pos_id":728,"description2":"Custom Sale","order_number_id":"327","parent_type_id":"1-5906","transaction_type_id":"3","discount_value":"100","buying_price":"0","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 14:06:53","quantity":"1","user_id":"141409402634","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5906","updated_at":"2014-10-17 14:06:53","sold_price":"0.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5906","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5906","change_value":"0.00","created_at":"2014-10-17 14:06:53","shop_order_payment_id":342,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"327","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 14:06:53","order_discount_value":"0","employee_id":"0","shop_order_id":"327","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":729,"description2":"null","order_number_id":"328","parent_type_id":"1-5907","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 14:25:40","quantity":"1","user_id":"141409402634","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5907","updated_at":"2014-10-17 14:25:40","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":730,"description2":"null","order_number_id":"328","parent_type_id":"1-5907","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 14:25:40","quantity":"1","user_id":"141409402634","item_cms_id":"544","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5907","updated_at":"2014-10-17 14:25:40","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":731,"description2":"500","order_number_id":"328","parent_type_id":"1-5907","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 14:25:40","quantity":"1","user_id":"141409402634","item_cms_id":"551","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5907","updated_at":"2014-10-17 14:25:40","sold_price":"0.00","color":"","item_id":"7311","group":"Drikkevarer"}],"shop_receipt_id":"1-5907","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5907","change_value":"0.00","created_at":"2014-10-17 14:25:40","shop_order_payment_id":343,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"328","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 14:25:40","order_discount_value":"0","employee_id":"0","shop_order_id":"328","order_discount_type":"1"},{"total_amount":"-50","transactions":[{"promotion_ids":"","supplier_number":"","description1":"indkab","description3":"indkab","pos_id":732,"description2":"indkab","order_number_id":"330","parent_type_id":"9","transaction_type_id":"7","discount_value":"","buying_price":"","parent_type":"Cash Out","selling_price":"","created_at":"2014-10-17 14:46:00","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1151413481097634","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-17 14:46:00","sold_price":"-50","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1151413481097634","shop_user_id":"1","sold_total_amount":"-50","payments":[{"total_amount":"-50","cc_type_id":"","day_start_id":"1151413481097634","shop_receipt_id":"0","change_value":"","created_at":"2014-10-17 14:46:00","shop_order_payment_id":344,"shop_order_user_id":"1","change_type":"","shop_order_id":"330","payment_type_id":"6"}],"status_id":"3","created_at":"2014-10-17 14:46:00","order_discount_value":"","employee_id":"","shop_order_id":"330","order_discount_type":""},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":733,"description2":"null","order_number_id":"329","parent_type_id":"1-5908","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 15:18:40","quantity":"1","user_id":"141409402634","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5908","updated_at":"2014-10-17 15:18:40","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":734,"description2":"null","order_number_id":"329","parent_type_id":"1-5908","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 15:18:40","quantity":"1","user_id":"141409402634","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5908","updated_at":"2014-10-17 15:18:40","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"1111111","description1":"birk","description3":"Custom Sale","pos_id":735,"description2":"Custom Sale","order_number_id":"329","parent_type_id":"1-5908","transaction_type_id":"3","discount_value":"100","buying_price":"0","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 15:18:40","quantity":"1","user_id":"141409402634","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5908","updated_at":"2014-10-17 15:18:40","sold_price":"0.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5908","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5908","change_value":"0.00","created_at":"2014-10-17 15:18:40","shop_order_payment_id":345,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"329","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 15:18:40","order_discount_value":"0","employee_id":"0","shop_order_id":"329","order_discount_type":"1"},{"total_amount":"108.00","transactions":[{"supplier_number":"Meyerfood","description1":"Meyerfood","description3":"33cl","pos_id":736,"description2":"&Oslash;kologisk &aelig;blemost","order_number_id":"331","parent_type_id":"1-5909","transaction_type_id":"3","discount_value":"0","buying_price":"10","selling_price":"49.00","parent_type":"receipt_numbers","created_at":"2014-10-17 15:22:17","quantity":"1","user_id":"141409402634","item_cms_id":"132","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5707428084029","shop_receipt_id":"1-5909","updated_at":"2014-10-17 15:22:17","sold_price":"49.00","color":"","item_id":"2806","group":"Most"},{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":737,"description2":"null","order_number_id":"331","parent_type_id":"1-5909","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 15:22:17","quantity":"1","user_id":"141409402634","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5909","updated_at":"2014-10-17 15:22:17","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"},{"supplier_number":"NORDISK","description1":"Suppe - Kartoffel","description3":"500","pos_id":738,"description2":"500","order_number_id":"331","parent_type_id":"1-5909","transaction_type_id":"3","discount_value":"0","buying_price":"28","selling_price":"59.00","parent_type":"receipt_numbers","created_at":"2014-10-17 15:22:17","quantity":"1","user_id":"141409402634","item_cms_id":"230","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5711558119018","shop_receipt_id":"1-5909","updated_at":"2014-10-17 15:22:17","sold_price":"59.00","color":"","item_id":"3415","group":"Suppe"}],"shop_receipt_id":"1-5909","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"108.00","payments":[{"total_amount":"108.00","cc_type_id":"3","day_start_id":"1151413481097634","shop_receipt_id":"1-5909","change_value":"0.00","created_at":"2014-10-17 15:22:17","shop_order_payment_id":346,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"331","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-17 15:22:17","order_discount_value":"0","employee_id":"0","shop_order_id":"331","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":739,"description2":"null","order_number_id":"332","parent_type_id":"1-5910","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 15:25:38","quantity":"1","user_id":"141409402634","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5910","updated_at":"2014-10-17 15:25:38","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5910","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5910","change_value":"0.00","created_at":"2014-10-17 15:25:38","shop_order_payment_id":347,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"332","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 15:25:38","order_discount_value":"0","employee_id":"0","shop_order_id":"332","order_discount_type":"1"},{"total_amount":"29400.00","transactions":[{"supplier_number":"Simply Chocolate","description1":"36 STK &quot;I CAN STOP WHENEVER I WANT&quot;","description3":"null","pos_id":740,"description2":"360","order_number_id":"333","transaction_type_id":"3","discount_value":"0","buying_price":"11650","selling_price":"22900.00","parent_type":"parked","created_at":"2014-10-17 15:33:23","quantity":"1","user_id":"141409402634","receipt_number_id":"0","item_cms_id":"335","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"5710885001768","shop_receipt_id":"1-5910","updated_at":"2014-10-17 15:33:23","sold_price":"22900.00","color":"","item_id":"1203","group":"Chokolade"},{"supplier_number":"Simply Chocolate","description1":"8 STK &quot;YOU ME COFFEE NOW&quot;","description3":"null","pos_id":741,"description2":"80","order_number_id":"333","transaction_type_id":"3","discount_value":"0","buying_price":"3175","selling_price":"6500.00","parent_type":"parked","created_at":"2014-10-17 15:33:23","quantity":"1","user_id":"141409402634","receipt_number_id":"0","item_cms_id":"333","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"5710885001744","shop_receipt_id":"1-5910","updated_at":"2014-10-17 15:33:23","sold_price":"6500.00","color":"","item_id":"1201","group":"Chokolade"}],"shop_receipt_id":"1-5910","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"29400.00","payments":[{"day_start_id":"1151413481097634","shop_order_payment_id":"0","payment_type_id":""},{"shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-17 15:33:23","order_discount_value":"0","employee_id":"0","shop_order_id":"333","order_discount_type":"1"},{"total_amount":"139.00","transactions":[{"supplier_number":"Kathrine Andersen Chokolade","description1":"Gave&aelig;ske 200 gr.","description3":"null","pos_id":742,"description2":"200","order_number_id":"334","transaction_type_id":"3","discount_value":"0","buying_price":"72","selling_price":"139.00","parent_type":"parked","created_at":"2014-10-17 15:55:54","quantity":"1","user_id":"141409402634","receipt_number_id":"0","item_cms_id":"584","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"5151","shop_receipt_id":"1-5910","updated_at":"2014-10-17 15:55:54","sold_price":"139.00","color":"","item_id":"6017","group":"Chokolade"}],"shop_receipt_id":"1-5910","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"139.00","payments":[{"day_start_id":"1151413481097634","shop_order_payment_id":"0","payment_type_id":""},{"shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-17 15:55:54","order_discount_value":"0","employee_id":"0","shop_order_id":"334","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":743,"description2":"null","order_number_id":"335","parent_type_id":"1-5911","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 15:56:11","quantity":"1","user_id":"161409403010","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5911","updated_at":"2014-10-17 15:56:11","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5911","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5911","change_value":"0.00","created_at":"2014-10-17 15:56:11","shop_order_payment_id":348,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"335","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 15:56:11","order_discount_value":"0","employee_id":"0","shop_order_id":"335","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":744,"description2":"null","order_number_id":"336","parent_type_id":"1-5912","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:06:31","quantity":"2","user_id":"141409402634","item_cms_id":"547","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5912","updated_at":"2014-10-17 16:06:31","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":745,"description2":"null","order_number_id":"336","parent_type_id":"1-5912","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:06:31","quantity":"2","user_id":"141409402634","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5912","updated_at":"2014-10-17 16:06:31","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"1111111","description1":"birk","description3":"Custom Sale","pos_id":746,"description2":"Custom Sale","order_number_id":"336","parent_type_id":"1-5912","transaction_type_id":"3","discount_value":"100","buying_price":"0","selling_price":"17.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:06:31","quantity":"2","user_id":"141409402634","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5912","updated_at":"2014-10-17 16:06:31","sold_price":"0.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5912","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5912","change_value":"0.00","created_at":"2014-10-17 16:06:31","shop_order_payment_id":349,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"336","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 16:06:31","order_discount_value":"0","employee_id":"0","shop_order_id":"336","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":747,"description2":"null","order_number_id":"337","parent_type_id":"1-5913","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:10:50","quantity":"1","user_id":"141409402634","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5913","updated_at":"2014-10-17 16:10:50","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":748,"description2":"null","order_number_id":"337","parent_type_id":"1-5913","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:10:50","quantity":"1","user_id":"141409402634","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5913","updated_at":"2014-10-17 16:10:50","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get 1.stk","description3":"null","pos_id":749,"description2":"null","order_number_id":"337","parent_type_id":"1-5913","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:10:50","quantity":"1","user_id":"141409402634","item_cms_id":"546","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7306","shop_receipt_id":"1-5913","updated_at":"2014-10-17 16:10:50","sold_price":"0.00","color":"","item_id":"7306","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":750,"description2":"null","order_number_id":"337","parent_type_id":"1-5913","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:10:50","quantity":"1","user_id":"141409402634","item_cms_id":"543","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5913","updated_at":"2014-10-17 16:10:50","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"}],"shop_receipt_id":"1-5913","day_start_id":"1151413481097634","shop_user_id":"141409402634","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5913","change_value":"0.00","created_at":"2014-10-17 16:10:50","shop_order_payment_id":350,"shop_order_user_id":"141409402634","change_type":"0","shop_order_id":"337","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 16:10:50","order_discount_value":"0","employee_id":"0","shop_order_id":"337","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":751,"description2":"null","order_number_id":"338","parent_type_id":"1-5914","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:54:04","quantity":"1","user_id":"161409403010","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5914","updated_at":"2014-10-17 16:54:04","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5914","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5914","change_value":"0.00","created_at":"2014-10-17 16:54:04","shop_order_payment_id":351,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"338","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 16:54:04","order_discount_value":"0","employee_id":"0","shop_order_id":"338","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":752,"description2":"null","order_number_id":"339","parent_type_id":"1-5915","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:55:27","quantity":"1","user_id":"161409403010","item_cms_id":"547","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5915","updated_at":"2014-10-17 16:55:27","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":753,"description2":"null","order_number_id":"339","parent_type_id":"1-5915","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:55:27","quantity":"1","user_id":"161409403010","item_cms_id":"541","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5915","updated_at":"2014-10-17 16:55:27","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"1111111","description1":"2 x sealand birk til deal","description3":"Custom Sale","pos_id":754,"description2":"Custom Sale","order_number_id":"339","parent_type_id":"1-5915","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 16:55:27","quantity":"1","user_id":"161409403010","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5915","updated_at":"2014-10-17 16:55:27","sold_price":"0.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5915","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5915","change_value":"0.00","created_at":"2014-10-17 16:55:27","shop_order_payment_id":352,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"339","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 16:55:27","order_discount_value":"0","employee_id":"0","shop_order_id":"339","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":755,"description2":"null","order_number_id":"340","parent_type_id":"1-5916","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:10:19","quantity":"1","user_id":"161409403010","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5916","updated_at":"2014-10-17 17:10:19","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5916","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5916","change_value":"0.00","created_at":"2014-10-17 17:10:19","shop_order_payment_id":353,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"340","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 17:10:19","order_discount_value":"0","employee_id":"0","shop_order_id":"340","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":756,"description2":"null","order_number_id":"341","parent_type_id":"1-5917","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:13:01","quantity":"1","user_id":"161409403010","item_cms_id":"543","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5917","updated_at":"2014-10-17 17:13:01","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":757,"description2":"null","order_number_id":"341","parent_type_id":"1-5917","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:13:01","quantity":"1","user_id":"161409403010","item_cms_id":"547","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5917","updated_at":"2014-10-17 17:13:01","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get 1.stk","description3":"null","pos_id":758,"description2":"null","order_number_id":"341","parent_type_id":"1-5917","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:13:01","quantity":"1","user_id":"161409403010","item_cms_id":"546","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7306","shop_receipt_id":"1-5917","updated_at":"2014-10-17 17:13:01","sold_price":"0.00","color":"","item_id":"7306","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - &AElig;ggesalat 1.stk","description3":"null","pos_id":759,"description2":"500","order_number_id":"341","parent_type_id":"1-5917","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:13:01","quantity":"1","user_id":"161409403010","item_cms_id":"548","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7308","shop_receipt_id":"1-5917","updated_at":"2014-10-17 17:13:01","sold_price":"0.00","color":"","item_id":"7308","group":"Sandwich"},{"supplier_number":"1111111","description1":"4 x sealand","description3":"Custom Sale","pos_id":760,"description2":"Custom Sale","order_number_id":"341","parent_type_id":"1-5917","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:13:01","quantity":"1","user_id":"161409403010","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5917","updated_at":"2014-10-17 17:13:01","sold_price":"0.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5917","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5917","change_value":"0.00","created_at":"2014-10-17 17:13:01","shop_order_payment_id":354,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"341","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 17:13:01","order_discount_value":"0","employee_id":"0","shop_order_id":"341","order_discount_type":"1"},{"total_amount":"238.00","transactions":[{"supplier_number":"1111111","description1":"chai","description3":"Custom Sale","pos_id":761,"description2":"Custom Sale","order_number_id":"342","parent_type_id":"1-5918","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"75.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:18:37","quantity":"1","user_id":"161409403010","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5918","updated_at":"2014-10-17 17:18:37","sold_price":"75.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"1111111","description1":"pasta rasta ","description3":"Custom Sale","pos_id":762,"description2":"Custom Sale","order_number_id":"342","parent_type_id":"1-5918","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"45.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:18:37","quantity":"1","user_id":"161409403010","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5918","updated_at":"2014-10-17 17:18:37","sold_price":"45.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"1111111","description1":"pesto x 2","description3":"Custom Sale","pos_id":763,"description2":"Custom Sale","order_number_id":"342","parent_type_id":"1-5918","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"30.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:18:37","quantity":"1","user_id":"161409403010","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5918","updated_at":"2014-10-17 17:18:37","sold_price":"30.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"MORTEN HEIBERG","description1":"Rabarber\/vanille marmelade","description3":"375","pos_id":764,"description2":"375","order_number_id":"342","parent_type_id":"1-5918","transaction_type_id":"3","discount_value":"0","buying_price":"22","selling_price":"44.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:18:37","quantity":"1","user_id":"161409403010","item_cms_id":"182","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5704920000175","shop_receipt_id":"1-5918","updated_at":"2014-10-17 17:18:37","sold_price":"44.00","color":"","item_id":"1803","group":"Marmelade"},{"supplier_number":"MORTEN HEIBERG","description1":"Appelsin\/chilli sauce","description3":"250","pos_id":765,"description2":"250","order_number_id":"342","parent_type_id":"1-5918","transaction_type_id":"3","discount_value":"0","buying_price":"22","selling_price":"44.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:18:37","quantity":"1","user_id":"161409403010","item_cms_id":"180","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5704920000397","shop_receipt_id":"1-5918","updated_at":"2014-10-17 17:18:37","sold_price":"44.00","color":"","item_id":"1805","group":"Marmelade"}],"shop_receipt_id":"1-5918","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"238.00","payments":[{"total_amount":"238.00","cc_type_id":"1","day_start_id":"1151413481097634","shop_receipt_id":"1-5918","change_value":"0.00","created_at":"2014-10-17 17:18:37","shop_order_payment_id":355,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"342","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-17 17:18:37","order_discount_value":"0","employee_id":"0","shop_order_id":"342","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":766,"description2":"null","order_number_id":"343","parent_type_id":"1-5919","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:40:50","quantity":"1","user_id":"161409403010","item_cms_id":"543","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5919","updated_at":"2014-10-17 17:40:50","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":767,"description2":"null","order_number_id":"343","parent_type_id":"1-5919","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:40:50","quantity":"1","user_id":"161409403010","item_cms_id":"542","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5919","updated_at":"2014-10-17 17:40:50","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"}],"shop_receipt_id":"1-5919","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5919","change_value":"0.00","created_at":"2014-10-17 17:40:50","shop_order_payment_id":356,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"343","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 17:40:50","order_discount_value":"0","employee_id":"0","shop_order_id":"343","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":768,"description2":"null","order_number_id":"344","parent_type_id":"1-5920","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 17:48:40","quantity":"1","user_id":"161409403010","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5920","updated_at":"2014-10-17 17:48:40","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5920","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5920","change_value":"0.00","created_at":"2014-10-17 17:48:40","shop_order_payment_id":357,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"344","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 17:48:40","order_discount_value":"0","employee_id":"0","shop_order_id":"344","order_discount_type":"1"},{"total_amount":"82.00","transactions":[{"supplier_number":"Kathrine Andersen Chokolade","description1":"Plade m. Pistacie","description3":"100 g","pos_id":769,"description2":"100 g","order_number_id":"345","parent_type_id":"1-5921","transaction_type_id":"3","discount_value":"25","buying_price":"25","selling_price":"44.00","parent_type":"receipt_numbers","created_at":"2014-10-17 18:28:07","quantity":"1","user_id":"161409403010","item_cms_id":"470","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"6004","shop_receipt_id":"1-5921","updated_at":"2014-10-17 18:28:07","sold_price":"33.00","color":"","item_id":"6004","group":"Chokolade- s&oslash;de sager"},{"supplier_number":"Selleberg","description1":"Kirseb&aelig;rmarmelade","description3":"250","pos_id":770,"description2":"250","order_number_id":"345","parent_type_id":"1-5921","transaction_type_id":"3","discount_value":"0","buying_price":"24","selling_price":"49.00","parent_type":"receipt_numbers","created_at":"2014-10-17 18:28:07","quantity":"1","user_id":"161409403010","item_cms_id":"279","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5710594000083","shop_receipt_id":"1-5921","updated_at":"2014-10-17 18:28:07","sold_price":"49.00","color":"","item_id":"1902","group":"Marmelade"}],"shop_receipt_id":"1-5921","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"82.00","payments":[{"total_amount":"82.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5921","change_value":"418.00","created_at":"2014-10-17 18:28:07","shop_order_payment_id":358,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"345","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 18:28:07","order_discount_value":"0","employee_id":"0","shop_order_id":"345","order_discount_type":"1"},{"total_amount":"155.00","transactions":[{"supplier_number":"1111111","description1":"tapas","description3":"Custom Sale","pos_id":771,"description2":"Custom Sale","order_number_id":"346","parent_type_id":"1-5922","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"155.00","parent_type":"receipt_numbers","created_at":"2014-10-17 18:42:46","quantity":"1","user_id":"161409403010","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5922","updated_at":"2014-10-17 18:42:46","sold_price":"155.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5922","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"155.00","payments":[{"total_amount":"155.00","cc_type_id":"1","day_start_id":"1151413481097634","shop_receipt_id":"1-5922","change_value":"0.00","created_at":"2014-10-17 18:42:46","shop_order_payment_id":359,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"346","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-17 18:42:46","order_discount_value":"0","employee_id":"0","shop_order_id":"346","order_discount_type":"1"},{"total_amount":"46.00","transactions":[{"supplier_number":"1111111","description1":"us&oslash;det solb&aelig;r sidinge","description3":"Custom Sale","pos_id":772,"description2":"Custom Sale","order_number_id":"347","parent_type_id":"1-5923","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"30.00","parent_type":"receipt_numbers","created_at":"2014-10-17 18:45:39","quantity":"1","user_id":"161409403010","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5923","updated_at":"2014-10-17 18:45:39","sold_price":"30.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"Naturfrisk","description1":"Rabarber","description3":"275","pos_id":773,"description2":"275","order_number_id":"347","parent_type_id":"1-5923","transaction_type_id":"3","discount_value":"0","buying_price":"9.5","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-17 18:45:39","quantity":"1","user_id":"161409403010","item_cms_id":"536","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5708636222609","shop_receipt_id":"1-5923","updated_at":"2014-10-17 18:45:39","sold_price":"16.00","color":"","item_id":"2323","group":"Drikkevarer"}],"shop_receipt_id":"1-5923","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"46.00","payments":[{"total_amount":"46.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5923","change_value":"54.00","created_at":"2014-10-17 18:45:39","shop_order_payment_id":360,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"347","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 18:45:39","order_discount_value":"0","employee_id":"0","shop_order_id":"347","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":774,"description2":"null","order_number_id":"348","parent_type_id":"1-5924","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-17 19:24:35","quantity":"2","user_id":"161409403010","item_cms_id":"540","day_start_id":"1151413481097634","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5924","updated_at":"2014-10-17 19:24:35","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5924","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1151413481097634","shop_receipt_id":"1-5924","change_value":"0.00","created_at":"2014-10-17 19:24:35","shop_order_payment_id":361,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"348","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-17 19:24:35","order_discount_value":"0","employee_id":"0","shop_order_id":"348","order_discount_type":"1"},{"total_amount":"35.00","transactions":[{"supplier_number":"1111111","description1":"bolcher","description3":"Custom Sale","pos_id":775,"description2":"Custom Sale","order_number_id":"349","parent_type_id":"1-5925","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"35.00","parent_type":"receipt_numbers","created_at":"2014-10-17 19:33:26","quantity":"1","user_id":"161409403010","item_cms_id":"485","day_start_id":"1151413481097634","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5925","updated_at":"2014-10-17 19:33:26","sold_price":"35.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5925","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"35.00","payments":[{"total_amount":"35.00","cc_type_id":"1","day_start_id":"1151413481097634","shop_receipt_id":"1-5925","change_value":"0.00","created_at":"2014-10-17 19:33:26","shop_order_payment_id":362,"shop_order_user_id":"161409403010","change_type":"0","shop_order_id":"349","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-17 19:33:26","order_discount_value":"0","employee_id":"0","shop_order_id":"349","order_discount_type":"1"},{"total_amount":"-587.0","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":776,"description2":"","order_number_id":"351","parent_type_id":"1231413569143026","transaction_type_id":"9","discount_value":"","buying_price":"","parent_type":"Day End","selling_price":"","created_at":"2014-10-17 20:05:43","quantity":"","user_id":"161409403010","item_cms_id":"","day_start_id":"1151413481097634","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-17 20:05:43","sold_price":"-587.0","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1151413481097634","shop_user_id":"161409403010","sold_total_amount":"-587.0","payments":[{"total_amount":"-587.0","cc_type_id":"","day_start_id":"1151413481097634","shop_receipt_id":"0","change_value":"","created_at":"2014-10-17 20:05:43","shop_order_payment_id":363,"shop_order_user_id":"161409403010","change_type":"","shop_order_id":"351","payment_type_id":"8"}],"status_id":"3","created_at":"2014-10-17 20:05:43","order_discount_value":"","employee_id":"","shop_order_id":"351","order_discount_type":""},{"total_amount":"587.0","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":777,"description2":"","order_number_id":"352","parent_type_id":"1161413569143161","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2014-10-18 10:57:12","quantity":"","user_id":"131409402138","item_cms_id":"","day_start_id":"1161413569143161","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-18 10:57:12","sold_price":"587.0","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"587.0","payments":[{"total_amount":"587.0","cc_type_id":"","day_start_id":"1161413569143161","shop_receipt_id":"0","change_value":"","created_at":"2014-10-18 10:57:12","shop_order_payment_id":364,"shop_order_user_id":"131409402138","change_type":"","shop_order_id":"352","payment_type_id":"7"}],"status_id":"3","created_at":"2014-10-18 10:57:12","order_discount_value":"","employee_id":"","shop_order_id":"352","order_discount_type":""},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":778,"description2":"null","order_number_id":"350","parent_type_id":"1-5926","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 11:28:30","quantity":"1","user_id":"131409402138","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5926","updated_at":"2014-10-18 11:28:30","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5926","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5926","change_value":"0.00","created_at":"2014-10-18 11:28:30","shop_order_payment_id":365,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"350","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 11:28:30","order_discount_value":"0","employee_id":"0","shop_order_id":"350","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":779,"description2":"null","order_number_id":"353","parent_type_id":"1-5927","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 11:49:09","quantity":"1","user_id":"131409402138","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5927","updated_at":"2014-10-18 11:49:09","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5927","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5927","change_value":"0.00","created_at":"2014-10-18 11:49:09","shop_order_payment_id":366,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"353","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 11:49:09","order_discount_value":"0","employee_id":"0","shop_order_id":"353","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":780,"description2":"null","order_number_id":"353","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-18 11:55:56","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7300","shop_receipt_id":"1-5927","updated_at":"2014-10-18 11:55:56","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5927","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"day_start_id":"1161413569143161","shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-18 11:55:56","order_discount_value":"0","employee_id":"0","shop_order_id":"353","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":781,"description2":"null","order_number_id":"354","parent_type_id":"1-5928","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:22:24","quantity":"1","user_id":"131409402138","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5928","updated_at":"2014-10-18 12:22:24","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5928","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5928","change_value":"0.00","created_at":"2014-10-18 12:22:24","shop_order_payment_id":367,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"354","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 12:22:24","order_discount_value":"0","employee_id":"0","shop_order_id":"354","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":782,"description2":"null","order_number_id":"355","parent_type_id":"1-5929","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:40:11","quantity":"1","user_id":"131409402138","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5929","updated_at":"2014-10-18 12:40:11","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5929","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5929","change_value":"0.00","created_at":"2014-10-18 12:40:11","shop_order_payment_id":368,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"355","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 12:40:11","order_discount_value":"0","employee_id":"0","shop_order_id":"355","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"MY goodness","description1":"&Oslash;ko thorris - appelsin","description3":"25 cl","pos_id":783,"description2":"25 cl","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"100","buying_price":"8.75","selling_price":"15.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"1","user_id":"131409402138","item_cms_id":"390","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5704231100762","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"2500","group":"Drikkevarer"},{"supplier_number":"Naturfrisk","description1":"Bl&aring;b&aelig;r","description3":"275","pos_id":784,"description2":"275","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"100","buying_price":"9.5","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"1","user_id":"131409402138","item_cms_id":"197","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5708636219494","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"2303","group":"Drikkevarer"},{"supplier_number":"Naturfrisk","description1":"Hyldeblomst","description3":"275","pos_id":785,"description2":"275","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"100","buying_price":"9.5","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"1","user_id":"131409402138","item_cms_id":"194","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5708636219432","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"2300","group":"Drikkevarer"},{"supplier_number":"Naturfrisk","description1":"Hindb&aelig;r dream","description3":"27 cl","pos_id":786,"description2":"27 cl","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"100","buying_price":"10.75","selling_price":"20.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"2","user_id":"131409402138","item_cms_id":"205","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5708636220704","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"2312","group":"Drikkevarer"},{"supplier_number":"Naturfrisk","description1":"Hyldebrus","description3":"25 cl","pos_id":787,"description2":"25 cl","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"100","buying_price":"8","selling_price":"15.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"2","user_id":"131409402138","item_cms_id":"211","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5708636108705","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"2318","group":"Drikkevarer"},{"supplier_number":"Naturfrisk","description1":"Bl&aring;b&aelig;r dream","description3":"26 cl","pos_id":788,"description2":"26 cl","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"100","buying_price":"10.75","selling_price":"20.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"1","user_id":"131409402138","item_cms_id":"204","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5708636220681","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"2311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":789,"description2":"null","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"1","user_id":"131409402138","item_cms_id":"543","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":790,"description2":"null","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"3","user_id":"131409402138","item_cms_id":"544","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":791,"description2":"null","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"2","user_id":"131409402138","item_cms_id":"542","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Roastbeef 1.stk","description3":"null","pos_id":792,"description2":"500","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"1","user_id":"131409402138","item_cms_id":"549","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7309","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"7309","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get 1.stk","description3":"null","pos_id":793,"description2":"null","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"1","user_id":"131409402138","item_cms_id":"546","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7306","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"7306","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":794,"description2":"null","order_number_id":"356","parent_type_id":"1-5930","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:10","quantity":"1","user_id":"131409402138","item_cms_id":"541","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5930","updated_at":"2014-10-18 12:49:10","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"}],"shop_receipt_id":"1-5930","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5930","change_value":"0.00","created_at":"2014-10-18 12:49:10","shop_order_payment_id":369,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"356","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 12:49:10","order_discount_value":"0","employee_id":"0","shop_order_id":"356","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":795,"description2":"null","order_number_id":"357","parent_type_id":"1-5931","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:49:59","quantity":"2","user_id":"131409402138","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5931","updated_at":"2014-10-18 12:49:59","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5931","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5931","change_value":"0.00","created_at":"2014-10-18 12:49:59","shop_order_payment_id":370,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"357","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 12:49:59","order_discount_value":"0","employee_id":"0","shop_order_id":"357","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":796,"description2":"null","order_number_id":"358","parent_type_id":"1-5932","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:51:09","quantity":"1","user_id":"131409402138","item_cms_id":"544","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5932","updated_at":"2014-10-18 12:51:09","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":797,"description2":"null","order_number_id":"358","parent_type_id":"1-5932","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:51:09","quantity":"1","user_id":"131409402138","item_cms_id":"541","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5932","updated_at":"2014-10-18 12:51:09","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Naturfrisk","description1":"Bl&aring;b&aelig;r","description3":"275","pos_id":798,"description2":"275","order_number_id":"358","parent_type_id":"1-5932","transaction_type_id":"3","discount_value":"100","buying_price":"9.5","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-18 12:51:09","quantity":"2","user_id":"131409402138","item_cms_id":"197","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5708636219494","shop_receipt_id":"1-5932","updated_at":"2014-10-18 12:51:09","sold_price":"0.00","color":"","item_id":"2303","group":"Drikkevarer"}],"shop_receipt_id":"1-5932","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5932","change_value":"0.00","created_at":"2014-10-18 12:51:09","shop_order_payment_id":371,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"358","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 12:51:09","order_discount_value":"0","employee_id":"0","shop_order_id":"358","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":799,"description2":"null","order_number_id":"358","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-18 13:38:51","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"544","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7304","shop_receipt_id":"1-5932","updated_at":"2014-10-18 13:38:51","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":800,"description2":"null","order_number_id":"358","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-18 13:38:51","quantity":"1","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"541","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7301","shop_receipt_id":"1-5932","updated_at":"2014-10-18 13:38:51","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Naturfrisk","description1":"Bl&aring;b&aelig;r","description3":"275","pos_id":801,"description2":"275","order_number_id":"358","transaction_type_id":"3","discount_value":"100","buying_price":"9.5","selling_price":"16.00","parent_type":"parked","created_at":"2014-10-18 13:38:51","quantity":"2","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"197","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"2","size":"","ean":"5708636219494","shop_receipt_id":"1-5932","updated_at":"2014-10-18 13:38:51","sold_price":"0.00","color":"","item_id":"2303","group":"Drikkevarer"}],"shop_receipt_id":"1-5932","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"day_start_id":"1161413569143161","shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-18 13:38:51","order_discount_value":"0","employee_id":"0","shop_order_id":"358","order_discount_type":"1"},{"total_amount":"30.00","transactions":[{"supplier_number":"1111111","description1":"rugbr&oslash;d","description3":"Custom Sale","pos_id":802,"description2":"Custom Sale","order_number_id":"359","parent_type_id":"1-5933","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"30.00","parent_type":"receipt_numbers","created_at":"2014-10-18 14:10:27","quantity":"1","user_id":"131409402138","item_cms_id":"485","day_start_id":"1161413569143161","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5933","updated_at":"2014-10-18 14:10:27","sold_price":"30.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5933","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"30.00","payments":[{"total_amount":"30.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5933","change_value":"0.00","created_at":"2014-10-18 14:10:27","shop_order_payment_id":372,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"359","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 14:10:27","order_discount_value":"0","employee_id":"0","shop_order_id":"359","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":803,"description2":"null","order_number_id":"360","parent_type_id":"1-5934","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 14:36:40","quantity":"1","user_id":"131409402138","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5934","updated_at":"2014-10-18 14:36:40","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5934","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5934","change_value":"0.00","created_at":"2014-10-18 14:36:40","shop_order_payment_id":373,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"360","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 14:36:40","order_discount_value":"0","employee_id":"0","shop_order_id":"360","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":804,"description2":"null","order_number_id":"361","parent_type_id":"1-5935","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 15:54:01","quantity":"2","user_id":"131409402138","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5935","updated_at":"2014-10-18 15:54:01","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5935","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5935","change_value":"0.00","created_at":"2014-10-18 15:54:01","shop_order_payment_id":374,"shop_order_user_id":"131409402138","change_type":"0","shop_order_id":"361","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 15:54:01","order_discount_value":"0","employee_id":"0","shop_order_id":"361","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":805,"description2":"null","order_number_id":"361","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"parked","created_at":"2014-10-18 15:55:36","quantity":"2","user_id":"131409402138","receipt_number_id":"0","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"2","size":"","ean":"7300","shop_receipt_id":"1-5935","updated_at":"2014-10-18 15:55:36","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5935","day_start_id":"1161413569143161","shop_user_id":"131409402138","sold_total_amount":"0.00","payments":[{"day_start_id":"1161413569143161","shop_order_payment_id":"0","payment_type_id":""}],"status_id":"2","created_at":"2014-10-18 15:55:36","order_discount_value":"0","employee_id":"0","shop_order_id":"361","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":806,"description2":"null","order_number_id":"362","parent_type_id":"1-5936","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:29:04","quantity":"2","user_id":"1","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5936","updated_at":"2014-10-18 16:29:04","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5936","day_start_id":"1161413569143161","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5936","change_value":"0.00","created_at":"2014-10-18 16:29:04","shop_order_payment_id":375,"shop_order_user_id":"1","change_type":"0","shop_order_id":"362","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 16:29:04","order_discount_value":"0","employee_id":"0","shop_order_id":"362","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"1111111","description1":"naturfrisk","description3":"Custom Sale","pos_id":807,"description2":"Custom Sale","order_number_id":"363","parent_type_id":"1-5937","transaction_type_id":"3","discount_value":"100","buying_price":"0","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:30:09","quantity":"4","user_id":"1","item_cms_id":"485","day_start_id":"1161413569143161","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5937","updated_at":"2014-10-18 16:30:09","sold_price":"0.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":808,"description2":"null","order_number_id":"363","parent_type_id":"1-5937","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:30:09","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5937","updated_at":"2014-10-18 16:30:09","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":809,"description2":"null","order_number_id":"363","parent_type_id":"1-5937","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:30:09","quantity":"2","user_id":"1","item_cms_id":"541","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5937","updated_at":"2014-10-18 16:30:09","sold_price":"0.00","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":810,"description2":"null","order_number_id":"363","parent_type_id":"1-5937","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:30:09","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5937","updated_at":"2014-10-18 16:30:09","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"}],"shop_receipt_id":"1-5937","day_start_id":"1161413569143161","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5937","change_value":"0.00","created_at":"2014-10-18 16:30:09","shop_order_payment_id":376,"shop_order_user_id":"1","change_type":"0","shop_order_id":"363","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 16:30:09","order_discount_value":"0","employee_id":"0","shop_order_id":"363","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":811,"description2":"null","order_number_id":"364","parent_type_id":"1-5938","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:32:28","quantity":"1","user_id":"1","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5938","updated_at":"2014-10-18 16:32:28","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5938","day_start_id":"1161413569143161","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5938","change_value":"0.00","created_at":"2014-10-18 16:32:28","shop_order_payment_id":377,"shop_order_user_id":"1","change_type":"0","shop_order_id":"364","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 16:32:28","order_discount_value":"0","employee_id":"0","shop_order_id":"364","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":812,"description2":"null","order_number_id":"365","parent_type_id":"1-5939","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:49:37","quantity":"1","user_id":"1","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5939","updated_at":"2014-10-18 16:49:37","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5939","day_start_id":"1161413569143161","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5939","change_value":"0.00","created_at":"2014-10-18 16:49:37","shop_order_payment_id":378,"shop_order_user_id":"1","change_type":"0","shop_order_id":"365","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 16:49:37","order_discount_value":"0","employee_id":"0","shop_order_id":"365","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":813,"description2":"null","order_number_id":"366","parent_type_id":"1-5940","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:59:12","quantity":"2","user_id":"1","item_cms_id":"542","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5940","updated_at":"2014-10-18 16:59:12","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":814,"description2":"null","order_number_id":"366","parent_type_id":"1-5940","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:59:12","quantity":"2","user_id":"1","item_cms_id":"547","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5940","updated_at":"2014-10-18 16:59:12","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"1111111","description1":"drikke","description3":"Custom Sale","pos_id":815,"description2":"Custom Sale","order_number_id":"366","parent_type_id":"1-5940","transaction_type_id":"3","discount_value":"100","buying_price":"0","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-18 16:59:12","quantity":"4","user_id":"1","item_cms_id":"485","day_start_id":"1161413569143161","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5940","updated_at":"2014-10-18 16:59:12","sold_price":"0.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5940","day_start_id":"1161413569143161","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5940","change_value":"0.00","created_at":"2014-10-18 16:59:12","shop_order_payment_id":379,"shop_order_user_id":"1","change_type":"0","shop_order_id":"366","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 16:59:12","order_discount_value":"0","employee_id":"0","shop_order_id":"366","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":816,"description2":"null","order_number_id":"367","parent_type_id":"1-5941","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-18 17:15:19","quantity":"3","user_id":"1","item_cms_id":"540","day_start_id":"1161413569143161","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5941","updated_at":"2014-10-18 17:15:19","sold_price":"0.00","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5941","day_start_id":"1161413569143161","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1161413569143161","shop_receipt_id":"1-5941","change_value":"0.00","created_at":"2014-10-18 17:15:19","shop_order_payment_id":380,"shop_order_user_id":"1","change_type":"0","shop_order_id":"367","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-18 17:15:19","order_discount_value":"0","employee_id":"0","shop_order_id":"367","order_discount_type":"1"},{"total_amount":"-617.0","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":817,"description2":"","order_number_id":"369","parent_type_id":"1241413648297743","transaction_type_id":"9","discount_value":"","buying_price":"","parent_type":"Day End","selling_price":"","created_at":"2014-10-18 18:04:57","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1161413569143161","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-18 18:04:57","sold_price":"-617.0","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1161413569143161","shop_user_id":"1","sold_total_amount":"-617.0","payments":[{"total_amount":"-617.0","cc_type_id":"","day_start_id":"1161413569143161","shop_receipt_id":"0","change_value":"","created_at":"2014-10-18 18:04:57","shop_order_payment_id":381,"shop_order_user_id":"1","change_type":"","shop_order_id":"369","payment_type_id":"8"}],"status_id":"3","created_at":"2014-10-18 18:04:57","order_discount_value":"","employee_id":"","shop_order_id":"369","order_discount_type":""},{"total_amount":"617.0","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":818,"description2":"","order_number_id":"370","parent_type_id":"1171413648297892","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2014-10-20 11:01:55","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1171413648297892","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-20 11:01:55","sold_price":"617.0","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"617.0","payments":[{"total_amount":"617.0","cc_type_id":"","day_start_id":"1171413648297892","shop_receipt_id":"0","change_value":"","created_at":"2014-10-20 11:01:55","shop_order_payment_id":382,"shop_order_user_id":"1","change_type":"","shop_order_id":"370","payment_type_id":"7"}],"status_id":"3","created_at":"2014-10-20 11:01:55","order_discount_value":"","employee_id":"","shop_order_id":"370","order_discount_type":""},{"total_amount":"29.00","transactions":[{"supplier_number":"1111111","description1":"ost kirk","description3":"Custom Sale","pos_id":819,"description2":"Custom Sale","order_number_id":"368","parent_type_id":"1-5942","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"29.00","parent_type":"receipt_numbers","created_at":"2014-10-20 11:40:07","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1171413648297892","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5942","updated_at":"2014-10-20 11:40:07","sold_price":"29.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5942","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"14.50","payments":[{"total_amount":"14.50","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5942","change_value":"5.50","created_at":"2014-10-20 11:40:07","shop_order_payment_id":383,"shop_order_user_id":"1","change_type":"0","shop_order_id":"368","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 11:40:07","order_discount_value":"50.0","employee_id":"0","shop_order_id":"368","order_discount_type":"1"},{"total_amount":"65.00","transactions":[{"supplier_number":"Sidinge G&amp;aring;rdbutik","description1":"Valn&oslash;dde Dressing","description3":"200","pos_id":820,"description2":"200","order_number_id":"371","parent_type_id":"1-5943","transaction_type_id":"3","discount_value":"0","buying_price":"32.5","selling_price":"65.00","parent_type":"receipt_numbers","created_at":"2014-10-20 11:59:16","quantity":"1","user_id":"1","item_cms_id":"316","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"1433","shop_receipt_id":"1-5943","updated_at":"2014-10-20 11:59:16","sold_price":"65.00","color":"","item_id":"1433","group":"Dressing"}],"shop_receipt_id":"1-5943","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"65.00","payments":[{"total_amount":"65.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5943","change_value":"0.00","created_at":"2014-10-20 11:59:16","shop_order_payment_id":384,"shop_order_user_id":"1","change_type":"0","shop_order_id":"371","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 11:59:16","order_discount_value":"0","employee_id":"0","shop_order_id":"371","order_discount_type":"1"},{"total_amount":"0.0","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":821,"description2":"null","order_number_id":"361","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:07:28","user_id":"1","quantity":"2","receipt_number_id":"0","item_cms_id":"540","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7300","updated_at":"2014-10-20 12:07:28","sold_price":"0","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"0","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.0","payments":[{"total_amount":"0.0","cc_type_id":"0","day_start_id":"1171413648297892","created_at":"2014-10-20 12:07:28","shop_order_payment_id":"0","shop_order_user_id":"1","shop_order_id":"361","payment_type_id":""}],"status_id":"2","created_at":"2014-10-20 12:07:28","order_discount_value":"0","employee_id":"0","shop_order_id":"361","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":822,"description2":"null","order_number_id":"361","parent_type_id":"1-5944","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:07:34","quantity":"2","user_id":"1","item_cms_id":"540","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5944","updated_at":"2014-10-20 12:07:34","sold_price":"0","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5944","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5944","change_value":"0.00","created_at":"2014-10-20 12:07:34","shop_order_payment_id":385,"shop_order_user_id":"1","change_type":"0","shop_order_id":"361","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 12:07:34","order_discount_value":"0","employee_id":"0","shop_order_id":"361","order_discount_type":"1"},{"total_amount":"0.0","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":823,"description2":"null","order_number_id":"358","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:07:44","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"544","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7304","updated_at":"2014-10-20 12:07:44","sold_price":"0","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":824,"description2":"null","order_number_id":"358","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:07:44","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"541","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7301","updated_at":"2014-10-20 12:07:44","sold_price":"0","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Naturfrisk","description1":"Blabar","description3":"275","pos_id":825,"description2":"275","order_number_id":"358","parent_type_id":"0","transaction_type_id":"3","discount_value":"100","buying_price":"9.5","parent_type":"Unparked","selling_price":"16","created_at":"2014-10-20 12:07:44","user_id":"1","quantity":"2","receipt_number_id":"0","item_cms_id":"197","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"6","size":"","ean":"5708636219494","updated_at":"2014-10-20 12:07:44","sold_price":"0","color":"","item_id":"2303","group":"Drikkevarer"}],"shop_receipt_id":"0","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.0","payments":[{"total_amount":"0.0","cc_type_id":"0","day_start_id":"1171413648297892","created_at":"2014-10-20 12:07:44","shop_order_payment_id":"0","shop_order_user_id":"1","shop_order_id":"358","payment_type_id":""}],"status_id":"2","created_at":"2014-10-20 12:07:44","order_discount_value":"0","employee_id":"0","shop_order_id":"358","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":826,"description2":"null","order_number_id":"358","parent_type_id":"1-5945","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:07:50","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5945","updated_at":"2014-10-20 12:07:50","sold_price":"0","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":827,"description2":"null","order_number_id":"358","parent_type_id":"1-5945","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:07:50","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5945","updated_at":"2014-10-20 12:07:50","sold_price":"0","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Naturfrisk","description1":"Bl&aring;b&aelig;r","description3":"275","pos_id":828,"description2":"275","order_number_id":"358","parent_type_id":"1-5945","transaction_type_id":"3","discount_value":"100","buying_price":"9.5","selling_price":"16","parent_type":"receipt_numbers","created_at":"2014-10-20 12:07:50","quantity":"2","user_id":"1","item_cms_id":"197","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5708636219494","shop_receipt_id":"1-5945","updated_at":"2014-10-20 12:07:50","sold_price":"0","color":"","item_id":"2303","group":"Drikkevarer"}],"shop_receipt_id":"1-5945","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5945","change_value":"0.00","created_at":"2014-10-20 12:07:50","shop_order_payment_id":386,"shop_order_user_id":"1","change_type":"0","shop_order_id":"358","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 12:07:50","order_discount_value":"0","employee_id":"0","shop_order_id":"358","order_discount_type":"1"},{"total_amount":"0.0","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":829,"description2":"null","order_number_id":"353","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:07:57","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"540","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7300","updated_at":"2014-10-20 12:07:57","sold_price":"0","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"0","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.0","payments":[{"total_amount":"0.0","cc_type_id":"0","day_start_id":"1171413648297892","created_at":"2014-10-20 12:07:57","shop_order_payment_id":"0","shop_order_user_id":"1","shop_order_id":"353","payment_type_id":""}],"status_id":"2","created_at":"2014-10-20 12:07:57","order_discount_value":"0","employee_id":"0","shop_order_id":"353","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Laksetapas","description3":"null","pos_id":830,"description2":"null","order_number_id":"353","parent_type_id":"1-5946","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:07:59","quantity":"1","user_id":"1","item_cms_id":"540","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7300","shop_receipt_id":"1-5946","updated_at":"2014-10-20 12:07:59","sold_price":"0","color":"","item_id":"7300","group":"Laks"}],"shop_receipt_id":"1-5946","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5946","change_value":"0.00","created_at":"2014-10-20 12:07:59","shop_order_payment_id":387,"shop_order_user_id":"1","change_type":"0","shop_order_id":"353","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 12:07:59","order_discount_value":"0","employee_id":"0","shop_order_id":"353","order_discount_type":"1"},{"total_amount":"139.0","transactions":[{"supplier_number":"Kathrine Andersen Chokolade","description1":"Gaveaske 200 gr.","description3":"null","pos_id":831,"description2":"200","order_number_id":"334","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"72","parent_type":"Unparked","selling_price":"139","created_at":"2014-10-20 12:08:05","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"584","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"5151","updated_at":"2014-10-20 12:08:05","sold_price":"139","color":"","item_id":"6017","group":"Chokolade"}],"shop_receipt_id":"0","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"139.0","payments":[{"total_amount":"139.0","cc_type_id":"0","day_start_id":"1171413648297892","created_at":"2014-10-20 12:08:05","shop_order_payment_id":"0","shop_order_user_id":"1","shop_order_id":"334","payment_type_id":""}],"status_id":"2","created_at":"2014-10-20 12:08:05","order_discount_value":"0","employee_id":"0","shop_order_id":"334","order_discount_type":"1"},{"total_amount":"29400.0","transactions":[{"supplier_number":"Simply Chocolate","description1":"36 STK \"I CAN STOP WHENEVER I WANT\"","description3":"null","pos_id":832,"description2":"360","order_number_id":"333","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"11650","parent_type":"Unparked","selling_price":"22900","created_at":"2014-10-20 12:08:17","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"335","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"5710885001768","updated_at":"2014-10-20 12:08:17","sold_price":"22900","color":"","item_id":"1203","group":"Chokolade"},{"supplier_number":"Simply Chocolate","description1":"8 STK \"YOU ME COFFEE NOW\"","description3":"null","pos_id":833,"description2":"80","order_number_id":"333","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"3175","parent_type":"Unparked","selling_price":"6500","created_at":"2014-10-20 12:08:17","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"333","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"5710885001744","updated_at":"2014-10-20 12:08:17","sold_price":"6500","color":"","item_id":"1201","group":"Chokolade"}],"shop_receipt_id":"0","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"29400.0","payments":[{"total_amount":"29400.0","cc_type_id":"0","day_start_id":"1171413648297892","created_at":"2014-10-20 12:08:17","shop_order_payment_id":"0","shop_order_user_id":"1","shop_order_id":"333","payment_type_id":""}],"status_id":"2","created_at":"2014-10-20 12:08:17","order_discount_value":"0","employee_id":"0","shop_order_id":"333","order_discount_type":"1"},{"total_amount":"0.0","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldraget laks 1.stk","description3":"null","pos_id":834,"description2":"null","order_number_id":"322","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:08:27","user_id":"1","quantity":"2","receipt_number_id":"0","item_cms_id":"547","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7307","updated_at":"2014-10-20 12:08:27","sold_price":"0","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":835,"description2":"null","order_number_id":"322","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:08:27","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"541","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7301","updated_at":"2014-10-20 12:08:27","sold_price":"0","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":836,"description2":"null","order_number_id":"322","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:08:27","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"542","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7302","updated_at":"2014-10-20 12:08:27","sold_price":"0","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solbarsaft drikkeklar","description3":"null","pos_id":837,"description2":"500","order_number_id":"322","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:08:27","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"551","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7311","updated_at":"2014-10-20 12:08:27","sold_price":"0","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":838,"description2":"500","order_number_id":"322","parent_type_id":"0","transaction_type_id":"3","discount_value":"0","buying_price":"0","parent_type":"Unparked","selling_price":"0","created_at":"2014-10-20 12:08:27","user_id":"1","quantity":"1","receipt_number_id":"0","item_cms_id":"552","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"6","size":"","ean":"7312","updated_at":"2014-10-20 12:08:27","sold_price":"0","color":"","item_id":"7312","group":"Drikkevarer"}],"shop_receipt_id":"0","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.0","payments":[{"total_amount":"0.0","cc_type_id":"0","day_start_id":"1171413648297892","created_at":"2014-10-20 12:08:27","shop_order_payment_id":"0","shop_order_user_id":"1","shop_order_id":"322","payment_type_id":""}],"status_id":"2","created_at":"2014-10-20 12:08:27","order_discount_value":"0","employee_id":"0","shop_order_id":"322","order_discount_type":"1"},{"total_amount":"0.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":839,"description2":"null","order_number_id":"322","parent_type_id":"1-5947","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:08:32","quantity":"2","user_id":"1","item_cms_id":"547","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5947","updated_at":"2014-10-20 12:08:32","sold_price":"0","color":"","item_id":"7307","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Laksesalat 1.stk","description3":"null","pos_id":840,"description2":"null","order_number_id":"322","parent_type_id":"1-5947","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:08:32","quantity":"1","user_id":"1","item_cms_id":"541","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7301","shop_receipt_id":"1-5947","updated_at":"2014-10-20 12:08:32","sold_price":"0","color":"","item_id":"7301","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":841,"description2":"null","order_number_id":"322","parent_type_id":"1-5947","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:08:32","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5947","updated_at":"2014-10-20 12:08:32","sold_price":"0","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Solb&aelig;rsaft drikkeklar","description3":"null","pos_id":842,"description2":"500","order_number_id":"322","parent_type_id":"1-5947","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:08:32","quantity":"1","user_id":"1","item_cms_id":"551","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7311","shop_receipt_id":"1-5947","updated_at":"2014-10-20 12:08:32","sold_price":"0","color":"","item_id":"7311","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Mynte m\/citron","description3":"null","pos_id":843,"description2":"500","order_number_id":"322","parent_type_id":"1-5947","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0","parent_type":"receipt_numbers","created_at":"2014-10-20 12:08:32","quantity":"1","user_id":"1","item_cms_id":"552","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7312","shop_receipt_id":"1-5947","updated_at":"2014-10-20 12:08:32","sold_price":"0","color":"","item_id":"7312","group":"Drikkevarer"}],"shop_receipt_id":"1-5947","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5947","change_value":"0.00","created_at":"2014-10-20 12:08:32","shop_order_payment_id":388,"shop_order_user_id":"1","change_type":"0","shop_order_id":"322","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 12:08:32","order_discount_value":"0","employee_id":"0","shop_order_id":"322","order_discount_type":"1"},{"total_amount":"49.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Pastrami 1.stk","description3":"null","pos_id":844,"description2":"null","order_number_id":"372","parent_type_id":"1-5948","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 12:13:54","quantity":"1","user_id":"1","item_cms_id":"491","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7005","shop_receipt_id":"1-5948","updated_at":"2014-10-20 12:13:54","sold_price":"24.50","color":"","item_id":"7005","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Roast Beef 1.stk","description3":"null","pos_id":845,"description2":"null","order_number_id":"372","parent_type_id":"1-5948","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 12:13:54","quantity":"1","user_id":"1","item_cms_id":"580","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7010","shop_receipt_id":"1-5948","updated_at":"2014-10-20 12:13:54","sold_price":"24.50","color":"","item_id":"7010","group":"Sandwich"}],"shop_receipt_id":"1-5948","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"39.00","payments":[{"total_amount":"39.00","cc_type_id":"1","day_start_id":"1171413648297892","shop_receipt_id":"1-5948","change_value":"0.00","created_at":"2014-10-20 12:13:54","shop_order_payment_id":389,"shop_order_user_id":"1","change_type":"0","shop_order_id":"372","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-20 12:13:54","order_discount_value":"10.0","employee_id":"0","shop_order_id":"372","order_discount_type":"2"},{"total_amount":"0.00","transactions":[{"supplier_number":"MY goodness","description1":"Aronia &amp; Bluebrry","description3":"37 cl","pos_id":846,"description2":"37 cl","order_number_id":"373","parent_type_id":"1-5949","transaction_type_id":"3","discount_value":"100","buying_price":"10.5","selling_price":"19.00","parent_type":"receipt_numbers","created_at":"2014-10-20 12:24:33","quantity":"2","user_id":"1","item_cms_id":"193","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"1","supplier_item_number":"","status_id":"3","size":"","ean":"5707535001094","shop_receipt_id":"1-5949","updated_at":"2014-10-20 12:24:33","sold_price":"0.00","color":"","item_id":"2405","group":"Drikkevarer"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Pastrami 1.stk","description3":"null","pos_id":847,"description2":"null","order_number_id":"373","parent_type_id":"1-5949","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-20 12:24:33","quantity":"3","user_id":"1","item_cms_id":"545","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7305","shop_receipt_id":"1-5949","updated_at":"2014-10-20 12:24:33","sold_price":"0.00","color":"","item_id":"7305","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":848,"description2":"null","order_number_id":"373","parent_type_id":"1-5949","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-20 12:24:33","quantity":"1","user_id":"1","item_cms_id":"547","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7307","shop_receipt_id":"1-5949","updated_at":"2014-10-20 12:24:33","sold_price":"0.00","color":"","item_id":"7307","group":"Sandwich"}],"shop_receipt_id":"1-5949","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5949","change_value":"0.00","created_at":"2014-10-20 12:24:33","shop_order_payment_id":390,"shop_order_user_id":"1","change_type":"0","shop_order_id":"373","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 12:24:33","order_discount_value":"0","employee_id":"0","shop_order_id":"373","order_discount_type":"1"},{"total_amount":"49.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Pastrami 1.stk","description3":"null","pos_id":849,"description2":"null","order_number_id":"374","parent_type_id":"1-5950","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 12:34:10","quantity":"1","user_id":"1","item_cms_id":"491","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7005","shop_receipt_id":"1-5950","updated_at":"2014-10-20 12:34:10","sold_price":"24.50","color":"","item_id":"7005","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":850,"description2":"null","order_number_id":"374","parent_type_id":"1-5950","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 12:34:10","quantity":"1","user_id":"1","item_cms_id":"487","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7001","shop_receipt_id":"1-5950","updated_at":"2014-10-20 12:34:10","sold_price":"24.50","color":"","item_id":"7001","group":"Sandwich"}],"shop_receipt_id":"1-5950","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"30.00","payments":[{"total_amount":"30.00","cc_type_id":"1","day_start_id":"1171413648297892","shop_receipt_id":"1-5950","change_value":"0.00","created_at":"2014-10-20 12:34:10","shop_order_payment_id":391,"shop_order_user_id":"1","change_type":"0","shop_order_id":"374","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-20 12:34:10","order_discount_value":"19.0","employee_id":"0","shop_order_id":"374","order_discount_type":"2"},{"total_amount":"49.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":851,"description2":"null","order_number_id":"375","parent_type_id":"1-5951","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 13:08:24","quantity":"2","user_id":"1","item_cms_id":"487","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7001","shop_receipt_id":"1-5951","updated_at":"2014-10-20 13:08:24","sold_price":"49.00","color":"","item_id":"7001","group":"Sandwich"}],"shop_receipt_id":"1-5951","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"39.00","payments":[{"total_amount":"39.00","cc_type_id":"1","day_start_id":"1171413648297892","shop_receipt_id":"1-5951","change_value":"0.00","created_at":"2014-10-20 13:08:24","shop_order_payment_id":392,"shop_order_user_id":"1","change_type":"0","shop_order_id":"375","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-20 13:08:24","order_discount_value":"10.0","employee_id":"0","shop_order_id":"375","order_discount_type":"2"},{"total_amount":"89.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":852,"description2":"null","order_number_id":"376","parent_type_id":"1-5952","transaction_type_id":"3","discount_value":"4,5","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 13:17:57","quantity":"1","user_id":"1","item_cms_id":"487","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7001","shop_receipt_id":"1-5952","updated_at":"2014-10-20 13:17:57","sold_price":"20.00","color":"","item_id":"7001","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":853,"description2":"null","order_number_id":"376","parent_type_id":"1-5952","transaction_type_id":"3","discount_value":"4,5","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 13:17:57","quantity":"1","user_id":"1","item_cms_id":"493","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7007","shop_receipt_id":"1-5952","updated_at":"2014-10-20 13:17:57","sold_price":"20.00","color":"","item_id":"7007","group":"Sandwich"},{"supplier_number":"1111111","description1":"stenalderen ost","description3":"Custom Sale","pos_id":854,"description2":"Custom Sale","order_number_id":"376","parent_type_id":"1-5952","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"49.00","parent_type":"receipt_numbers","created_at":"2014-10-20 13:17:57","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1171413648297892","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5952","updated_at":"2014-10-20 13:17:57","sold_price":"49.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5952","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"89.00","payments":[{"total_amount":"89.00","cc_type_id":"1","day_start_id":"1171413648297892","shop_receipt_id":"1-5952","change_value":"0.00","created_at":"2014-10-20 13:17:57","shop_order_payment_id":393,"shop_order_user_id":"1","change_type":"0","shop_order_id":"376","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-20 13:17:57","order_discount_value":"0","employee_id":"0","shop_order_id":"376","order_discount_type":"1"},{"total_amount":"158.00","transactions":[{"supplier_number":"ToGo","description1":"Pasta - Varmr&oslash;get","description3":"null","pos_id":855,"description2":"null","order_number_id":"377","parent_type_id":"1-5953","transaction_type_id":"3","discount_value":"0","buying_price":"25","selling_price":"49.00","parent_type":"receipt_numbers","created_at":"2014-10-20 13:31:20","quantity":"2","user_id":"1","item_cms_id":"496","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7101","shop_receipt_id":"1-5953","updated_at":"2014-10-20 13:31:20","sold_price":"98.00","color":"","item_id":"7101","group":"Pasta"},{"supplier_number":"Pastariget","description1":"Chili","description3":"250 g","pos_id":856,"description2":"250 g","order_number_id":"377","parent_type_id":"1-5953","transaction_type_id":"3","discount_value":"0","buying_price":"17","selling_price":"30.00","parent_type":"receipt_numbers","created_at":"2014-10-20 13:31:20","quantity":"2","user_id":"1","item_cms_id":"240","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5710366220039","shop_receipt_id":"1-5953","updated_at":"2014-10-20 13:31:20","sold_price":"60.00","color":"","item_id":"4104","group":"Pasta"}],"shop_receipt_id":"1-5953","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"158.00","payments":[{"total_amount":"158.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5953","change_value":"42.00","created_at":"2014-10-20 13:31:20","shop_order_payment_id":394,"shop_order_user_id":"1","change_type":"0","shop_order_id":"377","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 13:31:20","order_discount_value":"0","employee_id":"0","shop_order_id":"377","order_discount_type":"1"},{"total_amount":"59.00","transactions":[{"supplier_number":"1111111","description1":"juice","description3":"Custom Sale","pos_id":857,"description2":"Custom Sale","order_number_id":"378","parent_type_id":"1-5954","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"22.00","parent_type":"receipt_numbers","created_at":"2014-10-20 13:35:07","quantity":"1","user_id":"1","item_cms_id":"485","day_start_id":"1171413648297892","taxation_code":"1","discount_type_id":"2","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5954","updated_at":"2014-10-20 13:35:07","sold_price":"22.00","color":"","item_id":"0000000001","group":"11"},{"supplier_number":"ToGo","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":858,"description2":"null","order_number_id":"378","parent_type_id":"1-5954","transaction_type_id":"3","discount_value":"4,5","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 13:35:07","quantity":"1","user_id":"1","item_cms_id":"493","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7007","shop_receipt_id":"1-5954","updated_at":"2014-10-20 13:35:07","sold_price":"20.00","color":"","item_id":"7007","group":"Sandwich"},{"supplier_number":"Simply Chocolate","description1":"Grainy Gus","description3":"40","pos_id":859,"description2":"40","order_number_id":"378","parent_type_id":"1-5954","transaction_type_id":"3","discount_value":"1","buying_price":"8.79","selling_price":"18.00","parent_type":"receipt_numbers","created_at":"2014-10-20 13:35:07","quantity":"1","user_id":"1","item_cms_id":"342","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5710885000198","shop_receipt_id":"1-5954","updated_at":"2014-10-20 13:35:07","sold_price":"17.00","color":"","item_id":"1210","group":"Chokolade"}],"shop_receipt_id":"1-5954","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"59.00","payments":[{"total_amount":"59.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5954","change_value":"41.00","created_at":"2014-10-20 13:35:07","shop_order_payment_id":395,"shop_order_user_id":"1","change_type":"0","shop_order_id":"378","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 13:35:07","order_discount_value":"0","employee_id":"0","shop_order_id":"378","order_discount_type":"1"},{"total_amount":"49.00","transactions":[{"supplier_number":"ToGo","description1":"Sandwich - Koldr&oslash;get laks 1.stk","description3":"null","pos_id":860,"description2":"null","order_number_id":"379","parent_type_id":"1-5955","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 14:30:10","quantity":"1","user_id":"1","item_cms_id":"487","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7001","shop_receipt_id":"1-5955","updated_at":"2014-10-20 14:30:10","sold_price":"24.50","color":"","item_id":"7001","group":"Sandwich"},{"supplier_number":"ToGo","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":861,"description2":"null","order_number_id":"379","parent_type_id":"1-5955","transaction_type_id":"3","discount_value":"0","buying_price":"12.5","selling_price":"24.50","parent_type":"receipt_numbers","created_at":"2014-10-20 14:30:10","quantity":"1","user_id":"1","item_cms_id":"493","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7007","shop_receipt_id":"1-5955","updated_at":"2014-10-20 14:30:10","sold_price":"24.50","color":"","item_id":"7007","group":"Sandwich"}],"shop_receipt_id":"1-5955","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"49.00","payments":[{"total_amount":"49.00","cc_type_id":"1","day_start_id":"1171413648297892","shop_receipt_id":"1-5955","change_value":"0.00","created_at":"2014-10-20 14:30:10","shop_order_payment_id":396,"shop_order_user_id":"1","change_type":"0","shop_order_id":"379","payment_type_id":"2"}],"status_id":"3","created_at":"2014-10-20 14:30:10","order_discount_value":"0","employee_id":"0","shop_order_id":"379","order_discount_type":"1"},{"total_amount":"70.00","transactions":[{"supplier_number":"Sweetdeal","description1":"Sandwich - Kyllingsalat 1.stk","description3":"null","pos_id":862,"description2":"null","order_number_id":"380","parent_type_id":"1-5956","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:06:47","quantity":"1","user_id":"1","item_cms_id":"544","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7304","shop_receipt_id":"1-5956","updated_at":"2014-10-20 16:06:47","sold_price":"0.00","color":"","item_id":"7304","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Tunsalat 1.stk","description3":"null","pos_id":863,"description2":"null","order_number_id":"380","parent_type_id":"1-5956","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:06:47","quantity":"1","user_id":"1","item_cms_id":"543","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7303","shop_receipt_id":"1-5956","updated_at":"2014-10-20 16:06:47","sold_price":"0.00","color":"","item_id":"7303","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Krebsehale 1.stk","description3":"null","pos_id":864,"description2":"null","order_number_id":"380","parent_type_id":"1-5956","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:06:47","quantity":"1","user_id":"1","item_cms_id":"542","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7302","shop_receipt_id":"1-5956","updated_at":"2014-10-20 16:06:47","sold_price":"0.00","color":"","item_id":"7302","group":"Sandwich"},{"supplier_number":"Sweetdeal","description1":"Sandwich - Pastrami 1.stk","description3":"null","pos_id":865,"description2":"null","order_number_id":"380","parent_type_id":"1-5956","transaction_type_id":"3","discount_value":"0","buying_price":"0","selling_price":"0.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:06:47","quantity":"1","user_id":"1","item_cms_id":"545","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"7305","shop_receipt_id":"1-5956","updated_at":"2014-10-20 16:06:47","sold_price":"0.00","color":"","item_id":"7305","group":"Sandwich"},{"supplier_number":"Naturfrisk","description1":"Solb&aelig;r","description3":"275","pos_id":866,"description2":"275","order_number_id":"380","parent_type_id":"1-5956","transaction_type_id":"3","discount_value":"0","buying_price":"9.5","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:06:47","quantity":"1","user_id":"1","item_cms_id":"198","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5708636219449","shop_receipt_id":"1-5956","updated_at":"2014-10-20 16:06:47","sold_price":"16.00","color":"","item_id":"2304","group":"Drikkevarer"},{"supplier_number":"Naturfrisk","description1":"Hyldeblomst","description3":"275","pos_id":867,"description2":"275","order_number_id":"380","parent_type_id":"1-5956","transaction_type_id":"3","discount_value":"0","buying_price":"9.5","selling_price":"16.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:06:47","quantity":"1","user_id":"1","item_cms_id":"194","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5708636219432","shop_receipt_id":"1-5956","updated_at":"2014-10-20 16:06:47","sold_price":"16.00","color":"","item_id":"2300","group":"Drikkevarer"},{"supplier_number":"MY goodness","description1":"Blackberry &amp; Strawberry","description3":"34 cl","pos_id":868,"description2":"34 cl","order_number_id":"380","parent_type_id":"1-5956","transaction_type_id":"3","discount_value":"0","buying_price":"10.5","selling_price":"19.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:06:47","quantity":"1","user_id":"1","item_cms_id":"190","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5707535001124","shop_receipt_id":"1-5956","updated_at":"2014-10-20 16:06:47","sold_price":"19.00","color":"","item_id":"2402","group":"Drikkevarer"},{"supplier_number":"MY goodness","description1":"Aronia &amp; Bluebrry","description3":"37 cl","pos_id":869,"description2":"37 cl","order_number_id":"380","parent_type_id":"1-5956","transaction_type_id":"3","discount_value":"0","buying_price":"10.5","selling_price":"19.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:06:47","quantity":"1","user_id":"1","item_cms_id":"193","day_start_id":"1171413648297892","taxation_code":"10","discount_type_id":"2","supplier_item_number":"","status_id":"3","size":"","ean":"5707535001094","shop_receipt_id":"1-5956","updated_at":"2014-10-20 16:06:47","sold_price":"19.00","color":"","item_id":"2405","group":"Drikkevarer"}],"shop_receipt_id":"1-5956","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"0.00","payments":[{"total_amount":"0.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5956","change_value":"0.00","created_at":"2014-10-20 16:06:47","shop_order_payment_id":397,"shop_order_user_id":"1","change_type":"0","shop_order_id":"380","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 16:06:47","order_discount_value":"100.0","employee_id":"0","shop_order_id":"380","order_discount_type":"1"},{"total_amount":"132.00","transactions":[{"supplier_number":"1111111","description1":"citron laks","description3":"Custom Sale","pos_id":870,"description2":"Custom Sale","order_number_id":"381","parent_type_id":"1-5957","transaction_type_id":"3","discount_value":"40","buying_price":"0","selling_price":"55.00","parent_type":"receipt_numbers","created_at":"2014-10-20 16:11:06","quantity":"4","user_id":"1","item_cms_id":"485","day_start_id":"1171413648297892","taxation_code":"1","discount_type_id":"1","supplier_item_number":"11","status_id":"3","size":"","ean":"0000000001","shop_receipt_id":"1-5957","updated_at":"2014-10-20 16:11:06","sold_price":"132.00","color":"","item_id":"0000000001","group":"11"}],"shop_receipt_id":"1-5957","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"132.00","payments":[{"total_amount":"132.00","cc_type_id":"0","day_start_id":"1171413648297892","shop_receipt_id":"1-5957","change_value":"20.00","created_at":"2014-10-20 16:11:06","shop_order_payment_id":398,"shop_order_user_id":"1","change_type":"0","shop_order_id":"381","payment_type_id":"1"}],"status_id":"3","created_at":"2014-10-20 16:11:06","order_discount_value":"0","employee_id":"0","shop_order_id":"381","order_discount_type":"1"},{"total_amount":"-1045.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":872,"description2":"","order_number_id":"383","parent_type_id":"1251413879524604","transaction_type_id":"9","discount_value":"","buying_price":"","parent_type":"Day End","selling_price":"","created_at":"2014-10-21 10:18:44","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1171413648297892","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-21 10:18:44","sold_price":"-1045.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1171413648297892","shop_user_id":"1","sold_total_amount":"-1045.5","payments":[{"total_amount":"-1045.5","cc_type_id":"","day_start_id":"1171413648297892","shop_receipt_id":"0","change_value":"","created_at":"2014-10-21 10:18:44","shop_order_payment_id":399,"shop_order_user_id":"1","change_type":"","shop_order_id":"383","payment_type_id":"8"}],"status_id":"3","created_at":"2014-10-21 10:18:44","order_discount_value":"","employee_id":"","shop_order_id":"383","order_discount_type":""},{"total_amount":"1045.5","transactions":[{"promotion_ids":"","supplier_number":"","description1":"","description3":"","pos_id":873,"description2":"","order_number_id":"384","parent_type_id":"1181413879524788","transaction_type_id":"8","discount_value":"","buying_price":"","parent_type":"Day Start","selling_price":"","created_at":"2014-10-21 10:19:10","quantity":"","user_id":"1","item_cms_id":"","day_start_id":"1181413879524788","taxation_code":"","discount_type_id":"","supplier_item_number":"","status_id":"3","size":"","ean":"","shop_receipt_id":"0","updated_at":"2014-10-21 10:19:10","sold_price":"1045.5","color":"","item_id":"","group":""}],"shop_receipt_id":"0","day_start_id":"1181413879524788","shop_user_id":"1","sold_total_amount":"1045.5","payments":[{"total_amount":"1045.5","cc_type_id":"","day_start_id":"1181413879524788","shop_receipt_id":"0","change_value":"","created_at":"2014-10-21 10:19:10","shop_order_payment_id":400,"shop_order_user_id":"1","change_type":"","shop_order_id":"384","payment_type_id":"7"}],"status_id":"3","created_at":"2014-10-21 10:19:10","order_discount_value":"","employee_id":"","shop_order_id":"384","order_discount_type":""}]';


        $ch = curl_init('http://dbnposcms.zap-itsolutions.com/b2c.php/pScripts/syncSalesTransaction?shop_id=1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);

        echo $result;
        return sfView::NONE;
    }

}
