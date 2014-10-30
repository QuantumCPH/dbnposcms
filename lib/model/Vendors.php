<?php

require_once(sfConfig::get('sf_lib_dir') . '/emailLib.php');

class Vendors extends BaseVendors {

    public function __toString() {
        return $this->getTitle();
    }

    /*
     * This is a topup engine. From where we will be writting our code to topup for each vendor.
     */

    public function topUp(TopupTransactions $transaction, CardNumbers $card_number, $mobile_number) {
        $topup = false;
        switch ($this->getId()) {
            case 1: // Zerocall
                $url = 'http://customer.zerocall.com/b2c.php/pScripts/refillScratchCardMeasypayAPI';
                $data = array("mobile" => $mobile_number, "message" => $card_number->getCardNumber(), "api" => "yes");
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                $result = curl_exec($ch) or die(curl_error($ch));
                curl_close($ch);
                if (strstr(strtolower($result), "success")) {
                    $cut = commissionLib::topUp($transaction->getAgentCompanyId(), $transaction->getProductId(), $transaction->getId());
                    $topup = true;
                    $sc = new Criteria();
                    $sc->add(SmsTextPeer::VENDOR_ID, $this->getId());
                    $sms = SmsTextPeer::doSelectOne($sc);
                    $sms = str_replace("(produkt name)", $card_number->getProduct()->getName(), $sms->getMessageText());
                    //echo $sms;
                    ROUTED_SMS::Send($mobile_number, $sms, null, null, $transaction->getId());
                    // ROUTED_SMS::Send("45" . $mobile_number, $sms);
                } else {
                    $topup = false;
                    $card_number->setStatus(1);
                    $card_number->setAgentCompanyId($transaction->getAgentCompanyId());
                    $card_number->setAgentUserId($transaction->getAgentUserId());
                    $card_number->setComments("Card is defected and not loaded.");
                    $card_number->setUsedAt(date("Y-m-d H:i:s"));
                    $card_number->save();
                    $message = "Mobile Number=" . $mobile_number . " Card Number=" . $card_number->getCardNumber();
                    emailLib::sendTopupErrorEmail($message);
                }
                break;
            case 2: // Salam Mobile
                $url = 'http://services.salammobile.com/b2c.php/pScripts/Refill';
                $data = array("MSISDN" => $mobile_number, "Text" => $card_number->getCardNumber(), "api" => "yes");
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                $result = curl_exec($ch);
                curl_close($ch);
                if (strstr(strtolower($result), "success")) {
                    $cut = commissionLib::topUp($transaction->getAgentCompanyId(), $transaction->getProductId(), $transaction->getId());
                    $sc = new Criteria();
                    $sc->add(SmsTextPeer::VENDOR_ID, $this->getId());
                    $sms = SmsTextPeer::doSelectOne($sc);
                    $sms = str_replace("(produkt name)", $card_number->getProduct()->getName(), $sms->getMessageText());
                    // echo $sms;
                    // ROUTED_SMS::Send("45" . $mobile_number, $sms);
                    ROUTED_SMS::Send($mobile_number, $sms, null, null, $transaction->getId());

                    $topup = true;
                } else {
                    $topup = false;
                    $card_number->setStatus(1);
                    $card_number->setAgentCompanyId($transaction->getAgentCompanyId());
                    $card_number->setAgentUserId($transaction->getAgentUserId());
                    $card_number->setComments("Card is defected and not loaded");
                    $card_number->setUsedAt(date("Y-m-d H:i:s"));
                    $card_number->save();

                    $message = "Mobile Number=" . $mobile_number . " Card Number=" . $card_number->getCardNumber();
                    emailLib::sendTopupErrorEmail($message);
                }

                break;
            case 19: // woiize
                $url = 'http://customer.woiize.com/b2c.php/pScripts/refillScratchCardMeasypayAPI';
                $data = array("mobile" => $mobile_number, "message" => $card_number->getCardNumber(), "api" => "yes");
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                $result = curl_exec($ch) or die(curl_error($ch));
                curl_close($ch);
                if (strstr(strtolower($result), "success")) {
                    $cut = commissionLib::topUp($transaction->getAgentCompanyId(), $transaction->getProductId(), $transaction->getId());
                    $topup = true;
                    $sc = new Criteria();
                    $sc->add(SmsTextPeer::VENDOR_ID, $this->getId());
                    $sms = SmsTextPeer::doSelectOne($sc);
                    $sms = str_replace("(produkt name)", $card_number->getProduct()->getName(), $sms->getMessageText());
                    //echo $sms;
                    ROUTED_SMS::Send($mobile_number, $sms, null, null, $transaction->getId());
                    // ROUTED_SMS::Send("45" . $mobile_number, $sms);
                } else {
                    $topup = false;
                    $card_number->setStatus(1);
                    $card_number->setAgentCompanyId($transaction->getAgentCompanyId());
                    $card_number->setAgentUserId($transaction->getAgentUserId());
                    $card_number->setComments("Card is defected and not loaded.");
                    $card_number->setUsedAt(date("Y-m-d H:i:s"));
                    $card_number->save();
                    $message = "Mobile Number=" . $mobile_number . " Card Number=" . $card_number->getCardNumber();
                    emailLib::sendTopupErrorEmail($message);
                }
                break;

            default:
                if (commissionLib::topUp($transaction->getAgentCompanyId(), $transaction->getProductId(), $transaction->getId())) {
                    $sc = new Criteria();
                    $sc->add(SmsTextPeer::VENDOR_ID, $this->getId());
                    $sms = SmsTextPeer::doSelectOne($sc);
                    $sms = str_replace("(voucher)", $card_number->getCardNumber(), $sms->getMessageText());
                    $sms = str_replace("(produkt name)", $card_number->getProduct()->getName(), $sms);
                    $sms = str_replace("(serial)", $card_number->getCardSerial(), $sms);
                    //echo $sms;
                    // ROUTED_SMS::Send("45" . $mobile_number, $sms);
                    ROUTED_SMS::Send($mobile_number, $sms, null, null, $transaction->getId());
                    $topup = true;
                }
                break;
        }
        if ($topup) {
            $card_number->setStatus(1);
            $card_number->setAgentCompanyId($transaction->getAgentCompanyId());
            $card_number->setAgentUserId($transaction->getAgentUserId());
            $card_number->setCustomerMobile($mobile_number);
            $card_number->setUsedAt(date("Y-m-d H:i:s"));
            $card_number->save();
            $transaction->setCardNumber($card_number->getCardNumber());
            $transaction->setCardTypeId($card_number->getCardTypeId());
            $transaction->setCardPurchasePrice($card_number->getCardPurchasePrice());
            $transaction->setStatus(3);
            $transaction->save();

            $aoc = new Criteria();

            $aoc->add(CardNumbersPeer::CARD_TYPE_ID, $card_number->getCardTypeId());
            $aoc->add(CardNumbersPeer::STATUS, 0);
            $aoc->add(CardNumbersPeer::PRODUCT_ID, $card_number->getProductId());
            $cardCount = CardNumbersPeer::doCount($aoc);
            if ($cardCount < 4) {
                $message = "Product Name =" . $card_number->getProduct()->getName() . " Card Type=" . $card_number->getCardTypes()->getName() . "Number of card remaining=" . $cardCount;
                emailLib::sendCardFinishEmail($message);
            }

            TransactionPeer::AssignTopUpReceiptNumber($transaction);
        }
        return $topup;
    }

    public function VendingMachinetopUp(TopupTransactions $transaction, CardNumbers $card_number, $dibsCall) {
        $topup = false;

        if (commissionLib::topUp($transaction->getAgentCompanyId(), $transaction->getProductId(), $transaction->getId())) {
            $sc = new Criteria();

            switch ($this->getId()) {
                case 1:
                    $sc->add(SmsTextPeer::ID, 21);
                    break;
                case 2:
                    $sc->add(SmsTextPeer::ID, 22);
                    break;
                case 19:
                    $sc->add(SmsTextPeer::ID, 41);
                    break;
                default:
                    $sc->add(SmsTextPeer::VENDOR_ID, $this->getId());
                    break;
            }



            $sms = SmsTextPeer::doSelectOne($sc);
            $sms = str_replace("(voucher)", $card_number->getCardNumber(), $sms->getMessageText());
            $sms = str_replace("(produkt name)", $card_number->getProduct()->getName(), $sms);
            $sms = str_replace("(serial)", $card_number->getCardSerial(), $sms);
            //echo $sms;
            // ROUTED_SMS::Send("45" . $mobile_number, $sms);
            //$sms = mb_convert_encoding($sms, "ISO-8859-1",mb_detect_encoding( $sms, "auto" ));
            echo wordwrap($sms, 32, "\n", true);
            $customerreceipt = $sms;
            $topup = true;
        }

        if ($topup) {
            $card_number->setStatus(1);
            $card_number->setAgentCompanyId($transaction->getAgentCompanyId());
            $card_number->setAgentUserId($transaction->getAgentUserId());
            $card_number->setCustomerMobile($mobile_number);
            $card_number->setUsedAt(date("Y-m-d H:i:s"));
            $card_number->save();
            $transaction->setCardNumber($card_number->getCardNumber());
            $transaction->setCardTypeId($card_number->getCardTypeId());
            $transaction->setCardPurchasePrice($card_number->getCardPurchasePrice());
            $transaction->setStatus(3);
            $transaction->save();

            $aoc = new Criteria();

            $aoc->add(CardNumbersPeer::CARD_TYPE_ID, $card_number->getCardTypeId());
            $aoc->add(CardNumbersPeer::STATUS, 0);
            $aoc->add(CardNumbersPeer::PRODUCT_ID, $card_number->getProductId());
            $cardCount = CardNumbersPeer::doCount($aoc);
            if ($cardCount < 4) {
                $message = "Product Name =" . $card_number->getProduct()->getName() . " Card Type=" . $card_number->getCardTypes()->getName() . "Number of card remaining=" . $cardCount;
                emailLib::sendCardFinishEmail($message);
            }

            TransactionPeer::AssignTopUpReceiptNumber($transaction);
        }
        $dibsCall->setCustomerReceipt($customerreceipt);
        $dibsCall->save();
        return $topup;
    }

    function getLogoImage() {
        $image_url = "";
        if ($this->getLogo() != "") {
            $image_url = "<img src='" . sfConfig::get("app_web_url") . "uploads/card_vendors_logo/" . $this->getLogo() . "' alt='" . $this->getTitle() . "' />";
        }
        return $image_url;
    }

    public function SmartPhoneApptopUp(TopupTransactions $transaction, CardNumbers $card_number, $dibsCall, $vendorMobile) {
        $topup = false;

        if (commissionLib::topUp($transaction->getAgentCompanyId(), $transaction->getProductId(), $transaction->getId())) {
            $sc = new Criteria();

            switch ($this->getId()) {
                case 1:
                    $sc->add(SmsTextPeer::ID, 21);
                    break;
                case 2:
                    $sc->add(SmsTextPeer::ID, 22);
                    break;
                case 19:
                    $sc->add(SmsTextPeer::ID, 41);
                    break;
                default:
                    $sc->add(SmsTextPeer::VENDOR_ID, $this->getId());
                    break;
            }



            $sms = SmsTextPeer::doSelectOne($sc);
            $sms = str_replace("(voucher)", $card_number->getCardNumber(), $sms->getMessageText());
            $sms = str_replace("(produkt name)", $card_number->getProduct()->getName(), $sms);
            $sms = str_replace("(serial)", $card_number->getCardSerial(), $sms);
            //echo $sms;
            // ROUTED_SMS::Send("45" . $mobile_number, $sms);
            //$sms = mb_convert_encoding($sms, "ISO-8859-1",mb_detect_encoding( $sms, "auto" ));

            if (ROUTED_SMS::Send($vendorMobile, wordwrap($sms, 32, "\n", true), null, null, $transaction->getId())) {
                $customerreceipt = $sms;
                $topup = true;
            }
        }

        if ($topup) {
            $card_number->setStatus(1);
            $card_number->setAgentCompanyId($transaction->getAgentCompanyId());
            $card_number->setAgentUserId($transaction->getAgentUserId());
            $card_number->setCustomerMobile($vendorMobile);
            $card_number->setUsedAt(date("Y-m-d H:i:s"));
            $card_number->save();
            $transaction->setCardNumber($card_number->getCardNumber());
            $transaction->setCardTypeId($card_number->getCardTypeId());
            $transaction->setCardPurchasePrice($card_number->getCardPurchasePrice());
            $transaction->setStatus(3);
            $transaction->save();

            $aoc = new Criteria();

            $aoc->add(CardNumbersPeer::CARD_TYPE_ID, $card_number->getCardTypeId());
            $aoc->add(CardNumbersPeer::STATUS, 0);
            $aoc->add(CardNumbersPeer::PRODUCT_ID, $card_number->getProductId());
            $cardCount = CardNumbersPeer::doCount($aoc);
            if ($cardCount < 4) {
                $message = "Product Name =" . $card_number->getProduct()->getName() . " Card Type=" . $card_number->getCardTypes()->getName() . "Number of card remaining=" . $cardCount;
                emailLib::sendCardFinishEmail($message);
            }

            TransactionPeer::AssignTopUpReceiptNumber($transaction);
        }
        $dibsCall->setCustomerReceipt($customerreceipt);
        $dibsCall->save();
        return $topup;
    }

}