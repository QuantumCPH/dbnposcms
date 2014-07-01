<?php

set_time_limit(10000000);
require_once(sfConfig::get('sf_lib_dir') . '/changeLanguageCulture.php');
require_once(sfConfig::get('sf_lib_dir') . '/emailLib.php');
require_once(sfConfig::get('sf_lib_dir') . '/ForumTel.php');
require_once(sfConfig::get('sf_lib_dir') . '/commissionLib.php');
require_once(sfConfig::get('sf_lib_dir') . '/curl_http_client.php');
require_once(sfConfig::get('sf_lib_dir') . '/smsCharacterReplacement.php');
require_once(sfConfig::get('sf_lib_dir') . '/zerocall_out_sms.php');

/**
 * scripts actions.
 *
 * @package    Zapna
 * @subpackage scripts
 * @author     Baran Khursheed Khan
 * @version    actions.class.php,v 1.5 2012-01-16 22:20:12 BK Exp $
 */
class pScriptsActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    private $currentCulture;

    public function executeMobAccepted(sfWebRequest $request) {
        $order_id = $request->getParameter("orderid");

        $this->forward404Unless($order_id || $order_amount);

        $order = CustomerOrderPeer::retrieveByPK($order_id);

        $subscription_id = $request->getParameter("subscriptionid");
        $order_amount = ((double) $request->getParameter('amount')) / 100;

        $this->forward404Unless($order);

        $c = new Criteria;
        $c->add(TransactionPeer::ORDER_ID, $order_id);

        $transaction = TransactionPeer::doSelectOne($c);

        //echo var_dump($transaction);

        $order->setOrderStatusId(sfConfig::get('app_status_completed', 3)); //completed
        //$order->getCustomer()->setCustomerStatusId(sfConfig::get('app_status_completed', 3)); //completed
        $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3)); //completed




        if ($transaction->getAmount() > $order_amount) {
            //error
            $order->setOrderStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->setTransactionStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            //$order->getCustomer()->setCustomerStatusId(sfConfig::get('app_status_completed', 5)); //error in amount
        } else if ($transaction->getAmount() < $order_amount) {
            //$extra_refill_amount = $order_amount;
            $order->setExtraRefill($order_amount);
            $transaction->setAmount($order_amount);
        }





        //set active agent_package in case customer was registerred by an affiliate
        if ($order->getCustomer()->getAgentCompany()) {
            $order->setAgentCommissionPackageId($order->getCustomer()->getAgentCompany()->getAgentCommissionPackageId());
        }


        //set subscription id in case 'use current c.c for future auto refills' is set to 1
        if ($request->getParameter('USER_ATTR_20') == '1')
            $order->getCustomer()->setSubscriptionId($subscription_id);

        //set subscription id also when there is was no subscription for old customers
        if (!$order->getCustomer()->getSubscriptionId())
            $order->getCustomer()->setSubscriptionId($subscription_id);

        //set auto_refill amount
        if ($is_auto_refill_activated = $request->getParameter('USER_ATTR_1') == '1') {
            //set subscription id
            $order->getCustomer()->setSubscriptionId($subscription_id);

            //auto_refill_amount
            $auto_refill_amount_choices = array_keys(ProductPeer::getRefillHashChoices());

            $auto_refill_amount = in_array($request->getParameter('USER_ATTR_2'), $auto_refill_amount_choices) ? $request->getParameter('USER_ATTR_2') : $auto_refill_amount_choices[0];
            $order->getCustomer()->setAutoRefillAmount($auto_refill_amount);


            //auto_refill_lower_limit
            $auto_refill_lower_limit_choices = array_keys(ProductPeer::getAutoRefillLowerLimitHashChoices());

            $auto_refill_min_balance = in_array($request->getParameter('USER_ATTR_3'), $auto_refill_lower_limit_choices) ? $request->getParameter('USER_ATTR_3') : $auto_refill_lower_limit_choices[0];
            $order->getCustomer()->setAutoRefillMinBalance($auto_refill_min_balance);
        } else {
            //disable the auto-refill feature
            $order->getCustomer()->setAutoRefillAmount(0);
        }



        $order->save();
        $transaction->save();
        TransactionPeer::AssignReceiptNumber($transaction);


        $this->customer = $order->getCustomer();
        $c = new Criteria;
        $c->add(CustomerPeer::ID, $order->getCustomerId());
        $customer = CustomerPeer::doSelectOne($c);
        $agentid = $customer->getReferrerId();
        $productid = $order->getProductId();
        $transactionid = $transaction->getId();
        if (isset($agentid) && $agentid != "") {
            commissionLib::refilCustomer($agentid, $productid, $transactionid);
        }

        //TODO ask if recharge to be done is same as the transaction amount
        Fonet::recharge($this->customer, $transaction->getAmount());





// Update cloud 9
        c9Wrapper::equateBalance($this->customer);


        //set vat
        $vat = 0;
        $subject = $this->getContext()->getI18N()->__('Payment Confirmation');
        $sender_email = sfConfig::get('app_email_sender_email', 'support@kimarin.es');
        $sender_name = sfConfig::get('app_email_sender_name', 'Kimarin support');

        $recepient_email = trim($this->customer->getEmail());
        $recepient_name = sprintf('%s %s', $this->customer->getFirstName(), $this->customer->getLastName());
        $referrer_id = trim($this->customer->getReferrerId());

        if ($referrer_id):
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $referrer_id);

            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        endif;

        //send email
        $message_body = $this->getPartial('payments/order_receipt', array(
            'customer' => $this->customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'wrap' => false
        ));



        /*
          require_once(sfConfig::get('sf_lib_dir').'/swift/lib/swift_init.php');

          $connection = Swift_SmtpTransport::newInstance()
          ->setHost(sfConfig::get('app_email_smtp_host'))
          ->setPort(sfConfig::get('app_email_smtp_port'))
          ->setUsername(sfConfig::get('app_email_smtp_username'))
          ->setPassword(sfConfig::get('app_email_smtp_password'));

          $mailer = new Swift_Mailer($connection);

          $message_1 = Swift_Message::newInstance($subject)
          ->setFrom(array($sender_email => $sender_name))
          ->setTo(array($recepient_email => $recepient_name))
          ->setBody($message_body, 'text/html')
          ;

          $message_2 = Swift_Message::newInstance($subject)
          ->setFrom(array($sender_email => $sender_name))
          ->setTo(array($sender_email => $sender_name))
          ->setBody($message_body, 'text/html')
          ;

          if (!($mailer->send($message_1) && $mailer->send($message_2)))
          $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__(
          "Email confirmation is not sent" ));
         */

        //This Seciton For Make The Log History When Complete registration complete - Agent
        //echo sfConfig::get('sf_data_dir');
        $invite_data_file = sfConfig::get('sf_data_dir') . '/invite.txt';
        $invite2 = "Customer Refill Account \n";
        $invite2 .= "Recepient Email: " . $recepient_email . ' \r\n';
        $invite2 .= " Agent Email: " . $recepient_agent_email . ' \r\n';
        $invite2 .= " Sender Email: " . $sender_email . ' \r\n';

        file_put_contents($invite_data_file, $invite2, FILE_APPEND);


        //Send Email to User/Agent/Support --- when Customer Refilll --- 01/15/11
        $this->setPreferredCulture($this->customer);
        emailLib::sendCustomerRefillEmail($this->customer, $order, $transaction);
        $this->updatePreferredCulture();
        $this->setLayout(false);
    }

    public function executeAutoRefill(sfWebRequest $request) {
        //call Culture Method For Get Current Set Culture - Against Feature# 6.1 --- 02/28/11
        changeLanguageCulture::languageCulture($request, $this);

        //get customers to refill
        $c = new Criteria();

        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, sfConfig::get('app_status_completed'));
        $c->add(CustomerPeer::AUTO_REFILL_AMOUNT, 0, Criteria::NOT_EQUAL);
        $c->add(CustomerPeer::SUBSCRIPTION_ID, null, Criteria::ISNOTNULL);

        //$c1 = $c->getNewCriterion(CustomerPeer::LAST_AUTO_REFILL, 'TIMESTAMPDIFF(MINUTE, LAST_AUTO_REFILL, NOW()) > 1' , Criteria::CUSTOM);
        $c1 = $c->getNewCriterion(CustomerPeer::ID, null, Criteria::ISNOTNULL); //just accomodate missing disabled $c1
        $c2 = $c->getNewCriterion(CustomerPeer::LAST_AUTO_REFILL, null, Criteria::ISNULL);

        $c1->addOr($c2);

        $c->add($c1);

        $epay_con = new EPay();

        $customer = new Customer();

        //  var_dump(CustomerPeer::doCount($c));


        try {
            foreach (CustomerPeer::doSelect($c) as $customer) {

                $customer_balance = Fonet::getBalance($customer);

                var_dump($customer_balance);
                //if customer balance is less than 10
                if ($customer_balance != null && $customer_balance <= $customer->getAutoRefillMinBalance()) {



                    //create an order and transaction
                    $customer_order = new CustomerOrder();
                    $customer_order->setCustomer($customer);

                    //select order product
                    $c = new Criteria();
                    $c->add(CustomerProductPeer::CUSTOMER_ID, $customer->getId());
                    $customer_product = CustomerProductPeer::doSelectOne($c);

                    var_dump(CustomerProductPeer::doCount($c));



                    $customer_order->setProduct($customer_product->getProduct());
                    $customer_order->setQuantity(1);
                    $customer_order->setExtraRefill($customer->getAutoRefillAmount());


                    //create a transaction
                    $transaction = new Transaction();
                    $transaction->setCustomer($customer);
                    $transaction->setAmount($customer->getAutoRefillAmount());
                    $transaction->setDescription('Auto refill');



                    //associate transaction with customer order
                    $customer_order->addTransaction($transaction);

                    //save order to get order_id that is required to create a transaction via epay api
                    $customer_order->save();



                    if ($epay_con->authorize(sfConfig::get('app_epay_merchant_number'), $customer->getSubscriptionId(), $customer_order->getId(), $customer->getAutoRefillAmount(), 208, 1)) {
                        $customer->setLastAutoRefill(date('Y-m-d H:i:s'));
                        $customer_order->setOrderStatusId(sfConfig::get('app_status_completed'));
                        $transaction->setTransactionStatusId(sfConfig::get('app_status_completed'));
                    } else {
                        die('unauthorized epay');
                    }

                    $customer->save();
                    $customer_order->save();

                    if ($customer_order->getOrderStatusId() == sfConfig::get('app_status_completed') &&
                            Fonet::recharge($customer, $customer->getAutoRefillAmount())) {

                        $this->customer = $customer;
                        $TelintaMobile = sfConfig::get('app_country_code') . $this->customer->getMobileNumber();
                        $emailId = $this->customer->getEmail();
                        $OpeningBalance = $customer->getAutoRefillAmount();
                        $customerPassword = $this->customer->getPlainText();
                        $getFirstnumberofMobile = substr($this->customer->getMobileNumber(), 0, 1);     // bcdef
                        if ($getFirstnumberofMobile == 0) {
                            $TelintaMobile = substr($this->customer->getMobileNumber(), 1);
                            $TelintaMobile = sfConfig::get('app_country_code') . $TelintaMobile;
                        } else {
                            $TelintaMobile = sfConfig::get('app_country_code') . $this->customer->getMobileNumber();
                        }
                        $uniqueId = $this->customer->getUniqueid();
                        //This is for Recharge the Customer
                        $MinuesOpeningBalance = $OpeningBalance * 3;
                        $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name=' . $uniqueId . '&amount=' . $OpeningBalance . '&type=customer');
                        //This is for Recharge the Account
                        //this condition for if follow me is Active
                        $getvoipInfo = new Criteria();
                        $getvoipInfo->add(SeVoipNumberPeer::CUSTOMER_ID, $this->customer->getMobileNumber());
                        $getvoipInfos = SeVoipNumberPeer::doSelectOne($getvoipInfo); //->getId();
                        if (isset($getvoipInfos)) {
                            $voipnumbers = $getvoipInfos->getNumber();
                            $voip_customer = $getvoipInfos->getCustomerId();
                            //$telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name='.$voipnumbers.'&amount='.$OpeningBalance.'&type=account');
                        } else {
                            // $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name='.$uniqueId.'&amount='.$OpeningBalance.'&type=account');
                        }

                        // $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name=a'.$TelintaMobile.'&amount='.$OpeningBalance.'&type=account');
                        // $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name=cb'.$TelintaMobile.'&amount='.$OpeningBalance.'&type=account');

                        $MinuesOpeningBalance = $OpeningBalance * 3;
                        //type=<account_customer>&action=manual_charge&name=<name>&amount=<amount>
                        //This is for Recharge the Customer
                        // $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?type=customer&action=manual_charge&name='.$uniqueId.'&amount='.$MinuesOpeningBalance);
                        //update cloud 9
                        c9Wrapper::equateBalance($customer);


                        //send invoices

                        $message_body = $this->getPartial('customer/order_receipt', array(
                            'customer' => $customer,
                            'order' => $customer_order,
                            'transaction' => $transaction,
                            'vat' => 0,
                            'wrap' => false
                        ));

                        $subject = $this->getContext()->getI18N()->__('Payment Confirmation');
                        $sender_email = sfConfig::get('app_email_sender_email', 'support@landncall.com');
                        $sender_name = sfConfig::get('app_email_sender_name', 'LandNCall AB support');

                        $recepient_email = trim($this->customer->getEmail());
                        $recepient_name = sprintf('%s %s', $this->customer->getFirstName(), $this->customer->getLastName());


                        //This Seciton For Make The Log History When Complete registration complete - Agent
                        //echo sfConfig::get('sf_data_dir');
                        $invite_data_file = sfConfig::get('sf_data_dir') . '/invite.txt';
                        $invite2 = " AutoRefill - pScript \n";
                        $invite2 = "Recepient Email: " . $recepient_email . ' \r\n';


                        //Send Email to User/Agent/Support --- when Agent register Customer --- 01/15/11
                        emailLib::sendCustomerAutoRefillEmail($this->customer, $message_body);
                    }
                }
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }

        return sfView::NONE;
    }

    public function executeRemoveInactiveUsers(sfWebRequest $request) {
        $c = new Criteria();

        $c->add(CustomerOrderPeer::CUSTOMER_ID, 'customer_id IN (SELECT id FROM customer WHERE TIMESTAMPDIFF(MINUTE, NOW(), created_at) >= -30 AND customer_status_id = 1)'
                , Criteria::CUSTOM);

        $this->remove_propel_object_list(CustomerOrderPeer::doSelect($c));

        //now transaction
        $c = new Criteria();

        $c->add(TransactionPeer::CUSTOMER_ID, 'customer_id IN (SELECT id FROM customer WHERE TIMESTAMPDIFF(MINUTE, NOW(), created_at) >= -30 AND customer_status_id = 1)'
                , Criteria::CUSTOM);

        $this->remove_propel_object_list(TransactionPeer::doSelect($c));

        //now customer
        $c = new Criteria();

        $c->add(CustomerPeer::ID, 'id IN (SELECT id FROM customer WHERE TIMESTAMPDIFF(MINUTE, NOW(), created_at) >= -30 AND customer_status_id = 1)'
                , Criteria::CUSTOM);

        $this->remove_propel_object_list(CustomerPeer::doSelect($c));

        $this->renderText('last deleted on ' . date(DATE_RFC822));

        return sfView::NONE;
    }

    public function executeSMS(sfWebRequest $request) {


        $sms = SMS::receive($request);

        if ($sms) {
            //take action
            $valid_keywords = array('ZEROCALLS', 'ZEROCALLR', 'ZEROCALLN');

            if (in_array($sms->getKeyword(), $valid_keywords)) {
                //get voucher info
                $c = new Criteria();

                $c->add(VoucherPeer::PIN_CODE, $sms->getMessage());
                $c->add(VoucherPeer::USED_ON, null, CRITERIA::ISNULL);

                $is_voucher_ok = false;
                $voucher = VoucherPeer::doSelectOne($c);

                switch (strtolower($sms->getKeyword())) {
                    case 'zerocalls': //register + refill
                        //purchaes a product in 0 rs, and 200 refill
                        //create customer
                        //create order for a product
                        //don't create trnsaction for product order
                        //create refill order for product
                        //create transaction for refill order

                        if ($voucher) {
                            $is_voucher_ok = $voucher->getType() == 's';

                            $is_voucher_ok = $is_voucher_ok &&
                                    ($voucher->getAmount() == 200);
                        }

                        if ($is_voucher_ok) {
                            //check if customer already exists
                            if ($this->is_mobile_number_exists($sms->getMobileNumber())) {
                                $message = $this->getContext()->getI18N()->__('
		  						You mobile number is already registered with %1%.
		  					', array('%1%' => sfConfig::get('app_site_title')));

                                echo $message;
                                SMS::send($message, $sms->getMobileNumber());
                                break;
                            }

                            //This Function For Get the Enable Country Id =
                            $calingcode = sfConfig::get("app_country_code");
                            $countryId = $this->getEnableCountryId($calingcode);

                            //create a customer
                            $customer = new Customer();

                            $customer->setMobileNumber($sms->getMobileNumber());
                            $customer->setCountryId($countryId); //denmark;
                            $customer->setAddress('Street address');
                            $customer->setCity('City');
                            $customer->setDeviceId(1);
                            $customer->setEmail($sms->getMobileNumber() . '@zerocall.com');
                            $customer->setFirstName('First name');
                            $customer->setLastName('Last name');

                            $password = substr(md5($customer->getMobileNumber() . 'jhom$brabar_x'), 0, 8);
                            $customer->setPassword($password);

                            //crete an order of startpackage
                            $customer_order = new CustomerOrder();
                            $customer_order->setCustomer($customer);
                            $customer_order->setProductId(1);
                            $customer_order->setExtraRefill($voucher->getAmount());
                            $customer_order->setQuantity(0);
                            $customer_order->setIsFirstOrder(true);

                            //set customer_product

                            $customer_product = new CustomerProduct();

                            $customer_product->setCustomer($customer);
                            $customer_product->setProduct($customer_order->getProduct());

                            //crete a transaction of product price
                            $transaction = new Transaction();
                            $transaction->setAmount($voucher->getAmount());
                            $transaction->setDescription($this->getContext()->getI18N()->__('Product  purchase & refill, via voucher'));
                            $transaction->setOrderId($customer_order->getId());
                            $transaction->setCustomer($customer);


                            $customer->setCustomerStatusId(sfConfig::get('app_status_completed', 3));
                            $customer_order->setOrderStatusId(sfConfig::get('app_status_completed', 3));
                            $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3));


                            $customer->save();
                            $customer_order->save();
                            $customer_product->save();
                            $transaction->save();
                            TransactionPeer::AssignReceiptNumber($transaction);


                            //save voucher so it can't be reused
                            $voucher->setUsedOn(date('Y-m-d'));

                            $voucher->save();

                            //register with fonet
                            Fonet::registerFonet($customer);
                            Fonet::recharge($customer, $transaction->getAmount());

                            $this->customer = $customer;
                            $getFirstnumberofMobile = substr($this->customer->getMobileNumber(), 0, 1);     // bcdef
                            if ($getFirstnumberofMobile == 0) {
                                $TelintaMobile = substr($this->customer->getMobileNumber(), 1);
                                $TelintaMobile = sfConfig::get('app_country_code') . $TelintaMobile;
                            } else {
                                $TelintaMobile = sfConfig::get('app_country_code') . $this->customer->getMobileNumber();
                            }
                            $uniqueId = $this->customer->getUniqueid();
                            $emailId = $this->customer->getEmail();
                            $OpeningBalance = $transaction->getAmount();
                            $customerPassword = $this->customer->getPlainText();

                            //Section For Telinta Add Cusomter
                            $telintaRegisterCus = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?reseller=R_WLS_Kimarin_ES&action=add&name=' . $uniqueId . '&currency=' . sfConfig::get('app_currency_symbol') . '&opening_balance=0&credit_limit=0&enable_dialingrules=Yes&int_dial_pre=00&email=' . $emailId . '&type=customer');

                            // For Telinta Add Account
                            $telintaAddAccount = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?type=account&action=activate&name=' . $uniqueId . '&customer=' . $uniqueId . '&opening_balance=-' . $OpeningBalance . '&product=YYYLandncall_Forwarding&outgoing_default_r_r=2034&activate_follow_me=Yes&follow_me_number=0&billing_model=1&password=' . $customerPassword);
                            $telintaAddAccountA = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?type=account&action=activate&name=a' . $TelintaMobile . '&customer=' . $TelintaMobile . '&opening_balance=-' . $OpeningBalance . '&product=YYYLandncall_CT&outgoing_default_r_r=2034&billing_model=1&password=' . $customerPassword);
                            $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?type=account&action=activate&name=cb' . $TelintaMobile . '&customer=' . $TelintaMobile . '&opening_balance=-' . $OpeningBalance . '&product=YYYLandncall_callback&outgoing_default_r_r=2034&billing_model=1&password=' . $customerPassword);

                            //This is for Recharge the Customer
                            $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name=' . $uniqueId . '&amount=' . $OpeningBalance . '&type=customer');



                            $message = $this->getContext()->getI18N()->__('
			  			You have been registered to ZerOcall.' /* \n
                                      You can use following login information to access your account.\n
                                      Email: '. $customer->getEmail(). '\n' .
                                      'Password: ' . $password */
                            );

                            echo $message;
                            SMS::send($message, $customer->getMobileNumber());
                        } else {
                            $invalid_pin_sms = SMS::send($this->getContext()->getI18N()->__('Invalid pin code.'), $sms->getMobileNumber());
                            echo $invalid_pin_sms;
                            $this->logMessage('invaild pin sms sent to ' . $sms->getMobileNumber());
                        }

                        break;
                    case 'zerocallr': //refill
                        //check if mobile number exists?
                        //create an order for sms refill
                        //create a transaction
                        if ($voucher) {
                            $is_voucher_ok = $voucher->getType() == 'r';

                            $valid_refills = array(100, 200, 500);

                            $is_voucher_ok = $is_voucher_ok && in_array($voucher->getAmount(), $valid_refills);
                        }

                        if ($is_voucher_ok) {
                            //check if customer already exists
                            if (!$this->is_mobile_number_exists($sms->getMobileNumber())) {
                                $message = $this->getContext()->getI18N()->__('
		  						Your mobile number is not registered with LandNCall AB.
		  					');

                                echo $message;
                                SMS::send($message, $sms->getMobileNumber());
                                break;
                            }
                            //get the customer

                            $c = new Criteria();
                            $c->add(CustomerPeer::MOBILE_NUMBER, $sms->getMobileNumber());


                            $customer = CustomerPeer::doSelectOne($c);

                            //create new customer order
                            $customer_order = new CustomerOrder();
                            $customer_order->setCustomer($customer);

                            //get customer product

                            $c = new Criteria();
                            $c->add(CustomerProductPeer::CUSTOMER_ID, $customer->getId());

                            $customer_product = CustomerProductPeer::doSelectOne($c);

                            //set customer product
                            $customer_order->setProduct($customer_product->getProduct());

                            $customer_order->setExtraRefill($voucher->getAmount());
                            $customer_order->setQuantity(0);
                            $customer_order->setIsFirstOrder(false);


                            //crete a transaction of product price
                            $transaction = new Transaction();
                            $transaction->setAmount($voucher->getAmount());
                            $transaction->setDescription($this->getContext()->getI18N()->__('LandNCall AB  Refill, via voucher'));
                            $transaction->setOrderId($customer_order->getId());
                            $transaction->setCustomer($customer);


                            $customer_order->setOrderStatusId(sfConfig::get('app_status_completed', 3));
                            $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3));

                            $customer_order->save();
                            $transaction->save();
                            TransactionPeer::AssignReceiptNumber($transaction);
                            Fonet::recharge($customer, $transaction->getAmount());


                            //save voucher so it can't be reused
                            $voucher->setUsedOn(date('Y-m-d H:i:s'));

                            $voucher->save();

                            $message = $this->getContext()->getI18N()->__('
			  			You account has been topped up.' /* \n
                                      You can use following login information to access your account.\n
                                      Email: '. $customer->getEmail(). '\n' .
                                      'Password: ' . $password */
                            );

                            echo $message;
                            SMS::send($message, $sms->getMobileNumber());
                        } else {
                            $invalid_pin_sms = SMS::send($this->getContext()->getI18N()->__('Invalid pin code.'), $sms->getMobileNumber());
                            echo $invalid_pin_sms;
                            $this->logMessage('invaild pin sms sent to ' . $sms->getMobileNumber());
                        }

                        break;
                    case 'zerocalln':
                        //purchases a 100 product, no refill
                        //check if pin code
                        // pin code matches
                        // not used before
                        //	type is n, amount eq to gt than product price



                        if ($voucher) {
                            $is_voucher_ok = $voucher->getType() == 'n';

                            $is_voucher_ok = $is_voucher_ok &&
                                    ($voucher->getAmount() >= ProductPeer::retrieveByPK(1)->getPrice());
                        }

                        if ($is_voucher_ok) {
                            //check if customer already exists
                            if ($this->is_mobile_number_exists($sms->getMobileNumber())) {
                                $message = $this->getContext()->getI18N()->__('
		  						You mobile number is already registered with %1%.
		  					', array('%1%', sfConfig::get('app_site_title')));

                                echo $message;
                                SMS::send($message, $sms->getMobileNumber());
                                break;
                            }

                            //This Function For Get the Enable Country Id =
                            $calingcode = sfConfig::get("app_country_code");
                            $countryId = $this->getEnableCountryId($calingcode);

                            //create a customer
                            $customer = new Customer();

                            $customer->setMobileNumber($sms->getMobileNumber());
                            $customer->setCountryId($countryId); //denmark;
                            $customer->setAddress('Street address');
                            $customer->setCity('City');
                            $customer->setDeviceId(1);
                            $customer->setEmail($sms->getMobileNumber() . '@zerocall.com');
                            $customer->setFirstName('First name');
                            $customer->setLastName('Last name');

                            $password = substr(md5($customer->getMobileNumber() . 'jhom$brabar_x'), 0, 8);
                            $customer->setPassword($password);

                            //crete an order of startpackage
                            $customer_order = new CustomerOrder();
                            $customer_order->setCustomer($customer);
                            $customer_order->setProductId(1);
                            $customer_order->setExtraRefill(0);
                            $customer_order->setQuantity(1);
                            $customer_order->setIsFirstOrder(true);

                            //set customer_product

                            $customer_product = new CustomerProduct();

                            $customer_product->setCustomer($customer);
                            $customer_product->setProduct($customer_order->getProduct());

                            //crete a transaction of product price
                            $transaction = new Transaction();
                            $transaction->setAmount($customer_order->getProduct()->getPrice() * $customer_order->getQuantity());
                            $transaction->setDescription($this->getContext()->getI18N()->__('Product  purchase, via voucher'));
                            $transaction->setOrderId($customer_order->getId());
                            $transaction->setCustomer($customer);


                            $customer->setCustomerStatusId(sfConfig::get('app_status_completed', 3));
                            $customer_order->setOrderStatusId(sfConfig::get('app_status_completed', 3));
                            $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3));


                            $customer->save();
                            $customer_order->save();
                            $customer_product->save();
                            $transaction->save();
                            TransactionPeer::AssignReceiptNumber($transaction);

                            //save voucher so it can't be reused
                            $voucher->setUsedOn(date('Y-m-d'));

                            $voucher->save();

                            //register with fonet
                            Fonet::registerFonet($customer);

                            $message = $this->getContext()->getI18N()->__('
			  			You have been registered to %1%.' /* \n
                                      You can use following login information to access your account.\n
                                      Email: '. $customer->getEmail(). '\n' .
                                      'Password: ' . $password */
                                    , array('%1%', sfConfig::get('app_site_title')));

                            echo $message;
                            SMS::send($message, $sms->getMobileNumber());
                        } else {
                            $invalid_pin_sms = SMS::send($this->getContext()->getI18N()->__('Invalid pin code.'), $sms->getMobileNumber());
                            echo $invalid_pin_sms;
                            $this->logMessage('invaild pin sms sent to ' . $sms->getMobileNumber());
                        }

                        break;
                }
            }
        }

        $this->renderText('completed');

        return sfView::NONE;
    }

    private function is_mobile_number_exists($mobile_number) {
        $c = new Criteria();

        $c->add(CustomerPeer::MOBILE_NUMBER, $mobile_number);

        if (CustomerPeer::doSelectOne($c))
            return true;
    }

    private function remove_propel_object_list($list) {
        foreach ($list as $list_item) {
            $list_item->delete();
        }
    }

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
                ->setPassword(sfConfig::get('app_email_smtp_password'));




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

    public function executeC9invoke(sfWebRequest $request) {

        $this->logMessage(print_r($_POST, true));

        // creating model object
        $c9Data = new cloud9_data();

        //setting data in model
        $c9Data->setRequestType($request->getParameter('request_type'));
        $c9Data->setC9Timestamp($request->getParameter('timestamp'));
        $c9Data->setTransactionID($request->getParameter('transactionid'));
        $c9Data->setCallDate($request->getParameter('call_date'));
        $c9Data->setCdr($request->getParameter('cdr_id'));
        $c9Data->setCid($request->getParameter('carrierid'));
        $c9Data->setMcc($request->getParameter('mcc'));
        $c9Data->setMnc($request->getParameter('mnc'));
        $c9Data->setImsi($request->getParameter('imsi'));
        $c9Data->setMsisdn($request->getParameter('msisdn'));
        $c9Data->setDestination($request->getParameter('destination'));
        $c9Data->setLeg($request->getParameter('leg'));
        $c9Data->setLegDuration($request->getParameter('leg_duration'));
        $c9Data->setResellerCharge($request->getParameter('reseller_charge'));
        $c9Data->setClientCharge($request->getParameter('client_charge'));
        $c9Data->setUserCharge($request->getParameter('user_charge'));
        $c9Data->setIot($request->getParameter('IOT'));
        $c9Data->setUserBalance($request->getParameter('user_balance'));

//saving model object in Database	
        $c9Data->save();



        $conversion_rate = CurrencyConversionPeer::retrieveByPK(1);

        $exchange_rate = $conversion_rate->getBppDkk();

        $amt_bpp = $c9Data->getUserBalance();

        $amt_dkk = $amt_bpp * $exchange_rate;

//find the customer.

        $c = new Criteria();
        $c->add(CustomerPeer::C9_CUSTOMER_NUMBER, $c9Data->getMsisdn());
        $customer = CustomerPeer::doSelectOne($c);


//get fonet balance

        $fonet = new Fonet();
        $balance = $fonet->getBalance($customer, true);

//update Balance on Fonet if there's a difference

        if ($fonet->recharge($customer, number_format($amt_dkk - $balance, 2), true)) {

//if fonet customer found, send success response.

            $this->getResponse()->setContentType("text/xml");
            $this->getResponse()->setContent("<?xml version=\"1.0\"?>
        <CDR_response>
        <cdr_id>" . $request->getParameter('cdr_id') . "</cdr_id>
        <cdr_status>1</cdr_status>
        </CDR_response> ");
        }

        return sfView::NONE;
    }

    public function c9_follow_up(Cloud9Data $c9Data) {

        echo("inside follow up \n: ");



        echo("calculcated amount: ");
        echo($amt_dkk);

//
//        $balance = $amt * $exchange_rate->getBppDkk();
//
//        echo($balance);
//
//        //echo($user_balance_dkk);
//
//        $cust = CustomerPeer::retrieveByPK(22);
//
//        $cust->setC9CustomerNumber($balance);
//
//        $cust->save();
//
//        return $cust;
//            echo('hello/');
//            $customer = CustomerPeer::retrieveByPK(1);
//            echo('world/');
//
//            $fonet = new Fonet();
//            $balance = $fonet->getBalance($customer, true);
//            echo('hilo/');
//            echo($balance);
//            echo('verden/');
//
//            $fonet->recharge($customer, -20, true);
//            echo('hilo 2/');
//            $balance = $fonet->getBalance($customer, true);
//            echo('hilo 3/');
//            echo($balance);
//            echo('world');
        //echo($balance->getBalance(&$customer));
    }

    public function executeBalanceAlert(sfWebRequest $request) {
        $username = 'zerocall';
        $password = 'ok20717786';
        //$c=new Criteria();
        //$fonet=new Fonet();
        //  $customers=CustomerPeer::doSelect($c);
        $balance = $request->getParameter('balance');
        $mobileNo = $request->getParameter('mobile');
        //foreach($customers as $customer)
        //{
        $balance_data_file = sfConfig::get('sf_data_dir') . '/balanceTest.txt';
        $baltext = "";
        $baltext .= "Mobile No: {$mobileNo} , Balance: {$balance} \r\n";

        file_put_contents($balance_data_file, $baltext, FILE_APPEND);

        if ($mobileNo) {
            if ($balance < 25 && $balance > 10) {

                $baltext .= "balance < 25 && balance > 10";
                $data = array(
                    'username' => $username,
                    'password' => $password,
                    'mobile' => $mobileNo,
                    'message' => "You balance is below 25 " . sfConfig::get('app_currency_code') . ", Please refill your account. " . sfConfig::get('app_site_title') . " - Support "
                );
                $queryString = http_build_query($data, '', '&');
                $this->response_text = file_get_contents('http://sms.gratisgateway.dk/send.php?' . $queryString);
                echo $this->response_text;
            } else if ($balance < 10.00 && $balance > 0.00) {

                $data = array(
                    'username' => $username,
                    'password' => $password,
                    'mobile' => $mobileNo,
                    'message' => "You balance is below 10 " . sfConfig::get('app_currency_code') . ", Please refill your account. " . sfConfig::get('app_site_title') . " - Support"
                );
                $queryString = http_build_query($data, '', '&');
                $this->response_text = file_get_contents('http://sms.gratisgateway.dk/send.php?' . $queryString);
                $baltext .= "balance < 10 && balance > 0";
            } else if ($balance <= 0.00) {


                $data = array(
                    'username' => $username,
                    'password' => $password,
                    'mobile' => $mobileNo,
                    'message' => "You balance is 0 " . sfConfig::get('app_currency_code') . ", Please refill your account. " . sfConfig::get('app_site_title') . " - Support "
                );
                $queryString = http_build_query($data, '', '&');
                $this->response_text = file_get_contents('http://sms.gratisgateway.dk/send.php?' . $queryString);
                $baltext .= "balance 0";
            }
        }


        $baltext .= $this->response_text;
        file_put_contents($balance_data_file, $baltext, FILE_APPEND);


        $data = array(
            'mobile' => $mobileNo,
            'balance' => $balance
        );

        $queryString = http_build_query($data, '', '&');
        $this->redirect('pScripts/balanceAlert?' . $queryString);



        return sfView::NONE;
    }

    public function executeBalanceEmail(sfWebRequest $request) {


        $balance = $request->getParameter('balance');
        $mobileNo = $request->getParameter('mobile');

        $email_data_file = sfConfig::get('sf_data_dir') . '/EmailAlert.txt';
        $email_msg = "";
        $email_msg .= "Mobile No: {$mobileNo} , Balance: {$balance} \r\n";
        file_put_contents($email_data_file, $email_msg, FILE_APPEND);

        //$fonet=new Fonet();
        //
      
      $c = new Criteria();
        $c->add(CustomerPeer::MOBILE_NUMBER, $mobileNo);
        $customers = CustomerPeer::doSelect($c);
        $recepient_name = '';
        $recepient_email = '';
        foreach ($customers as $customer) {
            $recepient_name = $customer->getFirstName() . ' ' . $customer->getLastName();
            $recepient_email = $customer->getEmail();
        }


        //$recepient_name=
        //foreach($customers as $customer)
        //{

        file_put_contents($email_data_file, $email_msg, FILE_APPEND);

        if ($mobileNo) {
            if ($balance < 25.00 && $balance > 10.00) {
                $email_msg .= "\r\n balance < 25 && balance > 10";
                //echo 'mail sent to you';
                $subject = 'Test Email: Balance Email ';
                $message_body = "Test Email:  Your balance is below 25" . sfConfig::get('app_currency_code') . " , please refill otherwise your account will be closed. \r\n - " . sfConfig::get('app_site_title') . " Support \r\n Company Contact Info";

                //This Seciton For Make The Log History When Complete registration complete - Agent
                //echo sfConfig::get('sf_data_dir');
                $invite_data_file = sfConfig::get('sf_data_dir') . '/invite.txt';
                $invite2 = " Balance Email - pScript \n";
                if ($recepient_email):
                    $invite2 = "Recepient Email: " . $recepient_email . ' \r\n';
                endif;

                //Send Email to Customer For Balance --- 01/15/11
                emailLib::sendCustomerBalanceEmail($customers, $message_body);
            }
            else if ($balance < 10.00 && $balance > 0.00) {

                $email_msg .= "\r\n balance < 10 && balance > 0";
                $subject = 'Test Email: Balance Email ';
                $message_body = "Test Email:  Your balance is below 10" . sfConfig::get('app_currency_code') . " , please refill otherwise your account will be closed. \r\n - " . sfConfig::get('app_site_title') . " Support \r\n Company Contact Info";

                //This Seciton For Make The Log History When Complete registration complete - Agent
                //echo sfConfig::get('sf_data_dir');
                $invite_data_file = sfConfig::get('sf_data_dir') . '/invite.txt';
                $invite2 = " Balance Email - pScript \n";
                if ($recepient_email):
                    $invite2 = "Recepient Email: " . $recepient_email;
                endif;

                //Send Email to Customer For Balance --- 01/15/11
                emailLib::sendCustomerBalanceEmail($customers, $message_body);
            }
            else if ($balance <= 0.00) {
                $email_msg .= "\r\n balance < 10 && balance > 0";
                $subject = 'Test Email: Balance Email ';
                $message_body = "Test Email:  Your balance is 0 " . sfConfig::get('app_currency_symbol') . ", please refill otherwise your account will be closed. \r\n - " . sfConfig::get('app_site_title') . " Support \r\n Company Contact Info";

                //This Seciton For Make The Log History When Complete registration complete - Agent
                //echo sfConfig::get('sf_data_dir');
                $invite_data_file = sfConfig::get('sf_data_dir') . '/invite.txt';
                $invite2 = " Balance Email - pScript \n";
                if ($recepient_email):
                    $invite2 = "Recepient Email: " . $recepient_email;
                endif;

                //Send Email to Customer For Balance --- 01/15/11
                emailLib::sendCustomerBalanceEmail($customers, $message_body);
            }
        }


        $email_msg .= $message_body;
        $email_msg .= "\r\n Email Sent";
        file_put_contents($email_data_file, $email_msg, FILE_APPEND);
        return sfView::NONE;
    }

    public function executeWebSms(sfWebRequest $request) {
        require_once(sfConfig::get('sf_lib_dir') . '\SendSMS.php');
        require_once(sfConfig::get('sf_lib_dir') . '\IncomingFormat.php');
        require_once(sfConfig::get('sf_lib_dir') . '\ClientPolled.php');


        //$sms_username = "zapna01";
        //$sms_password = "Zapna2010";




        $replies = send_sms_full("923454375829", "CBF", "Test SMS: Taisys Test SMS form test.Zerocall.com"); //or die ("Error: " .$errstr. " \n");
        //$replies = send_sms("44123456789,44987654321,44214365870","SMS_Service", "This is a message from me.") or die ("Error: " . $errstr . "\n");

        echo "<br /> Response from Taisys <br />";
        echo $replies;
        echo $errstr;
        echo "<br />";

        file_get_contents("http://sms1.cardboardfish.com:9001/HTTPSMS?S=H&UN=zapna1&P=Zapna2010&DA=923454375829&ST=5&SA=Zerocall&M=Test+SMS%3A+Taisys+Test+SMS+form+test.Zerocall.com");

        return sfView::NONE;
    }

    public function executeTaisys(sfWebrequest $request) {

        $taisys = new Taisys();

        $taisys->setServ($request->getParameter('serv'));
        $taisys->setImsi($request->getParameter('imsi'));
        $taisys->setDn($request->getParameter('dest'));
        $taisys->setSmscontent($request->getParameter('content'));
        $taisys->setChecksum($request->getParameter('mac'));
        $taisys->setChecksumVerification(true);

        $taisys->save();

        $data = array(
            'S' => 'H',
            'UN' => 'zapna1',
            'P' => 'Zapna2010',
            'DA' => $taisys->getDn(),
            'SA' => 'Zerocall',
            'M' => $taisys->getSmscontent(),
            'ST' => '5'
        );


        $queryString = http_build_query($data, '', '&');
        $queryString = smsCharacter::smsCharacterReplacement($queryString);
        $res = file_get_contents('http://sms1.cardboardfish.com:9001/HTTPSMS?' . $queryString);
        $this->res_cbf = 'Response from CBF is: ';
        $this->res_cbf .= $res;

        echo $this->res_cbf;
        return sfView::NONE;
    }

    public function executeSmsRegistration(sfWebrequest $request) {

        $number = $request->getParameter('mobile');
        $customercount = 0;
        $agentCount = 0;
        $productCount = 0;
        $mnc = new Criteria();
        $mnc->add(CustomerPeer::MOBILE_NUMBER, substr($number, 2));
        $mnc->addAnd(CustomerPeer::CUSTOMER_STATUS_ID, 3);
        $customercount = CustomerPeer::doCount($mnc);
        if ($customercount > 0) {
            echo "Mobile number Already exist";
            $sm = new Criteria();
            $sm->add(SmsTextPeer::ID, 1);
            $smstext = SmsTextPeer::doSelectOne($sm);
            $sms_text = $smstext->getMessageText();
            CARBORDFISH_SMS::Send($number, $sms_text);
            die;
        }
        $message = $request->getParameter('message');
        $keyword = $request->getParameter('keyword');
        $agent_code = substr($message, 0, 4);
        $product_code = substr($message, 4, 2);
        $uniqueid = substr($message, 6, 6);

        $c = new Criteria();
        $c->add(AgentCompanyPeer::SMS_CODE, $agent_code);
        $agentCount = AgentCompanyPeer::doCount($c);
        if ($agentCount == 0) {
            echo "Agent not found";
            $sm = new Criteria();
            $sm->add(SmsTextPeer::ID, 3);
            $smstext = SmsTextPeer::doSelectOne($sm);
            $sms_text = $smstext->getMessageText();
            CARBORDFISH_SMS::Send($number, $sms_text);
            die;
        }

        $c = new Criteria();
        $c->add(AgentCompanyPeer::SMS_CODE, $agent_code);
        $agent = AgentCompanyPeer::doSelectOne($c);
        //geting product sms code
        $pc = new Criteria();
        $pc->add(ProductPeer::SMS_CODE, $product_code);
        $productCount = ProductPeer::doCount($pc);
        if ($productCount == 0) {
            echo 'Product not found';
            $sm = new Criteria();
            $sm->add(SmsTextPeer::ID, 4);
            $smstext = SmsTextPeer::doSelectOne($sm);
            $sms_text = $smstext->getMessageText();
            CARBORDFISH_SMS::Send($number, $sms_text);
            die;
        }


        $pc = new Criteria();
        $pc->add(ProductPeer::SMS_CODE, $product_code);
        $product = ProductPeer::doSelectOne($pc);
        $mobile = substr($number, 2);
        //This Function For Get the Enable Country Id =
        $calingcode = sfConfig::get('app_country_code');
        $customer = new Customer();
        $customer->setFirstName($mobile);
        $customer->setLastName($mobile);
        $customer->setMobileNumber($mobile);
        $customer->setPassword($mobile);
        $customer->setEmail($agent->getEmail());
        $customer->setReferrerId($agent->getId());
        $customer->setCountryId(1);
        $customer->setCity("");
        $customer->setAddress("");
        $customer->setTelecomOperatorId(1);
        $customer->setDeviceId(1474);
        $customer->setCustomerStatusId(1);
        $customer->setPlainText($mobile);
        $customer->setRegistrationTypeId(4);
        $customer->save();


        $order = new CustomerOrder();
        $order->setProductId($product->getId());
        $order->setCustomerId($customer->getId());
        $order->setExtraRefill($order->getProduct()->getInitialBalance());
        $order->setIsFirstOrder(1);
        $order->setOrderStatusId(1);
        $order->save();

        $this->customer = $customer;
        $transaction = new Transaction();
        $transaction->setAgentCompanyId($customer->getReferrerId());
        $transaction->setAmount($order->getProduct()->getPrice() + $order->getProduct()->getRegistrationFee() + ($order->getProduct()->getRegistrationFee() * sfConfig::get('app_vat_percentage')));
        $transaction->setDescription('Registration');
        $transaction->setOrderId($order->getId());
        $transaction->setCustomerId($customer->getId());
        $transaction->setTransactionStatusId(1);
        $transaction->save();
        TransactionPeer::AssignReceiptNumber($transaction);
        $customer_product = new CustomerProduct();
        $customer_product->setCustomer($order->getCustomer());
        $customer_product->setProduct($order->getProduct());
        $customer_product->save();

        $uc = new Criteria();
        $uc->add(UniqueIdsPeer::REGISTRATION_TYPE_ID, 2);
        $uc->addAnd(UniqueIdsPeer::STATUS, 0);
        $uc->addAnd(UniqueIdsPeer::UNIQUE_NUMBER, $uniqueid);
        $availableUniqueCount = UniqueIdsPeer::doCount($uc);
        $availableUniqueId = UniqueIdsPeer::doSelectOne($uc);

        if ($availableUniqueCount == 0) {
            echo $this->getContext()->getI18N()->__("Unique Ids are not avaialable.  send email to the support.");
            $sm = new Criteria();
            $sm->add(SmsTextPeer::ID, 6);
            $smstext = SmsTextPeer::doSelectOne($sm);
            $sms_text = $smstext->getMessageText();
            CARBORDFISH_SMS::Send($number, $sms_text);
            die;
        } else {
            $availableUniqueId->setStatus(1);
            $availableUniqueId->setAssignedAt(date('Y-m-d H:i:s'));
            $availableUniqueId->save();
        }
        $this->customer->setUniqueid(str_replace(' ', '', $uniqueid));
        $this->customer->save();


        $agentid = $agent->getId();
        $productid = $product->getId();
        $transactionid = $transaction->getId();

        $massage = commissionLib::registrationCommission($agentid, $productid, $transactionid);
        if (isset($massage) && $massage == "balance_error") {
            echo $this->getContext()->getI18N()->__('balance issue');
            $sm = new Criteria();
            $sm->add(SmsTextPeer::ID, 7);
            $smstext = SmsTextPeer::doSelectOne($sm);
            $sms_text = $smstext->getMessageText();
            CARBORDFISH_SMS::Send($number, $sms_text);
            $availableUniqueId->setStatus(0);
            $availableUniqueId->setAssignedAt(" ");
            $availableUniqueId->save();
            die;
        }

        $sm = new Criteria();
        $sm->add(SmsTextPeer::ID, 1);
        $smstext = SmsTextPeer::doSelectOne($sm);
        $sms_text = $smstext->getMessageText();
        CARBORDFISH_SMS::Send($number, $sms_text);

        $transaction->setTransactionStatusId(3);
        $transaction->save();
        TransactionPeer::AssignReceiptNumber($transaction);
        $order->setOrderStatusId(3);
        $order->save();


        $callbacklog = new CallbackLog();
        $callbacklog->setMobileNumber($calingcode . $this->customer->getMobileNumber());
        $callbacklog->setuniqueId($this->customer->getUniqueid());
        $callbacklog->setCheckStatus(3);
        $callbacklog->save();

        $customer->setCustomerStatusId(3);
        $customer->save();

        $telintaObj = new Telienta();
        $telintaObj->ResgiterCustomer($this->customer, $order->getExtraRefill());
        $telintaObj->createAAccount($calingcode . $this->customer->getMobileNumber(), $this->customer);

        emailLib::sendCustomerRegistrationViaAgentSMSEmail($this->customer, $order);
        return sfView::NONE;
    }

    public function executeSmsCode(sfWebRequest $request) {

        $c = new Criteria();
        $agents = AgentCompanyPeer::doSelect($c);

        $count = 1;
        foreach ($agents as $agent) {
            $cvr = $agent->getCvrNumber();
            if (strlen($cvr) == 4) {
                $agent->setSmsCode($cvr);
                $agent->save();
            } else {
                $cvr = substr($cvr, 0, 4);
                $agent->setSmsCode($cvr);
                $agent->save();
            }
            echo $agent->getCvrNumber();
            echo ' : ';
            echo $cvr;
            echo '<br/>';
            $count = $count + 1;
        }

        return sfView::NONE;
    }

    public function executeDeleteValues(sfWebRequest $request) {

        $c = new Criteria();
        $orders = CustomerOrderPeer::doSelect($c);

        foreach ($orders as $order) {
            $cr = new Criteria();
            $cr->add(CustomerPeer::ID, $order->getCustomerId());
            $customer = CustomerPeer::doSelectOne($cr);

            if (!$customer) {
                //$order->delete();
                echo $order->getCustomerId();
                echo "<br/>";
            }
        }

        echo "transactions";
        $ct = new Criteria();
        $transactions = TransactionPeer::doSelect($ct);

        foreach ($transactions as $transaction) {
            $cr = new Criteria();
            $cr->add(CustomerPeer::ID, $transaction->getCustomerId());
            $customer = CustomerPeer::doSelectOne($cr);

            if (!$customer) {
                //$transaction->delete();
                echo $transaction->getCustomerId();
                echo "<br/>";
            }
        }

        echo "customer products";
        $cp = new Criteria();
        $cps = CustomerProductPeer::doSelect($cp);

        foreach ($cps as $cp) {
            $cr = new Criteria();
            $cr->add(CustomerPeer::ID, $cp->getCustomerId());
            $customer = CustomerPeer::doSelectOne($cr);

            if (!$customer) {
                //$cp->delete();
                echo $cp->getCustomerId();
                echo "<br/>";
            }
        }

        return sfView::NONE;
    }

    public function executeRegistrationType(sfWebRequest $request) {

        $c = new Criteria();
        $customers = CustomerPeer::doSelect($c);

        foreach ($customers as $customer) {
            if ($customer->getReferrerId()) {
                if (!$customer->getRegistrationTypeId()) {
                    $customer->setRegistrationTypeId(2);
                    $customer->save();
                }
            } else {
                $customer->setRegistrationTypeId(1);
                $customer->save();
            }
        }
        return sfView::NONE;
    }

    public function executeGetBalanceAll() {

        $balance = 0;
        $total_unassigned = 0;
        $total_assigned = 0;

        $c = new Criteria();
        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, 3);
        $customers = CustomerPeer::doSelect($c);

        echo "Total customers: " . count($customers);
        foreach ($customers as $customer) {
            $balance = Fonet::getBalance($customer);
            if ($balance > 0) {
                echo "<br/>";
                echo "Registered: " . $customer->getMobileNumber() . ", Balance: " . $balance;
                $total_assigned++;
            } else {
                echo "<br/>";
                echo "Not Registered: " . $customer->getMobileNumber() . ", Balance: " . $balance;
                $total_unassigned++;
            }
        }

        echo "<br/>";
        echo "Total UnRegistered: " . $total_unassigned++;
        echo "<br/>";
        echo "Total Registered: " . $total_assigned++;
    }

    public function executeRescueRegister() {

        $balance = 0;
        $already_registered = 0;
        $newly_registered = 0;
        $not_registered = 0;

        $c = new Criteria();
        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, 3);
        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, 0, Criteria::GREATER_THAN);
        $customers = CustomerPeer::doSelect($c);

        echo "Total customers: " . count($customers);

        foreach ($customers as $customer) {

            $balance = Fonet::getBalance($customer);
            if ($balance > 0) {
                echo "<br/>";
                echo++$already_registered . ") Already Registered: " . $customer->getMobileNumber() . ", Balance: " . $balance;
                echo "<br/>";
            } else {
                echo "<br/>";
                echo++$not_registered . ") Not Registered: " . $customer->getMobileNumber() . ", Balance: " . $balance;


                $query_vars = array(
                    'Action' => 'Activate',
                    'ParentCustomID' => 1393238,
                    'AniNo' => $customer->getMobileNumber(),
                    'DdiNo' => 25998893,
                    'CustomID' => $customer->getFonetCustomerId()
                );

                $url = 'http://fax.fonet.dk/cgi-bin/ZeroCallV2Control.pl' . '?' . http_build_query($query_vars);
                $res = file_get_contents($url);
                echo "<br/>";
                echo 'Registered :' . $customer->getMobileNumber() . ", status: " . substr($res, 0, 2);
                echo++$newly_registered;
            }
        }
    }

    public function executeRescueDefaultBalance(sfWebRequest $request) {

        $balance = 0;
        $already_registered = 0;
        $newly_registered = 0;
        $not_registered = 0;

        $c = new Criteria();
        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, 3);
        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, 0, Criteria::GREATER_THAN);
        $customers = CustomerPeer::doSelect($c);

        echo "Total customers: " . count($customers);

        foreach ($customers as $customer) {

            $balance = Fonet::getBalance($customer);
            if ($balance > 0) {
                echo "<br/>";
                echo++$already_registered . ") Already Registered: " . $customer->getMobileNumber() . ", Balance: " . $balance;
                echo "<br/>";
            } else {
                $cp = new Criteria();
                $cp->add(CustomerProductPeer::PRODUCT_ID, 7, Criteria::NOT_EQUAL);
                $cp->add(CustomerProductPeer::CUSTOMER_ID, $customer->getId());
                $customer_product = CustomerProductPeer::doSelectOne($cp);

                if ($customer_product) {
                    $query_vars = array(
                        'Action' => 'Recharge',
                        'ParentCustomID' => 1393238,
                        'CustomID' => $customer->getFonetCustomerId(),
                        'ChargeValue' => 20 * 100
                    );

                    $url = 'http://fax.fonet.dk/cgi-bin/ZeroCallV2Control.pl' . '?' . http_build_query($query_vars);
                    $res = file_get_contents($url);
                    echo "<br/>";
                    echo++$balance_assigned . ')Recharged :' . $customer->getMobileNumber() . ", status: " . substr($res, 0, 2);
                    echo "<br/>";
                }
            }
        }
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

    public function executeSmsRegisterationwcb(sfWebrequest $request) {
        $urlval = "WCR-" . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->save();

        die;
        $number = $request->getParameter('from');
        $mobileNumber = substr($number, 2, strlen($number) - 2);
        if ($mobileNumber[0] != "0") {
            $mobileNumber = "0" . $mobileNumber;
        }
        $textParamter = $request->getParameter('text');
        $requestType = substr($textParamter, 0, 2);
        $requestType = strtolower($requestType);



        if ($requestType == "hc") {

            /* $dialerIdLenght = strlen($textParamter);
              $uniqueId = substr($textParamter, $dialerIdLenght - 7, $dialerIdLenght - 1);
              $mnc = new Criteria();
              $mnc->add(CustomerPeer::MOBILE_NUMBER, $mobileNumber);
              $mnc->add(CustomerPeer::CUSTOMER_STATUS_ID, 3);
              $cusCount = CustomerPeer::doCount($mnc);
              if ($cusCount < 1) {
              $uc = new Criteria();
              $uc->add(UniqueIdsPeer::UNIQUE_NUMBER, $uniqueId);
              $uc->addAnd(UniqueIdsPeer::STATUS, 0);
              $callbackq = UniqueIdsPeer::doCount($uc);
              if ($callbackq== 1) {
              $availableUniqueId = UniqueIdsPeer::doSelectOne($uc);
              $pc = new Criteria();
              $pc->add(ProductPeer::SMS_CODE, "50");
              $product = ProductPeer::doSelectOne($pc);
              $calingcode = sfConfig::get('app_country_code');
              $password = $this->randomNumbers(6);
              $customer = new Customer();
              $customer->setFirstName($mobileNumber);
              $customer->setLastName($mobileNumber);
              $customer->setMobileNumber($mobileNumber);
              $customer->setPassword($password);
              $customer->setEmail("retail@example.com");
              $customer->setCountryId(2);
              $customer->setCity("");
              $customer->setAddress("");
              $customer->setSimTypeId($availableUniqueId->getSimTypeId());
              $customer->setTelecomOperatorId(1);
              $customer->setDeviceId(1474);
              $customer->setUniqueId($uniqueId);
              $customer->setCustomerStatusId(3);
              $customer->setPlainText($password);
              $customer->setRegistrationTypeId(6);
              $customer->save();

              $order = new CustomerOrder();
              $order->setProductId($product->getId());
              $order->setCustomerId($customer->getId());
              $order->setExtraRefill($order->getProduct()->getInitialBalance());
              $order->setIsFirstOrder(1);
              $order->setOrderStatusId(3);
              $order->save();

              $transaction = new Transaction();
              $transaction->setAgentCompanyId($customer->getReferrerId());
              $transaction->setAmount($order->getProduct()->getPrice());
              $transactiondescription=  TransactionDescriptionPeer::retrieveByPK(8);
              $transaction->setTransactionTypeId($transactiondescription->getTransactionType());
              $transaction->setTransactionDescriptionId($transactiondescription->getId());
              $transaction->setDescription($transactiondescription->getTitle());
              $transaction->setOrderId($order->getId());
              $transaction->setCustomerId($customer->getId());
              $transaction->setTransactionStatusId(3);
              $transaction->save();

              $customer_product = new CustomerProduct();
              $customer_product->setCustomer($order->getCustomer());
              $customer_product->setProduct($order->getProduct());
              $customer_product->save();

              $callbacklog = new CallbackLog();
              $callbacklog->setMobileNumber($number);
              $callbacklog->setuniqueId($uniqueId);
              $callbacklog->setImei($splitedText[1]);
              $callbacklog->setImsi($splitedText[2]);
              $callbacklog->setCheckStatus(3);
              $callbacklog->save();
              $telintaObj = new Telienta();
              if ($telintaObj->ResgiterCustomer($customer, $order->getExtraRefill())) {
              $availableUniqueId->setAssignedAt(date("Y-m-d H:i:s"));
              $availableUniqueId->setStatus(1);
              $availableUniqueId->setRegistrationTypeId(4);
              $availableUniqueId->save();
              $telintaObj->createAAccount($number, $customer);
              $telintaObj->createCBAccount($number, $customer);
              }

              $sms = SmsTextPeer::retrieveByPK(10);
              $smsText = $sms->getMessageText();
              $smsText = str_replace("(balance)", $order->getExtraRefill(), $smsText);
              ROUTED_SMS::Send($number, $smsText);

              $sms = SmsTextPeer::retrieveByPK(12);
              $smsText = $sms->getMessageText();
              $smsText = str_replace("(username)", $mobileNumber, $smsText);
              $smsText = str_replace("(password)", $password, $smsText);
              ROUTED_SMS::Send($number, $smsText);
              emailLib::sendCustomerRegistrationViaRetail($customer, $order);
              die;
              }

              $smstext = SmsTextPeer::retrieveByPK(8);
              echo $smstext->getMessageText();
              ROUTED_SMS::Send($number, $smstext->getMessageText());
              $message="HC Registration Failed".$smstext->getMessageText()."<br>".$urlval;
              emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
              die;
              }/*
              /*
              $customer = CustomerPeer::doSelectOne($mnc);

              $callbackq = new Criteria();
              $callbackq->add(CallbackLogPeer::UNIQUEID, $uniqueId);
              $callbackq = CallbackLogPeer::doCount($callbackq);

              if ($callbackq < 1) {
              $smstext = SmsTextPeer::retrieveByPK(7);
              echo $smstext->getMessageText();
              ROUTED_SMS::Send($number, $smstext->getMessageText());
              $message=$smstext->getMessageText()."<br>".$urlval;
              emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
              die;
              }

              $callbacklog = new CallbackLog();
              $callbacklog->setMobileNumber($number);
              $callbacklog->setuniqueId($uniqueId);
              $callbacklog->save();

              $getvoipInfo = new Criteria();
              $getvoipInfo->add(SeVoipNumberPeer::CUSTOMER_ID, $customer->getId());
              $getvoipInfos = SeVoipNumberPeer::doSelectOne($getvoipInfo); //->getId();
              if (isset($getvoipInfos)) {
              $voipnumbers = $getvoipInfos->getNumber();
              $voipnumbers = substr($voipnumbers, 2);
              }

              $tc = new Criteria();
              $tc->add(TelintaAccountsPeer::ACCOUNT_TITLE, $voipnumbers);
              $tc->add(TelintaAccountsPeer::STATUS, 3);
              if (TelintaAccountsPeer::doCount($tc) > 0) {
              $telintaAccount = TelintaAccountsPeer::doSelectOne($tc);
              $telintaObj = new Telienta();
              $telintaObj->terminateAccount($telintaAccount);
              }
              $telintaObj = new Telienta();
              $telintaObj->createReseNumberAccount($voipnumbers, $customer, $number);

              $smstext = SmsTextPeer::retrieveByPK(2);
              ROUTED_SMS::Send($number, $smstext->getMessageText());
              die; */
            return sfView::NONE;
        } elseif ($requestType == "ic") {

            /* $dialerIdLenght = strlen($textParamter);
              $uniqueId = substr($textParamter, 3);
              echo "<br/>";
              echo $uniqueId."<hr/>";

              $callbackq = new Criteria();
              $callbackq->add(CallbackLogPeer::UNIQUEID, $uniqueId);
              $callbackq = CallbackLogPeer::doCount($callbackq);

              if ($callbackq < 1) {
              $smstext = SmsTextPeer::retrieveByPK(7);
              ROUTED_SMS::Send($number, $smstext->getMessageText());
              $message=$smstext->getMessageText()."<br>".$urlval;
              emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
              die;
              }

              $mnc = new Criteria();
              $mnc->add(CustomerPeer::UNIQUEID, $uniqueId);
              $mnc->add(CustomerPeer::CUSTOMER_STATUS_ID, 3);
              $cusCount = CustomerPeer::doCount($mnc);

              if ($cusCount < 1) {
              $smstext = SmsTextPeer::retrieveByPK(7);
              ROUTED_SMS::Send($number, $smstext->getMessageText());
              $message=$smstext->getMessageText()."<br>".$urlval;
              emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
              die;
              }
              $customer = CustomerPeer::doSelectOne($mnc);

              $callbacklog = new CallbackLog();
              $callbacklog->setMobileNumber($number);
              $callbacklog->setuniqueId($uniqueId);
              $callbacklog->setcallingCode(46);
              $callbacklog->save();
              $telintaObj = new Telienta();
              $telintaObj->createCBAccount($number, $customer,11648);  //11648 is Call back product for IC call

              $telintaGetBalance = $telintaObj->getBalance($customer);

              $getvoipInfo = new Criteria();
              $getvoipInfo->add(SeVoipNumberPeer::CUSTOMER_ID, $customer->getId());
              $getvoipInfos = SeVoipNumberPeer::doSelectOne($getvoipInfo); //->getId();
              if (isset($getvoipInfos)) {
              $voipnumbers = $getvoipInfos->getNumber();
              $voipnumbers = substr($voipnumbers, 2);

              $tc = new Criteria();
              $tc->add(TelintaAccountsPeer::ACCOUNT_TITLE, $voipnumbers);
              $tc->add(TelintaAccountsPeer::STATUS, 3);
              if (TelintaAccountsPeer::doCount($tc) > 0) {
              $telintaAccount = TelintaAccountsPeer::doSelectOne($tc);
              $telintaObj = new Telienta();
              $telintaObj->terminateAccount($telintaAccount);
              }
              $telintaObj = new Telienta();
              $telintaObj->createReseNumberAccount($voipnumbers, $customer, $number);
              }

              $smstext = SmsTextPeer::retrieveByPK(3);
              ROUTED_SMS::Send($number, $smstext->getMessageText());
              die; */
            return sfView::NONE;
        } else {

            $text = $this->hextostr($request->getParameter('text'));
            $splitedText = explode(";", $text);
            if ($splitedText[3] != sfConfig::get("app_dialer_pin") && $splitedText[3] != "9998888999" && $splitedText[4] != sfConfig::get("app_dialer_pin") && $splitedText[4] != "9998888999") {
                echo "Invalid Request Dialer Pin<br/>";
                $sms = SmsTextPeer::retrieveByPK(8);
                ROUTED_SMS::Send($number, $sms->getMessageText());
                $message = $sms->getMessageText() . "Invalid Request due to dialer Pin:" . $splitedText[3] . "<br>Mobile Number=" . $number . "<br>Text=" . $text;
                emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
                die;
            }
            $mobileNumber = substr($number, 2, strlen($number) - 2);

            echo "<hr/>";
            echo count($splitedText);
            echo "<hr/>";
            if (count($splitedText) == 4) {
                $dialerIdLenght = strlen($splitedText[0]);
                $uniqueId = substr($splitedText[0], $dialerIdLenght - 7, $dialerIdLenght - 1);
                echo "uniqueid:" . $uniqueId;
            } else {
                echo strtolower(substr($splitedText[0], 0, 2));
                echo "<br/>";
                echo $splitedText[0];
                if (strtolower(substr($splitedText[0], 0, 2)) == "re" && strlen($splitedText[0]) == 12) {
                    $dialerIdLenght = strlen($splitedText[0]);
                    echo $location = 4;
                    echo "<br/>";
                    $uniqueId = substr($splitedText[0], $dialerIdLenght - 7, $dialerIdLenght - 1);
                    echo "uniqueid:" . $uniqueId;
                } else {
                    $dialerIdLenght = strlen($splitedText[1]);
                    echo "DialerLenght:" . $dialerIdLenght . "<br/>";
                    $uniqueId = substr($splitedText[1], $dialerIdLenght - 7, $dialerIdLenght - 1);
                    echo $location = 5;
                    echo "<br/>";
                    echo "uniqueid:" . $uniqueId;
                }
            }

            $c = new Criteria();
            $c->add(CustomerPeer::MOBILE_NUMBER, $mobileNumber);
            $c->addAnd(CustomerPeer::CUSTOMER_STATUS_ID, 3);
            $c->addAnd(CustomerPeer::UNIQUEID, $uniqueId);


            if ($dialerIdLenght == 10 && count($splitedText) == 4) {/*
              echo "Register Customer<br/>";
              //Registration Call, Register Customer In this block
              $uc = new Criteria();
              $uc->addAnd(UniqueIdsPeer::STATUS, 0);
              $uc->addAnd(UniqueIdsPeer::UNIQUE_NUMBER, $uniqueId);

              $ucc = new Criteria();
              $ucc->addAnd(UniqueIdsPeer::UNIQUE_NUMBER, $uniqueId);

              if (UniqueIdsPeer::doCount($ucc) == 0) {
              echo "Unique Id Not Found";
              $sms = SmsTextPeer::retrieveByPK(8);
              ROUTED_SMS::Send($number, $sms->getMessageText());
              $message=$sms->getMessageText()."<br>Unique Id Not Found:".$uniqueId."<br/>Mobile Number=".$number."<br>Text=".$text;
              emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
              die;
              }

              $cc = new Criteria();
              $cc->add(CustomerPeer::MOBILE_NUMBER, $mobileNumber);
              $cc->addAnd(CustomerPeer::CUSTOMER_STATUS_ID, 3);

              if (CustomerPeer::doCount($cc) > 0) {
              echo "Already Registerd";
              //$sms = SmsTextPeer::retrieveByPK(10);
              //ROUTED_SMS::Send($number, $sms->getMessageText());
              die;
              }

              if (UniqueIdsPeer::doCount($uc) > 0) {
              $availableUniqueId = UniqueIdsPeer::doSelectOne($uc);

              $pc = new Criteria();
              $pc->add(ProductPeer::SMS_CODE, "50");
              $product = ProductPeer::doSelectOne($pc);

              $calingcode = sfConfig::get('app_country_code');
              $password = $this->randomNumbers(6);
              $customer = new Customer();
              $customer->setFirstName($mobileNumber);
              $customer->setLastName($mobileNumber);
              $customer->setMobileNumber($mobileNumber);
              $customer->setNiePassportNumber($mobileNumber);

              $customer->setPassword($password);
              $customer->setSimTypeId($availableUniqueId->getSimTypeId());
              $customer->setEmail("retail@example.com");
              $customer->setCountryId(1);
              $customer->setCity("");
              $customer->setAddress($mobileNumber);
              $customer->setTelecomOperatorId(1);
              $customer->setDeviceId(1474);
              $customer->setUniqueId($uniqueId);
              $customer->setCustomerStatusId(3);
              $customer->setPlainText($password);
              $customer->setRegistrationTypeId(6);
              $customer->save();

              $order = new CustomerOrder();
              $order->setProductId($product->getId());
              $order->setCustomerId($customer->getId());
              $order->setExtraRefill($order->getProduct()->getInitialBalance());
              $order->setIsFirstOrder(1);
              $order->setOrderStatusId(3);
              $order->save();

              $transaction = new Transaction();
              $transaction->setAgentCompanyId($customer->getReferrerId());
              $transaction->setAmount($order->getProduct()->getPrice());
              $transactiondescription =  TransactionDescriptionPeer::retrieveByPK(8);
              $transaction->setTransactionTypeId($transactiondescription->getTransactionType());
              $transaction->setTransactionDescriptionId($transactiondescription->getId());
              $transaction->setDescription($transactiondescription->getTitle());
              $transaction->setOrderId($order->getId());
              $transaction->setCustomerId($customer->getId());
              $transaction->setTransactionStatusId(3);
              $transaction->save();

              $customer_product = new CustomerProduct();
              $customer_product->setCustomer($order->getCustomer());
              $customer_product->setProduct($order->getProduct());
              $customer_product->save();

              $callbacklog = new CallbackLog();
              $callbacklog->setMobileNumber($number);
              $callbacklog->setuniqueId($uniqueId);
              $callbacklog->setImei($splitedText[1]);
              $callbacklog->setImsi($splitedText[2]);
              $callbacklog->setCheckStatus(3);
              $callbacklog->save();
              $telintaObj = new Telienta();
              if ($telintaObj->ResgiterCustomer($customer, $order->getExtraRefill())) {
              $availableUniqueId->setAssignedAt(date("Y-m-d H:i:s"));
              $availableUniqueId->setStatus(1);
              $availableUniqueId->setRegistrationTypeId(4);
              $availableUniqueId->save();
              $telintaObj->createAAccount($number, $customer);
              $telintaObj->createCBAccount($number, $customer);
              }

              $sms = SmsTextPeer::retrieveByPK(10);
              $smsText = $sms->getMessageText();
              $smsText = str_replace("(balance)", $order->getExtraRefill(), $smsText);
              ROUTED_SMS::Send($number, $smsText);

              $sms = SmsTextPeer::retrieveByPK(12);
              $smsText = $sms->getMessageText();
              $smsText = str_replace("(username)", $mobileNumber, $smsText);
              $smsText = str_replace("(password)", $password, $smsText);
              ROUTED_SMS::Send($number, $smsText);
              emailLib::sendCustomerRegistrationViaRetail($customer, $order);
              } else {
              $sms = SmsTextPeer::retrieveByPK(7);
              $smsText = $sms->getMessageText();
              ROUTED_SMS::Send($number, $smsText);
              $message=$sms->getMessageText()."<br>Mobile Number=".$number."<br>Text=".$text;
              emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
              die;
              }

              //End of Registration.
             */
            } else {
                $c = new Criteria();
                $c->add(CustomerPeer::MOBILE_NUMBER, $mobileNumber);
                $c->addAnd(CustomerPeer::CUSTOMER_STATUS_ID, 3);
                $c->addAnd(CustomerPeer::UNIQUEID, $uniqueId);

                if (CustomerPeer::doCount($c) > 0) {

                    $command = substr($splitedText[0], 0, 2);


                    $command = strtolower($command);
                    echo "<hr/>";
                    echo $command;
                    echo "<hr/>";
                    $customer = CustomerPeer::doSelectOne($c);
                    if ($command == "cb") {

                        echo "Check Balance Request<br/>";
                        $telintaObj = new Telienta();
                        $balance = $telintaObj->getBalance($customer);
                        $sms = SmsTextPeer::retrieveByPK(6);
                        $smsText = $sms->getMessageText();
                        $smsText = str_replace("(balance)", $balance, $smsText);
                        $number;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                        $c = new Criteria();
                        $c->add(SmsLogPeer::MOBILE_NUMBER, $number);
                        $c->addAnd(SmsLogPeer::SMS_TYPE, 2);
                        $c->addDescendingOrderByColumn(SmsLogPeer::CREATED_AT);
                        $value = SmsLogPeer::doCount($c);

                        if ($value > 0) {
                            $smsRow = SmsLogPeer::doSelectOne($c);
                            $createdAtValue = $smsRow->getCreatedAt();
                            echo $date1 = $createdAtValue;
                            $asd = 0;
                            $d1 = $date1;
                            $d2 = date("Y-m-d h:m:s");
                            $asd = ((strtotime($d2) - strtotime($d1)) / 3600);
                            $asd = intval($asd);


                            if ($asd > 3) {
                                ROUTED_SMS::Send($number, $smsText, null, 2);
                                die;
                            }
                        } else {
                            ROUTED_SMS::Send($number, $smsText, null, 2);
                            die;
                        }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    } elseif ($command == "re") {/*
                      echo "Recharge Request<br/>";
                      $cc = new Criteria();

                      if(count($splitedText)==5){
                      $cardNumber= $splitedText[4];
                      }else{
                      $cardNumber= $splitedText[$location];
                      }

                      $cc->add(CardNumbersPeer::CARD_NUMBER,"00880".$cardNumber);
                      $cc->addAnd(CardNumbersPeer::STATUS, 0);
                      if (CardNumbersPeer::doCount($cc) == 1) {
                      $scratchCard = CardNumbersPeer::doSelectOne($cc);
                      //new order
                      $order = new CustomerOrder();
                      $customer_products = $customer->getProducts();
                      $order->setProduct($customer_products[0]);
                      $order->setCustomer($customer);
                      $order->setQuantity(1);
                      $order->setExtraRefill($scratchCard->getCardPrice());
                      $order->save();

                      //new transaction
                      $transaction = new Transaction();
                      $transaction->setAmount($scratchCard->getCardPrice());
                      $transactiondescription =  TransactionDescriptionPeer::retrieveByPK(8);
                      $transaction->setTransactionTypeId($transactiondescription->getTransactionType());
                      $transaction->setTransactionDescriptionId($transactiondescription->getId());
                      $transaction->setDescription($transactiondescription->getTitle());
                      $transaction->setOrderId($order->getId());
                      $transaction->setCustomerId($order->getCustomerId());
                      $transaction->save();
                      $telintaObj = new Telienta();
                      if ($telintaObj->recharge($customer, $scratchCard->getCardPrice(), $transactiondescription->getTitle())) {
                      $scratchCard->setStatus(1);
                      $scratchCard->setUsedAt(date("Y-m-d H:i:s"));
                      $scratchCard->setCustomerId($customer->getId());
                      $scratchCard->save();
                      $order->setOrderStatusId(3);
                      $order->save();
                      $transaction->setTransactionStatusId(3);
                      $transaction->save();

                      // Send Customer Balance SMS after succesful recharge
                      $balance = $telintaObj->getBalance($customer);
                      $sms = SmsTextPeer::retrieveByPK(6);
                      $smsText = $sms->getMessageText();
                      $smsText = str_replace("(balance)", $balance, $smsText);
                      ROUTED_SMS::Send($number, $smsText);
                      // Send email to Support after Recharge
                      emailLib::sendRetailRefillEmail($customer, $order);
                      } else {
                      echo "Unable to charge";
                      $sms = SmsTextPeer::retrieveByPK(8);
                      ROUTED_SMS::Send($number, $sms->getMessageText());
                      $message=$sms->getMessageText()."<br>Unable to charge due to telinta issue Mobile Number=".$number."<br>Text=".$text;
                      emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
                      }
                      } else {
                      echo "CARD ALREADY USED<br/>";
                      $sms = SmsTextPeer::retrieveByPK(7);
                      ROUTED_SMS::Send($number, $sms->getMessageText());
                      $message=$sms->getMessageText()."<br>CARD:".$cardNumber."  ALREADY USED<br/>Mobile Number=".$number."<br>Text=".$text;
                      emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
                      }
                      die; */
                    }
                } else {
                    echo "Invalid Command 1";
                    $sms = SmsTextPeer::retrieveByPK(8);
                    ROUTED_SMS::Send($number, $sms->getMessageText());
                    $message = $sms->getMessageText() . "<br>Invalid Command<br/>Mobile Number=" . $number . "<br>Text=" . $text;
                    emailLib::sendErrorInAutoReg("Auto Registration Error:", $message);
                    die;
                }
            }
        }
        return sfView::NONE;
    }

    public function executeAutorefil(sfWebRequest $request) {
        //call Culture Method For Get Current Set Culture - Against Feature# 6.1 --- 02/28/11
        //echo "get customers to refill";
        $c = new Criteria();

        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, 3);
        $c->addAnd(CustomerPeer::AUTO_REFILL_AMOUNT, 0, Criteria::NOT_EQUAL);
        //$c->addAnd(CustomerPeer::UNIQUEID, 99999, Criteria::GREATER_EQUAL);
        $c->addAnd(CustomerPeer::TICKETVAL, null, Criteria::ISNOTNULL);
        $c->addDescendingOrderByColumn(CustomerPeer::CREATED_AT);
        //$c1 = $c->getNewCriterion(CustomerPeer::LAST_AUTO_REFILL, 'TIMESTAMPDIFF(MINUTE, LAST_AUTO_REFILL, NOW()) > 1' , Criteria::CUSTOM);
        $c1 = $c->getNewCriterion(CustomerPeer::ID, null, Criteria::ISNOTNULL); //just accomodate missing disabled $c1
        $c2 = $c->getNewCriterion(CustomerPeer::LAST_AUTO_REFILL, null, Criteria::ISNULL);

        //$c1->addOr($c2);
        //$c->add($c1);

        $vt = 0;

        $customer = new Customer();

        $vt = CustomerPeer::doCount($c);


        if ($vt > 0) {

            $i = 0;
            $customers = CustomerPeer::doSelect($c);

            foreach ($customers as $customer) {

                //echo "UniqueID:";
                $uniqueId = $customer->getUniqueid();
                if ((int) $uniqueId > 200000) {
                    $Tes = ForumTel::getBalanceForumtel($customer->getId());

                    $customer_balance = $Tes;
                } else {
                    //echo "This is for Retrieve balance From Telinta"."<br/>";
                    $telintaGetBalance = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=getbalance&name=' . $uniqueId . '&type=customer');
                    sleep(0.25);
                    if (!$telintaGetBalance) {
                        //emailLib::sendErrorInTelinta("Error in Balance Fetching", "We have faced an issue in autorefill on telinta. this is the error on the following url https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=getbalance&name=" . $uniqueId . "&type=customer. <br/> Please Investigate.");
                        continue;
                    }
                    parse_str($telintaGetBalance);
                    if (isset($success) && $success != "OK") {
                        emailLib::sendErrorInTelinta("Error in Balance Status", "We have faced an issue in autorefill on telinta. after fetching data from the following url https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=getbalance&name=" . $uniqueId . "&type=customer. we are unable to find the status in the string <br/> Please Investigate.");
                        continue;
                    }
                    $customer_balance = $Balance * (-1);
                }
                echo "<br/>";
                // $customer_balance = Fonet::getBalance($customer);
                //if customer balance is less than 10
                if ($customer_balance != null && (float) $customer_balance <= (float) $customer->getAutoRefillMinBalance()) {


                    echo $customer_balance;
                    $customer_id = $customer->getId();

                    $this->customer = CustomerPeer::retrieveByPK($customer_id);

                    $this->order = new CustomerOrder();

                    $customer_products = $this->customer->getProducts();

                    $this->order->setProduct($customer_products[0]);
                    $this->order->setCustomer($this->customer);
                    $this->order->setQuantity(1);
                    $this->order->setExtraRefill($customer->getAutoRefillAmount());
                    $this->order->save();


                    $transaction = new Transaction();

                    $transaction->setAmount($this->order->getExtraRefill());
                    $transaction->setDescription($this->getContext()->getI18N()->__('Auto Refill'));
                    $transaction->setOrderId($this->order->getId());
                    $transaction->setCustomerId($this->order->getCustomerId());


                    $transaction->save();



                    $order_id = $this->order->getId();
                    $total = 100 * $this->order->getExtraRefill();
                    $tickvalue = $this->customer->getTicketval();
                    $form = new Curl_HTTP_Client();


                    $form->set_user_agent("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
                    $form->set_referrer(sfConfig::get('app_customer_url'));
                    $post_data = array(
                        'merchant' => '90049676',
                        'amount' => $total,
                        'currency' => '752',
                        'orderid' => $order_id,
                        'textreply' => true,
                        'account' => 'YTIP',
                        'status' => '',
                        'ticket' => $tickvalue,
                        'lang' => 'sv',
                        'HTTP_COOKIE' => getenv("HTTP_COOKIE"),
                        'cancelurl' => sfConfig::get('app_customer_url'),
                        'callbackurl' => sfConfig::get('app_customer_url') . "pScripts/autorefilconfirmation?accept=yes&subscriptionid=&orderid=$order_id&amount=$total",
                        'accepturl' => sfConfig::get('app_customer_url')
                    );
//var_dump($post_data);
//echo "<br/>Baran<br/>";

                    $html_data = $form->send_post_data("https://payment.architrade.com/cgi-ssl/ticket_auth.cgi", $post_data);
//echo $html_data;
//echo "<br/>";
                    // die("khan");
                }

                sleep(0.5);
            }
        }

        return sfView::NONE;
        // $this->setLayout(false);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////

    public function executeAutorefilconfirmation(sfWebRequest $request) {

        //call Culture Method For Get Current Set Culture - Against Feature# 6.1 --- 02/28/11
        changeLanguageCulture::languageCulture($request, $this);

        $urlval = 0;
        $urlval = "autorefil-" . $request->getParameter('transact');

        $email2 = new DibsCall();
        $email2->setCallurl($urlval);

        $email2->save();
        $urlval = $request->getParameter('transact');
        if (isset($urlval) && $urlval > 0) {
            $order_id = $request->getParameter("orderid");

            $this->forward404Unless($order_id || $order_amount);

            $order = CustomerOrderPeer::retrieveByPK($order_id);

            $order_amount = ((double) $request->getParameter('amount')) / 100;

            $this->forward404Unless($order);

            $c = new Criteria;
            $c->add(TransactionPeer::ORDER_ID, $order_id);

            $transaction = TransactionPeer::doSelectOne($c);

            //echo var_dump($transaction);

            $order->setOrderStatusId(sfConfig::get('app_status_completed', 3)); //completed
            //$order->getCustomer()->setCustomerStatusId(sfConfig::get('app_status_completed', 3)); //completed
            $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3)); //completed




            if ($transaction->getAmount() > $order_amount) {
                //error
                $order->setOrderStatusId(sfConfig::get('app_status_error', 5)); //error in amount
                $transaction->setTransactionStatusId(sfConfig::get('app_status_error', 5)); //error in amount
                //$order->getCustomer()->setCustomerStatusId(sfConfig::get('app_status_completed', 5)); //error in amount
            } else if ($transaction->getAmount() < $order_amount) {
                //$extra_refill_amount = $order_amount;
                $order->setExtraRefill($order_amount);
                $transaction->setAmount($order_amount);
            }
            //set active agent_package in case customer was registerred by an affiliate
            if ($order->getCustomer()->getAgentCompany()) {
                $order->setAgentCommissionPackageId($order->getCustomer()->getAgentCompany()->getAgentCommissionPackageId());
            }

            //set subscription id in case 'use current c.c for future auto refills' is set to 1
            //set auto_refill amount

            $order->save();
            $transaction->save();
            TransactionPeer::AssignReceiptNumber($transaction);
            $this->customer = $order->getCustomer();
            $c = new Criteria;
            $c->add(CustomerPeer::ID, $order->getCustomerId());
            $customer = CustomerPeer::doSelectOne($c);

            $customer->setLastAutoRefill(date('Y-m-d H:i:s'));
            $customer->save();
            echo "ag" . $agentid = $customer->getReferrerId();
            echo "prid" . $productid = $order->getProductId();
            echo "trid" . $transactionid = $transaction->getId();
            if (isset($agentid) && $agentid != "") {
                echo "getagentid";
                commissionLib::refilCustomer($agentid, $productid, $transactionid);
            }
            //TODO ask if recharge to be done is same as the transaction amount
            //die;
            //  Fonet::recharge($this->customer, $transaction->getAmount());
            $getFirstnumberofMobile = substr($this->customer->getMobileNumber(), 0, 1);     // bcdef
            if ($getFirstnumberofMobile == 0) {
                $TelintaMobile = substr($this->customer->getMobileNumber(), 1);
                $TelintaMobile = sfConfig::get('app_country_code') . $TelintaMobile;
            } else {
                $TelintaMobile = sfConfig::get('app_country_code') . $this->customer->getMobileNumber();
            }
            //$TelintaMobile = sfConfig::get('app_country_code').$this->customer->getMobileNumber();
            $emailId = $this->customer->getEmail();
            $uniqueId = $this->customer->getUniqueid();
            $OpeningBalance = $transaction->getAmount();
            //This is for Recharge the Customer
            if ((int) $uniqueId > 200000) {
                $cuserid = $this->customer->getId();
                $amt = $OpeningBalance;
                $amt = CurrencyConverter::convertSekToUsd($amt);
                $Test = ForumTel::rechargeForumtel($cuserid, $amt);
            } else {


                $MinuesOpeningBalance = $OpeningBalance * 3;
                $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name=' . $uniqueId . '&amount=' . $OpeningBalance . '&type=customer');
            }
            //This is for Recharge the Account
            //this condition for if follow me is Active
            $getvoipInfo = new Criteria();
            $getvoipInfo->add(SeVoipNumberPeer::CUSTOMER_ID, $this->customer->getMobileNumber());
            $getvoipInfos = SeVoipNumberPeer::doSelectOne($getvoipInfo); //->getId();
            if (isset($getvoipInfos)) {
                $voipnumbers = $getvoipInfos->getNumber();
                $voip_customer = $getvoipInfos->getCustomerId();
                //  $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name='.$voipnumbers.'&amount='.$OpeningBalance.'&type=account');
            } else {
                //  $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name='.$uniqueId.'&amount='.$OpeningBalance.'&type=account');
            }
            // $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name=a'.$TelintaMobile.'&amount='.$OpeningBalance.'&type=account');
            // $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?action=recharge&name=cb'.$TelintaMobile.'&amount='.$OpeningBalance.'&type=account');

            $MinuesOpeningBalance = $OpeningBalance * 3;
            //type=<account_customer>&action=manual_charge&name=<name>&amount=<amount>
            //This is for Recharge the Customer
            // $telintaAddAccountCB = file_get_contents('https://mybilling.telinta.com/htdocs/zapna/zapna.pl?type=customer&action=manual_charge&name='.$uniqueId.'&amount='.$MinuesOpeningBalance);
//echo 'NOOO';
// Update cloud 9
            //c9Wrapper::equateBalance($this->customer);
//echo 'Comeing';
            //set vat
            $vat = 0;
            $subject = $this->getContext()->getI18N()->__('Payment Confirmation');
            $sender_email = sfConfig::get('app_email_sender_email', 'support@landncall.com');
            $sender_name = sfConfig::get('app_email_sender_name', 'LandNCall AB support');

            $recepient_email = trim($this->customer->getEmail());
            $recepient_name = sprintf('%s %s', $this->customer->getFirstName(), $this->customer->getLastName());
            $referrer_id = trim($this->customer->getReferrerId());
            if ($referrer_id):
                $c = new Criteria();
                $c->add(AgentCompanyPeer::ID, $referrer_id);

                $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
                $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
            endif;

            //send email
            $message_body = $this->getPartial('pScripts/order_receipt', array(
                'customer' => $this->customer,
                'order' => $order,
                'transaction' => $transaction,
                'vat' => $vat,
                'wrap' => false
            ));


            $this->setPreferredCulture($this->customer);

            emailLib::sendCustomerRefillEmail($this->customer, $order, $transaction);
            $this->updatePreferredCulture();
        }
    }

    public function executeUsageAlert(sfWebRequest $request) {
        //call Culture Method For Get Current Set Culture - Against Feature# 6.1 --- 02/28/11
        changeLanguageCulture::languageCulture($request, $this);
        //-----------------------

        $CallCode = sfConfig::get('app_country_code');
        $countryId = "1";


        $usagealerts = new Criteria();
        $usagealerts->add(UsageAlertPeer::COUNTRY, $countryId);
        $usageAlerts = UsageAlertPeer::doSelect($usagealerts);
        $c = new Criteria();
        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, 3);
        $c->addAnd(CustomerPeer::COUNTRY_ID, $countryId);
        $customers = CustomerPeer::doSelect($c);
        $telintaObj = new Telienta();
        foreach ($customers as $customer) {
            $retries = 0;
            $maxRetries = 5;
            do {

                $customer_balance = $telintaObj->getBalance($customer);
                $retries++;
                echo $customer->getId() . ":" . $customer_balance . ":" . $retries . "<br/>";
            } while (!$customer_balance && $retries <= $maxRetries);

            if ($retries == ++$maxRetries) {
                continue;
            }

            $customer_balance = (double) $customer_balance;

            $actual_balance = $customer_balance;
            if ($customer_balance < 1) {
                $customer_balance = 0;
            }
            foreach ($usageAlerts as $usageAlert) {
                //echo "<hr/>".$usageAlert->getId()."<hr/>";
                if ($customer_balance >= $usageAlert->getAlertAmountMin() && $customer_balance < $usageAlert->getAlertAmountMax()) {

                    $sender = new Criteria();
                    $sender->add(UsageAlertSenderPeer::ID, $usageAlert->getSenderName());
                    $senders = UsageAlertSenderPeer::doSelectOne($sender);
                    echo $senderName = $senders->getName();
                    echo "<br />";
                    echo $usageAlert->getId();

                    $regType = RegistrationTypePeer::retrieveByPK($customer->getRegistrationTypeId()); // && $customer->getFonetCustomerId()!=''
                    $referer = $customer->getReferrerId();
                    if (isset($referer) && $referer > 0) {
                        $Cname = new Criteria();
                        $Cname->add(AgentCompanyPeer::ID, $referer);
                        $Companies = AgentCompanyPeer::doSelectOne($Cname);
                        $comName = $Companies->getName();
                    } else {
                        $comName = "";
                    }
                    $Prod = new Criteria();
                    $Prod->addJoin(ProductPeer::ID, CustomerProductPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
                    $Prod->add(CustomerProductPeer::CUSTOMER_ID, $customer->getId());
                    $Product = ProductPeer::doSelectOne($Prod);

                    $cSMSent = new Criteria();
                    $cSMSent->add(SmsAlertSentPeer::USAGE_ALERT_STATUS_ID, $usageAlert->getId());
                    $cSMSent->addAnd(SmsAlertSentPeer::CUSTOMER_ID, $customer->getId());
                    $cSMSentCount = SmsAlertSentPeer::doCount($cSMSent);

                    if ($usageAlert->getSmsActive() && $cSMSentCount == 0) {
                        echo "Sms Alert Sent:";
                        $msgSent = new SmsAlertSent();
                        $msgSent->setCustomerId($customer->getId());
                        $msgSent->setCustomerName($customer->getFirstName());
                        $msgSent->setCustomerProduct($Product->getName());
                        $msgSent->setRegistrationType($regType->getDescription());
                        $msgSent->setAgentName($comName);
                        $msgSent->setCustomerEmail($customer->getEmail());
                        $msgSent->setMobileNumber($customer->getMobileNumber());
                        $msgSent->setUsageAlertStatusId($usageAlert->getId());
                        $msgSent->setAlertActivated($customer->getUsageAlertSMS());
                        //$msgSent->setFonetCustomerId($customer->getFonetCustomerId());
                        $msgSent->setMessageDescerption("Current Balance: " . $actual_balance);
                        //$msgSent->save();
                        /**
                         * SMS Sending Code
                         * */
                        if ($customer->getUsageAlertSMS()) {
                            echo "SMS Active<br/>";
                            $customerMobileNumber = $CallCode . $customer->getMobileNumber();
                            //die($customerMobileNumber);
                            //    $customerMobileNumber = "923334414765";
                            $sms_text = $usageAlert->getSmsAlertMessage();
                            $this->setPreferredCulture($customer);
                            //$sms_text = $this->getContext()->getI18N()->__("Sms Alert Sent");
                            $response = ROUTED_SMS::Send($customerMobileNumber, $sms_text, $senderName);
                            $this->updatePreferredCulture();
                            if ($response) {
                                $msgSent->setAlertSent(1);
                            }
                        }
                        $msgSent->save();
                    }

                    $cEmailSent = new Criteria();
                    $cEmailSent->add(EmailAlertSentPeer::USAGE_ALERT_STATUS_ID, $usageAlert->getId());
                    $cEmailSent->addAnd(EmailAlertSentPeer::CUSTOMER_ID, $customer->getId());
                    $cEmailSentCount = EmailAlertSentPeer::doCount($cEmailSent);

                    if ($usageAlert->getEmailActive() && $cEmailSentCount == 0) {
                        echo "Email Alert Sent:";
                        $msgSentE = new EmailAlertSent();
                        $msgSentE->setCustomerId($customer->getId());
                        $msgSentE->setCustomerName($customer->getFirstName());
                        $msgSentE->setCustomerProduct($Product->getName());
                        $msgSentE->setRegistrationType($regType->getDescription());
                        $msgSentE->setAgentName($comName);
                        $msgSentE->setCustomerEmail($customer->getEmail());
                        $msgSentE->setMobileNumber($customer->getMobileNumber());
                        $msgSentE->setUsageAlertStatusId($usageAlert->getId());
                        $msgSentE->setAlertActivated($customer->getUsageAlertEmail());
                        //$msgSentE->setFonetCustomerId($customer->getFonetCustomerId());
                        $msgSentE->setMessageDescerption("Current Balance: " . $actual_balance);
                        //$msgSentE->save();

                        if ($customer->getUsageAlertEmail()) {
                            echo "Email Active<br/>";
                            $message = $usageAlert->getEmailAlertMessage();
                            $this->setPreferredCulture($customer);
                            emailLib::sendCustomerBalanceEmail($customer, $message);
                            $this->updatePreferredCulture();
                            $msgSentE->setAlertSent(1);
                        }
                        $msgSentE->save();
                    }
                }
            }
        }

        return sfView::NONE;
    }

    public function executeAbcTest(sfWebRequest $request) {
        $Parameters = "testtsts" . $request->getURI();

        $email2 = new DibsCall();
        $email2->setCallurl($Parameters);

        $email2->save();
        return sfView::NONE;
    }

    public function executeAgentRefillThankyou(sfWebRequest $request) {

        $Parameters = $request->getURI();

        $email2 = new DibsCall();
        $email2->setCallurl($Parameters);
        $email2->save();

        //$order_id = $request->getParameter('orderid');
        //$amount = $request->getParameter('amount');

        $callbackparameters = $request->getParameter("p");
        $params = explode("-", $callbackparameters);

        $order_id = $params[0];
        $order_amount = $params[1];

        if ($order_id) {
            $c = new Criteria();
            $c->add(AgentOrderPeer::AGENT_ORDER_ID, $order_id);
            $c->add(AgentOrderPeer::STATUS, 1);
            $agent_order = AgentOrderPeer::doSelectOne($c);

            // $agent_order->setAmount($amount);
            $agent_order->setStatus(3);
            $agent_order->save();
            TransactionPeer::AssignAgentReceiptNumber($agent_order);
            $agent = AgentCompanyPeer::retrieveByPK($agent_order->getAgentCompanyId());
            $agent->setBalance($agent->getBalance() + ($agent_order->getAmount()));
            $agent->save();
            $this->agent = $agent;

            $conn = Propel::getConnection();
            $query = 'SELECT sum(balance) as totlabalance from agent_company where reseller_id=' . $agent->getResellerId();
            $statement = $conn->prepare($query);
            $statement->execute();
            $rowObj = $statement->fetch(PDO::FETCH_OBJ);
            $rsbalance = $rowObj->totlabalance;
            $agent_order->setResellerId($agent->getResellerId());
            $agent_order->save();
            $reseller = ResellerPeer::retrieveByPK($agent->getResellerId());
            $rsavbalance = $rsbalance - ($reseller->getCreditLimit());
            $amount = $agent_order->getAmount();
            $remainingbalance = $agent->getBalance();
            $aph = new AgentPaymentHistory();
            $aph->setAgentId($agent_order->getAgentCompanyId());
            $aph->setExpeneseType(3);
            $aph->setAmount($agent_order->getAmount());
            $aph->setRemainingBalance($remainingbalance);

            $aph->setResellerId($reseller->getId());
            $aph->setResellerCreditLimit($reseller->getCreditLimit());
            $aph->setResellerAvailableBalance($rsavbalance);
            $aph->setResellerActualBalance($rsbalance);

            $aph->save();

            emailLib::sendAgentRefilEmail($this->agent, $agent_order);
        }

        return sfView::NONE;
    }

    public function executeCalbackrefill(sfWebRequest $request) {

        $Parameters = $request->getURI();

        $email2 = new DibsCall();
        $email2->setCallurl($Parameters);
        $email2->save();

        // call back url $p="es-297-100"; lang_orderid_amount

        $callbackparameters = $request->getParameter("p");
        $params = explode("-", $callbackparameters);

        $lang = $params[0];
        $order_id = $params[1];
        $order_amount = $params[2];

        $this->getUser()->setCulture($lang);

        //$order_id = $request->getParameter("order_id");
        $this->forward404Unless($order_id);

        $order = CustomerOrderPeer::retrieveByPK($order_id);
        $this->forward404Unless($order);

        //$order_amount = ((double) $request->getParameter('amount'));

        $c = new Criteria;
        $c->add(TransactionPeer::ORDER_ID, $order_id);
        $transaction = TransactionPeer::doSelectOne($c);
        if ($order_amount == "")
            $order_amount = $transaction->getAmount();

        $order->setOrderStatusId(sfConfig::get('app_status_completed', 3)); //completed
        $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3)); //completed
        if ($transaction->getAmount() > $order_amount) {
            //error
            $order->setOrderStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->setTransactionStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->save();
            die;
        } else if (number_format($transaction->getAmount(), 2) < $order_amount) {
            //$extra_refill_amount = $order_amount;
            $order->setExtraRefill($order_amount);
            $transaction->setAmount($order_amount);
        }
        //set active agent_package in case customer was registerred by an affiliate
        if ($order->getCustomer()->getAgentCompany()) {
            $order->setAgentCommissionPackageId($order->getCustomer()->getAgentCompany()->getAgentCommissionPackageId());
        }
        $ticket_id = $request->getParameter('transact');

        $order->save();
        $transaction->save();
        TransactionPeer::AssignReceiptNumber($transaction);
        $this->customer = $order->getCustomer();
        $c = new Criteria;
        $c->add(CustomerPeer::ID, $order->getCustomerId());
        $customer = CustomerPeer::doSelectOne($c);
        echo "ag" . $agentid = $customer->getReferrerId();
        echo "prid" . $productid = $order->getProductId();
        echo "trid" . $transactionid = $transaction->getId();
        if (isset($agentid) && $agentid != "") {
            echo "getagentid";
            commissionLib::refilCustomer($agentid, $productid, $transactionid);
            $transaction->setAgentCompanyId($agentid);
            $transaction->save();
        }

        //TODO ask if recharge to be done is same as the transaction amount
        //die;
        $exest = $order->getExeStatus();
        if ($exest == 1) {
            
        } else {
            //  Fonet::recharge($this->customer, $transaction->getAmount());
            $vat = 0;

            $TelintaMobile = sfConfig::get('app_country_code') . $this->customer->getMobileNumber();
            $emailId = $this->customer->getEmail();
            $OpeningBalance = $transaction->getAmount();
            $customerPassword = $this->customer->getPlainText();
            $getFirstnumberofMobile = substr($this->customer->getMobileNumber(), 0, 1);     // bcdef
            if ($getFirstnumberofMobile == 0) {
                $TelintaMobile = substr($this->customer->getMobileNumber(), 1);
                $TelintaMobile = sfConfig::get('app_country_code') . $TelintaMobile;
            } else {
                $TelintaMobile = sfConfig::get('app_country_code') . $this->customer->getMobileNumber();
            }

            $unidc = $this->customer->getUniqueid();

            echo $unidc;
            echo "<br/>";
            $OpeningBalance = $order->getExtraRefill();
            $telintaObj = new Telienta();
            $telintaObj->recharge($this->customer, $OpeningBalance, 'Refill');

            $getvoipInfo = new Criteria();
            $getvoipInfo->add(SeVoipNumberPeer::CUSTOMER_ID, $this->customer->getMobileNumber());
            $getvoipInfos = SeVoipNumberPeer::doSelectOne($getvoipInfo); //->getId();
            if (isset($getvoipInfos)) {
                $voipnumbers = $getvoipInfos->getNumber();
                $voip_customer = $getvoipInfos->getCustomerId();
            } else {
                
            }
            $MinuesOpeningBalance = $OpeningBalance * 3;

            $subject = $this->getContext()->getI18N()->__('Payment Confirmation');
            $sender_email = sfConfig::get('app_email_sender_email', 'support@kimarin.es');
            $sender_name = sfConfig::get('app_email_sender_name', 'Kimarin support');

            $recepient_email = trim($this->customer->getEmail());
            $recepient_name = sprintf('%s %s', $this->customer->getFirstName(), $this->customer->getLastName());
            $referrer_id = trim($this->customer->getReferrerId());

            if ($referrer_id):
                $c = new Criteria();
                $c->add(AgentCompanyPeer::ID, $referrer_id);

                $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
                $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
            endif;

            //send email

            $unidid = $this->customer->getUniqueid();
            $agent_company_id = $transaction->getAgentCompanyId();
            if ($agent_company_id != '') {
                $c = new Criteria();
                $c->add(AgentCompanyPeer::ID, $agent_company_id);

                $agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
            } else {
                $agent_name = '';
            }
            $message_body = $this->getPartial('payments/order_receipt', array(
                'customer' => $this->customer,
                'order' => $order,
                'transaction' => $transaction,
                'vat' => $vat,
                'agent_name' => $agent_name,
                'wrap' => false
            ));


            $this->setPreferredCulture($this->customer);
            emailLib::sendCustomerRefillEmail($this->customer, $order, $transaction);
            $this->updatePreferredCulture();
        }

        $order->setExeStatus(1);
        $order->save();
        echo 'Yes';
        return sfView::NONE;
    }

    public function executeConfirmpayment(sfWebRequest $request) {

        $Parameters = $request->getURI();

        // $Parameters=$Parameters.$request->getParameter('amount');
        $email2 = new DibsCall();
        $email2->setCallurl($Parameters);

        $email2->save();

        $order_id = "";
        $order_amount = "";

        // call back url $p="es-297-100"; lang-orderid-amount

        $callbackparameters = $request->getParameter("p");
        $params = explode("-", $callbackparameters);

        $lang = $params[0];
        $order_id = $params[1];
        $order_amount = $params[2];
        $this->getUser()->setCulture($lang);

        $ticket_id = "";
        //  $this->getUser()->setCulture($request->getParameter('lng'));


        if ($order_id != '') {

            $this->logMessage(print_r($_GET, true));

            $is_transaction_ok = false;
            $subscription_id = '';

            $this->forward404Unless($order_id);
            //$this->forward404Unless($order_id || $order_amount);
            //get order object
            $order = CustomerOrderPeer::retrieveByPK($order_id);


            if (isset($ticket_id) && $ticket_id != "") {

                $subscriptionvalue = 0;

                $subscriptionvalue = $request->getParameter('subscriptionid');


                if (isset($subscriptionvalue) && $subscriptionvalue > 1) {
//  echo 'is autorefill activated';
                    //auto_refill_amount
                    $auto_refill_amount_choices = array_keys(ProductPeer::getRefillHashChoices());

                    $auto_refill_amount = in_array($request->getParameter('user_attr_2'), $auto_refill_amount_choices) ? $request->getParameter('user_attr_2') : $auto_refill_amount_choices[0];
                    $order->getCustomer()->setAutoRefillAmount($auto_refill_amount);


                    //auto_refill_lower_limit
                    $auto_refill_lower_limit_choices = array_keys(ProductPeer::getAutoRefillLowerLimitHashChoices());

                    $auto_refill_min_balance = in_array($request->getParameter('user_attr_3'), $auto_refill_lower_limit_choices) ? $request->getParameter('user_attr_3') : $auto_refill_lower_limit_choices[0];
                    $order->getCustomer()->setAutoRefillMinBalance($auto_refill_min_balance);

                    $order->getCustomer()->setTicketval($ticket_id);
                    $order->save();
                    $auto_refill_amount = "refill amount" . $auto_refill_amount;
                    $email2d = new DibsCall();
                    $email2d->setCallurl($auto_refill_amount);
                    $email2d->save();
                    $minbalance = "min balance" . $auto_refill_min_balance;
                    $email2dm = new DibsCall();
                    $email2dm->setCallurl($minbalance);
                    $email2dm->save();
                }
            }
            //check to see if that customer has already purchased this product
            $c = new Criteria();
            $c->add(CustomerProductPeer::CUSTOMER_ID, $order->getCustomerId());
            $c->addAnd(CustomerProductPeer::PRODUCT_ID, $order->getProductId());
            $c->addJoin(CustomerProductPeer::CUSTOMER_ID, CustomerPeer::ID);
            $c->addAnd(CustomerPeer::CUSTOMER_STATUS_ID, sfConfig::get('app_status_new'), Criteria::NOT_EQUAL);

            // echo 'retrieve order id: '.$order->getId().'<br />';

            if (CustomerProductPeer::doCount($c) != 0) {

                //Customer is already registered.
                //echo __('The customer is already registered.');
                //exit the script successfully
                return sfView::NONE;
            }

            //set subscription id
            //$order->getCustomer()->setSubscriptionId($subscription_id);
            //set auto_refill amount
            //if order is already completed > 404
            $this->forward404Unless($order->getOrderStatusId() != sfConfig::get('app_status_completed'));
            $this->forward404Unless($order);

            //  echo 'processing order <br />';

            $c = new Criteria;
            $c->add(TransactionPeer::ORDER_ID, $order_id);
            $transaction = TransactionPeer::doSelectOne($c);
            $order_amount = $transaction->getAmount();
            //  echo 'retrieved transaction<br />';

            if ($transaction->getAmount() > $order_amount) {
                //error
                $order->setOrderStatusId(sfConfig::get('app_status_error')); //error in amount
                $transaction->setTransactionStatusId(sfConfig::get('app_status_error')); //error in amount
                $order->getCustomer()->setCustomerStatusId(sfConfig::get('app_status_error')); //error in amount
                echo 'setting error <br /> ';
            } elseif (number_format($transaction->getAmount(), 2) < $order_amount) {
                $transaction->setAmount($order_amount);
            }

            $order->setOrderStatusId(sfConfig::get('app_status_completed')); //completed
            $order->getCustomer()->setCustomerStatusId(sfConfig::get('app_status_completed')); //completed
            $transaction->setTransactionStatusId(3); //completed
            $transactiondescription = TransactionDescriptionPeer::retrieveByPK(8);
            $transaction->setTransactionTypeId($transactiondescription->getTransactionTypeId());
            $transaction->setTransactionDescriptionId($transactiondescription->getId());
            $transaction->setDescription($transactiondescription->getTitle());
            // echo 'transaction=ok <br /> ';
            $is_transaction_ok = true;

            $order->setQuantity(1);
            // $order->getCustomer()->getAgentCompany();
            //set active agent_package in case customer
            if ($order->getCustomer()->getAgentCompany()) {
                $order->setAgentCommissionPackageId($order->getCustomer()->getAgentCompany()->getAgentCommissionPackageId());
                $transaction->setAgentCompanyId($order->getCustomer()->getReferrerId()); //completed
            }

            $order->save();
            $transaction->save();
            TransactionPeer::AssignReceiptNumber($transaction);
            if ($is_transaction_ok) {

                // echo 'Assigning Customer ID <br/>';
                //set customer's proudcts in use
                $customer_product = new CustomerProduct();

                $customer_product->setCustomer($order->getCustomer());
                $customer_product->setProduct($order->getProduct());

                $customer_product->save();

                //register to fonet
                $this->customer = $order->getCustomer();

                //Fonet::registerFonet($this->customer);
                //recharge the extra_refill/initial balance of the prouduct
                //Fonet::recharge($this->customer, $order->getExtraRefill());

                $cc = new Criteria();
                $cc->add(EnableCountryPeer::ID, $this->customer->getCountryId());
                $country = EnableCountryPeer::doSelectOne($cc);

                $mobile = $country->getCallingCode() . $this->customer->getMobileNumber();

                $getFirstnumberofMobile = substr($this->customer->getMobileNumber(), 0, 1);     // bcdef
                if ($getFirstnumberofMobile == 0) {
                    $TelintaMobile = substr($this->customer->getMobileNumber(), 1);
                    $TelintaMobile = sfConfig::get('app_country_code') . $TelintaMobile;
                } else {
                    $TelintaMobile = sfConfig::get('app_country_code') . $this->customer->getMobileNumber();
                }


                $uniqueId = $this->customer->getUniqueid();
                echo $uniqueId . "<br/>";
                $uc = new Criteria();
                $uc->add(UniqueIdsPeer::UNIQUE_NUMBER, $uniqueId);
                $selectedUniqueId = UniqueIdsPeer::doSelectOne($uc);
                echo $selectedUniqueId->getStatus() . "<br/>Baran";

                if ($selectedUniqueId->getStatus() == 0) {
                    echo "inside";
                    $selectedUniqueId->setStatus(1);
                    $selectedUniqueId->setAssignedAt(date('Y-m-d H:i:s'));
                    $selectedUniqueId->save();
                } else {
                    $uc = new Criteria();
                    $uc->add(UniqueIdsPeer::REGISTRATION_TYPE_ID, 1);
                    $uc->addAnd(UniqueIdsPeer::STATUS, 0);
                    $uc->addAnd(UniqueIdsPeer::SIM_TYPE_ID, $this->customer->getSimTypeId());
                    $availableUniqueCount = UniqueIdsPeer::doCount($uc);
                    $availableUniqueId = UniqueIdsPeer::doSelectOne($uc);

                    if ($availableUniqueCount == 0) {
                        // Unique Ids are not avaialable. Then Redirect to the sorry page and send email to the support.
                        emailLib::sendUniqueIdsShortage($this->customer->getSimTypeId());
                        exit;
                        //$this->redirect($this->getTargetUrl().'customer/shortUniqueIds');
                    }
                    $uniqueId = $availableUniqueId->getUniqueNumber();
                    $this->customer->setUniqueid($uniqueId);
                    $this->customer->save();
                    $availableUniqueId->setStatus(1);
                    $availableUniqueId->setAssignedAt(date('Y-m-d H:i:s'));
                    $availableUniqueId->save();
                }



                $callbacklog = new CallbackLog();
                $callbacklog->setMobileNumber($TelintaMobile);
                $callbacklog->setuniqueId($uniqueId);
                $callbacklog->setCallingcode(sfConfig::get("app_country_code"));
                $callbacklog->setCheckStatus(3);
                $callbacklog->save();




                $emailId = $this->customer->getEmail();
                $OpeningBalance = $order->getExtraRefill();
                $customerPassword = $this->customer->getPlainText();
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //Section For Telinta Add Cusomter
                $telintaObj = new Telienta();
                $telintaObj->ResgiterCustomer($this->customer, $OpeningBalance);
                // For Telinta Add Account

                $telintaObj->createAAccount($TelintaMobile, $this->customer);
                $telintaObj->createCBAccount($TelintaMobile, $this->customer);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //if the customer is invited, Give the invited customer a bonus of 10
                $invite_c = new Criteria();
                $invite_c->add(InvitePeer::INVITE_NUMBER, $this->customer->getMobileNumber());
                $invite_c->add(InvitePeer::INVITE_STATUS, 2);
                $invite = InvitePeer::doSelectOne($invite_c);
                if ($invite) {
                    $invite->setInviteStatus(3);
                    $invite->setInvitedCustomerId($this->customer->getId());
                    $products = new Criteria();
                    $products->add(ProductPeer::ID, 2);
                    $products = ProductPeer::doSelectOne($products);
                    $extrarefill = $products->getInitialBalance();
                    //if the customer is invited, Give the invited customer a bonus of 10
                    $inviteOrder = new CustomerOrder();
                    $inviteOrder->setProductId(2);
                    $inviteOrder->setQuantity(1);
                    $inviteOrder->setOrderStatusId(3);
                    $inviteOrder->setIsFirstOrder(4);
                    $inviteOrder->setCustomerId($invite->getCustomerId());
                    $inviteOrder->setExtraRefill($extrarefill);
                    $inviteOrder->save();
                    $OrderId = $inviteOrder->getId();
                    // make a new transaction to show in payment history
                    $transaction_i = new Transaction();

                    $transaction_i->setAmount($extrarefill);
                    $transactiondescriptionB = TransactionDescriptionPeer::retrieveByPK(10);
                    $transaction_i->setTransactionTypeId($transactiondescriptionB->getTransactionType());
                    $transaction_i->setTransactionDescriptionId($transactiondescriptionB->getId());
                    $transaction_i->setDescription($transactiondescriptionB->getTitle());

                    $transaction_i->setCustomerId($invite->getCustomerId());
                    $transaction_i->setOrderId($OrderId);
                    $transaction_i->setTransactionStatusId(3);

                    $this->customers = CustomerPeer::retrieveByPK($invite->getCustomerId());

                    //send Telinta query to update the balance of invite by 10
                    $getFirstnumberofMobile = substr($this->customers->getMobileNumber(), 0, 1);     // bcdef
                    if ($getFirstnumberofMobile == 0) {
                        $TelintaMobile = substr($this->customers->getMobileNumber(), 1);
                        $TelintaMobile = sfConfig::get('app_country_code') . $TelintaMobile;
                    } else {
                        $TelintaMobile = sfConfig::get('app_country_code') . $this->customers->getMobileNumber();
                    }
                    $uniqueId = $this->customers->getUniqueid();
                    $OpeningBalance = $extrarefill;
                    //This is for Recharge the Customer
                    $telintaObj = new Telienta();
                    $telintaObj->recharge($this->customers, $OpeningBalance, $transactiondescriptionB->getTitle());

                    //This is for Recharge the Account

                    $transaction_i->save();
                    TransactionPeer::AssignReceiptNumber($transaction_i);
                    $invite->setBonusTransactionId($transaction_i->getId());
                    $invite->save();

                    $invitevar = $invite->getCustomerId();
                    if (isset($invitevar)) {


                        $inviterCustomer = CustomerPeer::retrieveByPK($invitevar);
                        $this->setPreferredCulture($inviterCustomer);




                        emailLib::sendCustomerConfirmRegistrationEmail($invite->getCustomerId(), $this->customer, NULL, $inviteOrder, $transaction_i);
                        $this->updatePreferredCulture();
                    }
                }
                $lang = sfConfig::get('app_language_symbol');
                $this->lang = $lang;

                $countrylng = new Criteria();
                $countrylng->add(EnableCountryPeer::LANGUAGE_SYMBOL, $lang);
                $countrylng = EnableCountryPeer::doSelectOne($countrylng);
                if ($countrylng) {
                    $countryName = $countrylng->getName();
                    $languageSymbol = $countrylng->getLanguageSymbol();
                    $lngId = $countrylng->getId();

                    $postalcharges = new Criteria();
                    $postalcharges->add(PostalChargesPeer::COUNTRY, $lngId);
                    $postalcharges->add(PostalChargesPeer::STATUS, 1);
                    $postalcharges = PostalChargesPeer::doSelectOne($postalcharges);
                    if ($postalcharges) {
                        $postalcharge = $postalcharges->getCharges();
                    } else {
                        $postalcharge = '';
                    }
                }
                //$product_price = $order->getProduct()->getPrice() - $order->getExtraRefill();
                $product_price = $order->getProduct()->getPrice() - $order->getExtraRefill();

                $product_price_vat = sfConfig::get('app_vat_percentage') * ($order->getProduct()->getRegistrationFee() + $postalcharge);
                $message_body = $this->getPartial('payments/order_receipt', array(
                    'customer' => $this->customer,
                    'order' => $order,
                    'transaction' => $transaction,
                    'vat' => $product_price_vat,
                    'postalcharge' => $postalcharge,
                    'wrap' => true
                ));

                $subject = $this->getContext()->getI18N()->__('Payment Confirmation');
                $sender_email = sfConfig::get('app_email_sender_email', 'support@kimarin.es');
                $sender_name = sfConfig::get('app_email_sender_name', 'Kimarin support');

                $recepient_email = trim($this->customer->getEmail());
                $recepient_name = sprintf('%s %s', $this->customer->getFirstName(), $this->customer->getLastName());


                $agentid = $this->customer->getReferrerId();

                $cp = new Criteria;
                $cp->add(CustomerProductPeer::CUSTOMER_ID, $order->getCustomerId());
                $customerproduct = CustomerProductPeer::doSelectOne($cp);
                $productid = $customerproduct->getId();

                $transactionid = $transaction->getId();
                if (isset($agentid) && $agentid != "") {
                    commissionLib::registrationCommissionCustomer($agentid, $productid, $transactionid);
                }
                $this->setPreferredCulture($this->customer);
                emailLib::sendCustomerRegistrationViaWebEmail($this->customer, $order);
                $this->updatePreferredCulture();
//                $zeroCallOutSMSObject = new ZeroCallOutSMS();
//                $zeroCallOutSMSObject->toCustomerAfterReg($order->getProductId(), $this->customer);
                $this->order = $order;
            }//end if
            else {
                $this->logMessage('Error in transaction.');
            }
        }
        //header('HTTP/1.1 200 OK');
        return sfView::NONE;
    }

    public function executeEmailTest(sfWebRequest $request) {


        $order_id = $request->getParameter('orderId');

        $order = CustomerOrderPeer::retrieveByPK($order_id);
        $customer = CustomerPeer::retrieveByPK($order->getCustomerId());

        if ($order->getIsFirstOrder() == 1) {
            emailLib::sendCustomerRegistrationViaWebEmail($customer, $order);
        } else {
            $c = new Criteria;
            $c->add(TransactionPeer::ORDER_ID, $order_id);
            $transaction = TransactionPeer::doSelectOne($c);
            emailLib::sendCustomerRefillEmail($customer, $order, $transaction);
        }
        return sfView::NONE;
    }

    private function setPreferredCulture(Customer $customer) {
        $this->currentCulture = $this->getUser()->getCulture();
        $preferredLang = PreferredLanguagesPeer::retrieveByPK($customer->getPreferredLanguageId());
        $this->getUser()->setCulture($preferredLang->getLanguageCode());
    }

    private function updatePreferredCulture() {
        $this->getUser()->setCulture($this->currentCulture);
    }

    public function executeSaveCustomerCallHistory(sfWebRequest $request) {
//

        $c = new Criteria;
        $c->add(CustomerPeer::CUSTOMER_STATUS_ID, 3);
        $customers = CustomerPeer::doSelect($c);

        foreach ($customers as $customer) {

//        $fromdate = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
//        $this->fromdate = date("Y-m-d", $fromdate);
//        $this->todate = $fromdate;

            $fromdate = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
            $this->fromdate = date("Y-m-d", $fromdate);
            $todate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $this->todate = date("Y-m-d", $todate);
            $telintaObj = new Telienta();
            $tilentaCallHistryResult = $telintaObj->callHistory($customer, $this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59');
            //  var_dump($tilentaCallHistryResult);


            if ($tilentaCallHistryResult) {
                foreach ($tilentaCallHistryResult->xdr_list as $xdr) {


                    $emCalls = new EmployeeCustomerCallhistory();
                    $emCalls->setAccountId($xdr->account_id);
                    $emCalls->setBillStatus($xdr->bill_status);
                    $emCalls->setBillTime($xdr->bill_time);
                    $emCalls->setChargedAmount($xdr->charged_amount);
                    $emCalls->setChargedQuantity($xdr->charged_quantity);
                    $emCalls->setPhoneNumber($xdr->CLD);
                    $emCalls->setCli($xdr->CLI);
                    $emCalls->setConnectTime($xdr->connect_time);

                    $country = $xdr->country;
                    $cc = new Criteria();
                    $cc->add(CountryPeer::NAME, $country, Criteria::LIKE);
                    $ccount = CountryPeer::doCount($cc);
                    if ($ccount > 0) {
                        $csel = CountryPeer::doSelectOne($cc);
                        $countryid = $csel->getId();
                    } else {
                        $cin = new Country();
                        $cin->setName($country);
                        $cin->save();
                        $countryid = $cin->getId();
                    }
                    $emCalls->setParentTable('customer');
                    $emCalls->setCountryId($countryid);
                    $ce = new Criteria();
                    $ce->add(TelintaAccountsPeer::ACCOUNT_TITLE, $xdr->account_id);
                    $ce->addAnd(TelintaAccountsPeer::PARENT_TABLE, 'customer');
                    $ce->add(TelintaAccountsPeer::STATUS, 3);
                    if (TelintaAccountsPeer::doCount($ce) > 0) {
                        $emp = TelintaAccountsPeer::doSelectOne($ce);
                        $emCalls->setParentId($emp->getParentId());
                    }

                    $emCalls->setDescription($xdr->description);
                    $emCalls->setDisconnectCause($xdr->disconnect_cause);
                    $emCalls->setDisconnectTime($xdr->disconnect_time);
                    // $emCalls->setDurationMinutes($duration_minutes);
                    $emCalls->setICustomer($customer->getICustomer());
                    $emCalls->setIXdr($xdr->i_xdr);
                    $emCalls->setStatus(3);
                    $emCalls->setSubdivision($xdr->subdivision);
                    $emCalls->setUnixConnectTime($xdr->unix_connect_time);
                    $emCalls->setUnixDisconnectTime($xdr->unix_disconnect_time);
                    $emCalls->save();
                }
            } else {
                $callsHistory = new CallHistoryCallsLog();
                $callsHistory->setParent('customer');
                $callsHistory->setParentId($customer->getId());
                $callsHistory->setTodate($this->todate);
                $callsHistory->setFromdate($this->fromdate);
                $callsHistory->save();
            }
        }
        return sfView::NONE;
    }

    public function executeCallHistoryNotFetch(sfWebRequest $request) {

        $c = new Criteria;
        $c->add(CallHistoryCallsLogPeer::STATUS, 1);
        $callLogs = CallHistoryCallsLogPeer::doSelect($c);

        foreach ($callLogs as $callLog) {
            $this->fromdate = $callLog->getFromdate();
            $this->todate = $callLog->getTodate();
            $customer = CustomerPeer::retrieveByPK($callLog->getCustomerId());
            $telintaObj = new Telienta();
            $tilentaCallHistryResult = $telintaObj->callHistory($customer, $this->fromdate . ' 00:00:00', $this->todate . ' 23:59:59');
            if ($tilentaCallHistryResult) {
                foreach ($tilentaCallHistryResult->xdr_list as $xdr) {
                    $emCalls = new EmployeeCustomerCallhistory();
                    $emCalls->setAccountId($xdr->account_id);
                    $emCalls->setBillStatus($xdr->bill_status);
                    $emCalls->setBillTime($xdr->bill_time);
                    $emCalls->setChargedAmount($xdr->charged_amount);
                    $emCalls->setChargedQuantity($xdr->charged_quantity);
                    $emCalls->setPhoneNumber($xdr->CLD);
                    $emCalls->setCli($xdr->CLI);
                    $emCalls->setConnectTime($xdr->connect_time);

                    $country = $xdr->country;
                    $cc = new Criteria();
                    $cc->add(CountryPeer::NAME, $country, Criteria::LIKE);
                    $ccount = CountryPeer::doCount($cc);
                    if ($ccount > 0) {
                        $csel = CountryPeer::doSelectOne($cc);
                        $countryid = $csel->getId();
                    } else {
                        $cin = new Country();
                        $cin->setName($country);
                        $cin->save();
                        $countryid = $cin->getId();
                    }
                    $emCalls->setParentTable('customer');
                    $emCalls->setCountryId($countryid);
                    $ce = new Criteria();
                    $ce->add(TelintaAccountsPeer::ACCOUNT_TITLE, $xdr->account_id);
                    $ce->addAnd(TelintaAccountsPeer::PARENT_TABLE, 'customer');
                    $ce->add(TelintaAccountsPeer::STATUS, 3);
                    if (TelintaAccountsPeer::doCount($ce) > 0) {
                        $emp = TelintaAccountsPeer::doSelectOne($ce);
                        $emCalls->setParentId($emp->getParentId());
                    }

                    $emCalls->setDescription($xdr->description);
                    $emCalls->setDisconnectCause($xdr->disconnect_cause);
                    $emCalls->setDisconnectTime($xdr->disconnect_time);
                    // $emCalls->setDurationMinutes($duration_minutes);
                    $emCalls->setICustomer($customer->getICustomer());
                    $emCalls->setIXdr($xdr->i_xdr);
                    $emCalls->setStatus(3);
                    $emCalls->setSubdivision($xdr->subdivision);
                    $emCalls->setUnixConnectTime($xdr->unix_connect_time);
                    $emCalls->setUnixDisconnectTime($xdr->unix_disconnect_time);
                    $emCalls->save();
                }


                $callLogs->setStatus(3);
                $callLogs->save();
            }
        }

        return sfView::NONE;
    }

    /*
     * To remove Last Refill after 180 days. if not refilled again
     *
     */

    public function executeRemoveRefilBalance(sfWebRequest $request) {

        $date = date('Y-m-d 00:00:00', strtotime('-180 Days'));
        $c = new Criteria;
        $c->addJoin(CustomerPeer::ID, CustomerOrderPeer::CUSTOMER_ID, Criteria::LEFT_JOIN);
        $c->addJoin(CustomerOrderPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
        $c->addJoin(ProductPeer::PRODUCT_TYPE_ID, ProductTypePeer::ID, Criteria::LEFT_JOIN);
        $c->addAnd(CustomerPeer::CUSTOMER_STATUS_ID, 3);
        $c->addAnd(ProductTypePeer::ID, 2);
        $c->addAnd(CustomerOrderPeer::CREATED_AT, $date, Criteria::LESS_THAN);
        $c->addAnd(CustomerOrderPeer::ORDER_STATUS_ID, 3);
        $c->addGroupByColumn(CustomerPeer::ID);
        $c->addDescendingOrderByColumn(CustomerOrderPeer::CREATED_AT);
        $customers = CustomerPeer::doSelect($c);

        foreach ($customers as $customer) {
            echo $customer->getId();
            $telintaObj = new Telienta();
            $balance = $telintaObj->getBalance($customer);
            if ($balance > 0) {
                $order = new CustomerOrder();
                $order->setExtraRefill(-$balance);
                $order->setCustomerId($customer->getId());
                $order->setProductId(17);
                $order->setOrderStatusId(3);
                $order->setIsFirstOrder(10);  //// product type remove 
                $order->save();

                $transaction = new Transaction();
                $transactiondescription = TransactionDescriptionPeer::retrieveByPK(17);
                $transaction->setAmount(-$balance);
                $transaction->setOrderId($order->getId());
                $transaction->setTransactionStatusId(3);
                $transaction->setCustomerId($customer->getId());
                $transaction->setTransactionTypeId($transactiondescription->getTransactionTypeId());
                $transaction->setTransactionDescriptionId($transactiondescription->getId());
                $transaction->setDescription($transactiondescription->getTitle());
                $transaction->save();
                TransactionPeer::AssignReceiptNumber($transaction);
                $telintaObj = new Telienta();
                $telintaObj->charge($customer, $balance, $transactiondescription->getTitle());
            }
        }


        return sfView::NONE;
    }

    public function executeKs(sfWebRequest $request) {

        $customer = CustomerPeer::retrieveByPK(4);

        $c = new Criteria();
        $c->addJoin(CustomerPeer::ID, CustomerProductPeer::CUSTOMER_ID, Criteria::LEFT_JOIN);
        $c->addJoin(CustomerProductPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
        $c->addJoin(ProductPeer::BILLING_PRODUCT_ID, BillingProductsPeer::ID, Criteria::LEFT_JOIN);
        $c->addAnd(CustomerProductPeer::STATUS_ID, 3);
        $c->addAnd(CustomerPeer::ID, $customer->getId());
        $product = BillingProductsPeer::doSelectOne($c);
        echo $product->getAIproduct();

        die;
        return sfView::NONE;
    }

    public function executeCalbackChangeNumber(sfWebRequest $request) {

        $Parameters = $request->getURI();

        $email2 = new DibsCall();
        $email2->setCallurl($Parameters);
        $email2->save();

        // call back url $p="es-297-100"; lang_orderid_amount

        $callbackparameters = $request->getParameter("p");
        $params = explode("-", $callbackparameters);

        $lang = $params[0];
        $order_id = $params[1];
        $order_amount = $params[2];

        $this->getUser()->setCulture($lang);

        $this->forward404Unless($order_id);

        $order = CustomerOrderPeer::retrieveByPK($order_id);
        $this->forward404Unless($order);


        $c = new Criteria;
        $c->add(TransactionPeer::ORDER_ID, $order_id);
        $transaction = TransactionPeer::doSelectOne($c);
        if ($order_amount == "")
            $order_amount = $transaction->getAmount();

        $order->setOrderStatusId(sfConfig::get('app_status_completed', 3)); //completed
        $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3)); //completed
        if ($transaction->getAmount() > $order_amount) {
            //error
            $order->setOrderStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->setTransactionStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->save();
            die;
        } else if (number_format($transaction->getAmount(), 2) < $order_amount) {
            //$extra_refill_amount = $order_amount;
            // $order->setExtraRefill($order_amount);
            $transaction->setAmount($order_amount);
        }

        $customer = $order->getCustomer();
        $old_mobile_number = $customer->getMobileNumber();

        $cn = new Criteria();
        $cn->add(ChangeNumberDetailPeer::CUSTOMER_ID, $customer->getId());
        $cn->addAnd(ChangeNumberDetailPeer::OLD_NUMBER, $old_mobile_number);
        $cn->addAnd(ChangeNumberDetailPeer::STATUS, 0);
        $change_number = ChangeNumberDetailPeer::doSelectOne($cn);
        // var_dump($change_number);
        $new_mobile = $change_number->getNewNumber();
        $countrycode = sfConfig::get("app_country_code");

        $uniqueId = $customer->getUniqueid();

        $un = new Criteria();
        $un->add(CallbackLogPeer::UNIQUEID, $uniqueId);
        $un->addDescendingOrderByColumn(CallbackLogPeer::CREATED);
        $activeNumber = CallbackLogPeer::doSelectOne($un);
//var_dump($activeNumber);
        // As each customer have a single account search the previous account and terminate it.
        $cp = new Criteria;
        $cp->add(TelintaAccountsPeer::ACCOUNT_TITLE, 'a' . $activeNumber->getMobileNumber());
        $cp->addAnd(TelintaAccountsPeer::STATUS, 3);

        $getFirstnumberofMobile = substr($new_mobile, 0, 1);
        if ($getFirstnumberofMobile == 0) {
            $TelintaMobile = substr($new_mobile, 1);
            $TelintaMobile = sfConfig::get('app_country_code') . $TelintaMobile;
        } else {
            $TelintaMobile = sfConfig::get('app_country_code') . $new_mobile;
        }
        $new_mobile_number = $TelintaMobile;

        if (TelintaAccountsPeer::doCount($cp) > 0) {
            $telintaAccount = TelintaAccountsPeer::doSelectOne($cp);
            $a_acount = "a" . $new_mobile_number;

            $accountInfo = array('i_account' => $telintaAccount->getIAccount(), "id" => $a_acount);
            $telintaObj = new Telienta();
            if ($telintaObj->updateAccount($accountInfo)) {
                $telintaAccount->setStatus(5);
                $telintaAccount->save();

                $ta = new TelintaAccounts();
                $ta->setParentTable("customer");
                $ta->setParentId($customer->getId());
                $ta->setIAccount($telintaAccount->getIAccount());
                $ta->setICustomer($customer->getICustomer());
                $ta->setAccountTitle($a_acount);
                $ta->setAccountType('a');
                $ta->setStatus(3);
                $ta->save();
            }
        }


        $cb = new Criteria;
        $cb->add(TelintaAccountsPeer::ACCOUNT_TITLE, 'cb' . $activeNumber->getMobileNumber());
        $cb->addAnd(TelintaAccountsPeer::STATUS, 3);

        if (TelintaAccountsPeer::doCount($cb) > 0) {
            $telintaAccountsCB = TelintaAccountsPeer::doSelectOne($cb);
            $cb_acount = "cb" . $new_mobile_number;

            $accountInfo = array('i_account' => $telintaAccount->getIAccount(), "id" => $cb_acount);
            $telintaObj = new Telienta();
            if ($telintaObj->updateAccount($accountInfo)) {
                $telintaAccountsCB->setStatus(5);
                $telintaAccountsCB->save();

                $tcb = new TelintaAccounts();
                $tcb->setParentTable("customer");
                $tcb->setParentId($customer->getId());
                $tcb->setIAccount($telintaAccountsCB->getIAccount());
                $tcb->setICustomer($customer->getICustomer());
                $tcb->setAccountTitle($cb_acount);
                $tcb->setAccountType('cb');
                $tcb->setStatus(3);
                $tcb->save();
            }
        }

        $getvoipInfo = new Criteria();
        $getvoipInfo->add(SeVoipNumberPeer::CUSTOMER_ID, $customer->getId());
        $getvoipInfo->addAnd(SeVoipNumberPeer::IS_ASSIGNED, 1);
        $getvoipInfos = SeVoipNumberPeer::doSelectOne($getvoipInfo); //->getId();
        if (isset($getvoipInfos)) {
            $voipnumbers = $getvoipInfos->getNumber();
            $getFirstnumberofMobile = substr($voipnumbers, 0, 2);
            if ($getFirstnumberofMobile == sfConfig::get('app_country_code')) {
                $voipnumbers = substr($voipnumbers, 2);
            } else {
                $voipnumbers = $getvoipInfos->getNumber();
            }

            $tc = new Criteria();
            $tc->add(TelintaAccountsPeer::ACCOUNT_TITLE, $voipnumbers);
            $tc->add(TelintaAccountsPeer::STATUS, 3);
            if (TelintaAccountsPeer::doCount($tc) > 0) {
                $telintaAccountR = TelintaAccountsPeer::doSelectOne($tc);

                $accountInfo = array('i_account' => $telintaAccountR->getIAccount(), "id" => $voipnumbers);
                $telintaObj = new Telienta();
                if ($telintaObj->updateAccount($accountInfo)) {
                    $telintaAccountR->setStatus(5);
                    $telintaAccountR->save();

                    $tcb = new TelintaAccounts();
                    $tcb->setParentTable("customer");
                    $tcb->setParentId($customer->getId());
                    $tcb->setIAccount($telintaAccountR->getIAccount());
                    $tcb->setICustomer($customer->getICustomer());
                    $tcb->setAccountTitle($voipnumbers);
                    $tcb->setAccountType('r');
                    $tcb->setStatus(3);
                    $tcb->save();
                }
            }
        }

        $change_number->setStatus(1);
        $change_number->save();

        $customer->setMobileNumber($new_mobile);
        $customer->save();


        $order->save();
        $transaction->save();
        TransactionPeer::AssignReceiptNumber($transaction);
        $callbacklog = new CallbackLog();
        $callbacklog->setMobileNumber($new_mobile_number);
        $callbacklog->setuniqueId($uniqueId);
        $callbacklog->setcallingCode($countrycode);
        $callbacklog->save();
        $this->setPreferredCulture($customer);
        emailLib::sendCustomerChangeNumberEmail($customer, $order);
        $this->updatePreferredCulture();
        return sfView::NONE;
    }

    public function executeChangeCustomerProduct(sfWebRequest $request) {
        if (date("d") != 1) {
            die;
        }
        $ccp = new Criteria();
        $ccp->add(CustomerChangeProductPeer::STATUS, 2);
        $ChangeCustomers = CustomerChangeProductPeer::doSelect($ccp);

        foreach ($ChangeCustomers as $changeCustomer) {


            $customer = CustomerPeer::retrieveByPK($changeCustomer->getCustomerId());
            $this->customer = $customer;
            $product = ProductPeer::retrieveByPK($changeCustomer->getProductId());
            $order = CustomerOrderPeer::retrieveByPK($changeCustomer->getOrderId());
            $transaction = TransactionPeer::retrieveByPK($changeCustomer->getTransactionId());
            $Bproducts = BillingProductsPeer::retrieveByPK($product->getBillingProductId());
            $c = new Criteria;
            $c->add(TelintaAccountsPeer::I_CUSTOMER, $customer->getICustomer());
            $c->add(TelintaAccountsPeer::STATUS, 3);
            $tilentAccount = TelintaAccountsPeer::doSelectOne($c);
            //  foreach($tilentAccounts as $tilentAccount){
            $accountInfo['i_account'] = $tilentAccount->getIAccount();
            $accountInfo['i_product'] = $Bproducts->getAIproduct();
            $telintaObj = new Telienta();
            if ($telintaObj->updateAccount($accountInfo)) {
                $changeCustomer->setStatus(3);
                $changeCustomer->Save();
            }
            //   }  

            $cp = new Criteria();
            $cp->add(CustomerProductPeer::CUSTOMER_ID, $customer->getId());
            $cp->addAnd(CustomerProductPeer::STATUS_ID, 3);
            $customerProduct = CustomerProductPeer::doSelectOne($cp);
            $customerProduct->setStatusId(7);
            $customerProduct->Save();

            $cProduct = new CustomerProduct();
            $cProduct->setProductId($changeCustomer->getProductId());
            $cProduct->setCustomerId($changeCustomer->getCustomerId());
            $cProduct->setStatusId(3);
            $cProduct->save();


            $this->setPreferredCulture($this->customer);
            emailLib::sendCustomerChangeProductConfirm($this->customer, $order, $transaction);
            $this->updatePreferredCulture();
            //  order_receipt_product_change
        }
        return sfView::NONE;
    }

    public function executeCalbacknewcard(sfWebRequest $request) {

        $Parameters = $request->getURI();

        $email2 = new DibsCall();
        $email2->setCallurl($Parameters);
        $email2->save();

        // call back url $p="es-297-100"; lang_orderid_amount

        $callbackparameters = $request->getParameter("p");
        $params = explode("-", $callbackparameters);

        $lang = $params[0];
        $order_id = $params[1];
        $order_amount = $params[2];

        $this->getUser()->setCulture($lang);

        $this->forward404Unless($order_id);

        $order = CustomerOrderPeer::retrieveByPK($order_id);
        $this->forward404Unless($order);

        $c = new Criteria;
        $c->add(TransactionPeer::ORDER_ID, $order_id);
        $transaction = TransactionPeer::doSelectOne($c);
        if ($order_amount == "")
            $order_amount = $transaction->getAmount();

        $order->setOrderStatusId(sfConfig::get('app_status_completed', 3)); //completed
        $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3)); //completed
        if ($transaction->getAmount() > $order_amount) {
            //error
            $order->setOrderStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->setTransactionStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->save();
            die;
        } else if (number_format($transaction->getAmount(), 2) < $order_amount) {
            $transaction->setAmount($order_amount);
        }
        //set active agent_package in case customer was registerred by an affiliate
        /* if ($order->getCustomer()->getAgentCompany()) {
          $order->setAgentCommissionPackageId($order->getCustomer()->getAgentCompany()->getAgentCommissionPackageId());
          } */
        $order->save();
        $transaction->save();
        TransactionPeer::AssignReceiptNumber($transaction);
        $this->customer = $order->getCustomer();
        /* echo "ag" . $agentid = $this->customer->getReferrerId();
          echo "prid" . $productid = $order->getProductId();
          echo "trid" . $transactionid = $transaction->getId();
          if (isset($agentid) && $agentid != "") {
          echo "getagentid";
          commissionLib::refilCustomer($agentid, $productid, $transactionid);
          $transaction->setAgentCompanyId($agentid);
          $transaction->save();
          } */
        $cst = new Criteria();
        $cst->add(SimTypesPeer::ID, $order->getProduct()->getSimTypeId());
        $simtype = SimTypesPeer::doSelectOne($cst);
        echo $sim_type_id = $simtype->getId();
        $exest = $order->getExeStatus();
        if ($exest != 1) {

            $uniqueId = $this->customer->getUniqueid();
            $cb = new Criteria();
            $cb->add(CallbackLogPeer::UNIQUEID, $uniqueId);
            $cb->addDescendingOrderByColumn(CallbackLogPeer::CREATED);
            $activeNumber = CallbackLogPeer::doSelectOne($cb);

            $uc = new Criteria();
            $uc->add(UniqueIdsPeer::REGISTRATION_TYPE_ID, 1);
            $uc->addAnd(UniqueIdsPeer::STATUS, 0);
            $uc->addAnd(UniqueIdsPeer::SIM_TYPE_ID, $sim_type_id);
            $availableUniqueCount = UniqueIdsPeer::doCount($uc);
            $availableUniqueId = UniqueIdsPeer::doSelectOne($uc);

            if ($availableUniqueCount == 0) {
                // Unique Ids are not avaialable. Then Redirect to the sorry page and send email to the support.
                emailLib::sendUniqueIdsShortage($sim_type_id);
                exit;
                //$this->redirect($this->getTargetUrl().'customer/shortUniqueIds');
            }

            $callbacklog = new CallbackLog();
            $callbacklog->setMobileNumber($activeNumber->getMobileNumber());
            $callbacklog->setuniqueId($availableUniqueId->getUniqueNumber());
            $callbacklog->setcallingCode(sfConfig::get('app_country_code'));
            $callbacklog->save();

            $uniqueidlog = new UniqueidLog();
            $uniqueidlog->setCustomerId($this->customer->getId());
            $uniqueidlog->setUniqueNumber($uniqueId);
            $uniqueidlog->save();

            $availableUniqueId->setStatus(1);
            $availableUniqueId->setAssignedAt(date('Y-m-d H:i:s'));
            $availableUniqueId->save();
            $this->customer->setUniqueid($availableUniqueId->getUniqueNumber());
            $this->customer->setSimTypeId($sim_type_id);
            $this->customer->save();

            $this->setPreferredCulture($this->customer);
            emailLib::sendCustomerNewcardEmail($this->customer, $order, $transaction);
            $this->updatePreferredCulture();
        }

        $order->setExeStatus(1);
        $order->save();
        echo 'Yes';
        return sfView::NONE;
    }

    public function executeCalbackChangeProduct(sfWebRequest $request) {

        $Parameters = $request->getURI();

        $email2 = new DibsCall();
        $email2->setCallurl($Parameters);
        $email2->save();

        // call back url $p="es-297-100"; lang_orderid_amount

        $callbackparameters = $request->getParameter("p");
        $params = explode("-", $callbackparameters);

        $lang = $params[0];
        $order_id = $params[1];
        $order_amount = $params[2];
        $ccpid = $params[3];

        $this->getUser()->setCulture($lang);

        $this->forward404Unless($order_id);
        $CCP = CustomerChangeProductPeer::retrieveByPK($ccpid);
        $CCP->setStatus(2);
        $CCP->save();

        $order = CustomerOrderPeer::retrieveByPK($order_id);
        $this->forward404Unless($order);

        $c = new Criteria;
        $c->add(TransactionPeer::ORDER_ID, $order_id);
        $transaction = TransactionPeer::doSelectOne($c);
        if ($order_amount == "")
            $order_amount = $transaction->getAmount();

        $order->setOrderStatusId(sfConfig::get('app_status_completed', 3)); //completed
        $transaction->setTransactionStatusId(sfConfig::get('app_status_completed', 3)); //completed
        if ($transaction->getAmount() > $order_amount) {
            //error
            $order->setOrderStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->setTransactionStatusId(sfConfig::get('app_status_error', 5)); //error in amount
            $transaction->save();
            die;
        } else if (number_format($transaction->getAmount(), 2) < $order_amount) {
            $transaction->setAmount($order_amount);
        }

        $order->save();
        $transaction->save();
        TransactionPeer::AssignReceiptNumber($transaction);
        $this->customer = $order->getCustomer();


        $exest = $order->getExeStatus();


        $uniqueId = $this->customer->getUniqueid();

        $this->setPreferredCulture($this->customer);
        emailLib::sendCustomerChangeProduct($this->customer, $order, $transaction);
        $this->updatePreferredCulture();


        $order->setExeStatus(1);
        $order->save();
        echo 'Yes';
        return sfView::NONE;
    }

    private function hextostr($hex) {
        $str = '';
        for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
            $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $str;
    }

    public function executeGenerateTestString(sfWebRequest $request) {
        if ($request->isMethod('post')) {
            if ($request->getParameter("encrypt") == "on") {
                $str = substr(urldecode($request->getParameter("inputstr")), 4);
                echo "MPAY " . mcrypt_decrypt(MCRYPT_DES, "12345678", $this->hextostr(bin2hex(base64_decode($str))), MCRYPT_MODE_CBC, "12345678");
            } else {
                //Reversal: From String to Encripted Data.
                $key = '12345678';
                $data = trim(substr(urldecode($request->getParameter("inputstr")), 4));
                echo $data;
                echo "<br/>";
                $alg = MCRYPT_DES;
                $mode = MCRYPT_MODE_CBC;
                $encrypted_data = mcrypt_encrypt($alg, $key, $data, $mode, $key);
                $data = $this->strToHex($data);
                $data = $this->hextobin($data);
                $plain_text = base64_encode($encrypted_data);
                echo $plain_text;
                echo "<br/>";
                echo "MPAY+" . urlencode($plain_text);
            }
        }
    }

    private function randomNumbers($length) {
        $random = "";
        srand((double) microtime() * 1000000);
        $data = "0123456789";
        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }
        return $random;
    }

    private function strToHex($string) {
        $hex = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    public function executeEmailTestCenter(sfWebRequest $request) {



        $inviterCustomer = CustomerPeer::retrieveByPK(38);
        $this->setPreferredCulture($inviterCustomer);
        $inviteOrder = CustomerOrderPeer::retrieveByPK(304);
        $transaction_i = TransactionPeer::retrieveByPK(296);
        $this->customer = CustomerPeer::retrieveByPK(34);


        emailLib::sendCustomerConfirmRegistrationEmail($inviterCustomer->getId(), $this->customer, null, $inviteOrder, $transaction_i);
        $this->updatePreferredCulture();
        return sfView::NONE;
    }

    public function executeCardNumber(sfWebRequest $request) {


        function random($len) {


            $return = '';
            for ($i = 0; $i < $len; ++$i) {
                if (!isset($urandom)) {
                    if ($i % 2 == 0)
                        mt_srand(time() % 2147 * 1000000 + (double) microtime() * 1000000);
                    $rand = 48 + mt_rand() % 64;
                }
                else
                    $rand = 48 + ord($urandom[$i]) % 64;

                if ($rand > 57)
                    $rand+=7;
                if ($rand > 90)
                    $rand+=6;
                if ($rand > 80)
                    $rand-=5;


                if ($rand == 123)
                    $rand = 45;
                if ($rand == 124)
                    $rand = 46;
                $return.=$rand;
            }
            return $return;
        }

        $cardcount = 0;
        $serial = 100000;
        $i = 1;
        while ($i <= 20000) {


            $val = random(20);

            $randLength = strlen($val);

            if ($randLength > 11) {
                $resultvalue = (int) $randLength - 11;

                $rtvalue = mt_rand(1, $resultvalue);

                $resultvalue = substr($val, $rtvalue, 11);

                $cardnumber = "02149" . $resultvalue;
            }

            $CRcardcount = 0;
            $cq = new Criteria();
            $cq->add(CardNumbersPeer::CARD_NUMBER, $cardnumber);
            $CRcardcount = CardNumbersPeer::doCount($cq);

            if ($CRcardcount == 1) {
                
            } else {

                $cardTotalcount = 0;
                $ct = new Criteria();
                $cardTotalcount = CardNumbersPeer::doCount($ct);
                if ($cardTotalcount < 4000) {
                    $cardcount = 0;

                    $c = new Criteria();
                    $c->add(CardNumbersPeer::CARD_PRICE, 100);
                    $cardcount = CardNumbersPeer::doCount($c);
                    if ($cardcount < 2000) {

                        $price = 100;
                        $cr = new CardNumbers();
                        $cr->setCardNumber($cardnumber);
                        $cr->setCardPrice($price);
                        $cr->setCardSerial($serial);
                        $cr->save();
                        $serial++;
                    }
                } else {
                    $i = 2000;
                }
            }
            $i++;
        }


        return sfView::NONE;
    }

    public function executeAgentTopUpRequest(sfWebRequest $request) {




        //$str = 'RnMrgwAtLH57EduTU4XfXnFCsYLsIiTiL5cORWg4yrcnAXH99An/htUf7KlAYYcqVA/Rn9mzeis=';
        //GLDG#Z0000001#0000#Top up#Vendor 1#100#cash#20717786
        $str = substr($request->getParameter("message"), 4);
        echo $decrypt = mcrypt_decrypt(MCRYPT_DES, "12345678", $this->hextostr(bin2hex(base64_decode($str))), MCRYPT_MODE_CBC, "12345678");
//                die;
//        $str = substr($request->getParameter("message"), 4);
//        echo $decrypt = mcrypt_decrypt(MCRYPT_DES, "12345678", $this->hextostr(bin2hex(base64_decode($str))), MCRYPT_MODE_CBC, "12345678");
//        echo "<br/>";
//        die;
        $urlval = "AgnetTopUp - " . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setTransactionFromId(5);
        $dibsCall->setDecryptedData($decrypt);
        $dibsCall->save();

        $data = explode("#", $decrypt);
        $agentUniqueID = trim($data[1]);
        $vendor = strtolower(trim($data[2]));
        $amount = trim($data[3]);
        $customerMobile = trim($data[4]);

        if ($customerMobile == "") {
            $customerMobile = $request->getParameter("mobile");
        }
        $c = new Criteria();
        $c->add(AgentUserPeer::UNIQUE_ID, $agentUniqueID);
        $c->add(AgentUserPeer::STATUS_ID, 1);

        if (AgentUserPeer::doCount($c) > 0) {
            $agent = AgentUserPeer::doSelectOne($c);


            $ap = new Criteria();
            $ap->add(AgentProductPeer::AGENT_ID, $agent->getAgentCompanyId());
            $product_criteria = new Criteria();
            if (AgentProductPeer::doCount($ap) > 0) {
                $product_criteria->addJoin(ProductPeer::ID, AgentProductPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
                $product_criteria->addJoin(ProductPeer::VENDOR_ID, VendorsPeer::ID, Criteria::LEFT_JOIN);
                $product_criteria->addJoin(VendorsPeer::ID, VendorAliasPeer::VENDOR_ID, Criteria::LEFT_JOIN);
                $product_criteria->add(VendorAliasPeer::TITLE, $vendor);
                $product_criteria->addAnd(AgentProductPeer::AGENT_ID, $agent->getAgentCompanyId());
                $product_criteria->addAnd(ProductPeer::REGISTRATION_FEE, $amount);
                $product_criteria->addAnd(ProductPeer::IS_IN_STORE, true);
                //  echo "---".ProductPeer::doCount($product_criteria);die;
                if (ProductPeer::doCount($product_criteria) == 0) {
                    echo "Agent Product not subscribed";
                    $smst = SmsTextPeer::retrieveByPK(36)->getMessageText();
                    ROUTED_SMS::Send($request->getParameter("mobile"), $smst);
                    $dibsCall->setAgentReceipt($smst);
                    $dibsCall->save();
                    $msg = "Agent Product not subscribed other products are subscribed request by uniqueid:" . $agentUniqueID . "vendor:" . $vendor . ", amount:" . $amount . " message from sms text " . $smst . " All Requests id:" . $dibsCall->getId();
                    emailLib::sendErrorInTopUP($msg);
                    return sfView::NONE;
                }
            }

            $prc = new Criteria();
            $prc->addJoin(ProductPeer::ID, AgentProductPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
            $prc->addJoin(ProductPeer::VENDOR_ID, VendorsPeer::ID, Criteria::LEFT_JOIN);
            $prc->addJoin(VendorsPeer::ID, VendorAliasPeer::VENDOR_ID, Criteria::LEFT_JOIN);
            $prc->add(VendorAliasPeer::TITLE, $vendor);
            $prc->addAnd(ProductPeer::REGISTRATION_FEE, $amount);
            $prc->addAnd(ProductPeer::IS_IN_STORE, true);
            $prc->addAnd(ProductPeer::STATUS_ID, 1, Criteria::NOT_EQUAL);

            if (ProductPeer::doCount($prc) > 0) {
                echo $smst = SmsTextPeer::retrieveByPK(24)->getMessageText();
                ROUTED_SMS::Send($request->getParameter("mobile"), $smst);
                $dibsCall->setAgentReceipt($smst);
                $dibsCall->save();
                $msg = "Deactivated product request by uniqueid:" . $agentUniqueID . "vendor:" . $vendor . ", amount:" . $amount . " message from sms text " . $smst . " All Requests id:" . $dibsCall->getId();

                emailLib::sendErrorInTopUP($msg);
                return sfView::NONE;
            }

            $Prod = new Criteria();
            $Prod->addJoin(CardNumbersPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
            $Prod->addJoin(ProductPeer::VENDOR_ID, VendorsPeer::ID, Criteria::LEFT_JOIN);
            $Prod->addJoin(VendorsPeer::ID, VendorAliasPeer::VENDOR_ID, Criteria::LEFT_JOIN);
            $Prod->add(VendorAliasPeer::TITLE, $vendor);
            $Prod->addAnd(ProductPeer::REGISTRATION_FEE, $amount);
            $Prod->addAnd(CardNumbersPeer::STATUS, 0);
            $Prod->addAnd(CardNumbersPeer::CARD_TYPE_ID, 1);

            if (CardNumbersPeer::doCount($Prod) > 0) {
                $sc = new Criteria();
                $sc->addJoin(SmsTextPeer::VENDOR_ID, VendorsPeer::ID);
                $sc->addJoin(VendorsPeer::ID, VendorAliasPeer::VENDOR_ID, Criteria::LEFT_JOIN);
                $sc->add(VendorAliasPeer::TITLE, $vendor);
                if (SmsTextPeer::doCount($sc) > 0) {
                    $card_number = CardNumbersPeer::doSelectOne($Prod);
                    $sms = SmsTextPeer::doSelectOne($sc);
                    $tt = new TopupTransactions();
                    $tt->setAgentCompanyId($agent->getAgentCompanyId());
                    $tt->setProductId($card_number->getProduct()->getId());
                    $tt->setProductName($card_number->getProduct()->getName());
                    $tt->setProductVat($card_number->getProduct()->getVat());
                    $tt->setProductPrice($card_number->getProduct()->getPrice());
                    $tt->setProductRegistrationFee($card_number->getProduct()->getRegistrationFee());
                    $tt->setCustomerMobileNumber($customerMobile);
                    $tt->setAgentUserId($agent->getId());
                    $tt->setVendorId($card_number->getProduct()->getVendorId());
                    $tt->setVenderName($card_number->getProduct()->getVendors()->getTitle());
                    $tt->setCardPurchasePrice($card_number->getCardPurchasePrice());
                    $tt->setAgentEmail($agent->getAgentCompany()->getEmail());
                    $tt->setAgentCompanyName($agent->getAgentCompany()->getName());
                    $tt->setAgentUserName($agent->getUsername());
                    $reseller = ResellerPeer::retrieveByPK($agent->getAgentCompany()->getResellerId());
                    $tt->setResellerId($reseller->getId());
                    $tt->setResellerName($reseller->getName());
                    $tt->setResellerEmail($reseller->getEmail());
                    $tt->setResellerContactNumber($reseller->getContactNumber());

                    $tt->setTransactionFromId(5);
                    $tt->setDibsCallId($dibsCall->getId());
                    $tt->setStatus(1);
                    $tt->save();

                    if (commissionLib::topUp($agent->getAgentCompanyId(), $card_number->getProduct()->getId(), $tt->getId(), true)) {
                        $vendor = VendorsPeer::retrieveByPK($tt->getVendorId());
                        if ($vendor->topUp($tt, $card_number, $customerMobile)) {
                            $sms = SmsTextPeer::retrieveByPK(16)->getMessageText();
                            $sms = str_replace("(mobilenumber)", $customerMobile, $sms);
                            $sms = str_replace("(serial)", $card_number->getCardSerial(), $sms);
                            //$sms = "serial:" . $card_number->getCardSerial() . " Sent to customer mobile:" . $customerMobile;
                            if ($agent->getAgentCompany()->getIsPrepaid()) {
                                $sms .= " Your Balance is " . $agent->getAgentCompany()->getBalance() . " kr";
                            }
                            ROUTED_SMS::Send($request->getParameter("mobile"), $sms);
                            emailLib::sendCustomerTopup($tt, "pScripts");
                        } else {
                            $sms = SmsTextPeer::retrieveByPK(19)->getMessageText();
                            ROUTED_SMS::Send($request->getParameter("mobile"), $sms);
                            $msg = "exception occured of request of unique id:" . $agentUniqueID . "vendor:" . $vendor . ", amount:" . $amount . " message from sms text " . $sms . " All Requests id:" . $dibsCall->getId();
                            emailLib::sendErrorInTopUP($msg);
                        }
                    } else {
                        $sms = commissionLib::topUp($agent->getAgentCompanyId(), $card_number->getProduct()->getId(), $tt->getId(), true, true);
                        ROUTED_SMS::Send($request->getParameter("mobile"), $sms);
                        $msg = "Low balance request by unique id:" . $agentUniqueID . "vendor:" . $vendor . ", amount:" . $amount . " message from sms text " . $sms . " All Requests id:" . $dibsCall->getId();
                        emailLib::sendErrorInTopUP($msg);
                    }
                } else {
                    // SMS Not Found for the specified Vendor.   
                    echo $message = "Dear agent, vendor " . $vendor . " SMS does not exist, please contact support:70702126.";
                    emailLib::sendErrorInAutoReg("SMS not found", $message);
//                    ROUTED_SMS::Send($request->getParameter("mobile"), SmsTextPeer::retrieveByPK(18)->getMessageText());
                    $message = SmsTextPeer::retrieveByPK(31)->getMessageText();
                    $msg = "sms text not found of vendor " . $vendor . " request by unique id:" . $agentUniqueID . "vendor:" . $vendor . ", amount:" . $amount . " message from sms text " . $message . " All Requests id:" . $dibsCall->getId();
                    emailLib::sendErrorInTopUP($msg);
                }
            } else {
                // Card Not Found......
                echo "card not found";
                $sms = SmsTextPeer::retrieveByPK(2)->getMessageText();
                ROUTED_SMS::Send($request->getParameter("mobile"), $sms);
                $msg = "card not found request by unique id:" . $agentUniqueID . "vendor:" . $vendor . ", amount:" . $amount . " message from sms text " . $sms . " All Requests id:" . $dibsCall->getId();
                emailLib::sendErrorInTopUP($msg);
            }
        } else {
            // Agent Not Found..............
            echo "AGent Not Found";
            $sms = SmsTextPeer::retrieveByPK(37)->getMessageText();
            ROUTED_SMS::Send($request->getParameter("mobile"), $sms);
            $msg = "Unique ID Number not found:" . $agentUniqueID . " message from sms text " . $sms . " All Requests id:" . $dibsCall->getId();
            emailLib::sendErrorInTopUP($msg);
        }

        //$sms = SmsTextPeer::retrieveByPK(1);
        //$sms = str_replace("(voucher)", "11111111111111", $sms->getMessageText());
        //ROUTED_SMS::Send("45" . $customerMobile, $sms);

        return sfView::NONE;
    }

    public function executeVendingAgentReport(sfWebRequest $request) {
        $urlval = "VendingAgnetReport - " . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setTransactionFromId(6);
        $dibsCall->setDecryptedData($request->getParameter("machineid"));
        $dibsCall->save();

        $imei_number = $request->getParameter("machineid");

        $c = new Criteria();
        $c->addJoin(AgentUserPeer::IMEI_NUMBER_ID, ImeiNumbersPeer::ID, Criteria::LEFT_JOIN);
        $c->addAnd(ImeiNumbersPeer::IMEI_NUMBER, $imei_number);
        $c->add(AgentUserPeer::STATUS_ID, 1);
        $c->add(AgentUserPeer::STATUS_ID, 1);
        if (AgentUserPeer::doCount($c) > 0) {
            $agent = AgentUserPeer::doSelectOne($c);
            $conn = Propel::getConnection();
            $queryC = "SELECT COUNT(topup_transactions.PRODUCT_ID) AS countRow,vender_name,product_registration_fee FROM `topup_transactions` WHERE topup_transactions.TRANSACTION_FROM_ID=6 AND topup_transactions.AGENT_USER_ID=" . $agent->getId() . " AND created_at between '" . date("Y-m-d") . " 00:00:00' and '" . date("Y-m-d") . " 23:59:59' and status=3 GROUP BY topup_transactions.PRODUCT_ID";
            $statementC = $conn->prepare($queryC);
            $statementC->execute();

            $i = true;


            $report = "Products sold today\n--------------------\n";
            while ($rowObjCus = $statementC->fetch(PDO::FETCH_OBJ)) {
                $i = false;
                $report.=$rowObjCus->vender_name . " " . $rowObjCus->product_registration_fee . ": " . $rowObjCus->countRow . "\n";
            }

            if ($i) {
                $report.="No Transaction found for today\n";
            }

            $queryUsage = "select sum(product_registration_fee) as reg_fee, sum(agent_commission) as agent_commision, (sum(product_registration_fee)-sum(agent_commission)) as balance  from topup_transactions WHERE topup_transactions.TRANSACTION_FROM_ID=6 AND topup_transactions.AGENT_USER_ID=" . $agent->getId() . " AND created_at between '" . date("Y-m-d") . " 00:00:00' and '" . date("Y-m-d") . " 23:59:59' and status=3 ;";
            $statementU = $conn->prepare($queryUsage);
            $statementU->execute();
            $rowObjUsage = $statementU->fetch(PDO::FETCH_OBJ);
            $report.= "\n\n\nToday's revenue\n---------------\n";
            $report .= "Revenue: " . $rowObjUsage->reg_fee;
            $report .= "\nCommission: " . $rowObjUsage->agent_commision;
            $report .= "\nToday's balance: " . $rowObjUsage->balance;

            $report .= "\n\n\nOverall Balance Info\n--------------------\n";
            $report .= "Actual Bal: " . number_format($agent->getAgentCompany()->getBalance(), 2);
        } else {
            $report.="Sorry We are unable to recognize you. Please contact Support";
        }


        $dibsCall->setAgentReceipt($report);



        echo $report;


        $dibsCall->save();
        return sfView::NONE;
    }

    /**
     * 
     * @param sfWebRequest $request
     * @return type
     *  this will be use for the vendoing machine sim request
     * Baran
     */
    public function executeVendingAgentTopUpRequest_old(sfWebRequest $request) {


        $urlval = "VendingAgnetTopUp - " . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setTransactionFromId(6);
        $dibsCall->setDecryptedData($request->getParameter("itemNumber") . "---" . $request->getParameter("machineid"));
        $dibsCall->save();


        //   echo "Thanks Mathew For your help";
//        $data = explode("#", $decrypt);
//        $agentUniqueID = trim($data[1]);
//        $vendor = trim($data[2]);
//        $amount = trim($data[3]);
//        $customerMobile = trim($data[4]);
//
        $imei_number = $request->getParameter("machineid");
        $item_number = $request->getParameter("itemNumber");
        $c = new Criteria();

        /////////////need to put left join /////////////////
        $c->addJoin(AgentUserPeer::IMEI_NUMBER_ID, ImeiNumbersPeer::ID, Criteria::LEFT_JOIN);
        $c->addAnd(ImeiNumbersPeer::IMEI_NUMBER, $imei_number);
        $c->add(AgentUserPeer::STATUS_ID, 1);

        if (AgentUserPeer::doCount($c) > 0) {
            $agent = AgentUserPeer::doSelectOne($c);


            $ap = new Criteria();
            $ap->add(AgentProductPeer::AGENT_ID, $agent->getAgentCompanyId());
            $product_criteria = new Criteria();
            if (AgentProductPeer::doCount($ap) > 0) {
                $product_criteria->addJoin(ProductPeer::ID, AgentProductPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
                $product_criteria->addAnd(AgentProductPeer::AGENT_ID, $agent->getAgentCompanyId());
                $product_criteria->addAnd(ProductPeer::VENDING_MACHINE_ID, $item_number);
                $product_criteria->addAnd(ProductPeer::IS_IN_STORE, true);
                //  echo "---".ProductPeer::doCount($product_criteria);die;
                if (ProductPeer::doCount($product_criteria) == 0) {
                    echo $smst = SmsTextPeer::retrieveByPK(36)->getMessageText();
                    $dibsCall->setAgentReceipt($smst);
                    $msg = "Product not assigned in agent per product event occured at imei number request:" . $imei_number . " and vending machine id of product: " . $item_number . " message from sms text " . $smst . " All Request id:" . $dibsCall->getId();
                    emailLib::sendErrorInTopUP($msg);
                    $dibsCall->save();
                    return sfView::NONE;
                }
            }

            $prc = new Criteria();
            $prc->add(ProductPeer::VENDING_MACHINE_ID, $item_number);
            $prc->addAnd(ProductPeer::STATUS_ID, 1, Criteria::NOT_EQUAL);
            if (ProductPeer::doCount($prc) > 0) {
                echo $smst = SmsTextPeer::retrieveByPK(24)->getMessageText();
                $dibsCall->setAgentReceipt($smst);
                $msg = "Deactivated product event occured at imei number request:" . $imei_number . " and vending machine id of product: " . $item_number . " message from sms text " . $smst . " All Request id:" . $dibsCall->getId();
                emailLib::sendErrorInTopUP($msg);
                $dibsCall->save();
                return sfView::NONE;
            }

            $Prod = new Criteria();
            $Prod->addJoin(CardNumbersPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
            $Prod->addAnd(ProductPeer::VENDING_MACHINE_ID, $item_number);
            $Prod->addAnd(CardNumbersPeer::STATUS, 0);

            if (CardNumbersPeer::doCount($Prod) > 0) {
                $sc = new Criteria();
                $sc->addJoin(SmsTextPeer::VENDOR_ID, VendorsPeer::ID);
                $sc->addJoin(VendorsPeer::ID, ProductPeer::VENDOR_ID);
                $sc->add(ProductPeer::VENDING_MACHINE_ID, $item_number);
                if (SmsTextPeer::doCount($sc) > 0) {
                    $card_number = CardNumbersPeer::doSelectOne($Prod);
                    $sms = SmsTextPeer::doSelectOne($sc);
                    $tt = new TopupTransactions();
                    $tt->setAgentCompanyId($agent->getAgentCompanyId());
                    $tt->setProductId($card_number->getProduct()->getId());
                    $tt->setProductName($card_number->getProduct()->getName());
                    $tt->setProductVat($card_number->getProduct()->getVat());
                    $tt->setProductPrice($card_number->getProduct()->getPrice());
                    $tt->setProductRegistrationFee($card_number->getProduct()->getRegistrationFee());
                    $tt->setCustomerMobileNumber($customerMobile);
                    $tt->setAgentUserId($agent->getId());
                    $tt->setVendorId($card_number->getProduct()->getVendorId());
                    $tt->setVenderName($card_number->getProduct()->getVendors()->getTitle());
                    $tt->setCardPurchasePrice($card_number->getCardPurchasePrice());
                    $tt->setAgentEmail($agent->getAgentCompany()->getEmail());
                    $tt->setAgentCompanyName($agent->getAgentCompany()->getName());
                    $tt->setAgentUserName($agent->getUsername());
                    $reseller = ResellerPeer::retrieveByPK($agent->getAgentCompany()->getResellerId());
                    $tt->setResellerId($reseller->getId());
                    $tt->setResellerName($reseller->getName());
                    $tt->setResellerEmail($reseller->getEmail());
                    $tt->setResellerContactNumber($reseller->getContactNumber());
                    $tt->setTransactionFromId(6);
                    $tt->setStatus(1);
                    $tt->setTransactionFromId(6);
                    $tt->setImeiNumber($imei_number);
                    $tt->setImeiNumberId($agent->getImeiNumberId());
                    $tt->setDibsCallId($dibsCall->getId());
                    $tt->save();

                    if (commissionLib::topUp($agent->getAgentCompanyId(), $card_number->getProduct()->getId(), $tt->getId(), true)) {
                        $vendor = VendorsPeer::retrieveByPK($tt->getVendorId());
                        if ($vendor->VendingMachinetopUp($tt, $card_number, $dibsCall)) {
                            $sms = SmsTextPeer::retrieveByPK(16)->getMessageText();
                            $sms = str_replace("(productname)", $card_number->getProduct()->getName(), $sms);
                            $sms = str_replace("(serial)", $card_number->getCardSerial(), $sms);
                            //$sms = "serial:" . $card_number->getCardSerial() . " Sent to customer mobile:" . $customerMobile;
                            if ($agent->getAgentCompany()->getIsPrepaid()) {
                                //$sms .= " \nYour Balance is " . $agent->getAgentCompany()->getBalance() . " kr";
                            }
                            echo "~" . wordwrap($sms, 32, "\n", true);
                            $balanceInfo = "\n\nAvailable Bal: " . number_format($agent->getAgentCompany()->getBalance(), 2) . " \n";
                            $balanceInfo.= "Actual Bal: " . number_format(-($agent->getAgentCompany()->getCreditLimit() - $agent->getAgentCompany()->getBalance()), 2) . " \n";
                            echo wordwrap(mb_convert_encoding($balanceInfo, "ISO-8859-15", mb_detect_encoding($balanceInfo, "auto")), 32, "\n", true);




                            emailLib::sendCustomerTopup($tt, "pScripts");
                        } else {
                            echo $sms = SmsTextPeer::retrieveByPK(19)->getMessageText();
                            $msg = "exception occured at imei number request:" . $imei_number . " message from sms text " . $sms . " dibs call id:" . $dibsCall->getId();
                            emailLib::sendErrorInTopUP($msg);
                        }
                    } else {
                        // low balance
                        echo $sms = commissionLib::topUp($agent->getAgentCompanyId(), $card_number->getProduct()->getId(), $tt->getId(), true, true);
                        $msg = "low balance of imei number :" . $imei_number . " message from sms text " . $sms . " dibs call id:" . $dibsCall->getId();
                        emailLib::sendErrorInTopUP($msg);
                    }
                } else {
                    // SMS Not Found for the specified Vendor.   
                    echo $sms = SmsTextPeer::retrieveByPK(31)->getMessageText();
                    $msg = "sms not found of the vending machine id of product:" . $item_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                    emailLib::sendErrorInTopUP($msg);

//emailLib::sendErrorInAutoReg("SMS not found", $message);
//                    ROUTED_SMS::Send($request->getParameter("mobile"), SmsTextPeer::retrieveByPK(18)->getMessageText());
                }
            } else {
                // Card Not Found......

                echo $sms = SmsTextPeer::retrieveByPK(2)->getMessageText();
                $msg = "Card not found of the vending machine id:" . $item_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                emailLib::sendErrorInTopUP($msg);
            }
        } else {
            // Agent Not Found..............
            $sms = SmsTextPeer::retrieveByPK(29)->getMessageText();
            echo $sms;
            $msg = "Imei Number not found:" . $imei_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
            emailLib::sendErrorInTopUP($msg);
        }
        $agentreceipt = $sms;
        $dibsCall->setAgentReceipt($agentreceipt);

        $dibsCall->save();
        return sfView::NONE;
    }

    public function executeVendingAgentTopUpRequest(sfWebRequest $request) {


        $urlval = "VendingAgnetTopUp - " . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setTransactionFromId(6);
        $dibsCall->setDecryptedData($request->getParameter("itemNumber") . "---" . $request->getParameter("machineid"));
        $dibsCall->save();
        $dibscallid = $dibsCall->getId();


        //   echo "Thanks Mathew For your help";
//        $data = explode("#", $decrypt);
//        $agentUniqueID = trim($data[1]);
//        $vendor = trim($data[2]);
//        $amount = trim($data[3]);
//        $customerMobile = trim($data[4]);

        $imei_number = $request->getParameter("machineid");
        $item_number = $request->getParameter("itemNumber");
        $c = new Criteria();

        /////////////need to put left join /////////////////
        $c->addJoin(AgentUserPeer::IMEI_NUMBER_ID, ImeiNumbersPeer::ID, Criteria::LEFT_JOIN);
        $c->addAnd(ImeiNumbersPeer::IMEI_NUMBER, $imei_number);
        $c->add(AgentUserPeer::STATUS_ID, 1);

        if (AgentUserPeer::doCount($c) > 0) {
            $agent = AgentUserPeer::doSelectOne($c);


            $ap = new Criteria();
            $ap->add(AgentProductPeer::AGENT_ID, $agent->getAgentCompanyId());
            $product_criteria = new Criteria();
            if (AgentProductPeer::doCount($ap) > 0) {
                $product_criteria->addJoin(ProductPeer::ID, AgentProductPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
                $product_criteria->addAnd(AgentProductPeer::AGENT_ID, $agent->getAgentCompanyId());
                $product_criteria->addAnd(ProductPeer::VENDING_MACHINE_ID, $item_number);
                $product_criteria->addAnd(ProductPeer::IS_IN_STORE, true);
                //  echo "---".ProductPeer::doCount($product_criteria);die;
                if (ProductPeer::doCount($product_criteria) == 0) {
                    echo $smst = SmsTextPeer::retrieveByPK(36)->getMessageText();
                    $dibsCall->setAgentReceipt($smst);
                    $msg = "Product not assigned in agent per product event occured at imei number request:" . $imei_number . " and vending machine id of product: " . $item_number . " message from sms text " . $smst . " All Request id:" . $dibsCall->getId();
                    emailLib::sendErrorInTopUP($msg);
                    $dibsCall->save();
                    return sfView::NONE;
                }
            }

            $prc = new Criteria();
            $prc->add(ProductPeer::VENDING_MACHINE_ID, $item_number);
            $prc->addAnd(ProductPeer::STATUS_ID, 1, Criteria::NOT_EQUAL);
            if (ProductPeer::doCount($prc) > 0) {
                echo $smst = SmsTextPeer::retrieveByPK(24)->getMessageText();
                $dibsCall->setAgentReceipt($smst);
                $msg = "Deactivated product event occured at imei number request:" . $imei_number . " and vending machine id of product: " . $item_number . " message from sms text " . $smst . " All Request id:" . $dibsCall->getId();
                emailLib::sendErrorInTopUP($msg);
                $dibsCall->save();
                return sfView::NONE;
            }


            $Prod3 = new Criteria();
            $Prod3->addJoin(CardNumbersPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
            $Prod3->addAnd(ProductPeer::VENDING_MACHINE_ID, $item_number);
            $Prod3->addAnd(CardNumbersPeer::STATUS, 0);


            $Prod = new Criteria();
            $Prod->addJoin(CardNumbersPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
            $Prod->addAnd(ProductPeer::VENDING_MACHINE_ID, $item_number);
            $Prod->addAnd(CardNumbersPeer::STATUS, 0);


            if (CardNumbersPeer::doCount($Prod3) > 0) {

                ////////////////////////////kmmalik code/////////////////////////////////
                $Prod->addAnd(CardNumbersPeer::DIBS_ID, NULL);
                $card_number = CardNumbersPeer::doSelectOne($Prod);
                $card_number->setDibsId($dibscallid);
                $card_number->save();
                ////////////////////////////kmmalik code/////////////////////////////////


                $sc = new Criteria();
                $sc->addJoin(SmsTextPeer::VENDOR_ID, VendorsPeer::ID);
                $sc->addJoin(VendorsPeer::ID, ProductPeer::VENDOR_ID);
                $sc->add(ProductPeer::VENDING_MACHINE_ID, $item_number);
                if (SmsTextPeer::doCount($sc) > 0) {


                    ////////////////////////////kmmalik code/////////////////////////////////
                    $type = 6;
                    do {
                        $Prod1 = new Criteria();
                        $Prod1->addJoin(CardNumbersPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
                        $Prod1->addAnd(ProductPeer::VENDING_MACHINE_ID, $item_number);
                        $Prod1->addAnd(CardNumbersPeer::STATUS, 0);
                        $Prod1->addAnd(CardNumbersPeer::DIBS_ID, $dibscallid);
                        if (CardNumbersPeer::doCount($Prod1) > 0) {
                            $type = 5;
                        } else {
                            $Prod = new Criteria();
                            $Prod->addJoin(CardNumbersPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
                            $Prod->addAnd(ProductPeer::VENDING_MACHINE_ID, $item_number);
                            $Prod->addAnd(CardNumbersPeer::STATUS, 0);

                            ////////////////////////////kmmalik code/////////////////////////////////
                            $Prod->addAnd(CardNumbersPeer::DIBS_ID, NULL);
                            if (CardNumbersPeer::doCount($Prod) > 0) {
                                $card_number = CardNumbersPeer::doSelectOne($Prod);
                                $card_number->setDibsId($dibscallid);
                                $card_number->save();
                            } else {
                                // Card Not Found......

                                $sms = SmsTextPeer::retrieveByPK(2)->getMessageText();
                                $msg = "Card not found of the vending machine id:" . $item_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                                emailLib::sendErrorInTopUP($msg);

                                $type = 1;
                                exit;
                            }
                        }
                    } while ($type != 5);

                    if ($type == 1) {
                        echo $sms;
                        $dibsCall->setAgentReceipt($sms);
                        $dibsCall->save();
                        $msg = "Card not found of the vending machine id:" . $item_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                        emailLib::sendErrorInTopUP($msg);
                        die;
                    }

                    $card_number = CardNumbersPeer::doSelectOne($Prod1);
                    $card_number->setDibsId($dibscallid);
                    $card_number->save();



                    ////////////////////////////kmmalik code/////////////////////////////////
                    /////////////////////////////////////////////
                    //    $conn = Propel::getConnection();
//    $query = 'update card_number set dibs_is="'.$dibscallid.'" where dibs_is  IS NULL';
//    
//     $statement = $conn->prepare($query);
//    $statement->execute();
                    //////////////////////////////////////////////
                    $sms = SmsTextPeer::doSelectOne($sc);
                    $tt = new TopupTransactions();
                    $tt->setAgentCompanyId($agent->getAgentCompanyId());
                    $tt->setProductId($card_number->getProduct()->getId());
                    $tt->setProductName($card_number->getProduct()->getName());
                    $tt->setProductVat($card_number->getProduct()->getVat());
                    $tt->setProductPrice($card_number->getProduct()->getPrice());
                    $tt->setProductRegistrationFee($card_number->getProduct()->getRegistrationFee());
                    $tt->setCustomerMobileNumber($customerMobile);
                    $tt->setAgentUserId($agent->getId());
                    $tt->setVendorId($card_number->getProduct()->getVendorId());
                    $tt->setVenderName($card_number->getProduct()->getVendors()->getTitle());
                    $tt->setCardPurchasePrice($card_number->getCardPurchasePrice());
                    $tt->setAgentEmail($agent->getAgentCompany()->getEmail());
                    $tt->setAgentCompanyName($agent->getAgentCompany()->getName());
                    $tt->setAgentUserName($agent->getUsername());
                    $reseller = ResellerPeer::retrieveByPK($agent->getAgentCompany()->getResellerId());
                    $tt->setResellerId($reseller->getId());
                    $tt->setResellerName($reseller->getName());
                    $tt->setResellerEmail($reseller->getEmail());
                    $tt->setResellerContactNumber($reseller->getContactNumber());
                    $tt->setTransactionFromId(6);
                    $tt->setStatus(1);
                    $tt->setTransactionFromId(6);
                    $tt->setImeiNumber($imei_number);
                    $tt->setImeiNumberId($agent->getImeiNumberId());
                    $tt->setDibsCallId($dibsCall->getId());
                    $tt->save();

                    if (commissionLib::topUp($agent->getAgentCompanyId(), $card_number->getProduct()->getId(), $tt->getId(), true)) {
                        $vendor = VendorsPeer::retrieveByPK($tt->getVendorId());
                        if ($vendor->VendingMachinetopUp($tt, $card_number, $dibsCall)) {
                            $sms = SmsTextPeer::retrieveByPK(16)->getMessageText();
                            $sms = str_replace("(productname)", $card_number->getProduct()->getName(), $sms);
                            $sms = str_replace("(serial)", $card_number->getCardSerial(), $sms);
                            //$sms = "serial:" . $card_number->getCardSerial() . " Sent to customer mobile:" . $customerMobile;
                            if ($agent->getAgentCompany()->getIsPrepaid()) {
                                //$sms .= " \nYour Balance is " . $agent->getAgentCompany()->getBalance() . " kr";
                            }
                            echo "~" . wordwrap($sms, 32, "\n", true);
                            $balanceInfo = "\n\nAvailable Bal: " . number_format($agent->getAgentCompany()->getBalance(), 2) . " \n";
                            $balanceInfo.= "Actual Bal: " . number_format(-($agent->getAgentCompany()->getCreditLimit() - $agent->getAgentCompany()->getBalance()), 2) . " \n";
                            echo wordwrap(mb_convert_encoding($balanceInfo, "ISO-8859-15", mb_detect_encoding($balanceInfo, "auto")), 32, "\n", true);




                            emailLib::sendCustomerTopup($tt, "pScripts");
                        } else {
                            echo $sms = SmsTextPeer::retrieveByPK(19)->getMessageText();
                            $msg = "exception occured at imei number request:" . $imei_number . " message from sms text " . $sms . " dibs call id:" . $dibsCall->getId();
                            emailLib::sendErrorInTopUP($msg);
                        }
                    } else {
                        // low balance
                        echo $sms = commissionLib::topUp($agent->getAgentCompanyId(), $card_number->getProduct()->getId(), $tt->getId(), true, true);
                        $msg = "low balance of imei number :" . $imei_number . " message from sms text " . $sms . " dibs call id:" . $dibsCall->getId();
                        emailLib::sendErrorInTopUP($msg);
                        $card_number->setDibsId(null);
                        $card_number->save();
                    }
                } else {
                    // SMS Not Found for the specified Vendor.   
                    echo $sms = SmsTextPeer::retrieveByPK(31)->getMessageText();
                    $msg = "sms not found of the vending machine id of product:" . $item_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                    emailLib::sendErrorInTopUP($msg);
                    $card_number->setDibsId(null);
                    $card_number->save();

//emailLib::sendErrorInAutoReg("SMS not found", $message);
//                    ROUTED_SMS::Send($request->getParameter("mobile"), SmsTextPeer::retrieveByPK(18)->getMessageText());
                }
            } else {
                // Card Not Found......

                echo $sms = SmsTextPeer::retrieveByPK(2)->getMessageText();
                $msg = "Card not found of the vending machine id:" . $item_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                emailLib::sendErrorInTopUP($msg);
            }
        } else {
            // Agent Not Found..............
            $sms = SmsTextPeer::retrieveByPK(29)->getMessageText();
            echo $sms;
            $msg = "Imei Number not found:" . $imei_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
            emailLib::sendErrorInTopUP($msg);
        }
        $agentreceipt = $sms;
        $dibsCall->setAgentReceipt($agentreceipt);

        $dibsCall->save();
        return sfView::NONE;
    }

    public function executeVendingAgentReprintLastTrnx(sfWebRequest $request) {
        $urlval = "VendingAgnettReprintLastTrnx - " . $request->getURI();
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->setTransactionFromId(6);
        $dibsCall->setDecryptedData($request->getParameter("machineid"));
        $dibsCall->save();

        $imei_number = $request->getParameter("machineid");
        $c = new Criteria();

        /////////////need to put left join /////////////////
        $c->addJoin(AgentUserPeer::IMEI_NUMBER_ID, ImeiNumbersPeer::ID, Criteria::LEFT_JOIN);
        $c->addAnd(ImeiNumbersPeer::IMEI_NUMBER, $imei_number);
        $c->add(AgentUserPeer::STATUS_ID, 1);

        if (AgentUserPeer::doCount($c) > 0) {
            $agent = AgentUserPeer::doSelectOne($c);
            $t = new Criteria();
            $t->addAnd(TopupTransactionsPeer::AGENT_USER_ID, $agent->getId());
            $t->addAnd(TopupTransactionsPeer::TRANSACTION_FROM_ID, 6);
            $t->addAnd(TopupTransactionsPeer::STATUS, 3);
            $t->addDescendingOrderByColumn(TopupTransactionsPeer::ID);
            $transaction = TopupTransactionsPeer::doselectone($t);

            $dc = DibsCallPeer::retrieveByPK($transaction->getDibsCallId());
            $receipt = mb_convert_encoding($dc->getCustomerReceipt(), "ISO-8859-1", mb_detect_encoding($dc->getCustomerReceipt(), "auto")) . " ~ " . mb_convert_encoding($dc->getAgentReceipt(), "ISO-8859-1", mb_detect_encoding($dc->getAgentReceipt(), "auto"));
            echo wordwrap($receipt, 32, "\n", true);
            $dibsCall->setCustomerReceipt($dc->getCustomerReceipt());
            $dibsCall->setAgentReceipt($dc->getAgentReceipt());
        } else {
            // Agent Not Found..............
            $sms = SmsTextPeer::retrieveByPK(29)->getMessageText();
            $sms = mb_convert_encoding($sms, "ISO-8859-1", mb_detect_encoding($sms, "auto"));
            echo wordwrap($sms, 32, "\n", true);
            $msg = "Imei Number not found:" . $imei_number . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
            emailLib::sendErrorInTopUP($msg);
            $dibsCall->setAgentReceipt($sms);
        }



        $dibsCall->save();
        return sfView::NONE;
    }

    private function hextobin($hexstr) {
        $n = strlen($hexstr);
        $sbin = "";
        $i = 0;
        while ($i < $n) {
            $a = substr($hexstr, $i, 2);
            $c = pack("H*", $a);
            if ($i == 0) {
                $sbin = $c;
            } else {
                $sbin.=$c;
            }
            $i+=2;
        }
        return $sbin;
    }

    public function executeAppLogin(sfWebRequest $request) {

        $user = $request->getParameter("username");
        $pass = $request->getParameter("password");
        $pin = $request->getParameter("pincode");
        $urlval = "AppLogin-" . $request->getURI() . "?username=" . $user . "&password=" . $pass . "&pincode=" . $pin;
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->save();
        $user = $this->decryptData($request->getParameter("username"));
        $pass = $this->decryptData($request->getParameter("password"));
        $pin = $this->decryptData($request->getParameter("pincode"));
        $dibsCall->setDecryptedData("username=" . $user . "&password=" . $pass . "&pincode=" . $pin);
        $dibsCall->save();


        $c = new Criteria();
        $c->add(AgentUserPeer::USERNAME, $user);
        $c->add(AgentUserPeer::STATUS_ID, 1);
        $c->add(AgentUserPeer::PIN_CODE, $pin);
        $c->add(AgentUserPeer::PASSWORD, $pass);

        if (AgentUserPeer::doCount($c) > 0) {
            $resultado[] = array("logstatus" => 1);
        } else {
            $resultado[] = array("logstatus" => "0");
        }
        echo json_encode($resultado);
        return sfView::NONE;
    }

    public function executeAppGetBalance(sfWebRequest $request) {
        $user = $request->getParameter("username");
        $pass = $request->getParameter("password");
        $pin = $request->getParameter("pincode");
        $urlval = "AppBalance-" . $request->getURI() . "?username=" . $user . "&password=" . $pass . "&pincode=" . $pin;
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->save();

        $user = $this->decryptData($request->getParameter("username"));
        $pass = $this->decryptData($request->getParameter("password"));
        $pin = $this->decryptData($request->getParameter("pincode"));
        $dibsCall->setDecryptedData("username=" . $user . "&password=" . $pass . "&pincode=" . $pin);
        $dibsCall->save();



        $c = new Criteria();
        $c->addJoin(AgentCompanyPeer::ID, AgentUserPeer::AGENT_COMPANY_ID);
        $c->add(AgentUserPeer::USERNAME, $user);
        $c->add(AgentUserPeer::STATUS_ID, 1);
        $c->add(AgentUserPeer::PIN_CODE, $pin);
        $c->add(AgentUserPeer::PASSWORD, $pass);

        if (AgentCompanyPeer::doCount($c) > 0) {
            $agentCompany = AgentCompanyPeer::doSelectOne($c);
            $balance = $agentCompany->getBalance() - $agentCompany->getCreditLimit();
            if ($agentCompany->getBalance() == null)
                $balance = 0;
            $resultado[] = array("balance" => $balance, "isPrepaid" => 1);
        }else {
            $resultado[] = array("balance" => -1000.0);
        }
        echo json_encode($resultado);
        return sfView::NONE;
    }

    public function executeAppGetVendors(sfWebRequest $request) {
        $user = $request->getParameter("username");
        $pass = $request->getParameter("password");
        $pin = $request->getParameter("pincode");
        $urlval = "AppGetVendors-" . $request->getURI() . "?username=" . $user . "&password=" . $pass . "&pincode=" . $pin;
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->save();
        $user = $this->decryptData($request->getParameter("username"));
        $pass = $this->decryptData($request->getParameter("password"));
        $pin = $this->decryptData($request->getParameter("pincode"));
        $dibsCall->setDecryptedData("username=" . $user . "&password=" . $pass . "&pincode=" . $pin);
        $dibsCall->save();


        $c = new Criteria();
        $c->addJoin(AgentCompanyPeer::ID, AgentUserPeer::AGENT_COMPANY_ID);
        $c->add(AgentUserPeer::USERNAME, $user);
        $c->add(AgentUserPeer::STATUS_ID, 1);
        $c->add(AgentUserPeer::PIN_CODE, $pin);
        $c->add(AgentUserPeer::PASSWORD, $pass);

        if (AgentCompanyPeer::doCount($c) > 0) {
            $agentCompany = AgentCompanyPeer::doSelectOne($c);

            $dc = new Criteria();
            $dc->add(AgentProductPeer::AGENT_ID, $agentCompany->getId());
            $temp = AgentProductPeer::doCount($dc);
            $pc = new Criteria();
            $vc = new Criteria();
            if ($temp > 0) {
                $pc->addJoin(ProductPeer::ID, AgentProductPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
                $pc->add(AgentProductPeer::AGENT_ID, $agentCompany->getId());
                $vc->addJoin(VendorsPeer::ID, ProductPeer::VENDOR_ID, Criteria::LEFT_JOIN);
                $vc->addJoin(ProductPeer::ID, AgentProductPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
                $vc->add(AgentProductPeer::AGENT_ID, $agentCompany->getId());
                $vc->add(ProductPeer::IS_IN_STORE, true);
                $vc->addGroupByColumn(VendorsPeer::TITLE);
            }

            $vendors = VendorsPeer::doSelect($vc);
            foreach ($vendors as $vendor) {
                $arrVendors[] = $vendor->getTitle();
            }


            $pc->add(ProductPeer::VENDOR_ID, $vendors[0]->getId());
            $pc->add(ProductPeer::IS_IN_STORE, true);
            $products = ProductPeer::doSelect($pc);


            foreach ($products as $product) {
                $arrProducts[] = $product->getName();
            }
            $resultado[] = array("vendors" => implode($arrVendors, ","), "products" => implode($arrProducts, ","));
        } else {
            $resultado[] = array("vendors" => "-1000.0", "products" => "-1000.0");
        }
        echo json_encode($resultado);
        return sfView::NONE;
    }

    public function executeAppGetProducts(sfWebRequest $request) {
        $user = $request->getParameter("username");
        $pass = $request->getParameter("password");
        $pin = $request->getParameter("pincode");
        $vendor = trim($request->getParameter("vendor"));
        $urlval = "AppGetProducts-" . $request->getURI() . "?username=" . $user . "&password=" . $pass . "&pincode=" . $pin . "&vendor=" . $vendor;
        $dibsCall = new DibsCall();
        $dibsCall->setCallurl($urlval);
        $dibsCall->save();
        $user = $this->decryptData($request->getParameter("username"));
        $pass = $this->decryptData($request->getParameter("password"));
        $pin = $this->decryptData($request->getParameter("pincode"));
        $vendor = $this->decryptData(trim($request->getParameter("vendor")));
        $dibsCall->setDecryptedData("username=" . $user . "&password=" . $pass . "&pincode=" . $pin . "&vendor=" . $vendor);
        $dibsCall->save();

        $c = new Criteria();
        $c->addJoin(AgentCompanyPeer::ID, AgentUserPeer::AGENT_COMPANY_ID);
        $c->add(AgentUserPeer::USERNAME, $user);
        $c->add(AgentUserPeer::STATUS_ID, 1);
        $c->add(AgentUserPeer::PIN_CODE, $pin);
        $c->add(AgentUserPeer::PASSWORD, $pass);

        if (AgentCompanyPeer::doCount($c) > 0) {
            $agentCompany = AgentCompanyPeer::doSelectOne($c);
            $vc = new Criteria();
            $vc->add(VendorsPeer::ID, $vendor);
            $vendor = VendorsPeer::doSelectOne($vc);

            $dc = new Criteria();
            $dc->add(AgentProductPeer::AGENT_ID, $agentCompany->getId());
            $temp = AgentProductPeer::doCount($dc);
            $pc = new Criteria();
            if ($temp > 0) {
                $pc->add(ProductPeer::IS_IN_STORE, true);
                $pc->addJoin(ProductPeer::ID, AgentProductPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
                $pc->add(AgentProductPeer::AGENT_ID, $agentCompany->getId());
            } else {
                $pc->add(ProductPeer::IS_IN_STORE, true);
            }
            $pc->add(ProductPeer::VENDOR_ID, $vendor->getId());
            $pc->add(ProductPeer::STATUS_ID, 1);
            if (ProductPeer::doCount($pc) > 0) {
                $products = ProductPeer::doSelect($pc);
                foreach ($products as $product) {
                    $arrProducts[] = $product->getRegistrationFee();
                }
                $resultado[] = array("products" => implode($arrProducts, ","));
            } else {
                $resultado[] = array("products" => "NotAvailable");
            }
        } else {
            $resultado[] = array("products" => "-1000.0");
        }
        echo json_encode($resultado);
        return sfView::NONE;
    }

    public function executeAppTopUpRequest(sfWebRequest $request) {
        $product = $request->getParameter("product");
        $customerMobile = $request->getParameter("mobileNumber");
        $user = $request->getParameter("username");
        $pass = $request->getParameter("password");
        $pin = $request->getParameter("pincode");
        $vendor = trim($request->getParameter("vendor"));
        $urlval = "AppTopUpRequest - " . $request->getURI() . "?username=" . $user . "&password=" . $pass . "&pincode=" . $pin . "&vendor=" . $vendor . "&product=" . $product . "&mobileNumber=" . $customerMobile;
        $dibsCall = new DibsCall();
        $dibsCall->setTransactionFromId(4);
        $dibsCall->setCallurl($urlval);
        $dibsCall->save();

        $user = $this->decryptData($request->getParameter("username"));
        $pass = $this->decryptData($request->getParameter("password"));
        $pin = $this->decryptData($request->getParameter("pincode"));
        $product = $this->decryptData($request->getParameter("product"));
        $customerMobile = $this->decryptData($request->getParameter("mobileNumber"));
        $vendor = $this->decryptData(trim($request->getParameter("vendor")));
        $dibsCall->setDecryptedData("username=" . $user . "&password=" . $pass . "&pincode=" . $pin . "&vendor=" . $vendor . "&product=" . $product . "&mobileNumber=" . $customerMobile);
        $dibsCall->save();

        $c = new Criteria();
        $c->add(AgentUserPeer::USERNAME, $user);
        $c->add(AgentUserPeer::STATUS_ID, 1);
        $c->add(AgentUserPeer::PIN_CODE, $pin);
        $c->add(AgentUserPeer::PASSWORD, $pass);


        if (AgentUserPeer::doCount($c) != 0) {
            //echo "Agent User Found";
            $agent = AgentUserPeer::doSelectOne($c);



            $pc = new Criteria();
            $pc->add(ProductPeer::REGISTRATION_FEE, $product);
            $pc->add(ProductPeer::VENDOR_ID, $vendor);
            $productObj = ProductPeer::doSelectOne($pc);

            $Prod = new Criteria();
            $Prod->addJoin(CardNumbersPeer::PRODUCT_ID, ProductPeer::ID, Criteria::LEFT_JOIN);
            $Prod->add(ProductPeer::REGISTRATION_FEE, $product);
            $Prod->add(ProductPeer::VENDOR_ID, $vendor);
            $Prod->addAnd(CardNumbersPeer::STATUS, 0);
            $Prod->addAnd(CardNumbersPeer::CARD_TYPE_ID, 1);
            if (CardNumbersPeer::doCount($Prod) > 0) {

                // echo "card number found";
                $sc = new Criteria();
                $sc->add(SmsTextPeer::VENDOR_ID, $productObj->getVendorId());
                if (SmsTextPeer::doCount($sc) > 0) {
                    $card_number = CardNumbersPeer::doSelectOne($Prod);
                    $sms = SmsTextPeer::doSelectOne($sc);
                    $tt = new TopupTransactions();
                    $tt->setAgentCompanyId($agent->getAgentCompanyId());
                    $tt->setProductId($card_number->getProduct()->getId());
                    $tt->setProductName($card_number->getProduct()->getName());
                    $tt->setProductVat($card_number->getProduct()->getVat());
                    $tt->setProductPrice($card_number->getProduct()->getPrice());
                    $tt->setProductRegistrationFee($card_number->getProduct()->getRegistrationFee());
                    $tt->setCustomerMobileNumber($customerMobile);
                    $tt->setAgentUserId($agent->getId());
                    $tt->setVendorId($card_number->getProduct()->getVendorId());
                    $tt->setVenderName($card_number->getProduct()->getVendors()->getTitle());
                    $tt->setCardPurchasePrice($card_number->getCardPurchasePrice());
                    $tt->setAgentEmail($agent->getAgentCompany()->getEmail());
                    $tt->setAgentCompanyName($agent->getAgentCompany()->getName());
                    $tt->setAgentUserName($agent->getUsername());
                    $reseller = ResellerPeer::retrieveByPK($agent->getAgentCompany()->getResellerId());
                    $tt->setResellerId($reseller->getId());
                    $tt->setResellerName($reseller->getName());
                    $tt->setResellerEmail($reseller->getEmail());
                    $tt->setResellerContactNumber($reseller->getContactNumber());
                    $tt->setTransactionFromId(4);
                    $tt->setStatus(1);
                    $tt->setDibsCallId($dibsCall->getId());
                    $tt->save();
                    if (commissionLib::topUp($agent->getAgentCompanyId(), $card_number->getProduct()->getId(), $tt->getId(), true)) {
                        // echo "Agent Successfully Topped Up";
                        $vendor = VendorsPeer::retrieveByPK($tt->getVendorId());
                        if ($vendor->SmartPhoneApptopUp($tt, $card_number, $dibsCall, $customerMobile)) {
                            $sms = SmsTextPeer::retrieveByPK(16)->getMessageText();
                            $sms = str_replace("(productname)", $card_number->getProduct()->getName(), $sms);
                            $sms = str_replace("(mobilenumber)", $customerMobile, $sms);
                            $sms = str_replace("(serial)", $card_number->getCardSerial(), $sms);
                            //$sms = "serial:" . $card_number->getCardSerial() . " Sent to customer mobile:" . $customerMobile;
//                            if ($agent->getAgentCompany()->getIsPrepaid()) {
//                                $sms .= " Your Balance is " . $agent->getAgentCompany()->getBalance() - $agent->getAgentCompany()->getCreditLimit() . " kr";
//                            }
//                            ROUTED_SMS::Send($request->getParameter("mobile"), $sms);
                            $resultado[] = array("success" => $sms);
                            emailLib::sendCustomerTopup($tt, "pScripts");
                        } else {
                            $sms = SmsTextPeer::retrieveByPK(19)->getMessageText();
                            $resultado[] = array("error" => $sms);
                            $msg = "exception occuered at vendor id:" . $vendor . "  and product amount:" . $product . " user :" . $user . "pin: " . $pin . " pass:" . $pass . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                            emailLib::sendErrorInTopUP($msg);
                        }
                    } else {
                        //echo "Low Agent Balance";
                        $sms = commissionLib::topUp($agent->getAgentCompanyId(), $card_number->getProduct()->getId(), $tt->getId(), true, true);
                        $resultado[] = array("error" => $sms);
                        $msg = "Low balance alert of user :" . $user . "pin: " . $pin . " pass:" . $pass . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                        emailLib::sendErrorInTopUP($msg);
                    }
                } else {
                    // SMS Not Found for the specified Vendor.   

                    $sms = SmsTextPeer::retrieveByPK(31)->getMessageText();
                    $resultado[] = array("error" => $sms);
                    emailLib::sendErrorInAutoReg("SMS not found", $message);
                    $msg = "Vendor sms not found User Information not found where vendor id:" . $vendor . " user :" . $user . "pin: " . $pin . " pass:" . $pass . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                    emailLib::sendErrorInTopUP($msg);
                }
            } else {
                // Card Not Found......
                //echo "Card Not Found";
                $sms = SmsTextPeer::retrieveByPK(2)->getMessageText();
                $resultado[] = array("error" => $sms);
                $msg = "User Information not found user :" . $user . "pin: " . $pin . " pass:" . $pass . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
                emailLib::sendErrorInTopUP($msg);
            }
        } else {
            // Agent Not Found..............
            $sms = SmsTextPeer::retrieveByPK(29)->getMessageText();
            $resultado[] = array("error" => $sms);
            $msg = "User Information not found user :" . $user . "pin: " . $pin . " pass:" . $pass . " message from sms text " . $sms . " All Request id:" . $dibsCall->getId();
            emailLib::sendErrorInTopUP($msg);
        }


        echo json_encode($resultado);
        return sfView::NONE;
    }

    /*
     * This method is being used for decryption of the data for app. If you need to use the following decryption 
     * you will have to encrypt using the same values
     * 
     * <b> please don't change this method otherwise app will be stucked.</b>
     * 
     * 
     */

    private function decryptData($string) {
        $cipher = "rijndael-128";
        $mode = "cbc";
        $secret_key = "MusfirahKhan";
        //iv length should be 16 bytes 
        $iv = "fedcba9876543210";

        // Make sure the key length should be 16 bytes 
        $key_len = strlen($secret_key);
        if ($key_len < 16) {
            $addS = 16 - $key_len;
            for ($i = 0; $i < $addS; $i++) {
                $secret_key.=" ";
            }
        } else {
            $secret_key = substr($secret_key, 0, 16);
        }

        $td = mcrypt_module_open($cipher, "", $mode, $iv);
        mcrypt_generic_init($td, $secret_key, $iv);
        $decrypted_text = mdecrypt_generic($td, $this->hextobin($string));


        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return trim($decrypted_text);
    }

    function executeGenerateInvoiceMonthly(sfWebRequest $request) {
        //   $startdate =  date('Y-m-1 00:00:00', strtotime("last month"));
        //     $enddate = date('Y-m-t 23:59:59', strtotime("last month"));
//         $startdate = date('Y-07-08 00:00:00');
//         $enddate = date('Y-07-14 23:59:59');

        $startdate = date('Y-m-d 00:00:00', $request->getParameter('start_date'));
        $enddate = date('Y-m-d 23:59:59', $request->getParameter('end_date'));

//        $start_strtotime = strtotime($startdate);
//        $end_strototime = strtotime($enddate);

        $start_strtotime = strtotime($startdate);
        $end_strototime = strtotime($enddate);
        echo $startdate;
        echo ' - ';
        echo $enddate;
        echo '<br />';

        // die;
        $c = new Criteria();
        $companies = AgentCompanyPeer::doSelect($c);

        foreach ($companies as $company) {
            echo $company->getId() . "::::";
            echo $created_date = $company->getCreatedAt();

            echo "<br/>";

            $cl = new Criteria();
            $cl->addAnd(TopupTransactionsPeer::CREATED_AT, $startdate, Criteria::GREATER_EQUAL);
            $cl->addAnd(TopupTransactionsPeer::CREATED_AT, $enddate, Criteria::LESS_EQUAL);
            $cl->addAnd(TopupTransactionsPeer::AGENT_COMPANY_ID, $company->getId());
            $cl->addAnd(TopupTransactionsPeer::STATUS, 3);
            $nofCards = TopupTransactionsPeer::doCount($cl);
            echo "number of cards---" . $nofCards;
            if ($nofCards > 0) {
                echo "----companyid------" . $company->getId();
                echo "<br/>";
                echo $url1 = sfConfig::get('app_b2c_url') . 'pScripts/invoiceBilling?company_id=' . $company->getId() . '&start_date=' . $start_strtotime . '&end_date=' . $end_strototime;
                $invoice = file_get_contents($url1);
            }echo "<br/>";
        }
        return sfView::NONE;
    }

    public function executeInvoiceBilling(sfWebRequest $request) {
        $company_id = $request->getParameter('company_id');

        $this->billing_start_date = date('Y-m-d 00:00:00', $request->getParameter('start_date'));
        $this->billing_end_date = date('Y-m-d 23:59:59', $request->getParameter('end_date'));
        $this->forward404Unless($company_id && $this->billing_start_date && $this->billing_end_date);

        if (!($company = AgentCompanyPeer::retrieveByPK($company_id))) {
            $this->forward404();
        }
        $billings = array();
        $ratings = array();
        $bilcharge = 00.00;

        $cnb = new Criteria();
        $cnb->add(AgentCompanyNetBalancePeer::BILL_START, $this->billing_start_date);
        $cnb->addAnd(AgentCompanyNetBalancePeer::BILL_END, $this->billing_end_date);
        $cnb->addAnd(AgentCompanyNetBalancePeer::COMPANY_ID, $company_id);
        if (AgentCompanyNetBalancePeer::doCount($cnb) > 0) {
            $netbalance = AgentCompanyNetBalancePeer::doSelectOne($cnb);
            $net = $netbalance->getNetBalance();
            $this->netbalance = $net;
        } else {
            $this->netbalance = 0.00;
        }



        $billing_details = array();
        $this->details = $billing_details;

        $invoice_id = 2;
        $im = new Criteria();
        $im->add(InvoiceMethodPeer::ID, $invoice_id);
        $invoice = InvoiceMethodPeer::doSelectOne($im);
        $this->invoice_cost = $invoice->getCost();


        $epc = new Criteria();
        $epc->add(AgentCompanyInvoicePeer::AGENT_COMPANY_ID, $company->getId());
        $epc->addAnd(AgentCompanyInvoicePeer::BILLING_STARTING_DATE, $this->billing_start_date);
        $epc->addAnd(AgentCompanyInvoicePeer::BILLING_ENDING_DATE, $this->billing_end_date);
        $agentCompanyInvoiceCount = AgentCompanyInvoicePeer::doCount($epc);

        if ($agentCompanyInvoiceCount > 0) {
            $new_invoice = AgentCompanyInvoicePeer::doSelectOne($epc);
        } else {
            $new_invoice = new AgentCompanyInvoice();
        }
        $new_invoice->setAgentCompanyId($company_id);
        $new_invoice->setResellerId($company->getResellerId());
        $new_invoice->setIsPrepaid($company->getIsPrepaid());

        $new_invoice->setBillingStartingDate($this->billing_start_date);
        $new_invoice->setBillingEndingDate($this->billing_end_date);
        $new_invoice->setStartTime($request->getParameter('start_date'));
        $new_invoice->setEndTime($request->getParameter('end_date'));
        $billing_due_days = $invoice->getBillingdays();
        $due_date = date("Y-m-d H:i:s", time() + ((60 * 60) * 24) * $billing_due_days);
        $new_invoice->setDueDate($due_date);
        $new_invoice->setInvoiceStatusId(4); // inactive
        $new_invoice->save();

        $imn = new Criteria();

        $imn->addDescendingOrderByColumn(InvoiceNumberPeer::ID);
        $invoicenumberCount = InvoiceNumberPeer::doCount($imn);
        if ($invoicenumberCount > 0) {
            $lastInvoiceNumber = InvoiceNumberPeer::doSelectOne($imn);
            $inVoiceN = $lastInvoiceNumber->getId();
        } else {
            $inVoiceN = 0;
        }
        $invoiceNumber = $inVoiceN + 1;
        $new_invoice->setInvoiceNumber($invoiceNumber);


        $new_invoice->save();
        //   var_dump($new_invoice);
        $this->invoice_meta = $new_invoice;
        $this->company_meta = $company;
        $cpay = new Criteria();
        $cpay->add(AgentOrderPeer::AGENT_COMPANY_ID, $company_id);

        $cpay->addAnd(AgentOrderPeer::STATUS, 3);

        $cpay->addAnd(AgentOrderPeer::TRANSACTION_TYPE_ID, 6, Criteria::NOT_EQUAL);
        $cpay->addAnd(AgentOrderPeer::CREATED_AT, $this->billing_start_date, Criteria::GREATER_EQUAL);
        $cpay->addAnd(AgentOrderPeer::CREATED_AT, $this->billing_end_date, Criteria::LESS_EQUAL);
        $cpay->addDescendingOrderByColumn(AgentOrderPeer::CREATED_AT);
        $paycount = AgentOrderPeer::doCount($cpay);
        $this->payCount = $paycount;
        if ($paycount > 0) {
            $this->payments = AgentOrderPeer::doSelect($cpay);
        }

        ///// Select previous invoices
        $cip = new Criteria();
        $cip->add(AgentCompanyInvoicePeer::BILLING_ENDING_DATE, $this->billing_end_date, Criteria::LESS_THAN);
        $cip->addAnd(AgentCompanyInvoicePeer::AGENT_COMPANY_ID, $company_id);
        $cip->setLimit(10);
        $cip->addDescendingOrderByColumn(AgentCompanyInvoicePeer::BILLING_STARTING_DATE);
        $invoiceCount = AgentCompanyInvoicePeer::doCount($cip);

        $this->invoiceCount = $invoiceCount;
        if ($invoiceCount > 0) {
            $preInvoices = AgentCompanyInvoicePeer::doSelect($cip);
            $this->preInvoices = $preInvoices;
        }
        $this->setLayout(false);
    }

    public function executeAgentCompanyNetBalance(sfWebRequest $request) {

        //    $start_date =  date('Y-m-1 00:00:00', strtotime("last month"));
        //      $end_date = date('Y-m-t 23:59:59', strtotime("last month"));
//        $start_date = date('Y-07-08 00:00:00');
//        $end_date = date('Y-07-14 23:59:59');
        $start_date = date('Y-m-d 00:00:00', $request->getParameter('start_date'));
        $end_date = date('Y-m-d 23:59:59', $request->getParameter('end_date'));

//        $start_date = date('2013-06-24 00:00:00');
//        $end_date = date('2013-06-30 23:59:59');


        $cco = new Criteria();
        $companies = AgentCompanyPeer::doSelect($cco);
        $net_balance = 0.00;
        foreach ($companies as $company) {
            $ci = new Criteria();
            $ci->add(AgentCompanyInvoicePeer::AGENT_COMPANY_ID, $company->getId());
            $ci->add(AgentCompanyInvoicePeer::BILLING_ENDING_DATE, $end_date, Criteria::LESS_EQUAL);
            $ci->addSelectColumn('sum(' . AgentCompanyInvoicePeer::TOTALPAYMENT . ') AS total_payment');
            $sum = AgentCompanyInvoicePeer::doSelectStmt($ci);
            $resultset = $sum->fetch(PDO::FETCH_OBJ);
            $total_payment = $resultset->total_payment;

            $co = new Criteria();
            $co->add(AgentOrderPeer::AGENT_COMPANY_ID, $company->getId());
            $co->addAnd(AgentOrderPeer::CREATED_AT, $end_date, Criteria::LESS_EQUAL);
            $co->addAnd(AgentOrderPeer::STATUS, 3);
            $co->addAnd(AgentOrderPeer::TRANSACTION_TYPE_ID, 6, Criteria::NOT_EQUAL);
            $co->addSelectColumn('sum(' . AgentOrderPeer::AMOUNT . ') AS total_charged_amount');
            $sum_camount = AgentOrderPeer::doSelectStmt($co);
            $rs = $sum_camount->fetch(PDO::FETCH_OBJ);
            $total_charged_amount = $rs->total_charged_amount;
            echo 'companyid-' . $company->getId() . ' - ' . $total_payment . ' - ' . $total_charged_amount;
            echo '<br />';
            echo $net_balance = $total_payment - $total_charged_amount;



            $cpay = new Criteria();
            $cpay->add(AgentCompanyNetBalancePeer::COMPANY_ID, $company->getId());
            $cpay->addAnd(AgentCompanyNetBalancePeer::BILL_START, $start_date);
            $cpay->addAnd(AgentCompanyNetBalancePeer::BILL_END, $end_date);
            $companyBalanceCount = AgentCompanyNetBalancePeer::doCount($cpay);
            if ($companyBalanceCount > 0) {

                $cnb = AgentCompanyNetBalancePeer::doSelectOne($cpay);
                $cnb->setBillStart($start_date);
                $cnb->setBillEnd($end_date);
                $cnb->setCompanyId($company->getId());
                $cnb->setNetBalance($net_balance);
                $cnb->save();
            } else {
                $cnb = new AgentCompanyNetBalance();
                $cnb->setBillStart($start_date);
                $cnb->setBillEnd($end_date);
                $cnb->setCompanyId($company->getId());
                $cnb->setNetBalance($net_balance);
                $cnb->save();
            }
        }
        return sfView::NONE;
    }

    public function executeUpdateInvoiceBilling(sfWebRequest $request) {


        $invoiceId = $request->getParameter('invoiceid');
        $invoice = AgentCompanyInvoicePeer::retrieveByPK($invoiceId);
        $company = $invoice->getAgentCompany();
        $this->billing_start_date = $invoice->getBillingStartingDate();
        $this->billing_end_date = $invoice->getBillingEndingDate();
        $billings = array();
        $ratings = array();
        $bilcharge = 00.00;
        $cnb = new Criteria();
        $cnb->add(AgentCompanyNetBalancePeer::BILL_START, $this->billing_start_date);
        $cnb->addAnd(AgentCompanyNetBalancePeer::BILL_END, $this->billing_end_date);
        $cnb->addAnd(AgentCompanyNetBalancePeer::COMPANY_ID, $company->getId());
        // $netbalance = CompanyNetBalancePeer::doSelectOne($cnb);
        if (AgentCompanyNetBalancePeer::doCount($cnb) > 0) {
            $netbalance = AgentCompanyNetBalancePeer::doSelectOne($cnb);
            $net = $netbalance->getNetBalance();
            $this->netbalance = $net;
        } else {
            $this->netbalance = 0.00;
        }
        $this->invoice_meta = $invoice;
        $this->company_meta = $company;
        $cpay = new Criteria();
        $cpay->add(AgentOrderPeer::AGENT_COMPANY_ID, $company->getId());
        $cpay->addAnd(AgentOrderPeer::STATUS, 3);
        //$cpay->setLimit(10);
        $cpay->addAnd(AgentOrderPeer::CREATED_AT, $this->billing_start_date, Criteria::GREATER_EQUAL);
        $cpay->addAnd(AgentOrderPeer::CREATED_AT, $this->billing_end_date, Criteria::LESS_EQUAL);
        $cpay->addDescendingOrderByColumn(AgentOrderPeer::CREATED_AT);
        $paycount = AgentOrderPeer::doCount($cpay);
        $this->payCount = $paycount;
        if ($paycount > 0) {
            $this->payments = AgentOrderPeer::doSelect($cpay);
        }

        ///// Select previous invoices
        $cip = new Criteria();
        $cip->add(AgentCompanyInvoicePeer::BILLING_ENDING_DATE, $this->billing_end_date, Criteria::LESS_THAN);
        $cip->addAnd(AgentCompanyInvoicePeer::AGENT_COMPANY_ID, $company->getId());
        // $cip->setLimit(10);
        $cip->addDescendingOrderByColumn(AgentCompanyInvoicePeer::BILLING_STARTING_DATE);
        $invoiceCount = AgentCompanyInvoicePeer::doCount($cip);
        $this->invoiceCount = $invoiceCount;
        if ($invoiceCount > 0) {
            $preInvoices = AgentCompanyInvoicePeer::doSelect($cip);
            $this->preInvoices = $preInvoices;
        }
        $this->setLayout(false);
    }

    public function executeVendorProductZeroCall(sfWebRequest $request) {


        $pr = new Criteria();
        $pr->add(ProductPeer::IS_IN_ZEROCALL, 1);
        $pr->addAnd(ProductPeer::STATUS, 3);


        return sfView::NONE;
    }

    function executeUpdateRs(sfWebRequest $request) {

        $sltran = new Criteria();
        $sltran->addJoin(TopupTransactionsPeer::AGENT_COMPANY_ID, AgentCompanyPeer::ID, Criteria::LEFT_JOIN);
        $sltran->addAnd(AgentCompanyPeer::IS_PREPAID, 0);
        $sltran->addAnd(TopupTransactionsPeer::STATUS, 3);
        $seltransactions = TopupTransactionsPeer::doSelect($sltran);

        foreach ($seltransactions as $transaction) {

            echo "id " . $transaction->getId() . "agent commission >" . $transaction->getAgentCommission() . "<hr/>";

            $agentCompany = AgentCompanyPeer::retrieveByPK($transaction->getAgentCompanyId());
            $agentUser = AgentUserPeer::retrieveByPK($transaction->getAgentUserId());

            $agentCompany->setBalance($agentCompany->getBalance() - ($transaction->getProductRegistrationFee() - $transaction->getAgentCommission()));
            $agentCompany->save();
            $remainingbalance = $agentCompany->getBalance();
            $debit = -($transaction->getProductRegistrationFee() - $transaction->getAgentCommission());
            $amount = $transaction->getAgentCommission();
            $aph = new AgentPaymentHistory();
            $aph->setAgentId($transaction->getAgentCompanyId());
            $aph->setExpeneseType(7); // Expance Type 7 for MPayments Topups
            $aph->setAmount($amount);
            $aph->setCreditLimit($agentCompany->getCreditLimit());
            $aph->setRemainingBalance($remainingbalance);
            $aph->setTransactionId($transaction->getId());
            $aph->setCommission($amount);
            $aph->setDebit($debit);
            $aph->setCreatedAt($transaction->getCreatedAt());

            $aph->save();
        }


        return sfView::NONE;
    }

    function executeRunSocketServer(sfWebRequest $request) {
        error_reporting(E_ALL);

        /* Allow the script to hang around waiting for connections. */
        set_time_limit(0);

        /* Turn on implicit output flushing so we see what we're getting
         * as it comes in. */
        ob_implicit_flush();

        $address = '70.38.12.20';
        $port = 98765;

        if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
        }

        if (socket_bind($sock, $address, $port) === false) {
            echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        }

        if (socket_listen($sock, 5) === false) {
            echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        }

        do {
            if (($msgsock = socket_accept($sock)) === false) {
                echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
                break;
            }
            $dibs = new DibsCall();
            $dibs->setCallurl("scoket" . $request->getURI() . "====" . var_dump($request));
            $dibs->save();
            /* Send instructions. */
            $msg = "\nWelcome to the PHP Test Server. \n" .
                    "To quit, type 'quit'. To shut down the server type 'shutdown'.\n";
            socket_write($msgsock, $msg, strlen($msg));

            do {
                if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
                    echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
                    break 2;
                }

                $dibs = new DibsCall();
                $dibs->setCallurl("scoket" . $request->getURI());
                $dibs->save();
                if (!$buf = trim($buf)) {
                    continue;
                }
                if ($buf == 'quit') {
                    echo $buf;
                    break;
                }
                if ($buf == 'shutdown') {
                    echo $buf;
                    socket_close($msgsock);
                    break 2;
                }
                $talkback = "PHP: You said '$buf'.\n";
                socket_write($msgsock, $talkback, strlen($talkback));
                echo "$buf\n";
            } while (true);
            socket_close($msgsock);
        } while (true);

        socket_close($sock);

        return sfView::NONE;
    }

    function executeGenerateResellerInvoice(sfWebRequest $request) {
        //   $startdate =  date('Y-m-1 00:00:00', strtotime("last month"));
        //     $enddate = date('Y-m-t 23:59:59', strtotime("last month"));
        //   $startdate = date('Y-07-08 00:00:00');
        //     $enddate = date('Y-07-14 23:59:59');
        $startdate = date('Y-m-d 00:00:00', $request->getParameter('start_date'));
        $enddate = date('Y-m-d 23:59:59', $request->getParameter('end_date'));

        $start_strtotime = strtotime($startdate);
        $end_strototime = strtotime($enddate);
        echo $startdate;
        echo ' - ';
        echo $enddate;
        echo '<br />';

        // die;
        $c = new Criteria();
        $resellers = ResellerPeer::doSelect($c);

        foreach ($resellers as $reseller) {
            echo $reseller->getId() . "::::";
            echo $created_date = $reseller->getCreatedAt();
            echo "<br/>";
            $cl = new Criteria();
            $cl->addAnd(TopupTransactionsPeer::CREATED_AT, $startdate, Criteria::GREATER_EQUAL);
            $cl->addAnd(TopupTransactionsPeer::CREATED_AT, $enddate, Criteria::LESS_EQUAL);
            $cl->addAnd(TopupTransactionsPeer::RESELLER_ID, $reseller->getId());
            $nofCards = TopupTransactionsPeer::doCount($cl);
            echo "number of cards---" . $nofCards;
            if ($nofCards > 0) {
                echo "----reseller id------" . $reseller->getId();
                echo "<br/>";
                echo $url1 = sfConfig::get('app_b2c_url') . 'pScripts/resellerInvoiceBilling?reseller_id=' . $reseller->getId() . '&start_date=' . $start_strtotime . '&end_date=' . $end_strototime;
                $invoice = file_get_contents($url1);
            }echo "<br/>";
        }
        return sfView::NONE;
    }

    public function executeResellerInvoiceBilling(sfWebRequest $request) {
        $reseller_id = $request->getParameter('reseller_id');

        $this->billing_start_date = date('Y-m-d 00:00:00', $request->getParameter('start_date'));
        $this->billing_end_date = date('Y-m-d 23:59:59', $request->getParameter('end_date'));
        $this->forward404Unless($reseller_id && $this->billing_start_date && $this->billing_end_date);

        if (!($reseller = ResellerPeer::retrieveByPK($reseller_id))) {
            $this->forward404();
        }
//////////////////////////////////////////////////

        $invoice_id = 2;
        $im = new Criteria();
        $im->add(InvoiceMethodPeer::ID, $invoice_id);
        $invoice = InvoiceMethodPeer::doSelectOne($im);
        $this->invoice_cost = $invoice->getCost();




        $epc = new Criteria();
        $epc->add(ResellerInvoicePeer::RESELLER_ID, $reseller_id);
        $epc->addAnd(ResellerInvoicePeer::BILLING_STARTING_DATE, $this->billing_start_date);
        $epc->addAnd(ResellerInvoicePeer::BILLING_ENDING_DATE, $this->billing_end_date);
        $resellerInvoiceCount = ResellerInvoicePeer::doCount($epc);

        if ($resellerInvoiceCount > 0) {
            $new_invoice = ResellerInvoicePeer::doSelectOne($epc);
        } else {
            $new_invoice = new ResellerInvoice();
        }
        $new_invoice->setResellerId($reseller_id);
        $new_invoice->setBillingStartingDate($this->billing_start_date);
        $new_invoice->setBillingEndingDate($this->billing_end_date);
        $new_invoice->setStartTime($request->getParameter('start_date'));
        $new_invoice->setEndTime($request->getParameter('end_date'));
        $billing_due_days = $invoice->getBillingdays();
        $due_date = date("Y-m-d H:i:s", time() + ((60 * 60) * 24) * $billing_due_days);
        $new_invoice->setDueDate($due_date);
        $new_invoice->setInvoiceStatusId(4); // inactive
        $new_invoice->save();


        $imn = new Criteria();

        $imn->addDescendingOrderByColumn(ResellerInvoiceNumberPeer::ID);
        $invoicenumberCount = ResellerInvoiceNumberPeer::doCount($imn);
        if ($invoicenumberCount > 0) {
            $lastInvoiceNumber = ResellerInvoiceNumberPeer::doSelectOne($imn);
            $inVoiceN = $lastInvoiceNumber->getId();
        } else {
            $inVoiceN = 0;
        }
        $invoiceNumber = $inVoiceN + 1;
        $new_invoice->setInvoiceNumber($invoiceNumber);
        $new_invoice->save();
        //   var_dump($new_invoice);
        $this->invoice_meta = $new_invoice;
        $this->reseller_meta = $reseller;

/////////////////////////////////////////////////        
        $this->setLayout(false);
    }

    /*     * ***************** For zerocall portal ******************* */

    public function executeVenderCardAmount(sfWebRequest $request) {
        $vender_id = $request->getParameter('vender_id');
        $cc = new Criteria();
        $cc->add(ProductPeer::VENDOR_ID, $vender_id);
        // $cc->addAnd(ProductPeer::PRODUCT_TYPE_ID,10);
        $cc->addJoin(ProductPeer::ID, CardNumbersPeer::PRODUCT_ID, Criteria::LEFT_JOIN);
        $cc->addAnd(CardNumbersPeer::STATUS, 0, Criteria::EQUAL);
        $cc->addAnd(ProductPeer::REGISTRATION_FEE, 0, Criteria::GREATER_THAN);
        $cc->addGroupByColumn(CardNumbersPeer::PRODUCT_ID);
        $cc->addAscendingOrderByColumn(ProductPeer::REGISTRATION_FEE);
        $products = ProductPeer::doSelect($cc);
        $prods = "";
        if (ProductPeer::doCount($cc) > 0) {
            foreach ($products as $product) {
                $prods .= "<div class='products' id='product_id_" . $product->getId() . "' data-product_id='" . $product->getId() . "' data-product_price='" . $product->getRegistrationFee() . "' data-product_name='" . $product->getName() . "' data-product_ispercentage='" . $product->getIsDiscountInPercentage() . "' data-product_discount='" . $product->getDiscount() . "'>" . $product->getRegistrationFee() . "</div>";
            }
        } else {
            $prods = "No Product Found";
        }
        echo $prods;
        return sfView::NONE;
    }

    public function executeGetVenderCard(sfWebRequest $request) {
        $vendor_id = $request->getParameter('vendor_id');
        $product_id = $request->getParameter('product_id');
        $customer_id = $request->getParameter('customer_id');
        $customer_mobile = $request->getParameter('customer_mobile');
        $used_by = $request->getParameter('used_by');
        if (!$used_by)
            $used_by = "Zerocall";
        //   $price = $request->getParameter('price');
        $cc = new Criteria();
        $cc->add(CardNumbersPeer::PRODUCT_ID, $product_id);
        // $cc->addAnd(CardNumbersPeer::CARD_PRICE,$price);
        $cc->addAnd(CardNumbersPeer::STATUS, 0, Criteria::EQUAL);
        $card_number = CardNumbersPeer::doSelectOne($cc);
        $cards = "";
        if (CardNumbersPeer::doCount($cc) > 0) {
            // $cards .=  "<div class='cards' id='card_detail' data-card_id='".$card_number->getId()."' data-card_price='".$card_number->getCardPrice()." 'data-card_number='".$card_number->getCardNumber()."'>".$card_number->getCardNumber()."</div>";           
            $card_number->setCustomerMobile($customer_mobile);
            $card_number->setUsedBy(urldecode($used_by));
            $card_number->setCustomerId($customer_id);
            $card_number->setUsedAt(date("Y-m-d H:i:s"));
            $card_number->setStatus(1);
            $card_number->save();
            $cardstatus = "1";

            $c_num = $card_number->getCardNumber();

            $key = '12345678';
            $data = trim($c_num);
            $alg = MCRYPT_DES;
            $mode = MCRYPT_MODE_CBC;
            $data = $this->strToHex($data);
            $data = $this->hextobin($data);
            $encrypted_data = mcrypt_encrypt($alg, $key, $data, $mode, $key);

            $plain_text = base64_encode($encrypted_data);
            $enc_card_number = "MPAY+" . urlencode($plain_text);

            $csms = new Criteria();
            $csms->add(SmsTextPeer::VENDOR_ID, $vendor_id);
            $csms->addDescendingOrderByColumn(SmsTextPeer::ID);
            if (SmsTextPeer::doCount($csms) > 0) {
                $sms = SmsTextPeer::doSelectOne($csms);
                $sms_text = $sms->getMessageText();
            } else {
                $sms_text = "SMS Not Found";
            }
            $cardnum = array("card_id" => $card_number->getId(), "card_price" => $card_number->getCardPrice(), "card_product_id" => $card_number->getProductId(), "card_number" => $enc_card_number, "card_serial" => $card_number->getCardSerial(), "card_status" => $cardstatus, "sms_text" => $sms_text);
            $cards = json_encode($cardnum);
        } else {
            $cardnum = array("not_found" => "Card not found");
            $cards = json_encode($cardnum);
        }
        echo $cards;
        return sfView::NONE;
    }

    public function executeGetVenderCardWoiize(sfWebRequest $request) {
        $vendor_id = $request->getParameter('vendor_id');
        $product_id = $request->getParameter('product_id');
        $customer_id = $request->getParameter('customer_id');
        $customer_mobile = $request->getParameter('customer_mobile');
        $used_by = $request->getParameter('used_by');
        if (!$used_by)
            $used_by = "Woiize";
        //   $price = $request->getParameter('price');
        $cc = new Criteria();
        $cc->add(CardNumbersPeer::PRODUCT_ID, $product_id);
        // $cc->addAnd(CardNumbersPeer::CARD_PRICE,$price);
        $cc->addAnd(CardNumbersPeer::STATUS, 0, Criteria::EQUAL);
        $card_number = CardNumbersPeer::doSelectOne($cc);
        $cards = "";
        if (CardNumbersPeer::doCount($cc) > 0) {
            // $cards .=  "<div class='cards' id='card_detail' data-card_id='".$card_number->getId()."' data-card_price='".$card_number->getCardPrice()." 'data-card_number='".$card_number->getCardNumber()."'>".$card_number->getCardNumber()."</div>";           
            $card_number->setCustomerMobile($customer_mobile);
            $card_number->setUsedBy(urldecode($used_by));
            $card_number->setCustomerId($customer_id);
            $card_number->setUsedAt(date("Y-m-d H:i:s"));
            $card_number->setStatus(1);
            $card_number->save();
            $cardstatus = "1";

            $c_num = $card_number->getCardNumber();

            $key = '12345678';
            $data = trim($c_num);
            $alg = MCRYPT_DES;
            $mode = MCRYPT_MODE_CBC;
            $data = $this->strToHex($data);
            $data = $this->hextobin($data);
            $encrypted_data = mcrypt_encrypt($alg, $key, $data, $mode, $key);

            $plain_text = base64_encode($encrypted_data);
            $enc_card_number = "MPAY+" . urlencode($plain_text);

            $csms = new Criteria();
            $csms->add(SmsTextPeer::VENDOR_ID, $vendor_id);
            $csms->addDescendingOrderByColumn(SmsTextPeer::ID);
            if (SmsTextPeer::doCount($csms) > 0) {
                $sms = SmsTextPeer::doSelectOne($csms);
                $sms_text = $sms->getMessageText();
            } else {
                $sms_text = "SMS Not Found";
            }
            $cardnum = array("card_id" => $card_number->getId(), "card_price" => $card_number->getCardPrice(), "card_product_id" => $card_number->getProductId(), "card_number" => $enc_card_number, "card_serial" => $card_number->getCardSerial(), "card_status" => $cardstatus, "sms_text" => $sms_text);
            $cards = json_encode($cardnum);
        } else {
            $cardnum = array("not_found" => "Card not found");
            $cards = json_encode($cardnum);
        }
        echo $cards;
        return sfView::NONE;
    }

    public function executeChangeVenderCardStatus(sfWebRequest $request) {
        $card_number = $request->getParameter('card_number');
        $customerid = $request->getParameter('customer_id');
        $customer_mobile = $request->getParameter('customer_mobile');
        $flag = false;
        $vcn = new Criteria();
        $vcn->add(CardNumbersPeer::CARD_NUMBER, $card_number);
        $vcn->addAnd(CardNumbersPeer::STATUS, 0);
        $card_count = CardNumbersPeer::doCount($vcn);
        if ($card_count > 0) {
            $card = CardNumbersPeer::doSelectOne($vcn);
            $card->setStatus(1);
            $card->setCustomerMobile($customer_mobile);
            $card->setComments("Zerocall Customer id: " . $customerid);
            $card->setUsedAt(date("Y-m-d H:i:s"));
            $card->save();
            $flag = true;
        } else {
            $flag = false;
        }
        echo $flag;
        return sfView::NONE;
    }

    /*     * *****************End For zerocall portal ******************* */

    public function executeKhanResellerAssign(sfWebRequest $request) {

        die;
        $vcn = new Criteria();
        $vcn->add(TopupTransactionsPeer::STATUS, 3);
        $allTransactions = TopupTransactionsPeer::doSelect($vcn);
        foreach ($allTransactions as $allTransaction) {

            echo $allTransaction->getId() . "<hr/>";
            commissionLib::resellerCommissionAssigner($allTransaction->getAgentCompanyId(), $allTransaction->getProductId(), $allTransaction->getId());
        }

        return sfView::NONE;
    }

    public function executeKhanAgentResellerAssign(sfWebRequest $request) {
        $comapanyid = $request->getParameter('companyid');
        $vcn = new Criteria();
        $vcn->add(TopupTransactionsPeer::AGENT_COMPANY_ID, $comapanyid);
        $vcn->addAnd(TopupTransactionsPeer::STATUS, 3);
        $vcn->addAnd(TopupTransactionsPeer::CREATED_AT, '2013-11-04 00:00:00', Criteria::GREATER_EQUAL);
        $vcn->addAnd(TopupTransactionsPeer::CREATED_AT, '2013-11-06 23:59:59', Criteria::LESS_EQUAL);
        $allTransactions = TopupTransactionsPeer::doSelect($vcn);
        foreach ($allTransactions as $allTransaction) {

            echo "<hr/>" . $allTransaction->getId() . "-  company Id= " . $allTransaction->getAgentCompanyId();
            commissionLib::agentCommissionAssigner($allTransaction->getAgentCompanyId(), $allTransaction->getProductId(), $allTransaction->getId());
        }
        return sfView::NONE;
    }

    public function executeDealerDetailInvoice($request) {

        $dealerInvoice = AgentCompanyInvoicePeer::retrieveByPK($request->getParameter('InvoiceId'));
        $this->dealerInvoice = $dealerInvoice;
        $this->setLayout(false);
    }

    public function executeResellerDetailInvoice($request) {

        $resellerInvoice = ResellerInvoicePeer::retrieveByPK($request->getParameter('resellerInvoiceId'));
        $this->resellerInvoice = $resellerInvoice;
        $this->setLayout(false);
    }

    function executeCompanyDownlaodPdf(sfWebRequest $request) {

        $invoiceId = $request->getParameter('invoiceid');

        $invoice = AgentCompanyInvoicePeer::retrieveByPK($invoiceId);

        $filePathName = sfConfig::get('app_companypdfinvoice_url') . $invoice->getPdfFile();




        if ($invoice->getPdfFile() == "") {
            $invoice = "";
            $filePathName = "";
            $fileurl = "http://admin.m-easypay.com/b2c.php/pScripts/dealerDetailInvoice?InvoiceId=" . $invoiceId;
            file_get_contents($fileurl);
            sleep(70);
            $invoice = AgentCompanyInvoicePeer::retrieveByPK($invoiceId);

            $filePathName = sfConfig::get('app_companypdfinvoice_url') . $invoice->getPdfFile();
        }
        if (!file_exists($filePathName)) {
            $invoice = "";
            $filePathName = "";
            $fileurl = "http://admin.m-easypay.com/b2c.php/pScripts/dealerDetailInvoice?InvoiceId=" . $invoiceId;
            file_get_contents($fileurl);
            sleep(70);
            $invoice = AgentCompanyInvoicePeer::retrieveByPK($invoiceId);

            $filePathName = sfConfig::get('app_companypdfinvoice_url') . $invoice->getPdfFile();
        }


        if ($invoice->getPdfFile() != "") {

            header('Content-disposition: attachment; filename=' . $invoice->getPdfFile());
            header('Content-type: application/pdf');
            readfile($filePathName);
        } else {

            $filePathName1 = "";
            $invoice1 = AgentCompanyInvoicePeer::retrieveByPK($invoiceId);

            $filePathName = sfConfig::get('app_companypdfinvoice_url') . $invoice1->getPdfFile();
            header('Content-disposition: attachment; filename=' . $invoice1->getPdfFile());
            header('Content-type: application/pdf');
            readfile($filePathName1);
        }

        return sfView::NONE;
    }

    function executeResellerDownlaodPdf(sfWebRequest $request) {

        $invoiceId = $request->getParameter('invoiceid');

        $invoice = ResellerInvoicePeer::retrieveByPK($invoiceId);

        $filePathName = sfConfig::get('app_resellerpdfinvoice_url') . $invoice->getPdfFile();
        if ($invoice->getPdfFile() == "") {
            $invoice = "";
            $filePathName = "";
            $fileurl = "http://admin.m-easypay.com/b2c.php/pScripts/resellerDetailInvoice?resellerInvoiceId=" . $invoiceId;
            file_get_contents($fileurl);

            sleep(70);
            $invoice = ResellerInvoicePeer::retrieveByPK($invoiceId);

            echo $filePathName = sfConfig::get('app_resellerpdfinvoice_url') . $invoice->getPdfFile();
        }
        if (!file_exists($filePathName)) {
            $invoice = "";
            $filePathName = "";
            $fileurl = "http://admin.m-easypay.com/b2c.php/pScripts/resellerDetailInvoice?resellerInvoiceId=" . $invoiceId;
            file_get_contents($fileurl);
            sleep(70);
            $invoice = ResellerInvoicePeer::retrieveByPK($invoiceId);

            $filePathName = sfConfig::get('app_resellerpdfinvoice_url') . $invoice->getPdfFile();
        }

        if ($invoice->getPdfFile() != "") {
            header('Content-disposition: attachment; filename=' . $invoice->getPdfFile());
            header('Content-type: application/pdf');
            readfile($filePathName);
        } else {
            $invoice2 = "";
            $filePathName2 = "";
            $invoice2 = ResellerInvoicePeer::retrieveByPK($invoiceId);

            $filePathName = sfConfig::get('app_resellerpdfinvoice_url') . $invoice2->getPdfFile();
            header('Content-disposition: attachment; filename=' . $invoice2->getPdfFile());
            header('Content-type: application/pdf');
            readfile($filePathName2);
        }
        return sfView::NONE;
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

    public function executeAddItemsSubmit($request) {


        $xml_root_dir = '/home/sahdia/xml';

        $csv_root_dir = '/home/sahdia/csv';

        $xml_error_dir = $xml_root_dir . '/error';
        $xml_backup_dir = $xml_root_dir . '/backup';

        $csv_error_dir = $csv_root_dir . '/error';
        $csv_backup_dir = $csv_root_dir . '/backup';

        $ignore = array('.', '..', 'backup_staging_error', 'backup_staging', 'backup', 'error');

        $files = scandir($xml_root_dir);
        $files = array_diff($files, $ignore);

        if (count($files) == 0) {
            echo 'No file to process in "' . $xml_root_dir . '"';
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
                    var_dump($xml_obj);
                } catch (Exception $e) {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Process stoped as XML file is invalid fileName:" . $file;
                    emailLib::sendItemsXMLError($message);
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
                    emailLib::sendItemsXMLError($message);
                    continue;
                }

                if ($xml_obj->tableName == "") {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Empty Table Name Please check XML File:" . $file;
                    emailLib::sendItemsXMLError($message);
                    continue;
                }
                $tableName = $xml_obj->tableName;
                $os = array("items", "t5", "m6", "m16");
                if (!in_array($tableName, $os)) {
                    $tableName = FALSE;
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "Unknown Table Name Please check XML File:" . $file;
                    emailLib::sendItemsXMLError($message);
                    continue;
                }

                $fieldsArray = array();
                $countdata = $xml_obj->columns[0]->count();
                for ($i = 0; $i < $countdata; $i++) {

                    $datacolumnName = $xml_obj->columns[0]->column[$i]->columnName;
                    $datacolumnIndex = $xml_obj->columns[0]->column[$i]->columnIndex;
                    $fieldsArray["" . $datacolumnIndex] = $datacolumnName;
                    //  echo "<hr>";
                }
                ksort($fieldsArray);

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
                    emailLib::sendItemsXMLError($message);
                    continue;
                }



                $columnAray = array("id", "description1", "description2","description3", "supplier_number", "supplier_item_number", "ean", "color", "size", "buying_price", "selling_price", "taxation_code", "group");
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
                    emailLib::sendItemsXMLError($message);
                    continue;
                }





                $splits = explode('.', $xml_obj->file);
                $file_extension = end($splits);

//               echo  $csv_root_dir . '/' . $xml_obj->file;
//               die;


                if (file_exists($csv_root_dir . '/' . $xml_obj->file) && filesize($csv_root_dir . '/' . $xml_obj->file) != 0 && $file_extension == 'csv') {


                    $separater = $xml_obj->separator;





                    if ($xml_obj->header) {
                        $start = 1;
                    } else {
                        $start = 0;
                    }


                    $csv_file = fopen($csv_root_dir . '/' . $xml_obj->file, "r");

                    $csv = fread($csv_file, filesize($csv_root_dir . '/' . $xml_obj->file));
                    $csv = str_replace('"', '', $csv);
                    fclose($csv_file);

                    $data = explode("\n", $csv);
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
                    for ($i = $start; $i < count($data); $i++) {

                        if ($data[$i] != "") {
                            $c = explode($separater, $data[$i]);
                            $combine = array_combine($fieldsArray, $c);
                            if (!$combine) {
                                $combine_valid_csv = false;
                                rename("$csv_root_dir/$xml_obj->file", "$csv_error_dir/$xml_obj->file");
                                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                                $message = "CSV columns does not match with the defination xml columns. At row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                emailLib::sendItemsXMLError($message);
                                //$this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                            }
                            foreach ($combine as $key => $values) {
                                $combine[trim($key)] = trim($values);
                            }
                            $combine['selling_price'] = str_replace(",", ".", $combine['selling_price']);
                            $combine['buying_price'] = str_replace(",", ".", $combine['buying_price']);
                            if (!$combine_valid_csv) {
                                
                            } elseif (!is_numeric($combine['id']) || !is_numeric($combine['ean']) || !is_numeric($combine['buying_price']) || !is_numeric($combine['selling_price']) || !is_numeric($combine['taxation_code'])) {
                                $valid_csv = false;
                                $message = "Validation failed in CSV at row number: " . $i . " XML File:" . $file . " CSV File: " . $xml_obj->file;
                                emailLib::sendItemsXMLError($message);
                                //$this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                            }
                        }
                    }

                    if ($valid_csv && $combine_valid_csv) {
                        for ($i = $start; $i < count($data); $i++) {
                            if ($data[$i] != "") {
                                $c = explode($separater, $data[$i]);

                                $combine = array_combine($fieldsArray, $c);
                                foreach ($combine as $key => $values) {
                                    $combine[trim($key)] = trim($values);
                                }
                                $insert_new = itemsLib::populateItem($combine);
                            }
                        }
                        // as CSV parsed so move it and move XML as well to the backup.
                        rename("$csv_root_dir/$xml_obj->file", "$csv_backup_dir/$xml_obj->file");
                        rename("$xml_root_dir/$file", "$xml_backup_dir/$file");
                    }
                } else {
                    rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                    $message = "unable to parse xml file as csv file not found/invalid:" . $file;
                    emailLib::sendItemsXMLError($message);
                    continue;
                }
            } else {
                rename("$xml_root_dir/$file", "$xml_error_dir/$file");
                $message = "invalid extension or xml is empty. unable to parse " . $file;
                emailLib::sendItemsXMLError($message);
                continue;
            }
        }
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
                $this->redirect('pScripts/addItems');
                exit;
            } elseif ($extensionf != 'xml') {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Defination file must be in xml format.'));
                $this->redirect('pScripts/addItems');
                exit;
            }

            if (!$fileSize) {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Data file is empty.'));
                $this->redirect('pScripts/addItems');
                exit;
            } elseif ($extension != 'csv') {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Data File must be in CSV format.'));
                $this->redirect('pScripts/addItems');
                exit;
            }

            $filef = fopen($fileTmpNamef, "r");
            $xmlfile = fread($filef, $fileSizef);
            $xml_obj = new SimpleXMLElement($xmlfile);

            $separater = $xml_obj->separator;

            if ($xml_obj->header) {
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



            $file = fopen($fileTmpName, "r");
            if (!$file) {
                $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Error opening data file.'));
                $this->redirect('pScripts/addItems');
                exit;
            }
            $csv = fread($file, $fileSize);
            $csv = str_replace('"', '', $csv);
            fclose($file);

            $data = explode("\n", $csv);

            // var_dump($data);die;
            // This loop will check only for the errors in the CSV and the next loop will insert into the database;
            for ($i = $start; $i < count($data); $i++) {

                if ($data[$i] != "") {
                    $c = explode($separater, $data[$i]);
                    $combine = array_combine($fieldsArray, $c);
                    if (!$combine) {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                        $this->redirect('pScripts/addItems');
                    }
                    foreach ($combine as $key => $values) {
                        $combine[trim($key)] = trim($values);
                    }
                    if (!is_numeric($combine['id']) || !is_numeric($combine['supplier_number']) || !is_numeric($combine['supplier_item_number']) || !is_numeric($combine['ean']) || !is_numeric($combine['color']) || !is_numeric($combine['group']) || !is_numeric($combine['size']) || !is_numeric($combine['buying_price']) || !is_numeric($combine['selling_price']) || !is_numeric($combine['taxation_code'])) {
                        $this->getUser()->setFlash('file_error', $this->getContext()->getI18N()->__('Action Discarded as there is Error while parsing CSV on line number ' . $i));
                        $this->redirect('pScripts/addItems');
                    }
                }
            }

            for ($i = $start; $i < count($data); $i++) {
                if ($data[$i] != "") {
                    $c = explode($separater, $data[$i]);

                    $combine = array_combine($fieldsArray, $c);
                    foreach ($combine as $key => $values) {
                        $combine[trim($key)] = trim($values);
                    }
                    $c = new Criteria();
                    echo $combine['id'];
                    $c->add(ItemsPeer::ID, (int) $combine['id'], Criteria::EQUAL);

                    if (ItemsPeer::doCount($c) == 0) {
                        $item = new Items();
                    } else {
                        $item = ItemsPeer::doSelectOne($c);
                    }

                    $item->setId($combine['id']);
                    $item->setDescription1($combine['description1']);
                    $item->setDescription2($combine['description2']);
                        $item->setDescription3($combine['description3']);
                    $item->setSupplierNumber($combine['supplier_number']);
                    $item->setSupplierItemNumber($combine['supplier_item_number']);
                    $item->setEan($combine['ean']);
                    $item->setColor($combine['color']);
                    $item->setGroup($combine['group']);
                    $item->setSize($combine['size']);
                    $item->setBuyingPrice($combine['buying_price']);
                    $item->setSellingPrice($combine['selling_price']);
                    $item->setTaxationCode($combine['taxation_code']);
                    $item->setSmallPic($combine['small_pic']);
                    $item->save();
                    $insert_new = true;
                }
            }

            if ($insert_new) {
                $this->getUser()->setFlash('file_done', $this->getContext()->getI18N()->__('Items imported successfully'));
                $this->redirect('pScripts/addItems');
                exit;
            }
        }
        return sfView::NONE;
    }

}
