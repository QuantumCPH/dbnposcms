<?php

class gf {

    public static function checkImage($image) {


        $imagepathfile = sfConfig::get("app_web_url") . "uploads/images/thumbs/" . $image;
        $imagepathfileww = "/var/www/dbnposcms/web/uploads/images/thumbs/" . $image;

        $imagepathtempno = sfConfig::get("app_web_url") . "images/no-image.png";

        if (file_exists($imagepathfileww) && $image != "") {
            return $imagepathfile;
        } else {
            // return $imagepathfile;
            return $imagepathtempno;
        }
    }

    public static function checkImage1($image) {

        $imagearay = split("_", $image);
        $image = $imagearay[0];
        $imageName = $image . "_50.jpg";

        $imagepathfile = "http://dbnposcms.zap-itsolutions.com/uploads/images/thumbs/" . $imageName;
        $imagepathfileww = "/var/www/dbnposcms/web/uploads/images/thumbs/" . $imageName;

        $imagepathtempno = "http://dbnposcms.zap-itsolutions.com/images/no-image.png";

        if (file_exists($imagepathfileww) && $image != "") {
            return $imagepathfile;
        } else {
            // return $imagepathfile;
            return $imagepathtempno;
        }
    }

    public static function checkLargeImage($image) {

        $timdt=date('YmdHis');
        $imagepathfile = sfConfig::get("app_web_url") . "uploads/images/" . $image . ".jpg?".$timdt;
        $imagepathfileww = "/var/www/dbnposcms/web/uploads/images/" . $image . ".jpg";

        $imagepathtempno = sfConfig::get("app_web_url") . "images/no-image-large.png";

        if (file_exists($imagepathfileww) && $image != "") {
            return $imagepathfile;
        } else {
            // return $imagepathfile;
            return $imagepathtempno;
        }
    }

    public static function checkAndRenameImage($imageName,$updated_by) {
        $today = date('-Y-m-d-H-i');
        if($updated_by=="") $updated_by = 1;
        $imag0 = "/home/dbnposcms/images/backup/" . $imageName . ".jpg";
        $imag00 = "/home/dbnposcms/images/backup/" . $imageName . $today . ".jpg";
        $imag1 = "/var/www/dbnposcms/web/uploads/images/" . $imageName . ".jpg";
        $imag11 = "/var/www/dbnposcms/web/uploads/images/" . $imageName . $today . ".jpg";
        $imag2 = "/var/www/dbnposcms/web/uploads/images/thumbs/" . $imageName . "_32.jpg";
        $imag22 = "/var/www/dbnposcms/web/uploads/images/thumbs/" . $imageName . $today . "_32.jpg";
        $imag3 = "/var/www/dbnposcms/web/uploads/images/thumbs/" . $imageName . "_50.jpg";
        $imag33 = "/var/www/dbnposcms/web/uploads/images/thumbs/" . $imageName . $today . "_50.jpg";
        $imag4 = "/var/www/dbnposcms/web/uploads/images/thumbs/" . $imageName . "_187.jpg";
        $imag44 = "/var/www/dbnposcms/web/uploads/images/thumbs/" . $imageName . $today . "_187.jpg";

        if (file_exists($imag1) && $imageName != "") {
           
            $imageNameDet = $imageName . $today;
            $todaytime=time();
             $imageHu = new Criteria();
                $imageHu->add(ImageHistoryPeer::ITEM_ID, $imageName);
                $imageHu->addAnd(ImageHistoryPeer::IMAGE_STATUS_ID, 0);
               
                if (ImageHistoryPeer::doCount($imageHu) > 0) {
                     $imageHu->addDescendingOrderByColumn(ImageHistoryPeer::ID);
                    $itemtIHu = ImageHistoryPeer::doSelectOne($imageHu);
                    
                     $itemtIHu->setImageName($imageNameDet);
                    $itemtIHu->setUpdatedAt($todaytime);
                    $itemtIHu->setImageStatusId(1);
                    $itemtIHu->save();
                }
                $cim = new Criteria();
                $cim->add(ItemsLogPeer::ITEM_ID, $imageName);
                $cim->addAnd(ItemsLogPeer::IMAGE_STATUS, 0);
//                $cim->addAnd(ItemsLogPeer::IMAGE_NAME, "", Criteria::NOT_EQUAL);
                if (ItemsLogPeer::doCount($cim) > 0) {
                     $cim->addDescendingOrderByColumn(ItemsLogPeer::ID);
                    $itemlogIm = ItemsLogPeer::doSelectOne($cim);
                    $img = "<img src='".sfConfig::get("app_web_url")."uploads/images/thumbs/".$imageNameDet."_50.jpg'  >";
                    $itemlogIm->setImageName($img);
//                     $itemlogIm->setImageName($imageNameDet);
                    $itemlogIm->setUpdatedAt($todaytime);
                    $itemlogIm->setImageStatus(1);
                    $itemlogIm->save();
                }  
        }
        
         $imageH = new ImageHistory();
             $imageH->setItemId($imageName);
            $imageH->setImageName($imageName);
            $imageH->setUpdatedBy($updated_by);
           
            $imageH->save();
        
        
         if (file_exists($imag0) && $imageName != "") {
           rename($imag0, $imag00);
        }
        
        
        if (file_exists($imag1) && $imageName != "") {
            rename($imag1, $imag11);
        }
        if (file_exists($imag2) && $imageName != "") {
            rename($imag2, $imag22);
        }
        if (file_exists($imag3) && $imageName != "") {
            rename($imag3, $imag33);
        }
        if (file_exists($imag4) && $imageName != "") {
            rename($imag4, $imag44);
        }
        return true;
    }

    public static function getPreviousId($id) {
        $cs = new Criteria();
        $cs->add(ShopsPeer::ID, $id, Criteria::LESS_THAN);
        $cs->addDescendingOrderByColumn(ShopsPeer::ID);
        $cs->setLimit(1);
        if (ShopsPeer::doCount($cs) > 0) {
            $shop = ShopsPeer::doSelectOne($cs);
            return $shop->getId();
        } else {
            return "";
        }
    }

    public static function getNextId($id) {
        $cs = new Criteria();
        $cs->add(ShopsPeer::ID, $id, Criteria::GREATER_THAN);
        $cs->addAscendingOrderByColumn(ShopsPeer::ID);
        $cs->setLimit(1);
        if (ShopsPeer::doCount($cs) > 0) {
            $shop = ShopsPeer::doSelectOne($cs);
            return $shop->getId();
        } else {
            return "";
        }
    }
    
    public static function getPreviousDNId($note_id) {
        $ci = new Criteria();
//        $ci->addAscendingOrderByColumn(DeliveryNotesPeer::AUTO_INC_ID);        
        $ci->add(DeliveryNotesPeer::GROUP_ID, $note_id, Criteria::LESS_THAN);
        $ci->addDescendingOrderByColumn(DeliveryNotesPeer::GROUP_ID);
        $ci->setLimit(1);
        if (DeliveryNotesPeer::doCount($ci) > 0) {
            $dn = DeliveryNotesPeer::doSelectOne($ci);
            return $dn->getNoteId();
        } else {
            return "";
        }
    }

    public static function getNextDNId($note_id) {
        $ci = new Criteria();
        $ci->add(DeliveryNotesPeer::GROUP_ID, $note_id, Criteria::GREATER_THAN);
        $ci->addAscendingOrderByColumn(DeliveryNotesPeer::GROUP_ID);
        $ci->addGroupByColumn(DeliveryNotesPeer::NOTE_ID);
        
        $ci->setLimit(1);
        if (DeliveryNotesPeer::doCount($ci) > 0) {
            $dn = DeliveryNotesPeer::doSelectOne($ci);
            return $dn->getNoteId();
        } else {
            return "";
        }
    }
//////////////////////////////////////////////////////////////////////////////
 
  public static function getBPreviousDNId($note_id) {
        $ci = new Criteria();
    
        $ci->add(BookoutNotesPeer::GROUP_ID, $note_id, Criteria::LESS_THAN);
        $ci->addDescendingOrderByColumn(BookoutNotesPeer::GROUP_ID);
        $ci->setLimit(1);
        if (BookoutNotesPeer::doCount($ci) > 0) {
            $dn = BookoutNotesPeer::doSelectOne($ci);
            return $dn->getNoteId();
        } else {
            return "";
        }
    }

    public static function getBNextDNId($note_id) {
        $ci = new Criteria();
        $ci->add(BookoutNotesPeer::GROUP_ID, $note_id, Criteria::GREATER_THAN);
        $ci->addAscendingOrderByColumn(BookoutNotesPeer::GROUP_ID);
        $ci->addGroupByColumn(BookoutNotesPeer::NOTE_ID);
        
        $ci->setLimit(1);
        if (BookoutNotesPeer::doCount($ci) > 0) {
            $dn = BookoutNotesPeer::doSelectOne($ci);
            return $dn->getNoteId();
        } else {
            return "";
        }
    }   
    
//////////////////////////////////////////////////////////////////////////////    
}

?>
