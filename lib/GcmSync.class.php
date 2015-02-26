<?php

class GcmLib {

    private $command;
    private $reg_ids;
     private $shop;

    /*
     * This method will be seinding request to the GCM to update data on the POS.
     * @@$command is being used to enoforce which update should pos trigger.
     * @@$reg_ids is an array and will be used to send multiple POS gcms key to update.  
     */

    public function GcmLib($command, $reg_ids, $shop = null) {
        $this->command = $command;
        $this->reg_ids = $reg_ids;
         $this->shop = $shop;
        
        if($shop){
 $shop_id= $shop->getId();   
 
 
            $co = new Criteria();
            $co->add(GcmRequestPeer::SHOP_ID, $shop_id);
             $co->addAnd(GcmRequestPeer::ACTION_NAME, $command);
            if (GcmRequestPeer::doCount($co) == 0) {
                $gcm = new GcmRequest();
                $gcm->setShopId($shop_id);
            $gcm->setActionName($command);
              $gcm->setSentCount(1);
            } else {
                $gcm = GcmRequestPeer::doSelectOne($co);
                $rccount=(int) $gcm->getReceiveCount();
                $rccount=$rccount+1;
                  $gcm->setSentCount($rccount);
            }
            
            $gcm->setCreatedAt(time());
             $gcm->setRequestStatus(1);
           
           
          $gcm->save();
        }
        
        
        
        $this->sendRequest();
    }

    private function sendRequest() {
        $url = 'https://android.googleapis.com/gcm/send';
        $message = array("do" => $this->command);
        $fields = array(
            'registration_ids' => $this->reg_ids,
            'data' => $message,
        );
//old =   AIzaSyBjhoSXE3gYU1_hKxxRIT0PpA2dzS89vgU
// new = AIzaSyBUvFW57_LEh8i__BoHQqoIHYCjMsXuSf8        
        $headers = array(
            'Authorization: key=AIzaSyBUvFW57_LEh8i__BoHQqoIHYCjMsXuSf8',
            'Content-Type: application/json'
        );
// Open connection
        $ch = curl_init();

// Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

// Execute post
        curl_exec($ch);
// Close connection
        curl_close($ch);
    }

}
?>