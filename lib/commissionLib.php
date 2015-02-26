<?php

class commissionLib {

    public static function registrationCommission($gentid, $productid, $transactionid) {



        $agent_company_id = $gentid;
        $transactionid = $transactionid;
        $tr = new Criteria();
        $tr->add(TransactionPeer::ID, $transactionid);
        $transaction = TransactionPeer::doSelectOne($tr);


        $or = new Criteria();
        $or->add(CustomerOrderPeer::ID, $transaction->getOrderId());
        $order = CustomerOrderPeer::doSelectOne($or);

        $ca = new Criteria();
        $ca->add(AgentCompanyPeer::ID, $agent_company_id);
        $agent = AgentCompanyPeer::doSelectOne($ca);
        //echo $agent->getId();
        //getting agent commission
        $cc = new Criteria();
        $cc->add(AgentCommissionPackagePeer::ID, $agent->getAgentCommissionPackageId());
        $commission_package = AgentCommissionPackagePeer::doSelectOne($cc);

        ///////////////////////////commision calculation by agent product ///////////////////////////////////////
        $cp = new Criteria;
        $cp->add(AgentProductPeer::AGENT_ID, $agent_company_id);
        $cp->add(AgentProductPeer::PRODUCT_ID, $productid);
        $agentproductcount = AgentProductPeer::doCount($cp);
        $cs = new Criteria;
        $cs->add(CustomerPeer::ID, $order->getCustomerId());
        $customer = CustomerPeer::doSelectOne($cs);

        if ($agentproductcount > 0) {
            $p = new Criteria;
            $p->add(AgentProductPeer::AGENT_ID, $agent_company_id);
            $p->add(AgentProductPeer::PRODUCT_ID, $productid);

            $agentproductcomesion = AgentProductPeer::doSelectOne($p);
            $agentcomession = $agentproductcomesion->getRegShareEnable();
        }

        ////////   commission setting  through  agent commision//////////////////////

        if (isset($agentcomession) && $agentcomession != "") {


            if ($order->getIsFirstOrder()) {
                if ($agentproductcomesion->getIsRegShareValuePc()) {
                    $transaction->setCommissionAmount(($transaction->getAmount() / 100) * $agentproductcomesion->getRegShareValue());
                } else {

                    $transaction->setCommissionAmount($agentproductcomesion->getRegShareValue());
                }
            } else {
                if ($agentproductcomesion->getIsExtraPaymentsShareValuePc()) {
                    $transaction->setAgentCommission(($transaction->getAmount() / 100) * $agentproductcomesion->getExtraPaymentsShareValue());
                } else {
                    $transaction->setAgentCommission($agentproductcomesion->getExtraPaymentsShareValue());
                }
            }
        } else {

            if ($order->getIsFirstOrder()) {
                if ($commission_package->getIsRegShareValuePc()) {
                    $transaction->setCommissionAmount(($transaction->getAmount() / 100) * $commission_package->getRegShareValue());
                } else {

                    $transaction->setCommissionAmount($commission_package->getRegShareValue());
                }
            } else {
                if ($commission_package->getIsExtraPaymentsShareValuePc()) {
                    $transaction->setAgentCommission(($transaction->getAmount() / 100) * $commission_package->getExtraPaymentsShareValue());
                } else {
                    $transaction->setAgentCommission($commission_package->getExtraPaymentsShareValue());
                }
            }
        }


        $transaction->save();




/////////////////////////end of commission setting ////////////////////////////////////////////
//        echo 'entering if';
//        echo '<br/>';
        if ($agent->getIsPrepaid() == true) {

//            echo 'agent is prepaid';
//            echo '<br/>';
//            echo $agent->getBalance();
//            echo '<br/>';
//            echo $transaction->getCommissionAmount();
//            echo '<br/>';
//            echo $agent->getBalance() < $transaction->getCommissionAmount();

            if ($agent->getBalance() < ($transaction->getAmount() - $transaction->getCommissionAmount())) {
                //  $this->redirect('affiliate/setProductDetails?product_id='.$order->getProductId().'&customer_id='.$transaction->getCustomerId().'&balance_error=1');
                $varerror = "balance_error";

                ///////////////////////////////////////////////////
                $transaction->setTransactionStatusId(1);
                $transaction->save();
                ///////////////////////////////////////////////////

                $order->setOrderStatusId(1);
                $order->save();
                //////////////////////////////////////////////////////////
                $customer->setCustomerStatusId(1);

                $customer->save();
                ////////////////////////////////////////////////////////////////////////
                return $varerror;
            }
            $agent->setBalance($agent->getBalance() - ($transaction->getAmount() - $transaction->getCommissionAmount()));
            $agent->save();
            ////////////////////////////////////
            $remainingbalance = $agent->getBalance();
            $amount = $transaction->getAmount() - $transaction->getCommissionAmount();
            $amount = -$amount;
            $aph = new AgentPaymentHistory();
            $aph->setAgentId($agent_company_id);
            $aph->setCustomerId($transaction->getCustomerId());
            $aph->setExpeneseType(1);
            $aph->setAmount($amount);
            $aph->setRemainingBalance($remainingbalance);
            $aph->save();




            ////////////////////////////////////////////
        }

        $varerror = "success";


        return $varerror;
    }

    public static function registrationCommissionCustomer($gentid, $productid, $transactionid) {

        $agent_company_id = $gentid;
        $transactionid = $transactionid;
        $tr = new Criteria();
        $tr->add(TransactionPeer::ID, $transactionid);
        $transaction = TransactionPeer::doSelectOne($tr);


        $or = new Criteria();
        $or->add(CustomerOrderPeer::ID, $transaction->getOrderId());
        $order = CustomerOrderPeer::doSelectOne($or);

        $ca = new Criteria();
        $ca->add(AgentCompanyPeer::ID, $agent_company_id);
        $agent = AgentCompanyPeer::doSelectOne($ca);
        //$agent->getId();
        //getting agent commission
        $cc = new Criteria();
        $cc->add(AgentCommissionPackagePeer::ID, $agent->getAgentCommissionPackageId());
        $commission_package = AgentCommissionPackagePeer::doSelectOne($cc);

        ///////////////////////////commision calculation by agent product ///////////////////////////////////////
        $cp = new Criteria;
        $cp->add(AgentProductPeer::AGENT_ID, $agent_company_id);
        $cp->add(AgentProductPeer::PRODUCT_ID, $productid);
        $agentproductcount = AgentProductPeer::doCount($cp);
        $cs = new Criteria;
        $cs->add(CustomerPeer::ID, $order->getCustomerId());
        $customer = CustomerPeer::doSelectOne($cs);

        if ($agentproductcount > 0) {
            $p = new Criteria;
            $p->add(AgentProductPeer::AGENT_ID, $agent_company_id);
            $p->add(AgentProductPeer::PRODUCT_ID, $productid);

            $agentproductcomesion = AgentProductPeer::doSelectOne($p);
            $agentcomession = $agentproductcomesion->getRegShareEnable();
        }
        ////////   commission setting  through  agent commision//////////////////////
        if (isset($agentcomession) && $agentcomession != "") {
            if ($order->getIsFirstOrder()) {
                if ($agentproductcomesion->getIsRegShareValuePc()) {
                    $transaction->setCommissionAmount(($transaction->getAmount() / 100) * $agentproductcomesion->getRegShareValue());
                } else {

                    $transaction->setCommissionAmount($agentproductcomesion->getRegShareValue());
                }
            } else {
                if ($agentproductcomesion->getIsExtraPaymentsShareValuePc()) {
                    $transaction->setAgentCommission(($transaction->getAmount() / 100) * $agentproductcomesion->getExtraPaymentsShareValue());
                } else {
                    $transaction->setAgentCommission($agentproductcomesion->getExtraPaymentsShareValue());
                }
            }
        } else {

            if ($order->getIsFirstOrder()) {
                if ($commission_package->getIsRegShareValuePc()) {
                    $transaction->setCommissionAmount(($transaction->getAmount() / 100) * $commission_package->getRegShareValue());
                } else {

                    $transaction->setCommissionAmount($commission_package->getRegShareValue());
                }
            } else {
                if ($commission_package->getIsExtraPaymentsShareValuePc()) {
                    $transaction->setAgentCommission(($transaction->getAmount() / 100) * $commission_package->getExtraPaymentsShareValue());
                } else {
                    $transaction->setAgentCommission($commission_package->getExtraPaymentsShareValue());
                }
            }
        }


        $transaction->save();




/////////////////////////end of commission setting ////////////////////////////////////////////
        //echo 'entering if';
        // echo '<br/>';
        if ($agent->getIsPrepaid() == true) {

            // echo 'agent is prepaid';
            // echo '<br/>';
            $agent->getBalance();
            //echo '<br/>';
            $transaction->getCommissionAmount();
            // echo '<br/>';


            $agent->setBalance($agent->getBalance() + $transaction->getCommissionAmount());
            $agent->save();
            ////////////////////////////////////
            $remainingbalance = $agent->getBalance();
            $amount = $transaction->getCommissionAmount();
            $amount = $amount;
            $aph = new AgentPaymentHistory();
            $aph->setAgentId($agent_company_id);
            $aph->setCustomerId($transaction->getCustomerId());
            $aph->setExpeneseType(1);
            $aph->setAmount($amount);
            $aph->setRemainingBalance($remainingbalance);
            $aph->save();




            ////////////////////////////////////////////
        }

        $varerror = "success";


        return $varerror;
    }

///////////////////////////////////////////////////////////////////////////////////////
    /*
     * $retrunFailuresSMS is being used when there will be low balance of agent or reseller we need to send crosponding sms.
     */
    public static function topUp($gentid, $productid, $transactionid, $onlyValidate = false,$retrunFailureSMS=false) {
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $agent_company_id = $gentid;
        $transactionid = $transactionid;
        $tr = new Criteria();
        $tr->add(TopupTransactionsPeer::ID, $transactionid);
        $transaction = TopupTransactionsPeer::doSelectOne($tr);
        $ca = new Criteria();
        $ca->add(AgentCompanyPeer::ID, $agent_company_id);
        $agent = AgentCompanyPeer::doSelectOne($ca);
        //echo $agent->getId();
        //getting agent commission
        $reseller = ResellerPeer::retrieveByPK($agent->getResellerId());
        $cc = new Criteria();
        $cc->add(AgentCommissionPackagePeer::ID, $agent->getAgentCommissionPackageId());
        $commission_package = AgentCommissionPackagePeer::doSelectOne($cc);
        $cr = new Criteria();
        $cr->add(ResellerCommissionPackagePeer::ID, $reseller->getResellercommissionPackageId());
        $reseller_commission_package = ResellerCommissionPackagePeer::doSelectOne($cr);
        ///////////////////////////commision calculation by reseller product ///////////////////////////////////////
        $cp = new Criteria;
        $cp->add(ResellerProductPeer::RESELLER_ID, $reseller->getId());
        $cp->add(ResellerProductPeer::PRODUCT_ID, $productid);
        $resellerproductcount = ResellerProductPeer::doCount($cp);
        if ($resellerproductcount > 0) {
            $resellerproductcomesion = ResellerProductPeer::doSelectOne($cp);
            $resellercomession = $resellerproductcomesion->getExtraPaymentsShareEnable();
        }
        ////////////////////////commission calculation for reseller commission////////////////////
        if (isset($resellercomession) && $resellercomession != "") {
             if ($resellerproductcomesion->getIsExtraPaymentsShareValuePc()) {
                $transaction->setResellerCommission(($transaction->getProductRegistrationFee() / 100) * $resellerproductcomesion->getExtraPaymentsShareValue());
            } else {
                 $transaction->setResellerCommission($resellerproductcomesion->getExtraPaymentsShareValue());
            }
        } else {
  
            if ($reseller_commission_package->getIsExtraPaymentsShareValuePc()) {
                $transaction->setResellerCommission(($transaction->getProductRegistrationFee() / 100) * $reseller_commission_package->getExtraPaymentsShareValue());
            } else {
                $transaction->setResellerCommission($reseller_commission_package->getExtraPaymentsShareValue());
            }
        }
        ///////////////////////////commision calculation by agent product ///////////////////////////////////////
        $cp = new Criteria;
        $cp->add(AgentProductPeer::AGENT_ID, $agent_company_id);
        $cp->add(AgentProductPeer::PRODUCT_ID, $productid);
        $agentproductcount = AgentProductPeer::doCount($cp);

        if ($agentproductcount > 0) {

            $agentproductcomesion = AgentProductPeer::doSelectOne($cp);
            $agentcomession = $agentproductcomesion->getExtraPaymentsShareEnable();
        }

        ////////   commission setting  through  agent commision//////////////////////

        if (isset($agentcomession) && $agentcomession != "") {

            if ($agentproductcomesion->getIsExtraPaymentsShareValuePc()) {

                $transaction->setAgentCommission(($transaction->getProductRegistrationFee() / 100) * $agentproductcomesion->getExtraPaymentsShareValue());
            } else {
                $transaction->setAgentCommission($agentproductcomesion->getExtraPaymentsShareValue());
            }
        } else {
            if ($commission_package->getIsExtraPaymentsShareValuePc()) {
                $transaction->setAgentCommission(($transaction->getProductRegistrationFee() / 100) * $commission_package->getExtraPaymentsShareValue());
            } else {
                $transaction->setAgentCommission($commission_package->getExtraPaymentsShareValue());
            }
        }
        //calculated amount for agent commission
           $transaction->setResellerVatCommission($transaction->getResellerCommission() * sfConfig::get('app_vat_on_commission'));
        $transaction->setVatOnCommission($transaction->getAgentCommission() * sfConfig::get('app_vat_on_commission'));
        $transaction->save();
        //if ($agent->getIsPrepaid() == true) {
        $product = ProductPeer::retrieveByPK($productid);
        $totalBalanceLimit = -($agent->getCreditLimit() - $agent->getBalance());
        //new Statement by Khan Muhammad  kmmalik.com
        if ($totalBalanceLimit < ($product->getRegistrationFee() - $transaction->getAgentCommission())) {
           if($retrunFailureSMS){
                 $sms = SmsTextPeer::retrieveByPK(1)->getMessageText();
                 $sms = str_replace("(balance)", $totalBalanceLimit, $sms);
                 return $sms;
            }else{
               return false; 
            }
        }

        $reseller = ResellerPeer::retrieveByPK($agent->getResellerId());
        $rsavbalance =  - ($reseller->getCreditLimit()-$reseller->getBalance());
        if ($rsavbalance < ($product->getRegistrationFee() - $transaction->getAgentCommission())) {
           if($retrunFailureSMS){
                 $sms = SmsTextPeer::retrieveByPK(23)->getMessageText();
                 return $sms;
            }else{
               return false; 
            }
        }
        if (!$onlyValidate) {
            $agent->setBalance($agent->getBalance() - ($product->getRegistrationFee() - $transaction->getAgentCommission()));
            $agent->save();
            $remainingbalance = $agent->getBalance();
            $debit = -($product->getRegistrationFee() - $transaction->getAgentCommission());
            $amount = $transaction->getAgentCommission();
            $rscommision=$transaction->getResellerCommission();
            $compantAcBalance=$agent->getBalance();
            $companyAvBalance=-($agent->getCreditLimit() - $agent->getBalance());
            $aph = new AgentPaymentHistory();
            $aph->setAgentId($agent_company_id);
            $aph->setExpeneseType(7); // Expance Type 7 for MPayments Topups
            $aph->setAmount($amount);
            $aph->setCreditLimit($agent->getCreditLimit());
            $aph->setDebit($debit);
            $aph->setCompanyAvailableBalance($companyAvBalance);
            $aph->setCompanyActualBalance($compantAcBalance);
           
            $aph->setTransactionId($transaction->getId());
            $aph->setCommission($amount);
          /////////////////////////////////////////////////////
              $aph->setTransactionFromId($transaction->getTransactionFromId());
              $aph->setTransactionTypeId(7);
              $aph->setComments('Topup');
            
    ///////////////////////////////////////////////////////        
            
            
            $aph->save();
            //////////////////////////////////////////////////////////////////////////////////////////////
            
            
            $reseller->setBalance($reseller->getBalance() - ($product->getRegistrationFee() -$rscommision));
            $reseller->save();
            $resellerdebit = -($product->getRegistrationFee() - $transaction->getResellerCommission());
            $rsavbalance=$reseller->getBalance()-($reseller->getCreditLimit());
               $aph->setResellerDebit($resellerdebit);
               $aph->setResellerId($reseller->getId());
            $aph->setResellerCreditLimit($reseller->getCreditLimit());
            $aph->setResellerAvailableBalance($rsavbalance);
            $aph->setResellerActualBalance($reseller->getBalance());
            $aph->setResellerCommission($rscommision);
            $aph->save();
         ///////////////////////////////////////////////////////////////////////////////////////////////////   
        }
        //}
        return true;
    }

    public static function refilCustomer($gentid, $productid, $transactionid) {

        /////////////////////////////////////////////////////////////////////////////////////////////////

        $agent_company_id = $gentid;
        $transactionid = $transactionid;
        $tr = new Criteria();
        $tr->add(TransactionPeer::ID, $transactionid);
        $transaction = TransactionPeer::doSelectOne($tr);

        $or = new Criteria();
        $or->add(CustomerOrderPeer::ID, $transaction->getOrderId());
        $order = CustomerOrderPeer::doSelectOne($or);

        $ca = new Criteria();
        $ca->add(AgentCompanyPeer::ID, $agent_company_id);
        $agent = AgentCompanyPeer::doSelectOne($ca);
        //echo $agent->getId();
        //getting agent commission
        $cc = new Criteria();
        $cc->add(AgentCommissionPackagePeer::ID, $agent->getAgentCommissionPackageId());
        $commission_package = AgentCommissionPackagePeer::doSelectOne($cc);

        ///////////////////////////commision calculation by agent product ///////////////////////////////////////
        $cp = new Criteria;
        $cp->add(AgentProductPeer::AGENT_ID, $agent_company_id);
        $cp->add(AgentProductPeer::PRODUCT_ID, $productid);
        $agentproductcount = AgentProductPeer::doCount($cp);
        $cs = new Criteria;
        $cs->add(CustomerPeer::ID, $order->getCustomerId());
        $customer = CustomerPeer::doSelectOne($cs);

        if ($agentproductcount > 0) {
            $p = new Criteria;
            $p->add(AgentProductPeer::AGENT_ID, $agent_company_id);
            $p->add(AgentProductPeer::PRODUCT_ID, $order->getProductId());

            $agentproductcomesion = AgentProductPeer::doSelectOne($p);
            $agentcomession = $agentproductcomesion->getExtraPaymentsShareEnable();
        }

        ////////   commission setting  through  agent commision//////////////////////

        if (isset($agentcomession) && $agentcomession != "") {

            if ($agentproductcomesion->getIsExtraPaymentsShareValuePc()) {

                $transaction->setCommissionAmount(($transaction->getAmount() / 100) * $agentproductcomesion->getExtraPaymentsShareValue());
            } else {



                $transaction->setCommissionAmount($agentproductcomesion->getExtraPaymentsShareValue());
            }
        } else {


            if ($commission_package->getIsExtraPaymentsShareValuePc()) {
                $transaction->setCommissionAmount(($transaction->getAmount() / 100) * $commission_package->getExtraPaymentsShareValue());
            } else {
                $transaction->setCommissionAmount($commission_package->getExtraPaymentsShareValue());
            }
        }

        //calculated amount for agent commission



        $is_recharged = 1;

//                                echo 'is_recharged '.$is_recharged;
//                                echo '<br/>';
        if ($is_recharged) {
//                                    echo 'isrecharged = true';
            $transaction->save();

            if ($agent->getIsPrepaid() == true) {
                $agent->setBalance($agent->getBalance() + $transaction->getCommissionAmount());
                $agent->save();
                $remainingbalance = $agent->getBalance();
                $amount = $transaction->getCommissionAmount();
                $amount = $amount;
                $aph = new AgentPaymentHistory();
                $aph->setAgentId($agent_company_id);
                $aph->setCustomerId($transaction->getCustomerId());
                $aph->setExpeneseType(4);
                $aph->setAmount($amount);
                $aph->setRemainingBalance($remainingbalance);
                $aph->save();
            }
        }
    }
  public static function resellerCommissionAssigner($gentid, $productid, $transactionid) {
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $agent_company_id = $gentid;
        $transactionid = $transactionid;
        $tr = new Criteria();
        $tr->add(TopupTransactionsPeer::ID, $transactionid);
        $transaction = TopupTransactionsPeer::doSelectOne($tr);
        $ca = new Criteria();
        $ca->add(AgentCompanyPeer::ID, $agent_company_id);
        $agent = AgentCompanyPeer::doSelectOne($ca);
        //echo $agent->getId();
        //getting agent commission
        $reseller = ResellerPeer::retrieveByPK($agent->getResellerId());
        $cc = new Criteria();
        $cc->add(AgentCommissionPackagePeer::ID, $agent->getAgentCommissionPackageId());
        $commission_package = AgentCommissionPackagePeer::doSelectOne($cc);
        $cr = new Criteria();
        $cr->add(ResellerCommissionPackagePeer::ID, $reseller->getResellercommissionPackageId());
        $reseller_commission_package = ResellerCommissionPackagePeer::doSelectOne($cr);
        ///////////////////////////commision calculation by reseller product ///////////////////////////////////////
        $cp = new Criteria;
        $cp->add(ResellerProductPeer::RESELLER_ID, $reseller->getId());
        $cp->add(ResellerProductPeer::PRODUCT_ID, $productid);
        $resellerproductcount = ResellerProductPeer::doCount($cp);
        if ($resellerproductcount > 0) {
            $resellerproductcomesion = ResellerProductPeer::doSelectOne($cp);
            $resellercomession = $resellerproductcomesion->getExtraPaymentsShareEnable();
        }
        ////////////////////////commission calculation for reseller commission////////////////////
        if (isset($resellercomession) && $resellercomession != "") {
             if ($resellerproductcomesion->getIsExtraPaymentsShareValuePc()) {
                $transaction->setResellerCommission(($transaction->getProductRegistrationFee() / 100) * $resellerproductcomesion->getExtraPaymentsShareValue());
            } else {
                 $transaction->setResellerCommission($resellerproductcomesion->getExtraPaymentsShareValue());
            }
        } else {
  
            if ($reseller_commission_package->getIsExtraPaymentsShareValuePc()) {
                $transaction->setResellerCommission(($transaction->getProductRegistrationFee() / 100) * $reseller_commission_package->getExtraPaymentsShareValue());
            } else {
                $transaction->setResellerCommission($reseller_commission_package->getExtraPaymentsShareValue());
            }
        }
       
           $transaction->setResellerVatCommission($transaction->getResellerCommission() * sfConfig::get('app_vat_on_commission'));
       
        $transaction->save();
       
        

        
  
       
       
            
      
         
            
        
        return true;
    }

    
    
public static function agentCommissionAssigner($gentid, $productid, $transactionid) {
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $agent_company_id = $gentid;
        $transactionid = $transactionid;
        $tr = new Criteria();
        $tr->add(TopupTransactionsPeer::ID, $transactionid);
        $transaction = TopupTransactionsPeer::doSelectOne($tr);
        $ca = new Criteria();
        $ca->add(AgentCompanyPeer::ID, $agent_company_id);
        $agent = AgentCompanyPeer::doSelectOne($ca);
        //echo $agent->getId();
        //getting agent commission
        $reseller = ResellerPeer::retrieveByPK($agent->getResellerId());
        $cc = new Criteria();
        $cc->add(AgentCommissionPackagePeer::ID, $agent->getAgentCommissionPackageId());
        $commission_package = AgentCommissionPackagePeer::doSelectOne($cc);
        $cr = new Criteria();
        $cr->add(ResellerCommissionPackagePeer::ID, $reseller->getResellercommissionPackageId());
        $reseller_commission_package = ResellerCommissionPackagePeer::doSelectOne($cr);
        ///////////////////////////commision calculation by reseller product ///////////////////////////////////////
        $cp = new Criteria;
        $cp->add(ResellerProductPeer::RESELLER_ID, $reseller->getId());
        $cp->add(ResellerProductPeer::PRODUCT_ID, $productid);
        $resellerproductcount = ResellerProductPeer::doCount($cp);
        if ($resellerproductcount > 0) {
            $resellerproductcomesion = ResellerProductPeer::doSelectOne($cp);
            $resellercomession = $resellerproductcomesion->getExtraPaymentsShareEnable();
        }
        ////////////////////////commission calculation for reseller commission////////////////////
        if (isset($resellercomession) && $resellercomession != "") {
             if ($resellerproductcomesion->getIsExtraPaymentsShareValuePc()) {
                $transaction->setResellerCommission(($transaction->getProductRegistrationFee() / 100) * $resellerproductcomesion->getExtraPaymentsShareValue());
            } else {
                 $transaction->setResellerCommission($resellerproductcomesion->getExtraPaymentsShareValue());
            }
        } else {
  
            if ($reseller_commission_package->getIsExtraPaymentsShareValuePc()) {
                $transaction->setResellerCommission(($transaction->getProductRegistrationFee() / 100) * $reseller_commission_package->getExtraPaymentsShareValue());
            } else {
                $transaction->setResellerCommission($reseller_commission_package->getExtraPaymentsShareValue());
            }
        }
       
           $transaction->setResellerVatCommission($transaction->getResellerCommission() * sfConfig::get('app_vat_on_commission'));
       ///////////////////////////////////////////////Agent Commission  setting //////////////////////////
           
           
        $cp = new Criteria;
        $cp->add(AgentProductPeer::AGENT_ID, $agent_company_id);
        $cp->add(AgentProductPeer::PRODUCT_ID, $productid);
        $agentproductcount = AgentProductPeer::doCount($cp);

        if ($agentproductcount > 0) {

            $agentproductcomesion = AgentProductPeer::doSelectOne($cp);
            $agentcomession = $agentproductcomesion->getExtraPaymentsShareEnable();
        }
                   ////////   commission setting  through  agent commision//////////////////////

        if (isset($agentcomession) && $agentcomession != "") {

            if ($agentproductcomesion->getIsExtraPaymentsShareValuePc()) {

                $transaction->setAgentCommission(($transaction->getProductRegistrationFee() / 100) * $agentproductcomesion->getExtraPaymentsShareValue());
            } else {
                $transaction->setAgentCommission($agentproductcomesion->getExtraPaymentsShareValue());
            }
        } else {
            if ($commission_package->getIsExtraPaymentsShareValuePc()) {
                $transaction->setAgentCommission(($transaction->getProductRegistrationFee() / 100) * $commission_package->getExtraPaymentsShareValue());
            } else {
                $transaction->setAgentCommission($commission_package->getExtraPaymentsShareValue());
            }
        }
        //calculated amount for agent commission
         
        $transaction->setVatOnCommission($transaction->getAgentCommission() * sfConfig::get('app_vat_on_commission'));
        /////////////////////////////////////////////////////////////////////////////////////////////////
        $transaction->save();
       
        

        
  
       
       
            
      
         
            
        
        return true;
    }

    
    
}

?>