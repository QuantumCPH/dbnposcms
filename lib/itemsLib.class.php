<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class itemsLib {

    public static function currencyVersionConvertor($str) {
        $str = str_replace(".", "", $str);
        $str = str_replace(",", ".", $str);
        return $str;
    }

    public static function populateItem($combine) {
        $combine['selling_price'] = itemsLib::currencyVersionConvertor($combine['selling_price']);
        $combine['buying_price'] = itemsLib::currencyVersionConvertor($combine['buying_price']);
        $c = new Criteria();
        $c->add(ItemsPeer::ITEM_ID, $combine['id']);
        $today = time();
        $item = 0;
        $itembeforUpdate = "";
        if (ItemsPeer::doCount($c) == 0) {
            $item = new Items();
            $item->setCreatedAt($today);
            $item->setItemUpdatedAt($today);
            $item->setItemId($combine['id']);
        } else {
            $item = ItemsPeer::doSelectOne($c);
            $item->setItemUpdatedAt($today);
            // Log:::::::::
        }
        $varableupdate = false;
        $item_log = new ItemsLog();



        if ($combine['description1']) {
            if ($combine['description1'] != $item->getDescription1()) {
                $item->setDescription1($combine['description1']);
                $item_log->setDescription1("<div class='highlightblock'>" . $combine['description1'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setDescription1($item->getDescription1());
                $item_log->setDescription1($item->getDescription1());
            }
        }
        if ($combine['description2']) {
            if ($combine['description2'] != $item->getDescription2()) {
                $item->setDescription2($combine['description2']);
                $item_log->setDescription2("<div class='highlightblock'>" . $combine['description2'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setDescription2($item->getDescription2());
                $item_log->setDescription2($item->getDescription2());
            }
        }

        if ($combine['description3']) {
            if ($combine['description3'] != $item->getDescription3()) {
                $item->setDescription3($combine['description3']);
                $item_log->setDescription3("<div class='highlightblock'>" . $combine['description3'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setDescription3($item->getDescription3());
                $item_log->setDescription3($item->getDescription3());
            }
        }

        if ($combine['supplier_number']) {
            if ($combine['supplier_number'] != $item->getSupplierNumber()) {
                $item->setSupplierNumber($combine['supplier_number']);
                $item_log->setSupplierNumber("<div class='highlightblock'>" . $combine['supplier_number'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setSupplierNumber($item->getSupplierNumber());
                $item_log->setSupplierNumber($item->getSupplierNumber());
            }
        }

        if ($combine['supplier_item_number']) {
            if ($combine['supplier_item_number'] != $item->getSupplierItemNumber()) {
                $item->setSupplierItemNumber($combine['supplier_item_number']);
                $item_log->setSupplierItemNumber("<div class='highlightblock'>" . $combine['supplier_item_number'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setSupplierItemNumber($item->getSupplierItemNumber());
                $item_log->setSupplierItemNumber($item->getSupplierItemNumber());
            }
        }
        if ($combine['ean']) {
            if ($combine['ean'] != $item->getEan()) {
                $item->setEan($combine['ean']);
                $item_log->setEan("<div class='highlightblock'>" . $combine['ean'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setEan($item->getEan());
                $item_log->setEan($item->getEan());
            }
        }
        if ($combine['color']) {
            if ($combine['color'] != $item->getColor()) {
                $item->setColor($combine['color']);
                $item_log->setColor("<div class='highlightblock'>" . $combine['color'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setColor($item->getColor());
                $item_log->setColor($item->getColor());
            }

            //    die;
        }
        if ($combine['group']) {
            if ($combine['group'] != $item->getGroup()) {
                $item->setGroup($combine['group']);
                $item_log->setGroup("<div class='highlightblock'>" . $combine['group'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setGroup($item->getGroup());
                $item_log->setGroup($item->getGroup());
            }
        }
        if ($combine['size']) {
            if ($combine['size'] != $item->getSize()) {
                $item->setSize($combine['size']);
                $item_log->setSize("<div class='highlightblock'>" . $combine['size'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setSize($item->getSize());
                $item_log->setSize($item->getSize());
            }
        }
        if ($combine['buying_price']) {
            if ($combine['buying_price'] != $item->getBuyingPrice()) {
                $item->setBuyingPrice($combine['buying_price']);
                $item_log->setBuyingPrice("<div class='highlightblock'>" . $combine['buying_price'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setBuyingPrice($item->getBuyingPrice());
                $item_log->setBuyingPrice($item->getBuyingPrice());
            }
        }
        if ($combine['selling_price']) {
            if ($combine['selling_price'] != $item->getSellingPrice()) {
                $item->setSellingPrice($combine['selling_price']);
                $item_log->setSellingPrice("<div class='highlightblock'>" . $combine['selling_price'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setSellingPrice($item->getSellingPrice());
                $item_log->setSellingPrice($item->getSellingPrice());
            }
        }
        if ($combine['taxation_code']) {
            if ($combine['taxation_code'] != $item->getTaxationCode()) {
                $item->setTaxationCode($combine['taxation_code']);
                $item_log->setTaxationCode("<div class='highlightblock'>" . $combine['taxation_code'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setTaxationCode($item->getTaxationCode());
                $item_log->setTaxationCode($item->getTaxationCode());
            }
        }
        if ($combine['is_image_update']) {
            $img = "<div class='highlightblock'><img src='" . sfConfig::get("app_web_url") . "uploads/images/thumbs/" . $combine['id'] . "_50.jpg'  ></div>";
            $item_log->setImageName($img);
//            $item_log->setImageName($combine['id']);
            $varableupdate = true;
        } else {
            $img = "<img src='" . sfConfig::get("app_web_url") . "uploads/images/thumbs/" . $combine['id'] . "_50.jpg'  >";
            $item_log->setImageName($img);
//            $item_log->setImageName($combine['id']);
        }
        if ($combine['updated_by']) {
            $item_log->setUpdatedBy($combine['updated_by']);
//                $varableupdate = true;
        }
        //  if ($combine['small_pic']) {
        //   if ($combine['small_pic'] != $item->getSmallPic())
        $small_pic = $combine['id'] . "_32.jpg";
        $large_pic = $combine['id'] . "_187.jpg";
        $original_pic = $combine['id'] . ".jpg";
        $item->setSmallPic($small_pic);
        $item->setLargePic($large_pic);
        $item->setOriginalPic($original_pic);
        // }



        if ($item->save()) {
            //////////////////////////////log area //////////////////////////////////////////////  

            $item_log->setItemId($item->getItemId());

            if ($varableupdate) {
                $item_log->setSmallPic($small_pic);
                $item_log->setLargePic($large_pic);

                $item_log->setCreatedAt($today);
                $item_log->setUpdatedAt($today);
                $item_log->save();
            }
            ///////////////////////////////////////////////////////////////////////////////////////// 
//            $c = new Criteria();
//            if (ShopsPeer::doCount($c) > 0 && $varableupdate) {
//                $shops = ShopsPeer::doSelect($c);
//                foreach ($shops as $shop) {
//                    $item_sync = new ItemsSync();
//                    $item_sync->setItemId($item->getItemId());
//                    $item_sync->setDescription1($item->getDescription1());
//                    $item_sync->setDescription2($item->getDescription2());
//                    $item_sync->setDescription3($item->getDescription3());
//                    $item_sync->setSupplierNumber($item->getSupplierNumber());
//                    $item_sync->setSupplierItemNumber($item->getSupplierItemNumber());
//                    $item_sync->setEan($item->getEan());
//                    $item_sync->setColor($item->getColor());
//                    $item_sync->setGroup($item->getGroup());
//                    $item_sync->setSize($item->getSize());
//                    $item_sync->setBuyingPrice($item->getBuyingPrice());
//                    $item_sync->setSellingPrice($item->getSellingPrice());
//                    $item_sync->setTaxationCode($item->getTaxationCode());
//                    $item_sync->setSmallPic($item->getSmallPic());
//                    $item_sync->setLargePic($item->getLargePic());
//                    $item_sync->setCreatedAt($today);
//                    $item_sync->setShopId($shop->getId());
//                    $item_sync->save();
//                }
//            }
            return true;
        } else {
            return false;
        }
    }

    public static function populateDeliveryNotes($combine) {
        $today = time();
        $cdn = new Criteria();
        $cdn->add(DeliveryNotesPeer::NOTE_ID, $combine['delivery_number']);
        $dnCount = DeliveryNotesPeer::doCount($cdn);
        if ($dnCount > 0) {
            $deliverN = DeliveryNotesPeer::doSelectOne($cdn);
            $group_id = $deliverN->getGroupId();
        } else {
            $cd = new Criteria();
            $cd->clearSelectColumns();
            $cd->addSelectColumn('MAX(' . DeliveryNotesPeer::GROUP_ID . ') as maxgroup');
            $stmt = DeliveryNotesPeer::doSelectStmt($cd);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $group_id = $row['maxgroup'] + 1;
        }

        $dnCount = DeliveryNotesPeer::doCount(new Criteria());
        $id = "1" . $dnCount . time();

        $sc = new Criteria();
        $sc->add(ShopsPeer::COMPANY_NUMBER, $combine['company_number']);
        $sc->add(ShopsPeer::BRANCH_NUMBER, $combine['branch_number']);
        $shop = ShopsPeer::doSelectOne($sc);
        $dNote = new DeliveryNotes();
        $dNote->setId($id);
        $dNote->setQuantity($combine['quantity']);
        $dNote->setItemId($combine['item_number']);
        $dNote->setStatusId(1);
        $dNote->setDeliveryDate($combine['delivery_date']);
        $dNote->setNoteId($combine['delivery_number']);
        $dNote->setCreatedAt($today);
        $dNote->setBranchNumber($combine['branch_number']);
        $dNote->setCompanyNumber($combine['company_number']);
        $dNote->setShopId($shop->getId());
        $dNote->setUpdatedBy($combine['updated_by']);
        $dNote->setGroupId($group_id);

        if ($dNote->save()) {

            return true;
        } else {
            return false;
        }
    }

    public static function createTransactionUsingObject($object, $shop_id, $orderId = 0) {
        $transaction = new Transactions();
        $transaction->setTransactionTypeId($object->transaction_type_id);
        $transaction->setShopId($shop_id);
        $transaction->setShopTransactionId($object->pos_id);
        $transaction->setQuantity($object->quantity);
        $transaction->setItemId($object->item_id);
        $transaction->setShopOrderNumberId($object->order_number_id);
        $transaction->setShopReceiptNumberId($object->shop_receipt_id);
        $transaction->setStatusId($object->status_id);
        $transaction->setCreatedAt($object->created_at);
        $transaction->setUpdatedAt($object->updated_at);
        $transaction->setDiscountTypeId($object->discount_type_id);
        $transaction->setDiscountValue($object->discount_value);
        $transaction->setParentType($object->parent_type);
        $transaction->setCmsItemId($object->item_cms_id);
        $transaction->setParentTypeId($object->parent_type_id);
        $transaction->setSoldPrice($object->sold_price);
        $transaction->setDescription1($object->description1);
        $transaction->setDescription2($object->description2);
        $transaction->setDescription3($object->description3);
        $transaction->setSupplierItemNumber($object->supplier_item_number);
        $transaction->setSupplierNumber($object->supplier_number);
        $transaction->setEan($object->ean);
        $transaction->setDayStartId($object->day_start_id);
        $transaction->setColor($object->color);
        $transaction->setGroup($object->group);
        $transaction->setSize($object->size);
        $transaction->setSellingPrice($object->selling_price);
        $transaction->setBuyingPrice($object->buying_price);
        $transaction->setTaxationCode($object->taxation_code);
        $transaction->setUserId($object->user_id);
        if ($orderId != 0)
            $transaction->setOrderId($orderId);
        if ($transaction->save()) {
            return $transaction->getShopTransactionId();
        }
    }

    public static function createOrderUsingObject($object, $shop_id) {
        $order = new Orders();
        $order->setTotalAmount($object->total_amount);
        $order->setShopId($shop_id);
//        $order->setCreatedAt($object->created_at);
        $order->setCreatedAt(date("Y-m-d H:i:s"));
        $order->setStatusId($object->status_id);
        $order->setTotalSoldAmount($object->sold_total_amount);
        $order->setDiscountValue($object->order_discount_value);
        $order->setDiscountTypeId($object->order_discount_type);
        $order->setShopUserId($object->shop_user_id);
        $order->setDayStartId($object->day_start_id);
        $order->setShopOrderId($object->shop_order_id);
        $order->setShopReceiptNumberId($object->shop_receipt_id);
        $order->setUpdatedAt($object->updated_at);
        $order->setEmployeeId($object->employee_id);

        if ($order->save()) {
            return $order->getShopOrderId() . "~" . $order->getId();
        }
    }

    public static function updateOrderUsingObject($object, $shop_id) {

        $co = new Criteria();
        $co->add(OrdersPeer::SHOP_ORDER_ID, $object->shop_order_id);
        $co->add(OrdersPeer::SHOP_ID, $shop_id);
        $order = OrdersPeer::doSelectOne($co);

//        $order = new Orders();
        $order->setTotalAmount($object->total_amount);
        $order->setShopId($shop_id);
//        $order->setCreatedAt($object->created_at);
//        $order->setCreatedAt(date("Y-m-d H:i:s"));
        $order->setStatusId($object->status_id);
        $order->setTotalSoldAmount($object->sold_total_amount);
        $order->setDiscountValue($object->order_discount_value);
        $order->setDiscountTypeId($object->order_discount_type);
        $order->setShopUserId($object->shop_user_id);
        $order->setDayStartId($object->day_start_id);
        $order->setShopOrderId($object->shop_order_id);
        $order->setShopReceiptNumberId($object->shop_receipt_id);
        $order->setUpdatedAt($object->updated_at);
        $order->setEmployeeId($object->employee_id);

        if ($order->save()) {
            return $order->getShopOrderId() . "~" . $order->getId();
        }
    }

    public static function createOrderPaymentUsingObject($object, $shop_id, $order_id) {
        $orderpayment = new OrderPayments();
        $orderpayment->setOrderId($order_id);
        $orderpayment->setShopId($shop_id);
        $orderpayment->setCreatedAt($object->created_at);
        $orderpayment->setPaymentTypeId($object->payment_type_id);
        $orderpayment->setAmount($object->total_amount);
        $orderpayment->setShopOrderPaymentId($object->shop_order_payment_id);
        $orderpayment->setUpdatedAt($object->updated_at);
        $orderpayment->setDayStartId($object->day_start_id);
        $orderpayment->setShopOrderUserId($object->order_user_id);
        $orderpayment->setShopCreatedAt($object->created_at);
        $orderpayment->setCcTypeId($object->cc_type_id);

        if ($orderpayment->save()) {
            return $orderpayment->getShopOrderPaymentId();
        }
    }

    public static function populateWebItem($combine) {
        if (!is_numeric($combine['selling_price'])) {
            $combine['selling_price'] = itemsLib::currencyVersionConvertor($combine['selling_price']);
        }
        if (!is_numeric($combine['buying_price'])) {
            $combine['buying_price'] = itemsLib::currencyVersionConvertor($combine['buying_price']);
        }

        $c = new Criteria();
        $c->add(ItemsPeer::ITEM_ID, $combine['id']);
        $today = time();
        $item = 0;
        $itembeforUpdate = "";
        if (ItemsPeer::doCount($c) == 0) {
            $item = new Items();
            $item->setCreatedAt($today);
            $item->setItemUpdatedAt($today);
            $item->setItemId($combine['id']);
        } else {
            $item = ItemsPeer::doSelectOne($c);
            $item->setItemUpdatedAt($today);
            // Log:::::::::
        }
        $varableupdate = false;
        $item_log = new ItemsLog();



        if (isset($combine['description1'])) {
            if (trim($combine['description1']) != $item->getDescription1()) {
                $item->setDescription1($combine['description1']);
                $item_log->setDescription1("<div class='highlightblock'>" . $combine['description1'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setDescription1($item->getDescription1());
                $item_log->setDescription1($item->getDescription1());
            }
        }
        if (isset($combine['description2'])) {
            if (trim($combine['description2']) != $item->getDescription2()) {
                $item->setDescription2($combine['description2']);
                $item_log->setDescription2("<div class='highlightblock'>" . $combine['description2'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setDescription2($item->getDescription2());
                $item_log->setDescription2($item->getDescription2());
            }
        }

        if (isset($combine['description3'])) {
            if (trim($combine['description3']) != $item->getDescription3()) {
                $item->setDescription3($combine['description3']);
                $item_log->setDescription3("<div class='highlightblock'>" . $combine['description3'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setDescription3($item->getDescription3());
                $item_log->setDescription3($item->getDescription3());
            }
        }

        if (isset($combine['supplier_number'])) {
            if (trim($combine['supplier_number']) != $item->getSupplierNumber()) {
                $item->setSupplierNumber($combine['supplier_number']);
                $item_log->setSupplierNumber("<div class='highlightblock'>" . $combine['supplier_number'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setSupplierNumber($item->getSupplierNumber());
                $item_log->setSupplierNumber($item->getSupplierNumber());
            }
        }

        if (isset($combine['supplier_item_number'])) {
            if (trim($combine['supplier_item_number']) != $item->getSupplierItemNumber()) {
                $item->setSupplierItemNumber($combine['supplier_item_number']);
                $item_log->setSupplierItemNumber("<div class='highlightblock'>" . $combine['supplier_item_number'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setSupplierItemNumber($item->getSupplierItemNumber());
                $item_log->setSupplierItemNumber($item->getSupplierItemNumber());
            }
        }
        if (isset($combine['ean'])) {
            if (trim($combine['ean']) != $item->getEan()) {
                $item->setEan($combine['ean']);
                $item_log->setEan("<div class='highlightblock'>" . $combine['ean'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setEan($item->getEan());
                $item_log->setEan($item->getEan());
            }
        }
        if (isset($combine['color'])) {
            if (trim($combine['color']) != $item->getColor()) {
                $item->setColor($combine['color']);
                $item_log->setColor("<div class='highlightblock'>" . $combine['color'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setColor($item->getColor());
                $item_log->setColor($item->getColor());
            }

            //    die;
        }
        if (isset($combine['group'])) {
            if (trim($combine['group']) != $item->getGroup()) {
                $item->setGroup($combine['group']);
                $item_log->setGroup("<div class='highlightblock'>" . $combine['group'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setGroup($item->getGroup());
                $item_log->setGroup($item->getGroup());
            }
        }
        if (isset($combine['size'])) {
            if (trim($combine['size']) != $item->getSize()) {
                $item->setSize($combine['size']);
                $item_log->setSize("<div class='highlightblock'>" . $combine['size'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setSize($item->getSize());
                $item_log->setSize($item->getSize());
            }
        }
        if (isset($combine['buying_price'])) {
            if (trim($combine['buying_price']) != $item->getBuyingPrice()) {
                $item->setBuyingPrice($combine['buying_price']);
                $item_log->setBuyingPrice("<div class='highlightblock'>" . $combine['buying_price'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setBuyingPrice($item->getBuyingPrice());
                $item_log->setBuyingPrice($item->getBuyingPrice());
            }
        }
        if (isset($combine['selling_price'])) {
            if (trim($combine['selling_price']) != $item->getSellingPrice()) {
                $item->setSellingPrice($combine['selling_price']);
                $item_log->setSellingPrice("<div class='highlightblock'>" . $combine['selling_price'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setSellingPrice($item->getSellingPrice());
                $item_log->setSellingPrice($item->getSellingPrice());
            }
        }
        if (isset($combine['taxation_code'])) {
            if (trim($combine['taxation_code']) != $item->getTaxationCode()) {
                $item->setTaxationCode($combine['taxation_code']);
                $item_log->setTaxationCode("<div class='highlightblock'>" . $combine['taxation_code'] . "</div>");
                $varableupdate = true;
            } else {
                $item->setTaxationCode($item->getTaxationCode());
                $item_log->setTaxationCode($item->getTaxationCode());
            }
        }
        if ($combine['is_image_update']) {
            $timdt = date('YmdHis');
            $img = "<div class='highlightblock'><img src='" . sfConfig::get("app_web_url") . "uploads/images/thumbs/" . $combine['id'] . "_50.jpg?" . $timdt . "'  ></div>";
            $item_log->setImageName($img);
//            $item_log->setImageName($combine['id']);
            $varableupdate = true;
        } else {
            $img = "<img src='" . sfConfig::get("app_web_url") . "uploads/images/thumbs/" . $combine['id'] . "_50.jpg'  >";
            $item_log->setImageName($img);
//            $item_log->setImageName($combine['id']);
        }
        if (isset($combine['status_id'])) {
            if (trim($combine['status_id']) != $item->getStatusId()) {
                $item->setStatusId($combine['status_id']);
                if ($combine['status_id'] == 5) {
                    $st = "Inactive";
                } else {
                    $st = "Active";
                }
                $item_log->setItemStatusId("<div class='highlightblock'>" . $st . "</div>");
                $varableupdate = true;
            } else {
                $item->setStatusId($item->getStatusId());
                if ($item->getStatusId() == 5) {
                    $st = "Inactive";
                } else {
                    $st = "Active";
                }
                $item_log->setItemStatusId($st);
            }
        }

        if (isset($combine['updated_by'])) {
            $item_log->setUpdatedBy($combine['updated_by']);
//                $varableupdate = true;
        }
        //  if ($combine['small_pic']) {
        //   if ($combine['small_pic'] != $item->getSmallPic())
        $small_pic = $combine['id'] . "_32.jpg";
        $large_pic = $combine['id'] . "_187.jpg";
        $original_pic = $combine['id'] . ".jpg";
        $item->setSmallPic($small_pic);
        $item->setLargePic($large_pic);
        $item->setOriginalPic($original_pic);
        // }



        if ($item->save()) {
            //////////////////////////////log area //////////////////////////////////////////////  

            $item_log->setItemId($item->getItemId());

            if ($varableupdate) {
                $item_log->setSmallPic($small_pic);
                $item_log->setLargePic($large_pic);

                $item_log->setCreatedAt($today);
                $item_log->setUpdatedAt($today);
                $item_log->save();
            }
            ///////////////////////////////////////////////////////////////////////////////////////// 
//            $c = new Criteria();
//            if (ShopsPeer::doCount($c) > 0 && $varableupdate) {
//                $shops = ShopsPeer::doSelect($c);
//                foreach ($shops as $shop) {
//                    $item_sync = new ItemsSync();
//                    $item_sync->setItemId($item->getItemId());
//                    $item_sync->setDescription1($item->getDescription1());
//                    $item_sync->setDescription2($item->getDescription2());
//                    $item_sync->setDescription3($item->getDescription3());
//                    $item_sync->setSupplierNumber($item->getSupplierNumber());
//                    $item_sync->setSupplierItemNumber($item->getSupplierItemNumber());
//                    $item_sync->setEan($item->getEan());
//                    $item_sync->setColor($item->getColor());
//                    $item_sync->setGroup($item->getGroup());
//                    $item_sync->setSize($item->getSize());
//                    $item_sync->setBuyingPrice($item->getBuyingPrice());
//                    $item_sync->setSellingPrice($item->getSellingPrice());
//                    $item_sync->setTaxationCode($item->getTaxationCode());
//                    $item_sync->setSmallPic($item->getSmallPic());
//                    $item_sync->setLargePic($item->getLargePic());
//                    $item_sync->setCreatedAt($today);
//                    $item_sync->setShopId($shop->getId());
//                    $item_sync->save();
//                }
//            }
            return true;
        } else {
            return false;
        }
    }

}

?>
