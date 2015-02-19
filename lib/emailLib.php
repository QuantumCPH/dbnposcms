<?php

require_once(sfConfig::get('sf_lib_dir') . '/changeLanguageCulture.php');

class emailLib {

    public static function sendAgentRefilEmail(AgentCompany $agent, $agent_order) {
        $vat = 0;

//create transaction
//This Section For Get The Agent Information
        $agent_company_id = $agent->getId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        $agentamount = $agent_order->getAmount();
        $createddate = $agent_order->getCreatedAt('d-m-Y');
        $agentid = $agent_order->getReceiptNo();
        $agentOrderDescription = $agent_order->getOrderDescription();
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('affiliate/agent_order_receipt', array(
            'order' => $agentid,
            'transaction' => $agentamount,
            'createddate' => $createddate,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => true,
            'agent' => $agent,
            'orderdescription' => $agentOrderDescription
        ));


        $subject = __('Agent Payment Confirmation');


//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//--------------Sent The Email To Kimarin order email
        if (trim($sender_email_orders) != ''):
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email1->setMessage($message_body);
            $email1->save();
        endif;
//-----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendRefillEmail(Customer $customer, $order) {


//create transaction
//        $transaction = new Transaction();
//        $transaction->setOrderId($order->getId());
//        $transaction->setCustomer($customer);
//        $transaction->setAmount($order->getExtraRefill());


        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);
        $vat = $transaction->getAmount() - ($transaction->getAmount() / (sfConfig::get('app_vat_percentage') + 1));
//This Section For Get The Agent Information
        $agent_company_id = $customer->getReferrerId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }
        $vat = 0;
        $vat = $transaction->getVat();

        $agent_company_id = $transaction->getAgentCompanyId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);

            $agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $agent_name = '';
        }
//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('affiliate/refill_order_receipt', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $agent_name,
            'wrap' => false,
        ));

        $subject = __('Payment Confirmation');
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order email
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($agent_company_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendCustomerRegistrationViaAgentEmail(Customer $customer, $order) {


        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);


//This Section For Get The Agent Information
        $agent_company_id = $customer->getReferrerId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

        $postalcharge = 0;
        $customerorder = 1;
        $vat = $order->getProduct()->getRegistrationFee() * sfConfig::get('app_vat_percentage');
//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('affiliate/order_receipt_web_reg', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'postalcharge' => $postalcharge,
            'customerorder' => $customerorder,
            'wrap' => false
        ));


        $message_body_agent = get_partial('affiliate/order_receipt_web_reg', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'postalcharge' => $postalcharge,
            'customerorder' => $customerorder,
            'wrap' => false
        ));
        $subject = __('Payment Confirmation');
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }

        $customer_id = trim($customer->getId());

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order email
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via agent');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($agent_company_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via agent');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via agent');
            $email2->setMessage($message_body_agent);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To Okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via agent');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendForgetPasswordEmail(Customer $customer, $message_body, $subject) {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');

// $subject = __("Request for password");
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

//Support Information
//$sender_email = sfConfig::get('app_email_sender_email', 'support@Zapna.com');
//$sender_name = sfConfig::get('app_email_sender_name', 'Zapna support');
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setCutomerId($customer_id);
            $email->setAgentId($referrer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Forget Password');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
    }

    public static function sendCustomerRefillEmail(Customer $customer, $order, $transaction) {

//set vat


        $vat = $transaction->getAmount() - ($transaction->getAmount() / (sfConfig::get('app_vat_percentage') + 1));
        $subject = __('Payment Confirmation');
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

        if ($referrer_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $referrer_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('payments/refill_order_receipt', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));


//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin Order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($referrer_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer Registration');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($referrer_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer Registration');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):
            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($referrer_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Customer Registration');
            $email2->setMessage($message_body);
            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendCustomerAutoRefillEmail(Customer $customer, $message_body) {

        $subject = __('Payment Confirmation');

        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

//send to email to Kimain order 
        if (trim($sender_email_orders) != ''):
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setMessage($message_body);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setReceipientName($sender_name_orders);
            $email1->setCutomerId($customer_id);
            $email1->setAgentId($referrer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer Auto Refill');
            $email1->save();
        endif;


//send to user
        if (trim($recepient_email) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setMessage($message_body);
            $email->setReceipientEmail($recepient_email);
            $email->setReceipientName($recepient_name);
            $email->setCutomerId($customer_id);
            $email->setAgentId($referrer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer Auto Refill');

            $email->save();
        endif;

//send to OKHAN
        if (trim($sender_email) != ''):
            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setMessage($message_body);
            $email2->setReceipientEmail($sender_email);
            $email2->setReceipientName($sender_name);
            $email2->setCutomerId($customer_id);
            $email2->setAgentId($referrer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Customer Auto Refill');
            $email2->save();
        endif;
////////////////////////////////////////////////////////
//send to CDU
        if (trim($sender_emailcdu) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setMessage($message_body);
            $email3->setReceipientEmail($sender_emailcdu);
            $email3->setReceipientName($sender_namecdu);
            $email3->setCutomerId($customer_id);
            $email3->setAgentId($referrer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer Auto Refill');
            $email3->save();
        endif;

//send to CDU
        if (trim($sender_emailrs) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setMessage($message_body);
            $email3->setReceipientEmail($sender_emailrs);
            $email3->setReceipientName($sender_namers);
            $email3->setCutomerId($customer_id);
            $email3->setAgentId($referrer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer Auto Refill');
            $email3->save();
        endif;
    }

    public static function sendCustomerConfirmPaymentEmail(Customer $customer, $message_body) {


        $subject = __('Payment Confirmation');

        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

//send to Kimarin order 
        if (trim($sender_email_orders) != ''):
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setMessage($message_body);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setReceipientName($sender_name_orders);
            $email1->setCutomerId($customer_id);
            $email1->setAgentId($referrer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Payment');

            $email1->save();
        endif;

//send to user
        if (trim($recepient_email) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setMessage($message_body);
            $email->setReceipientEmail($recepient_email);
            $email->setReceipientName($recepient_name);
            $email->setCutomerId($customer_id);
            $email->setAgentId($referrer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Payment');

            $email->save();
        endif;

//send to okhan
        if (trim($sender_email) != ''):
            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setMessage($message_body);
            $email2->setReceipientEmail($sender_email);
            $email2->setReceipientName($sender_name);
            $email2->setCutomerId($customer_id);
            $email2->setAgentId($referrer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Payment');
            $email2->save();
        endif;
//send to cdu
        if (trim($sender_emailcdu) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setMessage($message_body);
            $email3->setReceipientEmail($sender_emailcdu);
            $email3->setReceipientName($sender_namecdu);
            $email3->setCutomerId($customer_id);
            $email3->setAgentId($referrer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Payment');
            $email3->save();
        endif;
//send to RS
        if (trim($sender_emailrs) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setMessage($message_body);
            $email3->setReceipientEmail($sender_emailrs);
            $email3->setReceipientName($sender_namers);
            $email3->setCutomerId($customer_id);
            $email3->setAgentId($referrer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Payment');
            $email3->save();
        endif;
    }

    public static function sendCustomerConfirmRegistrationEmail($inviteuserid, $customerr, $subject = null, $order, $transaction) {

        $c = new Criteria();
        $c->add(CustomerPeer::ID, $inviteuserid);
        $customer = CustomerPeer::doSelectOne($c);
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $sender_name = sfConfig::get('app_email_sender_name_sup');
        $sender_email = sfConfig::get('app_email_sender_email_sup');
        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

        $registered_customer_name = sprintf('%s %s', $customerr->getFirstName(), $customerr->getLastName());

        $vat = 0;
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('pScripts/bonus_web_reg', array(
            'customer' => $customer,
            'recepient_name' => $recepient_name,
            'registered_customer_name' => $registered_customer_name,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'wrap' => true,
        ));
        $subject = __('Bonus awarded');

//send to Kimarin order
        if ($sender_email_orders != ''):
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setMessage($message_body);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setReceipientName($sender_name_orders);
            $email1->setCutomerId($customer_id);
//$email->setAgentId($referrer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Bonus');

            $email1->save();
        endif;

//send to user
        if ($recepient_email != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setMessage($message_body);
            $email->setReceipientEmail($recepient_email);
            $email->setReceipientName($recepient_name);
            $email->setCutomerId($customer_id);
//$email->setAgentId($referrer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Bonus');

            $email->save();
        endif;

//send to okhan
        if ($sender_email != ''):
            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setMessage($message_body);
            $email2->setReceipientEmail($sender_email);
            $email2->setReceipientName($sender_name);
            $email2->setCutomerId($customer_id);
//$email2->setAgentId($referrer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Bonus');
            $email2->save();
        endif;
//////////////////////////////////////////////////////////////////
        if ($sender_emailcdu != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setMessage($message_body);
            $email3->setReceipientName($sender_namecdu);
            $email3->setReceipientEmail($sender_emailcdu);
            $email3->setCutomerId($customer_id);
//$email3->setAgentId($referrer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Bonus');
            $email3->save();
        endif;
// RS
        if ($sender_emailrs != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setMessage($message_body);
            $email3->setReceipientName($sender_namers);
            $email3->setReceipientEmail($sender_emailrs);
            $email3->setCutomerId($customer_id);
//$email3->setAgentId($referrer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer Confirm Bonus');
            $email3->save();
        endif;
    }

//////////////////////////////////////////////////////////////

    public static function sendCustomerRegistrationViaWebEmail(Customer $customer, $order) {


        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);


//This Section For Get The Agent Information
        $agent_company_id = $customer->getReferrerId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

        $lang = sfConfig::get('app_language_symbol');
// $this->lang = $lang;

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

        $vat = ($order->getProduct()->getRegistrationFee() + $postalcharge) * sfConfig::get('app_vat_percentage');

//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('pScripts/order_receipt_web_reg', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'postalcharge' => $postalcharge,
            'wrap' => true,
        ));


        $subject = __('Registration Confirmation');
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order
        if ($sender_email_orders != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via link');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if ($recepient_email != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via link');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
//--------------Sent The Email To Support
        if ($sender_email != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via link');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if ($sender_emailcdu != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via link');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if ($sender_emailrs != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via link');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

///////////////////////////////////////////////////////////

    public static function sendCustomerRegistrationViaAgentSMSEmail(Customer $customer, $order) {


        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);
//This Section For Get The Agent Information
        $agent_company_id = $customer->getReferrerId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }
        $vat = ($order->getProduct()->getRegistrationFee()) * sfConfig::get('app_vat_percentage');
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('pScripts/order_receipt_sms', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));

        $subject = __('Registration  Confirmation');
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent the Email To Kimarin order

        if (trim($sender_email_orders) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($sender_name_orders);
            $email2->setReceipientEmail($sender_email_orders);
            $email2->setAgentId($agent_company_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType('Customer registration via agent SMS ');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//------------------Sent the Email To Agent

        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType('Customer registration via agent SMS ');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To Okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType('Customer registration via agent SMS ');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Customer registration via agent SMS ');

            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Customer registration via agent SMS ');

            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendCustomerRegistrationViaAgentAPPEmail(Customer $customer, $order) {

        echo 'sending email';
        echo '<br/>';
        $product_price = $order->getProduct()->getPrice() - $order->getExtraRefill();
        echo $product_price;
        echo '<br/>';
        $vat = .20 * $product_price;
        echo $vat;
        echo '<br/>';

//        //create transaction
//        $transaction = new Transaction();
//        $transaction->setOrderId($order->getId());
//        $transaction->setCustomer($customer);
//        $transaction->setAmount($form['extra_refill']);

        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);


//This Section For Get The Agent Information
        $agent_company_id = $customer->getReferrerId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }
//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('pScripts/order_receipt_sms', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));

        $subject = __('Registration Confirmation');
        $recepient_email = trim($customer->getEmail());
        $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        $customer_id = trim($customer->getId());

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via APP');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($agent_company_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via APP');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via APP');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To Okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via APP');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via APP');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Customer registration via APP');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendvoipemail(Customer $customer, $order, $transaction) {

//set vat
        $vat = 0;
        $subject = 'Bekräftelse - nytt resenummer frän ' . sfConfig::get('app_site_title');
        $recepient_email = trim($customer->getEmail());
        $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

        if ($referrer_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $referrer_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
//        $message_body = get_partial('payments/order_receipt', array(
//                'customer'=>$customer,
//                'order'=>$order,
//                'transaction'=>$transaction,
//                'vat'=>$vat,
//                'agent_name'=>$recepient_agent_name,
//                'wrap'=>false,
//        ));
// Please remove the receipt that is sent out when activating
        $getvoipInfo = new Criteria();
        $getvoipInfo->add(SeVoipNumberPeer::CUSTOMER_ID, $customer->getMobileNumber());
        $getvoipInfos = SeVoipNumberPeer::doSelectOne($getvoipInfo); //->getId();
        if (isset($getvoipInfos)) {
            $voipnumbers = $getvoipInfos->getNumber();
            $voip_customer = $getvoipInfos->getCustomerId();
        } else {
            $voipnumbers = '';
            $voip_customer = '';
        }



        $message_body = "<table width='600px'><tr style='border:0px solid #fff'><td colspan='4' align='right' style='text-align:right; border:0px solid #fff'>" . image_tag('https://wls2.zerocall.com/images/zapna_logo_small.jpg') . "</tr></table><table cellspacing='0' width='600px'><tr><td>Grattis till ditt nya resenummer. Detta nummer är alltid kopplat till den telefon där du har Smartsim aktiverat. Med resenumret blir du nådd utomlands då du har ett lokalt SIM-kort. Se prislistan för hur mycket det kostar att ta emot samtal.
Ditt resenummer är $voipnumbers.<br/><br/>
Med vänlig hälsning<br/><br/>
" . sfConfig::get('app_site_title') . "<br/><a href='" . sfConfig::get('app_site_url') . "'>" . sfConfig::get('app_site_url') . "</a></td></tr></table>";

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($referrer_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType('Transation for VoIP Purchase');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):
//            $email2 = new EmailQueue();
//            $email2->setSubject($subject);
//            $email2->setReceipientName($recepient_agent_name);
//            $email2->setReceipientEmail($recepient_agent_email);
//            $email2->setAgentId($referrer_id);
//            $email2->setCutomerId($customer_id);
//            $email2->setEmailType('Transation for VoIP Purchase');
//            $email2->setMessage($message_body);
//            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType('Transation for VoIP Purchase');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Transation for VoIP Purchase');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Transation for VoIP Purchase');
            $email4->setMessage($message_body);
            $email4->save();
        endif;

//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendCustomerBalanceEmail(Customer $customer, $message_body) {

        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $email_content = get_partial('pScripts/user_alert', array(
            'message' => $message_body
        ));

        $subject = __('Balance Email');
        $recepient_name = '';
        $recepient_email = '';

        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $recepient_email = $customer->getEmail();
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

        if (trim($recepient_email) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setMessage($email_content);
            $email->setReceipientEmail($recepient_email);
            $email->setCutomerId($customer_id);
            $email->setAgentId($referrer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer Balance');
            $email->setReceipientName($recepient_name);
            $email->save();
        endif;
    }

    public static function sendErrorTelinta($message) {

        $subject = 'Error In Telinta';

        $recipient_name_rs = sfConfig::get('app_email_sender_name_rs');
        $recipient_email_rs = sfConfig::get('app_email_sender_email_rs');

        $recipient_name_support = sfConfig::get('app_recipient_name_support');
        $recipient_email_support = sfConfig::get('app_recipient_email_support');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

//********************Sent The Email To RS******************************
        if (trim($recipient_email_rs) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_rs);
            $email->setReceipientEmail($recipient_email_rs);
            $email->setEmailType('Telinta Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($recipient_email_support) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support);
            $email->setReceipientEmail($recipient_email_support);
            $email->setEmailType('Telinta Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendUniqueIdsShortage($sim_type) {

        $subject = 'Unique Ids finished.';
        $message_body = "Uniuqe Ids have been finsihed of SIM Type " . $sim_type . ".<br/><br/>" . sfConfig::get('app_site_title');

        $recipient_name_rs = sfConfig::get('app_email_sender_name_rs');
        $recipient_email_rs = sfConfig::get('app_email_sender_email_rs');

        $recipient_name_support = sfConfig::get('app_recipient_name_support');
        $recipient_email_support = sfConfig::get('app_recipient_email_support');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

//********************Sent The Email To RS******************************
        if (trim($recipient_email_rs) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_rs);
            $email->setReceipientEmail($recipient_email_rs);
            $email->setEmailType('Unique Ids Finished');
            $email->setMessage($message_body);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($recipient_email_support) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support);
            $email->setReceipientEmail($recipient_email_support);
            $email->setEmailType('Unique Ids Finished');
            $email->setMessage($message_body);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendUniqueIdsIssueAgent($uniqueid, Customer $customer) {

        $subject = 'Unique Ids finished.';
        $message_body = "Uniuqe Id " . $uniqueid . " has issue while assigning on " . $customer->getMobileNumber() . "<br/><br/>" . sfConfig::get('app_site_title');

        $recipient_name_rs = sfConfig::get('app_email_sender_name_rs');
        $recipient_email_rs = sfConfig::get('app_email_sender_email_rs');

        $recipient_name_support = sfConfig::get('app_recipient_name_support');
        $recipient_email_support = sfConfig::get('app_recipient_email_support');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

//********************Sent The Email To RS******************************
        if (trim($recipient_email_rs) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_rs);
            $email->setReceipientEmail($recipient_email_rs);
            $email->setEmailType('Unique Ids Finished');
            $email->setMessage($message_body);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($recipient_email_support) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support);
            $email->setReceipientEmail($recipient_email_support);
            $email->setEmailType('Unique Ids Finished');
            $email->setMessage($message_body);
            $email->save();
        endif;

//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendUniqueIdsIssueSmsReg($uniqueid, Customer $customer) {

        $subject = 'Unique Ids finished.';
        $message_body = "<table width='600px'><tr style='border:0px solid #fff'><td colspan='4' align='right' style='text-align:right; border:0px solid #fff'></tr></table><table cellspacing='0' width='600px'><tr><td>
             " . $message . " <br/><br/>
Uniuqe Id " . $uniqueid . " has issue while assigning on " . $customer->getMobileNumber() . " in sms registration<br/><br/>
" . sfConfig::get('app_site_title') . "<br/><a href='" . sfConfig::get('app_site_url') . "'>" . sfConfig::get('app_site_url') . "</a></td></tr></table>";

//Support Informationt
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');
//$sender_emailcdu = sfConfig::get('app_email_sender_email', 'zerocallengineering@googlegroups.com');
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType('Unique Ids Finished');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Unique Ids Finished');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendErrorInTelinta($subject, $message) {

        $recipient_name_rs = sfConfig::get('app_email_sender_name_rs');
        $recipient_email_rs = sfConfig::get('app_email_sender_email_rs');

        $recipient_name_support = sfConfig::get('app_recipient_name_support');
        $recipient_email_support = sfConfig::get('app_recipient_email_support');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

//********************Sent The Email To RS******************************
        if (trim($recipient_email_rs) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_rs);
            $email->setReceipientEmail($recipient_email_rs);
            $email->setEmailType('Telinta Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($recipient_email_support) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support);
            $email->setReceipientEmail($recipient_email_support);
            $email->setEmailType('Telinta Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendAdminRefilEmail(AgentCompany $agent, $agent_order) {
        $vat = 0;

//create transaction
//This Section For Get The Agent Information
        $agent_company_id = $agent->getId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        $agentamount = $agent_order->getAmount();
        $createddate = $agent_order->getCreatedAt('d-m-Y');
        $agentid = $agent_order->getReceiptNo();
        $order_des = $agent_order->getOrderDescription();
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('agent_company/agent_order_receipt', array(
            'order' => $agentid,
            'transaction' => $agentamount,
            'createddate' => $createddate,
            'description' => $order_des,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => true,
            'agent' => $agent
        ));


        $subject = __('Agent Payment Confirmation');


//Support Information
        $sender_name = sfConfig::get('app_email_sender_name', sfConfig::get('app_site_title'));
        $sender_email = sfConfig::get('app_email_sender_email');


        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu', sfConfig::get('app_site_title'));
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent the Email To Kimarin order
        if (trim($sender_email_orders) != ''):

            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email1->setMessage($message_body);

            $email1->save();
        endif;
//---------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendChangeNumberEmail(Customer $customer, $order) {
        $vat = 0;

        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);

//This Section For Get The Agent Information
        $agent_company_id = $customer->getReferrerId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }
        $vat = $transaction->getVat();



        $agent_company_id = $transaction->getAgentCompanyId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);

            $agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $agent_name = '';
        }
//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('affiliate/change_number_order_receipt', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $agent_name,
            'wrap' => false,
        ));

        $subject = __('Change number - payment confirmation');




        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType('Change Number');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($agent_company_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType('Change Number');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType('Change Number');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType('Change Number ');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Change Number');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Change Number');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendAdminRefillEmail(Customer $customer, $order) {
        $vat = 0;


        if ($order) {
            $vat = $order->getIsFirstOrder() == 1 ?
                    ($order->getProduct()->getPrice() * $order->getQuantity() -
                    $order->getProduct()->getInitialBalance()) * .20 :
                    0;
        }
//create transaction
        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);
//if(strstr($transaction->getDescription(),"Refill") || strstr($transaction->getDescription(),"Charge")){
//if(strstr($transaction->getDescription(),"Refill")){
        $vat = $transaction->getAmount() - ($transaction->getAmount() / (sfConfig::get('app_vat_percentage') + 1));
//}
//This Section For Get The Agent Information
        $agent_company_id = $customer->getReferrerId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }
//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('customer/order_receipt_simple', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));

        $subject = __('Payment Confirmation');
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_name_sup = sfConfig::get('app_email_sender_name_sup');
        $sender_email_sup = sfConfig::get('app_email_sender_email_sup');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');


//------------------Sent The Email To Kimarin Order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' refill/charge via admin');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($agent_company_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' refill/charge via admin');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Refill/charge via admin');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Refill/charge via admin');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Refill/charge via admin');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Refill/charge via admin');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendCustomerRegistrationViaRetail(Customer $customer, $order) {
        $product_price = $order->getProduct()->getPrice() - $order->getExtraRefill();
        $vat = sfConfig::get("app_vat_percentage") * $product_price;



        $tc = new Criteria();
        $tc->add(TransactionPeer::ORDER_ID, $order->getId());
        $transaction = TransactionPeer::doSelectOne($tc);


        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('pScripts/order_receipt_sms', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));

        $subject = __('Payment Confirmation');



        $sender_email = sfConfig::get('app_email_sender_email', 'okhan@zapna.com');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_name = sfConfig::get('app_email_sender_name', 'Kimarin');
        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu', 'Kimarin');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

/// Email to Kimarin order
        if ($sender_email_orders != "") {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setEmailType('Retail Activation');
            $email1->setMessage($message_body);
            $email1->save();
        }
//---------------------------------------
//--------------Sent The Email To Support

        $email3 = new EmailQueue();
        $email3->setSubject($subject);
        $email3->setReceipientName($sender_name);
        $email3->setReceipientEmail($sender_email);
        $email3->setEmailType('Retail Activation');
        $email3->setMessage($message_body);
        $email3->save();

        $email3 = new EmailQueue();
        $email3->setSubject($subject);
        $email3->setReceipientName($sender_namecdu);
        $email3->setReceipientEmail($sender_emailcdu);
        $email3->setEmailType('Retail Activation');
        $email3->setMessage($message_body);
        $email3->save();
//-----------------------------------------
        $email3 = new EmailQueue();
        $email3->setSubject($subject);
        $email3->setReceipientName($sender_namers);
        $email3->setReceipientEmail($sender_emailrs);
        $email3->setEmailType('Retail Activation');
        $email3->setMessage($message_body);
        $email3->save();
//-----------------------------------------
    }

    public static function sendErrorInAutoReg($subject, $message) {

        $recipient_name_rs = sfConfig::get('app_email_sender_name_rs');
        $recipient_email_rs = sfConfig::get('app_email_sender_email_rs');

        $recipient_name_support = sfConfig::get('app_recipient_name_support');
        $recipient_email_support = sfConfig::get('app_recipient_email_support');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

//********************Sent The Email To RS******************************
        if (trim($recipient_email_rs) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_rs);
            $email->setReceipientEmail($recipient_email_rs);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($recipient_email_support) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support);
            $email->setReceipientEmail($recipient_email_support);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendRetailRefillEmail(Customer $customer, $order) {
        $vat = 0;

        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);

//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('pScripts/order_receipt_sms', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));

        $subject = __('Payment Confirmation');
        $sender_email = sfConfig::get('app_email_sender_email', 'okhan@zapna.com');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_name = sfConfig::get('app_email_sender_name', 'Kimarin');
        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu', 'Kimarin');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

        if ($sender_email_orders != "") {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setEmailType('Retail Refil');
            $email1->setMessage($message_body);
            $email1->save();
        }
//---------------------------------------
//--------------Sent The Email To Support

        $email3 = new EmailQueue();
        $email3->setSubject($subject);
        $email3->setReceipientName($sender_name);
        $email3->setReceipientEmail($sender_email);
        $email3->setEmailType('Retail Refil');
        $email3->setMessage($message_body);
        $email3->save();

        $email3 = new EmailQueue();
        $email3->setSubject($subject);
        $email3->setReceipientName($sender_namecdu);
        $email3->setReceipientEmail($sender_emailcdu);
        $email3->setEmailType('Retail Refil');
        $email3->setMessage($message_body);
        $email3->save();

        $email3 = new EmailQueue();
        $email3->setSubject($subject);
        $email3->setReceipientName($sender_namers);
        $email3->setReceipientEmail($sender_emailrs);
        $email3->setEmailType('Retail Refil');
        $email3->setMessage($message_body);
        $email3->save();
//-----------------------------------------
    }

    public static function sendCustomerNewcardEmail(Customer $customer, $order, $transaction) {


        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());
        if ($referrer_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $referrer_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('pScripts/newcard_receipt', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
                /* 'vat' => $vat,
                  'agent_name' => $recepient_agent_name,
                  'wrap' => false, */
        ));

        $subject = __('New SIM-card confirmation');
//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($referrer_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType('New Sim Card Purchase');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($referrer_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType('New Sim Card Purchase');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        /* if (trim($recepient_agent_email) != ''):
          $email2 = new EmailQueue();
          $email2->setSubject($subject);
          $email2->setReceipientName($recepient_agent_name);
          $email2->setReceipientEmail($recepient_agent_email);
          $email2->setAgentId($referrer_id);
          $email2->setCutomerId($customer_id);
          $email2->setEmailType('New Sim Card Purchase');
          $email2->setMessage($message_body);
          $email2->save();
          endif; */
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType('New Sim Card Purchase');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('New Sim Card Purchase');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('New Sim Card Purchase');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendCustomerChangeNumberEmail(Customer $customer, $order) {

        $vat = $order->getProduct()->getRegistrationFee() * sfConfig::get('app_vat_percentage');

        $tc = new Criteria();
        $tc->add(TransactionPeer::CUSTOMER_ID, $customer->getId());
        $tc->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
        $transaction = TransactionPeer::doSelectOne($tc);

//This Section For Get The Agent Information
// old idea remove by khan muhammad on 06-12-2012
// $agent_company_id = $customer->getReferrerId();
        $agent_company_id = $transaction->getAgentCompanyId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }
//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('pScripts/change_number_order_receipt', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));

        $subject = __('Change number - payment confirmation');
        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());

//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType('Change number');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($agent_company_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType('Change number');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType('Change number');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType('Change number');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Change number');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('Change number');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendCustomerChangeProduct(Customer $customer, $order, $transaction) {

        $vat = $transaction->getAmount() - ($transaction->getAmount() / (sfConfig::get('app_vat_percentage') + 1));

        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

        if ($referrer_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $referrer_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('payments/order_receipt_payment', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));

        $subject = __('Change product - payment confirmation');
//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($referrer_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer Change Product');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($referrer_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer Change Product');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):
            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($referrer_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Customer  Change Product');
            $email2->setMessage($message_body);
            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . 'Customer  Change Product');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . 'Customer  Change Product');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
        if (trim($sender_emailrs) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namers);
            $email5->setReceipientEmail($sender_emailrs);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . 'Customer  Change Product');
            $email5->setMessage($message_body);
            $email5->save();
        endif;

        if (trim($sender_emailsup) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namesup);
            $email5->setReceipientEmail($sender_emailsup);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . 'Customer  Change Product');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
    }

    public static function sendCustomerChangeProductConfirm(Customer $customer, $order, $transaction) {

        $vat = $transaction->getAmount() - ($transaction->getAmount() / (sfConfig::get('app_vat_percentage') + 1));

        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

        if ($referrer_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $referrer_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('payments/order_receipt_product_change', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => false,
        ));

        $subject = __('Confirmation of product change');
//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');
        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');
        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):
            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($referrer_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . 'Confirmation of product change');
            $email2->setMessage($message_body);
            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . 'Confirmation of product change');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . 'Confirmation of product change');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
        if (trim($sender_emailrs) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namers);
            $email5->setReceipientEmail($sender_emailrs);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . 'Confirmation of product change');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
        if (trim($sender_emailsup) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namesup);
            $email5->setReceipientEmail($sender_emailsup);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . 'Confirmation of product change');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
    }

    public static function sendBlockCustomerEmail(Customer $customer) {


        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('customer/block_customer', array(
            'customer' => $customer,
            'wrap' => false,
        ));

        $subject = __('Block account');
//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');
        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');
        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($referrer_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . 'Block account');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . 'Block account');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . 'Block account');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
        if (trim($sender_emailrs) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namers);
            $email5->setReceipientEmail($sender_emailrs);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . 'Block account');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
        if (trim($sender_emailsup) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namesup);
            $email5->setReceipientEmail($sender_emailsup);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . 'Block account');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
    }

    public static function sendCustomerNewcardEmailAgent(Customer $customer, $order, $transaction, $agentId) {


        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
//  $referrer_id = trim($customer->getReferrerId());
        if ($agentId != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agentId);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }
        $vat = $transaction->getVat();

        $agent_company_id = $transaction->getAgentCompanyId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);

            $agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $agent_name = '';
        }
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('affiliate/newcard_receipt', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'wrap' => true,
            'agent_name' => $agent_name,
                /* 'wrap' => false, */
        ));
        $message_bodyAgent = get_partial('affiliate/newcard_receipt', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $agent_name,
            'wrap' => false,
        ));

        $subject = __('New SIM-card confirmation');
//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent The Email To Kimarin order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($referrer_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType('New Sim Card Purchase');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($referrer_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType('New Sim Card Purchase');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):
            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($referrer_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType('New Sim Card Purchase');
            $email2->setMessage($message_bodyAgent);
            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType('New Sim Card Purchase');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('New Sim Card Purchase');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType('New Sim Card Purchase');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendCustomerChangeProductAgent(Customer $customer, $order, $transaction) {

        $vat = $transaction->getAmount() - ($transaction->getAmount() / (sfConfig::get('app_vat_percentage') + 1));

        $recepient_email = trim($customer->getEmail());
        if ($customer->getBusiness()) {
            $recepient_name = $customer->getLastName();
        } else {
            $recepient_name = sprintf('%s %s', $customer->getFirstName(), $customer->getLastName());
        }
        $customer_id = trim($customer->getId());
        $referrer_id = trim($customer->getReferrerId());

        if ($referrer_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $referrer_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }
        $vat = $transaction->getVat();

        $agent_company_id = $transaction->getAgentCompanyId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);

            $agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $agent_name = '';
        }
//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('affiliate/order_receipt_payment', array(
            'customer' => $customer,
            'order' => $order,
            'transaction' => $transaction,
            'vat' => $vat,
            'agent_name' => $agent_name,
            'wrap' => false,
        ));

        $subject = __('Change product - payment confirmation');
//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");
        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');
//------------------Sent The Email To Kimarin order
        if (trim($sender_email_orders) != '') {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($referrer_id);
            $email1->setCutomerId($customer_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Customer Change Product');
            $email1->setMessage($message_body);
            $email1->save();
        }
//----------------------------------------
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($referrer_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Customer Change Product');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):
            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($referrer_id);
            $email2->setCutomerId($customer_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Customer  Change Product');
            $email2->setMessage($message_body);
            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . 'Customer  Change Product');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . 'Customer  Change Product');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
        if (trim($sender_emailrs) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namers);
            $email5->setReceipientEmail($sender_emailrs);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . 'Customer  Change Product');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
        if (trim($sender_emailsup) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namesup);
            $email5->setReceipientEmail($sender_emailsup);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . 'Customer  Change Product');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
    }

    public static function sendCustomerTopup($topupTransaction, $section = 'affiliate') {


        $recepient_email = $topupTransaction->getAgentEmail();
        $recepient_name = $topupTransaction->getAgentCompanyName();
        $customer_id = trim($topupTransaction->getcustomerMobileNumber());
        $referrer_id = trim($topupTransaction->getAgentCompanyId());

//$this->renderPartial('affiliate/order_receipt', array(
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial($section . '/topup_receipt', array(
            'topupTransaction' => $topupTransaction,
            'wrap' => true,
        ));


        $subject = __('Topup Customer');
//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');
        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');
        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');
//------------------Sent The Email To Customer
        if (trim($recepient_email) != '') {
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recepient_name);
            $email->setReceipientEmail($recepient_email);
            $email->setAgentId($referrer_id);
            $email->setCutomerId($customer_id);
            $email->setEmailType(sfConfig::get('app_site_title') . ' Topup account');
            $email->setMessage($message_body);
            $email->save();
        }
//----------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($referrer_id);
            $email3->setCutomerId($customer_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Topup account');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To CDU
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($referrer_id);
            $email4->setCutomerId($customer_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Topup account');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
        if (trim($sender_emailrs) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namers);
            $email5->setReceipientEmail($sender_emailrs);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . ' Topup account');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
        if (trim($sender_emailsup) != ''):
            $email5 = new EmailQueue();
            $email5->setSubject($subject);
            $email5->setReceipientName($sender_namesup);
            $email5->setReceipientEmail($sender_emailsup);
            $email5->setAgentId($referrer_id);
            $email5->setCutomerId($customer_id);
            $email5->setEmailType(sfConfig::get('app_site_title') . ' Topup account');
            $email5->setMessage($message_body);
            $email5->save();
        endif;
    }

    public static function sendTopupErrorEmail($message) {

        $subject = 'Error:Transaction Failed on MPayment';

        $recipient_name_rs = sfConfig::get('app_email_sender_name_rs');
        $recipient_email_rs = sfConfig::get('app_email_sender_email_rs');

        $recipient_name_support = sfConfig::get('app_recipient_name_support');
        $recipient_email_support = sfConfig::get('app_recipient_email_support');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

//********************Sent The Email To RS******************************
        if (trim($recipient_email_rs) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_rs);
            $email->setReceipientEmail($recipient_email_rs);
            $email->setEmailType('Telinta Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($recipient_email_support) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support);
            $email->setReceipientEmail($recipient_email_support);
            $email->setEmailType('Telinta Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function smsNotSentEmail($employeList) {

        $subject = "SMS Not Working";
        $message_body = "Please investigate <br/>" . $employeList;

        $recipient_name_rs = sfConfig::get('app_recipient_name_rs');
        $recipient_email_rs = sfConfig::get('app_recipient_email_rs');

        $recipient_name_support = sfConfig::get('app_recipient_name_support');
        $recipient_email_support = sfConfig::get('app_recipient_email_support');

        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');

//**********************Sent The Email To RS****************************
        if ($recipient_email_rs != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_rs);
            $email->setReceipientEmail($recipient_email_rs);
            $email->setEmailType('SMS not sent issue');
            $email->setMessage($message_body);
            $email->save();
        endif;
//**********************************************************************
//*******************Sent The Email To Support**************************
        if ($recipient_email_support != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support);
            $email->setReceipientEmail($recipient_email_support);
            $email->setEmailType('SMS not sent issue');
            $email->setMessage($message_body);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To MS*************************
        if (trim($sender_namesup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Auto Registration Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendCardFinishEmail($message) {

        $subject = 'Update:Cards are Finished';

        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');
        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_namesup = sfConfig::get('app_email_sender_name_sup');
        $sender_emailsup = sfConfig::get('app_email_sender_email_sup');
        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//********************Sent The Email To RS******************************
        if (trim($sender_email) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_name);
            $email->setReceipientEmail($sender_email);
            $email->setEmailType('Card update');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($sender_emailcdu) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namecdu);
            $email->setReceipientEmail($sender_emailcdu);
            $email->setEmailType('Card update');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($sender_emailsup) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namesup);
            $email->setReceipientEmail($sender_emailsup);
            $email->setEmailType('Card update');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//********************Sent The Email To Support*************************
        if (trim($sender_emailrs) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($sender_namers);
            $email->setReceipientEmail($sender_emailrs);
            $email->setEmailType('Card update');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendResellerAgentRefilEmail(AgentCompany $agent, $agent_order) {
        $vat = 0;

//create transaction
//This Section For Get The Agent Information
        $agent_company_id = $agent->getId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        $agentamount = $agent_order->getAmount();
        $createddate = $agent_order->getCreatedAt('d-m-Y');
        $agentid = $agent_order->getReceiptNo();
        $agentOrderDescription = $agent_order->getOrderDescription();
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('reseller/agent_order_receipt', array(
            'order' => $agentid,
            'transaction' => $agentamount,
            'createddate' => $createddate,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => true,
            'agent' => $agent,
            'orderdescription' => $agentOrderDescription
        ));


        $subject = __('Agent Payment Confirmation');


//Support Information
        $sender_name = sfConfig::get('app_email_sender_name');
        $sender_email = sfConfig::get('app_email_sender_email');

        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu');
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//--------------Sent The Email To Kimarin order email
        if (trim($sender_email_orders) != ''):
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email1->setMessage($message_body);
            $email1->save();
        endif;
//-----------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' refill via agent');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendAdminChargeEmail(AgentCompany $agent, $agent_order) {
        $vat = 0;

//create transaction
//This Section For Get The Agent Information
        $agent_company_id = $agent->getId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        $agentamount = $agent_order->getAmount();
        $createddate = $agent_order->getCreatedAt('d-m-Y');
        $agentid = $agent_order->getReceiptNo();
        $order_des = $agent_order->getOrderDescription();
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('agent_company/agent_order_receipt', array(
            'order' => $agentid,
            'transaction' => $agentamount,
            'createddate' => $createddate,
            'description' => $order_des,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => true,
            'agent' => $agent
        ));


        $subject = __('Agent-Charge');


//Support Information
        $sender_name = sfConfig::get('app_email_sender_name', sfConfig::get('app_site_title'));
        $sender_email = sfConfig::get('app_email_sender_email');


        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu', sfConfig::get('app_site_title'));
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent the Email To Kimarin order
        if (trim($sender_email_orders) != ''):

            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email1->setMessage($message_body);

            $email1->save();
        endif;
//---------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendErrorInTopUP($message) {

        $subject = 'TOPUP Error:';
        $recipient_name_support = sfConfig::get('app_email_sender_name');
        $recipient_email_support = sfConfig::get('app_email_sender_email');

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

        $recipient_name_support_tech = sfConfig::get('app_recipient_name_support');
        $recipient_email_support_tech = sfConfig::get('app_recipient_email_support');

//********************Sent The Email To Okhan*************************
        if (trim($recipient_email_support) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support);
            $email->setReceipientEmail($recipient_email_support);
            $email->setEmailType('TOPUP Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email4->setMessage($message);
            $email4->save();
        endif;
//-----------------------------------------
//********************Sent The Email To Support*************************
        if (trim($recipient_email_support_tech) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($recipient_name_support_tech);
            $email->setReceipientEmail($recipient_email_support_tech);
            $email->setEmailType('Telinta Error');
            $email->setMessage($message);
            $email->save();
        endif;
//**********************************************************************
    }

    public static function sendResellerRefillDealerEmail(AgentCompany $agent, $agent_order) {
        $vat = 0;

//create transaction
//This Section For Get The Agent Information
        $agent_company_id = $agent->getId();
        if ($agent_company_id != '') {
            $c = new Criteria();
            $c->add(AgentCompanyPeer::ID, $agent_company_id);
            $recepient_agent_email = AgentCompanyPeer::doSelectOne($c)->getEmail();
            $recepient_agent_name = AgentCompanyPeer::doSelectOne($c)->getName();
        } else {
            $recepient_agent_email = '';
            $recepient_agent_name = '';
        }

//$this->renderPartial('affiliate/order_receipt', array(
        $agentamount = $agent_order->getAmount();
        $createddate = $agent_order->getCreatedAt('d-m-Y');
        $agentid = $agent_order->getReceiptNo();
        $order_des = $agent_order->getOrderDescription();
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $message_body = get_partial('reseller/agent_order_receipt', array(
            'order' => $agentid,
            'transaction' => $agentamount,
            'createddate' => $createddate,
            'description' => $order_des,
            'vat' => $vat,
            'agent_name' => $recepient_agent_name,
            'wrap' => true,
            'agent' => $agent
        ));


        $subject = __('Company Payment Confirmation');


//Support Information
        $sender_name = sfConfig::get('app_email_sender_name', sfConfig::get('app_site_title'));
        $sender_email = sfConfig::get('app_email_sender_email');


        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu', sfConfig::get('app_site_title'));
        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');

        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
        $sender_email_orders = sfConfig::get("app_email_sender_email_order");

        $sender_namers = sfConfig::get('app_email_sender_name_rs');
        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');

//------------------Sent the Email To Kimarin order
        if (trim($sender_email_orders) != ''):

            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($sender_name_orders);
            $email1->setReceipientEmail($sender_email_orders);
            $email1->setAgentId($agent_company_id);
            $email1->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email1->setMessage($message_body);

            $email1->save();
        endif;
//---------------------------------------
//------------------Sent the Email To Agent
        if (trim($recepient_agent_email) != ''):

            $email2 = new EmailQueue();
            $email2->setSubject($subject);
            $email2->setReceipientName($recepient_agent_name);
            $email2->setReceipientEmail($recepient_agent_email);
            $email2->setAgentId($agent_company_id);
            $email2->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email2->setMessage($message_body);

            $email2->save();
        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setAgentId($agent_company_id);
            $email3->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setAgentId($agent_company_id);
            $email4->setEmailType(sfConfig::get('app_site_title') . ' Agent refill via admin');
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendCronJobHistory(CronJobHistory $cronHistory, CronJobs $cron_jobs) {


        $emails = explode(";", $cron_jobs->getEmail());
        $subject = "job name:" . $cron_jobs->getJobName() . " Started at:" . $cronHistory->getStart() . " Completed at: " . $cronHistory->getEnd();

        $message = "<table><tr>
                            <th>Defination File</th>
                            <th>Data File</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                    ";
        $c = new Criteria();
        $c->add(CronJobHistoryInfoPeer::CRON_JOB_HISTORY_ID, $cronHistory->getId());
        if (CronJobHistoryInfoPeer::doCount($c) > 0) {



            $cronjobHistoryInfos = CronJobHistoryInfoPeer::doSelect($c);
            foreach ($cronjobHistoryInfos as $cronjobHistoryInfo) {
                $message .= "<tr>
                        <td>" . $cronjobHistoryInfo->getXml() . "</td>
                        <td>" . $cronjobHistoryInfo->getCsv() . " </td>
                        <td>" . $cronjobHistoryInfo->getMessage() . " </td>
                
                        <td>";
                if ($cronjobHistoryInfo->getStatus()) {
                    $message .= "Successful";
                } else {
                    $message .= "Failed";
                }
                $message .= " </td>
                        
                        </tr>";
            }
        } else {
            $message .= "<tr>
                        <td colspan='4'> No History Info found.</td>
                        </tr>";
        }

        $message .= "</table>";

//echo $message;
        foreach ($emails as $email) {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($email);
            $email1->setReceipientEmail($email);
            $email1->setMessage($message);
            $email1->save();
        }
    }

    public static function sendCronJobsSuccessEmail($job_name, $cron_job_emails, $message_body) {
        $emails = explode(";", $cron_job_emails);
        $subject = "Scheduled Job:" . $job_name . " executed successfully.";
        foreach ($emails as $email) {
            $email1 = new EmailQueue();
            $email1->setSubject($subject);
            $email1->setReceipientName($email);
            $email1->setReceipientEmail($email);
            $email1->setMessage($message_body);
            $email1->save();
        }
    }

    public static function sendItemsXMLError($message_body) {
//Support Information
//        $sender_name = sfConfig::get('app_email_sender_name', sfConfig::get('app_site_title'));
//        $sender_email = sfConfig::get('app_email_sender_email');
//        
//
//        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu', sfConfig::get('app_site_title'));
//        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
//        
//        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
//        $sender_email_orders = sfConfig::get("app_email_sender_email_order");
//        
//        $sender_namers = sfConfig::get('app_email_sender_name_rs');
//        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');
//        
        $subject = "Error in XML parsing of Items";

//------------------Sent the Email To Kimarin order
//        if (trim($sender_email_orders) != ''):

        $email1 = new EmailQueue();
        $email1->setSubject($subject);
        $email1->setReceipientName("Okhan");
        $email1->setReceipientEmail("ok@zap-itsolutions.com");
        $email1->setMessage($message_body);

        $email1->save();
//        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendItemsImageError($message_body) {
//Support Information
//        $sender_name = sfConfig::get('app_email_sender_name', sfConfig::get('app_site_title'));
//        $sender_email = sfConfig::get('app_email_sender_email');
//        
//
//        $sender_namecdu = sfConfig::get('app_email_sender_name_cdu', sfConfig::get('app_site_title'));
//        $sender_emailcdu = sfConfig::get('app_email_sender_email_cdu');
//        
//        $sender_name_orders = sfConfig::get("app_email_sender_name_order");
//        $sender_email_orders = sfConfig::get("app_email_sender_email_order");
//        
//        $sender_namers = sfConfig::get('app_email_sender_name_rs');
//        $sender_emailrs = sfConfig::get('app_email_sender_email_rs');
//        
        $subject = "Item Not found for Image";

//------------------Sent the Email To Kimarin order
//        if (trim($sender_email_orders) != ''):

        $email1 = new EmailQueue();
        $email1->setSubject($subject);
        $email1->setReceipientName("system");
        $email1->setReceipientEmail("support.tech@zap-itsolutions.com");
        $email1->setMessage($message_body);

        $email1->save();
//        endif;
//---------------------------------------
//--------------Sent The Email To okhan
        if (trim($sender_email) != ''):
            $email3 = new EmailQueue();
            $email3->setSubject($subject);
            $email3->setReceipientName($sender_name);
            $email3->setReceipientEmail($sender_email);
            $email3->setMessage($message_body);
            $email3->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To cdu
        if (trim($sender_emailcdu) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namecdu);
            $email4->setReceipientEmail($sender_emailcdu);
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
//--------------Sent The Email To RS
        if (trim($sender_emailrs) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject($subject);
            $email4->setReceipientName($sender_namers);
            $email4->setReceipientEmail($sender_emailrs);
            $email4->setMessage($message_body);
            $email4->save();
        endif;
//-----------------------------------------
    }

    public static function sendAdminForgotPassworEmail($fpUser, $email_content) {
        $email_id = $fpUser->getEmail();
        $name = $fpUser->getName();
        $subject = "Reset Password";
        if (trim($email_id) != ''):
            $email = new EmailQueue();
            $email->setSubject($subject);
            $email->setReceipientName($name);
            $email->setReceipientEmail($email_id);
            $email->setMessage($email_content);
            $email->save();
        endif;
    }

    public static function sendErrorInVoucherSync($message) {
        $sender_name_admin = sfConfig::get('app_email_sender_name_admin');
        $sender_email_admin = sfConfig::get('app_email_sender_email_admin');
        if (trim($sender_email_admin) != ''):
            $email4 = new EmailQueue();
            $email4->setSubject("Sync error occured");
            $email4->setReceipientName($sender_name_admin);
            $email4->setReceipientEmail($sender_email_admin);
            $email4->setMessage($message);
            $email4->save();
        endif;
    }

    public static function sendEmailBookoutReceived($bookoutNote) {

        $uselect = new Criteria();
        $uselect->add(UserPeer::IS_SUPER_USER, 1);
        $uselect->addAnd(UserPeer::STATUS_ID, 3);
        $uselect->addAnd(UserPeer::BOOKOUT_SYNC_EMAIL, 1);
        $users = UserPeer::doSelect($uselect);
        foreach ($users as $user) {
            if (trim($user->getEmail()) != '') {
                $message = " Bookout Synced Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                $email4 = new EmailQueue();
                $email4->setSubject("Bookout Note Synced");
                $email4->setReceipientName($user->getName());
                $email4->setReceipientEmail($user->getEmail());
                $email4->setMessage($message);
                $email4->save();
            }
        }
    }

    public static function sendEmailBookoutOk($bookoutNoteId) {


        $bookout = new Criteria();
        $bookout->add(BookoutNotesPeer::NOTE_ID, $bookoutNoteId);

        $bookouts = BookoutNotesPeer::doSelect($bookout);
        $bookoutOk = 1;
        $branch = "";
        foreach ($bookouts as $bookout) {
//////////////////////////////////////////////////////////////  
            $branch = $bookout->getBranchNumber();
            if ($bookoutOk) {
                if ($bookout->getQuantity() == $bookout->getReceivedQuantity()) {
                    $bookoutOk = 1;
                } else {
                    $bookoutOk = 0;
                }
            }


//////////////////////////////////////////////////////////////            
        }



        $Message = "";
        $Message .="<h3 style=' border-bottom-style: solid;'> Note ID : " . $bookoutNoteId . "</h3>";
        $Message .="<h3 style=' border-bottom-style: solid;'> Branch ID : " . $branch . "</h3>";

        $Message .= "<table border=1 width='100%'><tr> <th>Item Id</th><th>Sent Quantity</th><th>Received Quantity</th><th>POS Comments</th><th>CMS Comments</th></tr>";
        foreach ($bookouts as $bookout) {

            $Message .="<tr> <td>" . $bookout->getItemId() . "</td><td>" . $bookout->getQuantity() . "</td><td>" . $bookout->getReceivedQuantity() . "</td><td>" . $bookout->getComment() . "</td><td>" . $bookout->getReplyComment() . "</td></tr>";
        }
        $Message .="</table>";

        $message = $Message;


        if ($bookoutOk) {
            $uselect = new Criteria();
            $uselect->add(UserPeer::IS_SUPER_USER, 1);
            $uselect->addAnd(UserPeer::STATUS_ID, 3);
            $uselect->addAnd(UserPeer::BOOKOUT_OK_EMAIL, 1);
            $users = UserPeer::doSelect($uselect);

            foreach ($users as $user) {
                if (trim($user->getEmail()) != '') {

                    // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                    $email4 = new EmailQueue();
                    $email4->setSubject("Bookout Note Updated OK ");
                    $email4->setReceipientName($user->getName());
                    $email4->setReceipientEmail($user->getEmail());
                    $email4->setMessage($message);
                    $email4->save();
                }
            }
        } else {
            $uselect = new Criteria();
            $uselect->add(UserPeer::IS_SUPER_USER, 1);
            $uselect->addAnd(UserPeer::STATUS_ID, 3);
            $uselect->addAnd(UserPeer::BOOKOUT_CHANGE_EMAIL, 1);
            $users = UserPeer::doSelect($uselect);

            foreach ($users as $user) {
                if (trim($user->getEmail()) != '') {

                    // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                    $email4 = new EmailQueue();
                    $email4->setSubject("Bookout Note Updated Change ");
                    $email4->setReceipientName($user->getName());
                    $email4->setReceipientEmail($user->getEmail());
                    $email4->setMessage($message);
                    $email4->save();
                }
            }
        }
    }

    public static function sendEmailDeliveryNote($bookoutNoteId) {


        $bookout = new Criteria();
        $bookout->add(DeliveryNotesPeer::NOTE_ID, $bookoutNoteId);

        $bookouts = DeliveryNotesPeer::doSelect($bookout);
        $bookoutOk = 1;
        foreach ($bookouts as $bookout) {
//////////////////////////////////////////////////////////////    
            $branch = $bookout->getBranchNumber();
            if ($bookoutOk) {
                if ($bookout->getQuantity() == $bookout->getReceivedQuantity()) {
                    $bookoutOk = 1;
                } else {
                    $bookoutOk = 0;
                }
            }


//////////////////////////////////////////////////////////////            
        }
        $Message = "";
        $Message .="<h3 style=' border-bottom-style: solid;'> Note ID : " . $bookoutNoteId . "</h3>";
        $Message .="<h3 style=' border-bottom-style: solid;'> Branch ID : " . $branch . "</h3>";

        $Message .= "<table border=1  width='100%'><tr> <th>Item Id</th><th>Quantity</th><th>Received Quantity</th><th>Comments</th></tr>";
        foreach ($bookouts as $bookout) {

            $Message .="<tr> <td>" . $bookout->getItemId() . "</td><td>" . $bookout->getQuantity() . "</td><td>" . $bookout->getReceivedQuantity() . "</td><td>" . $bookout->getComment() . "</td></tr>";
        }
        $Message .="</table>";

        $message = $Message;


        if ($bookoutOk) {
            $uselect = new Criteria();
            $uselect->add(UserPeer::IS_SUPER_USER, 1);
            $uselect->addAnd(UserPeer::STATUS_ID, 3);
            $uselect->addAnd(UserPeer::DELIVERYNOTE_OK_EMAIL, 1);
            $users = UserPeer::doSelect($uselect);

            foreach ($users as $user) {
                if (trim($user->getEmail()) != '') {

                    // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                    $email4 = new EmailQueue();
                    $email4->setSubject("Delivery Note Updated OK ");
                    $email4->setReceipientName($user->getName());
                    $email4->setReceipientEmail($user->getEmail());
                    $email4->setMessage($message);
                    $email4->save();
                }
            }
        } else {
            $uselect = new Criteria();
            $uselect->add(UserPeer::IS_SUPER_USER, 1);
            $uselect->addAnd(UserPeer::STATUS_ID, 3);
            $uselect->addAnd(UserPeer::DELIVERYNOTE_CHANGE_EMAIL, 1);
            $users = UserPeer::doSelect($uselect);

            foreach ($users as $user) {
                if (trim($user->getEmail()) != '') {

                    // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                    $email4 = new EmailQueue();
                    $email4->setSubject("Delivery Note Updated Change ");
                    $email4->setReceipientName($user->getName());
                    $email4->setReceipientEmail($user->getEmail());
                    $email4->setMessage($message);
                    $email4->save();
                }
            }
        }
    }

    /////////////////////Day Start Dinomination//////////////////////////////////// 
    public static function sendEmailDayStartDenomination($day_start_id) {

        //   echo "-----ds-----".$day_start_id."--------------------";
        $Message = "";
        $c = new Criteria();
        $c->add(DayStartsPeer::ID, $day_start_id);
        $dayStart = DayStartsPeer::doSelectOne($c);
        $denominations = DenominationsPeer::doSelect(new Criteria());

        $sp = new Criteria();
        $sp->add(ShopsPeer::ID, $dayStart->getShopId());
        if (ShopsPeer::doCount($sp) > 0) {
            $selectedshop = ShopsPeer::doSelectOne($sp);

            $Message .="<h3 style=' border-bottom-style: solid;'> Branch Id: " . $selectedshop->getBranchNumber() . "</h3>";
        }
        $Message .="<h3 style=' border-bottom-style: solid;'> Day started At: " . $dayStart->getDayStartedAt() . "</h3>";


        $Message .= "<table  border=1   width='100%'>
        <thead>
            <tr>
                <th  align=left >Denomination</th>";

        $c5 = new Criteria();
        $c5->add(DayStartsAttemptsPeer::DAY_START_ID, $dayStart->getId());
        $c5->addDescendingOrderByColumn(DayStartsAttemptsPeer::ID);
        $count = DayStartsAttemptsPeer::doCount($c5);
        for ($i = 1; $i <= $count; $i++) {


            $Message .="<th  align=left >Day Start</th>";
        }



        $Message .=" </tr>
        </thead>
        <tbody>";

        $i = 0;
        foreach ($denominations as $deomination) {

            $Message .="<tr >
                    <td  align=left >" . $deomination->getTitle() . "</td>";


            if (DayStartsAttemptsPeer::doCount($c5) > 0) {
                $daystarts = DayStartsAttemptsPeer::doSelect($c5);
                foreach ($daystarts as $daystart) {
                    $ced = new Criteria();
                    $ced->add(DayStartDenominationsPeer::DAY_ATTEMPT_ID, $daystart->getId());
                    $ced->add(DayStartDenominationsPeer::DENOMINATION_ID, $deomination->getId());
                    $Message .="<td  align=left >";
                    if (DayStartDenominationsPeer::doCount($ced) > 0) {
                        $dstartdenomination = DayStartDenominationsPeer::doSelectOne($ced);
                        $Message .=$dstartdenomination->getCount();
                    }

                    $Message .=" </td> ";
                }
            }








            $Message .="</tr>";

            $i++;
        }

        $Message .="</tbody>
        <tfoot>
            <tr>
                <th  align=left >Total:</th>";

        $dayStartAtteptsObj = DayStartsAttemptsPeer::doSelect($c5);
        foreach ($dayStartAtteptsObj as $dayStartAtteptObj) {



            $Message .="<th  align=left >" . number_format($dayStartAtteptObj->getTotalAmount(), 2, ',', '') . "</th>";
        }




        $Message .="</tr>
            <tr>
                <th  align=left >Expected Total:</th>";

        $daystartatemptObj = DayStartsAttemptsPeer::doSelect($c5);

        foreach ($daystartatemptObj as $daystartatemobj) {



            $Message .=" <th  align=left >" . number_format($daystartatemobj->getExpectedAmount(), 2, ',', '') . "</th>";
        }


        $Message .=" </tr>
        </tfoot> 
    </table>";



        $message = $Message;
        $uselect = new Criteria();
        $uselect->add(UserPeer::IS_SUPER_USER, 1);
        $uselect->addAnd(UserPeer::STATUS_ID, 3);
        $uselect->addAnd(UserPeer::DAYSTART_EMAIL, 1);
        $users = UserPeer::doSelect($uselect);

        foreach ($users as $user) {
            if (trim($user->getEmail()) != '') {

                // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                $email4 = new EmailQueue();
                $email4->setSubject("Day Start");
                $email4->setReceipientName($user->getName());
                $email4->setReceipientEmail($user->getEmail());
                $email4->setMessage($message);
                $email4->save();
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////   
    /////////////////////Day Start Dinomination//////////////////////////////////// 
    public static function sendEmailDayEndDenomination($day_end_id) {

        $de = new Criteria();
        $de->add(DayEndsPeer::ID, $day_end_id);

        $dayEndRs = DayEndsPeer::doSelectOne($de);


        $Message = "";
        $c = new Criteria();
        $c->add(DayStartsPeer::ID, $dayEndRs->getDayStartId());
        $dayStart = DayStartsPeer::doSelectOne($c);
        $denominations = DenominationsPeer::doSelect(new Criteria());

        $sp = new Criteria();
        $sp->add(ShopsPeer::ID, $dayEndRs->getShopId());
        if (ShopsPeer::doCount($sp) > 0) {
            $selectedshop = ShopsPeer::doSelectOne($sp);

            $Message .="<h3 style=' border-bottom-style: solid;'> Branch Id: " . $selectedshop->getBranchNumber() . "</h3>";
        }
        $Message .="<h3 style=' border-bottom-style: solid;'> Day Ended At: " . $dayEndRs->getDayEndedAt() . "</h3>";


        $Message .= "<table  border=1   width='100%'>
        <thead>
            <tr>
                <th  align=left >Denomination</th>";


        $c2 = new Criteria();
        $c2->add(DayEndsPeer::DAY_START_ID, $dayStart->getId());
        $c2->addDescendingOrderByColumn(DayEndsPeer::DAY_ENDED_AT);
        $count = DayEndsPeer::doCount($c2);
        for ($i = 1; $i <= $count; $i++) {


            $Message .="<th  align=left >Day End</th>";
        }


        $Message .=" </tr>
        </thead>
        <tbody>";

        $i = 0;
        foreach ($denominations as $deomination) {

            $Message .="<tr >
                    <td  align=left >" . $deomination->getTitle() . "</td>";



            if (DayEndsPeer::doCount($c2) > 0) {
                $dayends = DayEndsPeer::doSelect($c2);
                foreach ($dayends as $dayend) {
                    $ced = new Criteria();
                    $ced->add(DayEndDenominationsPeer::DAY_END_ID, $dayend->getId());
                    $ced->add(DayEndDenominationsPeer::DENOMINATION_ID, $deomination->getId());
                    $Message .="<td  align=left >";
                    if (DayEndDenominationsPeer::doCount($ced) > 0) {
                        $denddenomination = DayEndDenominationsPeer::doSelectOne($ced);
                        $Message .=$denddenomination->getCount();
                    }

                    $Message .=" </td> ";
                }
            }








            $Message .="</tr>";

            $i++;
        }

        $Message .="</tbody>
        <tfoot>
            <tr>
                <th  align=left >Total:</th>";


        $dayendsObj = DayEndsPeer::doSelect($c2);

        foreach ($dayendsObj as $dayendObj) {



            $Message .=" <th  align=left >" . number_format($dayendObj->getTotalAmount(), 2, ',', '') . "</th>";
        }




        $Message .="</tr>
            <tr>
                <th  align=left >Expected Total:</th>";

        $dayendsObj = DayEndsPeer::doSelect($c2);

        foreach ($dayendsObj as $dayendObj) {



            $Message .="<th  align=left >" .number_format($dayendObj->getExpectedAmount(), 2, ',', '') . "</th>";
        }

        $Message .=" </tr>
        </tfoot> 
    </table>";



        $message = $Message;
        $uselect = new Criteria();
        $uselect->add(UserPeer::IS_SUPER_USER, 1);
        $uselect->addAnd(UserPeer::STATUS_ID, 3);
        $uselect->addAnd(UserPeer::DAYEND_EMAIL, 1);
        $users = UserPeer::doSelect($uselect);

        foreach ($users as $user) {
            if (trim($user->getEmail()) != '') {

                // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                $email4 = new EmailQueue();
                $email4->setSubject("Day End");
                $email4->setReceipientName($user->getName());
                $email4->setReceipientEmail($user->getEmail());
                $email4->setMessage($message);
                $email4->save();
            }
        }

        ///////////////////////////////////////////////////////////////////////////////   
    }

    ///////////////////////////Sale email/////////////////////////////
    public static function sendEmailSale($shopTransactionIds, $shop_id) {


        $s = new Criteria();
        $s->add(TransactionsPeer::SHOP_ID, $shop_id);
        $s->addAnd(TransactionsPeer::STATUS_ID, 3);
        $s->addAnd(TransactionsPeer::SHOP_TRANSACTION_ID, $shopTransactionIds, Criteria::IN);
        $s->addAnd(TransactionsPeer::TRANSACTION_TYPE_ID, 3);
        $s->addGroupByColumn(TransactionsPeer::ORDER_ID);
        $transactions = TransactionsPeer::doSelect($s);
        // alla order selected



        foreach ($transactions as $transaction) {
            ////////////////selecting single order////////////////////////////////
            $or = new Criteria();
            $or->add(TransactionsPeer::SHOP_ID, $shop_id);
            $or->addAnd(TransactionsPeer::STATUS_ID, 3);
            $or->addAnd(TransactionsPeer::ORDER_ID, $transaction->getOrderId());
            $or->addAnd(TransactionsPeer::TRANSACTION_TYPE_ID, 3);
            $tordertransactions = TransactionsPeer::doSelect($or);
            //////////////////////////////////////////////////////////////////////////////    
            $shopC = new Criteria();
            $shopC->add(ShopsPeer::ID, $shop_id, Criteria::EQUAL);
            $shopData = ShopsPeer::doSelectOne($shopC);
            $Message = "";
            $Message .="<h3 style=' border-bottom-style: solid;'> Date : " . $transaction->getCreatedAt() . "</h3>";
            $Message .="<h3 style=' border-bottom-style: solid;'> Receipt Number : " . $transaction->getShopReceiptNumberId() . "</h3>";
            $Message .="<h3 style=' border-bottom-style: solid;'> Branch Number : " . $shopData->getBranchNumber() . "</h3>";
            $usersh = new Criteria();
            $usersh->add(UserPeer::ID, $transaction->getUserId(), Criteria::EQUAL);
            $userData = UserPeer::doSelectOne($usersh);


            $Message .="<h3 style=' border-bottom-style: solid;'> Cashier : " . $userData->getName() . "</h3>";

            $Message .= "<table  border=0 width='100%'>
        <thead>
            <tr>
                <th align=left>Sr#</th>  <th align=left>Item Name</th> <th align=left>Item Id</th> <th align=left>Quantity</th>  <th align=left>Price</th>  <th align=left>Discount</th><th align=left>Total</th><tr></thead><tbody>";
            $i = 1;
            $subtotal = 0;
            $discount = 0;
            foreach ($tordertransactions as $tordertransaction) {
                $subtotal = $subtotal + $tordertransaction->getSoldPrice();
                $discount = $discount + $tordertransaction->getDiscountValue();
                $transactionDiscountTitle = "";
                if ($tordertransaction->getDiscountTypeId() > 0) {
                    //  echo "-----discount type----".$tordertransaction->getDiscountTypeId();
                    $transac = new Criteria();
                    $transac->add(DiscountTypesPeer::ID, $tordertransaction->getDiscountTypeId(), Criteria::EQUAL);
                    $transactionDiscount = DiscountTypesPeer::doSelectOne($transac);
                    $transactionDiscountTitle = $transactionDiscount->getName();
                }
                $Message .="<tr style=' border-top-style: dotted;'>
                    <td style=' border-top-style: dotted;'>" . $i . "</td> <td style=' border-top-style: dotted;'>" . $tordertransaction->getDescription1() . "</td><td style=' border-top-style: dotted;'>" . $tordertransaction->getItemId() . "</td><td style=' border-top-style: dotted;'>" . $tordertransaction->getQuantity() . "</td><td style=' border-top-style: dotted;'>" . number_format($tordertransaction->getSellingPrice(), 2, ',', '') . "</td> <td style=' border-top-style: dotted;'>" . number_format($tordertransaction->getDiscountValue(), 2, ',', '') . " " . $transactionDiscountTitle . "</td><td style=' border-top-style: dotted;'>" . number_format($tordertransaction->getSoldPrice(), 2, ',', '') . "</td></tr>";


                $i++;
            }

            $csc = new Criteria();
            $csc->add(SystemConfigPeer::KEYS, "Vat Percentage", Criteria::EQUAL);
            $csc->addOr(SystemConfigPeer::ID, 6);
            $dnItem = SystemConfigPeer::doSelectOne($csc);


            $Message .="<tr><td colspan=7 ></td></tr>
       </tbody>
        <tfoot>
            <tr>
                <th align=left  colspan=2  style=' border-top-style: dotted;'>Sub Total:</th> <th colspan=4  style=' border-top-style: dotted;'></th> <th  align=left style=' border-top-style: dotted;'>" . number_format($subtotal, 2, ',', '') . "</th></tr>";

            $orderDetail = OrdersPeer::retrieveByPK($transaction->getOrderId());
            $orderDiscountName = "";
            if ($orderDetail->getDiscountTypeId() > 0) {

                $ordis = new Criteria();
                $ordis->add(DiscountTypesPeer::ID, $orderDetail->getDiscountTypeId(), Criteria::EQUAL);
                $orderDiscount = DiscountTypesPeer::doSelectOne($ordis);
                $orderDiscountName = $orderDiscount->getName();
            }
            $Message .="<tr>
                  <th align=left  colspan=2  style=' border-top-style: dotted;'>Discount:</th> <th colspan=4  style=' border-top-style: dotted;'> </th><th  align=left style=' border-top-style: dotted;'>" .number_format($orderDetail->getDiscountValue(), 2, ',', '') . " " . $orderDiscountName . "</th></tr>";
            $Message .="<tr>
                <th align=left  colspan=2  style=' border-top-style: dotted;'>Sale Tax</th> <th colspan=3  align=left style=' border-top-style: dotted;'></th><th  align=left style=' border-top-style: dotted;'>" . number_format($dnItem->getValues(), 2, ',', '') . " % </th> <th  align=left style=' border-top-style: dotted;'>" . number_format(($subtotal * $dnItem->getValues() / 100), 2, ',', '') . "</th></tr>";

            $orderPay = new Criteria();
            $orderPay->add(OrderPaymentsPeer::ORDER_ID, $transaction->getOrderId(), Criteria::EQUAL);
            $orderPayDatas = OrderPaymentsPeer::doSelect($orderPay);

            foreach ($orderPayDatas as $orderPayData) {
                $paymenttype = PaymentTypesPeer::retrieveByPK($orderPayData->getPaymentTypeId());

// number_format($number, 2, ',', '');
                $Message .="<tr>
                <th align=left  colspan=2  style=' border-top-style: dotted;'>Payment type</th>  <th  style=' border-top-style: dotted;'  align=left>" . $paymenttype->getTitle() . "</th> <th   align=left colspan=3  style=' border-top-style: dotted;'> Amount</th><th  align=left style=' border-top-style: dotted;'>" . number_format($orderPayData->getAmount(), 2, ',', '') . "</th></tr>";
            }

            $Message .="   
    </table>";



            $message = $Message;
            $uselect = new Criteria();
            $uselect->add(UserPeer::IS_SUPER_USER, 1);
            $uselect->addAnd(UserPeer::STATUS_ID, 3);
            $uselect->addAnd(UserPeer::SALE_EMAIL, 1);
            $users = UserPeer::doSelect($uselect);

            foreach ($users as $user) {
                if (trim($user->getEmail()) != '') {

                    // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                    $email4 = new EmailQueue();
                    $email4->setSubject("Sale Email");
                    $email4->setReceipientName($user->getName());
                    $email4->setReceipientEmail($user->getEmail());
                    $email4->setMessage($message);
                    $email4->save();
                }
            }
        }
    }

    /////////////////////////////////////////////////////////////////////////


    public static function sendEmailBranchAdded(Shops $shop) {

        $Message = "";


        if ($shop->getNegativeSale()) {
            $negativeSale = "Yes";
        } else {
            $negativeSale = "No";
        }

        $lanugages = LanguagesPeer::retrieveByPK($shop->getLanguageId());
        $saleReceiptFormat = ReceiptFormatsPeer::retrieveByPK($shop->getSaleReceiptFormatId());
        $returnReceiptFormat = ReceiptFormatsPeer::retrieveByPK($shop->getReturnReceiptFormatId());
        if ($shop->getStatusId() == 5) {
            $status = "Inactive";
        } elseif ($shop->getStatusId() == 3) {
            $status = "Active";
        } else {
            $status = "";
        }

        $bookoutFormat = ReceiptFormatsPeer::retrieveByPK($shop->getBookoutFormatId());
        $discountType = DiscountTypesPeer::retrieveByPK($shop->getDiscountTypeId());
        if ($shop->getReceiptAutoPrint()) {
            $receiptAutoPrint = "Yes";
        } else {
            $receiptAutoPrint = "No";
        }
        $Message .='<table width="100%" border="0">'
                . '<tr><td>Name : </td><td>' . $shop->getName() . '</td><td>Branch number: </td><td>' . $shop->getBranchNumber() . '</td></tr>'
                . '<tr><td>Company number: </td><td>' . $shop->getCompanyNumber() . '</td><td>Password: </td><td>' . $shop->getPassword() . '</td></tr>'
                . '<tr><td>Address: </td><td>' . $shop->getAddress() . '</td><td>Zip: </td><td>' . $shop->getZip() . '</td></tr>'
                . '<tr><td>Place: </td><td>' . $shop->getPlace() . '</td><td>Country: </td><td>' . $shop->getCountry() . '</td></tr>'
                . '<tr><td>Tel: </td><td>' . $shop->getTel() . '</td><td>Fax: </td><td>' . $shop->getFax() . '</td></tr>'
                . '<tr><td>Negative Sale:</td><td>' . $negativeSale . '</td><td>Language:</td><td>' . $lanugages->getTitle() . '</td></tr>'
                . '<tr><td>Time out: </td><td>' . $shop->getTimeOut() . '</td><td>Start Value Sale Receipt:</td><td>' . $shop->getStartValueSaleReceipt() . '</td></tr>'
                . '<tr><td>Start Value Bookout:</td><td>' . $shop->getStartValueBookout() . '</td><td>Sale Receipt Format:</td><td>' . $saleReceiptFormat->getTitle() . '</td></tr>'
               
                . '<tr><td>Status:</td><td>' . $status . '</td><td>Bookout number Format: </td><td>' . $bookoutFormat->getTitle() . '</td></tr>'
                . '<tr><td>Employee Discount Type: </td><td>' . $discountType->getName() . '</td><td>Employee Discount Value:</td><td>' . number_format($shop->getDiscountValue(), 2, ',', '') . '</td></tr>'
                . '<tr><td>Max Day End Attempts:</td><td>' . $shop->getMaxDayEndAttempts() . '</td><td>Receipt Header Position:</td><td>' . $shop->getReceiptHeaderPosition() . '</td></tr>'
                . '<tr><td>Receipt footer line 1: </td><td>' . $shop->getReceiptTaxStatmentOne() . '</td><td>Receipt footer line 2: </td><td>' . $shop->getReceiptTaxStatmentTwo() . '</td></tr>'
                . '<tr><td>Receipt footer line 3:</td><td>' . $shop->getReceiptTaxStatmentThree() . '</td><td>Receipt Auto Print:</td><td>' . $receiptAutoPrint . '</td></tr>'
                . '</table>';

        $message = $Message;
        $uselect = new Criteria();
        $uselect->add(UserPeer::IS_SUPER_USER, 1);
        $uselect->addAnd(UserPeer::STATUS_ID, 3);
        $uselect->addAnd(UserPeer::SETTING_EMAIL, 1);
        $users = UserPeer::doSelect($uselect);

        foreach ($users as $user) {
            if (trim($user->getEmail()) != '') {

                // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                $email4 = new EmailQueue();
                $email4->setSubject("New Branch Added");
                $email4->setReceipientName($user->getName());
                $email4->setReceipientEmail($user->getEmail());
                $email4->setMessage($message);
                $email4->save();
            }
        }
    }

/////////////////////////////////////////////////////////////////////////////////////

    public static function sendEmailBranchSettingUpdated($request) {

//        var
//        var_dump($request->getParameter('id'));
//        die;
        $shopOldData = ShopsPeer::retrieveByPK($request->getParameter('id'));
        $shopNewData = $shopOldData;
        $sendEmail = 0;
        $Message = "";
        $Message .="<h3 style=' border-bottom-style: solid;'> Branch Number : " . $shopNewData->getBranchNumber() . "</h3>";

        if ($request->getParameter('negative_sale')) {
            $negativeSale = "Yes";
        } else {
            $negativeSale = "No";
        }

        $lanugages = LanguagesPeer::retrieveByPK($request->getParameter('languages'));
        $saleReceiptFormat = ReceiptFormatsPeer::retrieveByPK($request->getParameter('saleFormat'));
        $returnReceiptFormat = ReceiptFormatsPeer::retrieveByPK($request->getParameter('returnFormat'));
        if ($request->getParameter('status_id') == 5) {
            $status = "Inactive";
        } elseif ($request->getParameter('status_id') == 3) {
            $status = "Active";
        } else {
            $status = "";
        }

        $bookoutFormat = ReceiptFormatsPeer::retrieveByPK($request->getParameter('bookout_format_id'));
        $discountType = DiscountTypesPeer::retrieveByPK($request->getParameter("discount_type_id"));
        if ($request->getParameter("receipt_auto_print")) {
            $receiptAutoPrint = "Yes";
        } else {
            $receiptAutoPrint = "No";
        }
        $Message .='<table width="100%" border="0">';

        if ($shopOldData->getName() != $request->getParameter('name')) {
            $Message .='<tr><td>Name : </td><td>' . $request->getParameter('name') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getBranchNumber() != $request->getParameter('branch_number')) {
            $Message .='<tr><td>Branch number: </td><td>' . $request->getParameter('branch_number') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getCompanyNumber() != $request->getParameter('company_number')) {
            $Message .='<tr><td>Company number:</td><td>' . $request->getParameter('company_number') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getPassword() != $request->getParameter('password')) {
            $Message .='<tr><td>Password:</td><td>' . $request->getParameter('password') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getAddress() != $request->getParameter('address')) {
            $Message .='<tr><td>Address:</td><td>' . $request->getParameter('address') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getZip() != $request->getParameter('zip')) {
            $Message .='<tr><td>Zip:</td><td>' . $request->getParameter('zip') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getPlace() != $request->getParameter('place')) {
            $Message .='<tr><td>Place:</td><td>' . $request->getParameter('place') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getCountry() != $request->getParameter('country')) {
            $Message .='<tr><td>Country:</td><td>' . $request->getParameter('country') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getTel() != $request->getParameter('tel')) {
            $Message .='<tr><td>Tel:</td><td>' . $request->getParameter('tel') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getFax() != $request->getParameter('fax')) {
            $Message .='<tr><td>Fax:</td><td>' . $request->getParameter('fax') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getNegativeSale() != $request->getParameter('negative_sale')) {
            $Message .='<tr><td>Negative Sale:</td><td>' . $negativeSale . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getLanguageId() != $request->getParameter('languages')) {
            $Message .='<tr><td>Language:</td><td>' . $lanugages->getTitle() . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getTimeOut() != $request->getParameter('time_out')) {
            $Message .='<tr><td>Time out:</td><td>' . $request->getParameter('time_out') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getStartValueSaleReceipt() != $request->getParameter('sale_receipt')) {
            $Message .='<tr><td>Start Value Sale Receipt:</td><td>' . $request->getParameter('sale_receipt') . '</td></tr>';
            $sendEmail = 1;
        }
//        if ($shopOldData->getStartValueReturnReceipt() != $request->getParameter('return_receipt')) {
//            $Message .='<tr><td>Start Value Return Receipt:</td><td>' . $request->getParameter('return_receipt') . '</td></tr>';
//            $sendEmail = 1;
//        }
        if ($shopOldData->getSaleReceiptFormatId() != $request->getParameter('saleFormat')) {
            $Message .='<tr><td>Sale Receipt Format:</td><td>' . $saleReceiptFormat->getTitle() . '</td></tr>';
            $sendEmail = 1;
        }

//        if ($shopOldData->getReturnReceiptFormatId() != $request->getParameter('returnFormat')) {
//            $Message .='<tr><td>Return Receipt Format:</td><td>' . $returnReceiptFormat->getTitle() . '</td></tr>';
//            $sendEmail = 1;
//        }
        if ($shopOldData->getStartValueBookout() != $request->getParameter('start_value_bookout')) {
            $Message .='<tr><td>Start Value Bookout:</td><td>' . $request->getParameter('start_value_bookout') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getStatusId() != $request->getParameter('status_id')) {
            $Message .='<tr><td>Status:</td><td>' . $status . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getBookoutFormatId() != $request->getParameter('bookout_format_id')) {
            $Message .='<tr><td>Bookout number Format:</td><td>' . $bookoutFormat->getTitle() . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getDiscountTypeId() != $request->getParameter("discount_type_id")) {
            $Message .='<tr><td>Employee Discount Type:</td><td>' . $discountType->getName() . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getDiscountValue() != $request->getParameter("discount_value")) {
            $Message .='<tr><td>Employee Discount Value:</td><td>' . number_format($request->getParameter("discount_value"), 2, ',', '') . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getMaxDayEndAttempts() != $request->getParameter("max_day_end_attempts")) {
            $Message .='<tr><td>Max Day End Attempts:</td><td>' . $request->getParameter("max_day_end_attempts") . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getReceiptHeaderPosition() != $request->getParameter("receipt_header_position")) {
            $Message .='<tr><td>Receipt Header Position:</td><td>' . $request->getParameter("receipt_header_position") . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getReceiptTaxStatmentOne() != $request->getParameter("receipt_tax_statement_one")) {
            $Message .='<tr><td>Receipt footer line 1:</td><td>' . $request->getParameter("receipt_tax_statement_one") . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getReceiptTaxStatmentTwo() != $request->getParameter("receipt_tax_statement_two")) {
            $Message .='<tr><td>Receipt footer line 2:</td><td>' . $request->getParameter("receipt_tax_statement_two") . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getReceiptTaxStatmentThree() != $request->getParameter("receipt_tax_statement_three")) {
            $Message .='<tr><td>Receipt footer line 3:</td><td>' . $request->getParameter("receipt_tax_statement_three") . '</td></tr>';
            $sendEmail = 1;
        }
        if ($shopOldData->getReceiptAutoPrint() != $request->getParameter("receipt_auto_print")) {
            $Message .='<tr><td>Receipt Auto Print:</td><td>' . $receiptAutoPrint . '</td></tr>';
            $sendEmail = 1;
        }



        $Message .='</table>';

        if ($sendEmail) {
            $message = $Message;
            $uselect = new Criteria();
            $uselect->add(UserPeer::IS_SUPER_USER, 1);
            $uselect->addAnd(UserPeer::STATUS_ID, 3);
            $uselect->addAnd(UserPeer::SETTING_EMAIL, 1);
            $users = UserPeer::doSelect($uselect);

            foreach ($users as $user) {
                if (trim($user->getEmail()) != '') {

                    // $message = " Bookout Received Note ID is " . $bookoutNote->getNoteId() . "  and  Branch Number is " . $bookoutNote->getBranchNumber();
                    $email4 = new EmailQueue();
                    $email4->setSubject("Branch Setting Updated");
                    $email4->setReceipientName($user->getName());
                    $email4->setReceipientEmail($user->getEmail());
                    $email4->setMessage($message);
                    $email4->save();
                }
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////
}

?>