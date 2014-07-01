<?php

require_once(sfConfig::get('sf_lib_dir') . '/smsCharacterReplacement.php');

/**
* Description of company_employe_activation
*
* @author baran
*/
class CARBORDFISH_SMS {

    //put your code here

    private static $S = 'H';
    private static $UN = 'zapna1';
    private static $P = 'Zapna2010';
    private static $SA = 'Measypay';
    private static $ST = 5;

    /*
* Description of Send
*
* @param $mobilenumber is the mobile number leading with country code;
* @smsText is for the text that will be sent.
* @param $Sender will be the sender name of the SMS;
*/

    public static function Send($mobileNumber, $smsText, $senderName = null, $smsType = null,$transaction_id=null) {
        if ($senderName == null)
            $senderName = self::$SA;
        if ($smsType == null)
            $smsType = 1;
        $data = array(
            'S' => self::$S,
            'UN' => self::$UN,
            'P' => self::$P,
            'DA' => $mobileNumber,
            'SA' => $senderName,
            'M' => $smsText,
            'ST' => self::$ST
        );
        $queryString = http_build_query($data, '', '&');
        $queryString = smsCharacter::smsCharacterReplacement($queryString);


        $res = file_get_contents('http://sms1.cardboardfish.com:9001/HTTPSMS?' . $queryString);
        sleep(0.15);

        $smsLog = new SmsLog();
        $smsLog->setMessage($smsText);
        $smsLog->setStatus($res);
        $smsLog->setSmsType($smsType);
        $smsLog->setSenderName($senderName);
        $smsLog->setMobileNumber($mobileNumber);
           $smsLog->setApiName('Cardbordfish');
        $smsLog->setTransactionId($transaction_id);
        $smsLog->save();
        if (substr($res, 0, 2) == 'OK')
            return true;
        else
            return false;
    }

}

class SMSNU {

    //put your code here

    private static $main = '13rkha84';
    private static $id = 'Measypay';

    /*
* Description of Send
*
* @param $mobilenumber is the mobile number leading with country code;
* @smsText is for the text that will be sent.
* @param $Sender will be the sender name of the SMS;
*/

    public static function Send($mobileNumber, $smsText, $senderName = null, $smsType = null,$transaction_id=null) {
        if ($senderName == null)
            $senderName = self::$id;
        if ($smsType == null)
            $smsType = 1;
        $data = array(
            'main' => self::$main,
            'til' => $mobileNumber,
            'id' => $senderName,
            'msgtxt' => $smsText
        );
        $message = "";
        $queryString = http_build_query($data, '', '&');
        // $queryString = smsCharacter::smsCharacterReplacement($queryString);
        $res = file_get_contents('http://smsnu.dk/sendsms?' . $queryString);
        sleep(0.15);
        if (!$res) {
            return false;
        }
        $smsLog = new SmsLog();
        $smsLog->setMessage($smsText);
        $smsLog->setStatus($res);

        $smsLog->setSmsType($smsType);
        $smsLog->setSenderName($senderName);
        $smsLog->setMobileNumber($mobileNumber);
        $smsLog->setApiName('SMS NU');
        $smsLog->setTransactionId($transaction_id);
        $smsLog->save();
        if (substr($res, 10, 2) == 'OK') {
            return true;
        } else {
            $message.="SMS not sent to this mobile numberc On WLS2 <br/>Mobile number =" . $mobileNumber . "<br/> Message is =" . $smsText . "<br/> and Time is " . $smsLog->getCreatedAt();
            emailLib::smsNotSentEmail($message);
            return false;
        }
    }

}

class ROUTE_API_Regular {

    private static $id = 'Measypay';

    public static function Send($mobile_number, $sms_text, $senderName = null, $smsType = null,$transaction_id=null) {

        if ($senderName == null)
            $senderName = self::$id;
        if ($smsType == null)
            $smsType = 1;
        $data1 = array(
            'username' => 'zapna',
            'password' => 'bc366nf',
            'dlr' => '1',
            'destination' => $mobile_number,
            'source' => $senderName,
            'message' => $sms_text,
            'type' => '0'
        );
        $queryString = http_build_query($data1, '', '&');
        $queryString = smsCharacter::smsCharacterReplacementReverse($queryString);
        $res = file_get_contents('http://smsplus3.routesms.com:8080/bulksms/bulksms?' . $queryString);
        $smsLog = new SmsLog();
        $smsLog->setMessage($sms_text);
        $smsLog->setStatus($res);
        $smsLog->setSmsType($smsType);
        $smsLog->setSenderName($senderName);
        $smsLog->setMobileNumber($mobile_number);
        $smsLog->setApiName('Route API Regular');
        $smsLog->setTransactionId($transaction_id);
        $smsLog->save();
        if (substr($res, 0, 4) == 1701) {
            return true;
        } else {
            $message.="SMS not sent via ROUTE_API_Regular to this mobile numberc On Measypay <br/>Mobile number =" . $mobileNumber . "<br/> Message is =" . $smsText . "<br/> Response from API =" . $res;
            emailLib::smsNotSentEmail($message);
            return false;
        }
    }

}

class ROUTE_API_Premium {

    //put your code here

    private static $username = 'zapna1';
    private static $password = 'lghanymb';
    private static $source = 'Measypay';
    private static $dlr = 1;
    private static $type = 0;

    public static function Send($mobileNumber, $smsText, $senderName = null, $smsType = null,$transaction_id=null) {

        $message = "";
        if ($senderName == null)
            $senderName = self::$source;
        if ($smsType == null)
            $smsType = 1;
        $data = array(
            'username' => self::$username,
            'password' => self::$password,
            'dlr' => self::$dlr,
            'destination' => $mobileNumber,
            'source' => $senderName,
            'message' => $smsText,
            'type' => self::$type
        );
        $queryString = http_build_query($data, '', '&');
        $queryString = smsCharacter::smsCharacterReplacementReverse($queryString);
        $res = file_get_contents('http://smpp5.routesms.com:8080/bulksms/sendsms?' . $queryString);
        // sleep(0.25);

        if (substr($res, 0, 4) == 1701) {

            $smsLog = new SmsLog();
            $smsLog->setMessage($smsText);
            $smsLog->setStatus($res);
            $smsLog->setSmsType($smsType);
            $smsLog->setSenderName($senderName);
            $smsLog->setMobileNumber($mobileNumber);
         $smsLog->setApiName('Route API Premium');
        $smsLog->setTransactionId($transaction_id);
            $smsLog->save();

            return true;
        } else {
            $message.="SMS not sent via ROUTE_API_Premium to this mobile numberc On Meaasypay <br/>Mobile number =" . $mobileNumber . "<br/> Message is =" . $smsText . "<br/> Response from API =" . $res;
            emailLib::smsNotSentEmail($message);
            return false;
        }
    }

}


class STADEL{

    //put your code here

    private static $username = 'zapna';
    private static $password = 'Okh20717786';
    private static $source = 'Measypay';
  
   

    public static function Send($mobileNumber, $smsText, $senderName = null, $smsType = null,$transaction_id=null) {
 $dateparm=date('Y-m-d H:i');
        $message = "";
        if ($senderName == null)
            $senderName = self::$source;
        if ($smsType == null)
            $smsType = 1;
        $data = array(
        'user' => self::$username,
        'pass' => self::$password,
        'mobile' => $mobileNumber,
        'sender' => $senderName,
        'message' => $smsText,
        'time' =>$dateparm
        );
        $queryString = http_build_query($data, '', '&');
     //   $queryString = smsCharacter::smsCharacterReplacementReverse($queryString);
        $res = file_get_contents('http://sms.stadel.dk/send.php?' . $queryString);
        // sleep(0.25);

        if (substr($res, 0, 2) == 'OK') {

            $smsLog = new SmsLog();
            $smsLog->setMessage($smsText);
            $smsLog->setStatus($res);
            $smsLog->setSmsType($smsType);
            $smsLog->setSenderName($senderName);
            $smsLog->setMobileNumber($mobileNumber);
         $smsLog->setApiName('STADEL API');
        $smsLog->setTransactionId($transaction_id);
            $smsLog->save();

            return true;
        } else {
            $message.="SMS not sent via STADEL API to this mobile numberc On Meaasypay <br/>Mobile number =" . $mobileNumber . "<br/> Message is =" . $smsText . "<br/> Response from API =" . $res;
            emailLib::smsNotSentEmail($message);
            return false;
        }
    }

}



class ROUTED_SMS_OLD {

    public static function Send($mobileNumber, $smsText, $senderName = null, $smsType = null,$transaction_id=null) {
        
        $mobileNumber = trim($mobileNumber);
        
        if (substr($mobileNumber, 0, 2) != "45")
            $mobileNumber = "45" . $mobileNumber;
//         if (!STADEL::Send($mobileNumber, $smsText, $senderName,$smsType,$transaction_id)) {

        if (!SMSNU::Send($mobileNumber, $smsText, $senderName,$smsType,$transaction_id)) {
            if (!ROUTE_API_Regular::Send($mobileNumber, $smsText, $senderName, $smsType,$transaction_id)) {
                if (!ROUTE_API_Premium::Send($mobileNumber, $smsText, $senderName, $smsType,$transaction_id)) {
                    if (!CARBORDFISH_SMS::Send($mobileNumber, $smsText, $senderName, $smsType,$transaction_id)) {

                        if ($senderName == null)
                            $senderName = "Measypay";
                        if ($smsType == null)
                            $smsType = 1;
                        $smsLog = new SmsLog();
                        $smsLog->setMessage($smsText);
                        $smsLog->setSmsType($smsType);
                        $smsLog->setStatus("Unable to send from all");
                        $smsLog->setSenderName($senderName);
                        $smsLog->setMobileNumber($mobileNumber);
                        $smsLog->save();
                        return false;
                    }else {
                        return true;
                    }
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

}

class ROUTED_SMS {

    public static function Send($mobileNumber, $smsText, $senderName = null, $smsType = null,$transaction_id=null) {
         $mobileNumber = trim($mobileNumber);
        
        if (substr($mobileNumber, 0, 2) != "45")
            $mobileNumber = "45" . $mobileNumber;
        
         $sms = new Criteria();
         $sms->add(SmsApiPeer::STATUS,1);
         $sms->addAscendingOrderByColumn(SmsApiPeer::PRIORITY);
         $smsapiscount=SmsApiPeer::doCount($sms);
         $smsapis=SmsApiPeer::doSelect($sms);
         
         $i = 1;
         
         if($smsapiscount > 0){
             foreach($smsapis as $smsapi){
               $res = self::sms_api($smsapi->getName(),$mobileNumber,$smsText,$senderName,$smsType,$transaction_id);  
               if($res){
                   return true;
                   break;
               }elseif(!$res && $i==$smsapiscount){
                   if ($senderName == null) $senderName = "Measypay";
                   if ($smsType == null)    $smsType = 1;
                    $smsLog = new SmsLog();
                    $smsLog->setMessage($smsText);
                    $smsLog->setSmsType($smsType);
                    $smsLog->setStatus("Unable to send from all");
                    $smsLog->setSenderName($senderName);
                    $smsLog->setMobileNumber($mobileNumber);
                    $smsLog->save();
                    $message="";
                    $message.="SMS not sent via all apis to this mobile numberc On Measypay <br/>Mobile number =" . $mobileNumber . "<br/> Message is =" . $smsText ;
                    emailLib::smsNotSentEmail($message);
                    return false;
                    break;
               }else{
 
               }
               $i++;
             }
         }else{
            $message = "There is not any SMS Api active. Please activate any api from admin ";
            emailLib::smsNotSentEmail($message);
            return false;
         }

    }
    public static function sms_api($api_name,$mobileNumber,$smsText,$senderName,$smsType,$transaction_id){
        if($api_name == "ROUTE_API_Regular"){
            $response = ROUTE_API_Regular::Send($mobileNumber, $smsText, $senderName, $smsType,$transaction_id); 
            return $response;
        } elseif($api_name == "ROUTE_API_Premium"){
            $response = ROUTE_API_Premium::Send($mobileNumber, $smsText, $senderName, $smsType,$transaction_id); 
            return $response;
        } elseif($api_name == "SMSNU"){
            $response = SMSNU::Send($mobileNumber, $smsText, $senderName, $smsType,$transaction_id); 
            return $response;
        }  elseif($api_name == "CARBORDFISH_SMS"){
            $response = CARBORDFISH_SMS::Send($mobileNumber, $smsText, $senderName, $smsType,$transaction_id); 
            return $response;
        } else{
            return false;
        }            
    }
}
?>
