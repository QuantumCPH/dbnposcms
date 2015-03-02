<?php

/**
 * delivery_notes actions.
 *
 * @package    zapnacrm
 * @subpackage delivery_notes
 * @author     Khan Muhammad
 * @version    SVN: $Id: actions.class.php 5125 2007-09-16 00:53:55Z dwhittle $
 */
class delivery_notesActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
        if (Access::checkPermissions(4, "index", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $searchFld = $request->getParameter('searchFld');


        $c = new Criteria();

        if ($searchFld) {
            $c->add(DeliveryNotesPeer::NOTE_ID, $searchFld . "%", Criteria::LIKE);
        }
        $c->addGroupByColumn(DeliveryNotesPeer::NOTE_ID);
        $c->setLimit(1000);

        $this->notes_list = DeliveryNotesPeer::doSelect($c);
        $this->notesCount = DeliveryNotesPeer::doCount(new Criteria());
    }

    public function executeView($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
        if (Access::checkPermissions(4, "view", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $delivery_id = $request->getParameter("id");
        $c = new Criteria();
        $c->add(DeliveryNotesPeer::NOTE_ID, $delivery_id);
        if (DeliveryNotesPeer::doCount($c) == 0) {
            $this->redirect('delivery_notes/index');
        }
        $cd = new Criteria();
        $cd->clearSelectColumns();
        $cd->add(DeliveryNotesPeer::NOTE_ID, $delivery_id);
        $cd->addSelectColumn('SUM(' . DeliveryNotesPeer::IS_SYNCED . ') as sumsync');
        $cd->addGroupByColumn(DeliveryNotesPeer::NOTE_ID);
        $d_note = DeliveryNotesPeer::doSelectStmt($cd);
        $row = $d_note->fetch(PDO::FETCH_ASSOC);
        $this->edit_del = false;
        if ($row['sumsync'] == 0) {
            $this->edit_del = ture;
        }
        $delivery_note = DeliveryNotesPeer::doSelectOne($c);
        $this->dn = $delivery_note;
        $this->id = $delivery_note->getNoteId();
        $this->branch = $delivery_note->getBranchNumber();
        $this->company = $delivery_note->getCompanyNumber();
        $this->delivery_date = $delivery_note->getDeliveryDate();
        $this->notes_list = DeliveryNotesPeer::doSelect($c);
    }

    public function executeDeleteNotes($request) {
        $delivery_id = $request->getParameter("id");
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
        if (Access::checkPermissions(4, "deleteNotes", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $c = new Criteria();
        $c->add(DeliveryNotesPeer::NOTE_ID, $delivery_id);
        $deliveryNotes = DeliveryNotesPeer::doSelect($c);
        foreach ($deliveryNotes as $deliveryNote) {
            $deliveryNote->delete();
        }
        $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Delivery notes has been deleted.'));
        $this->redirect($request->getReferer());
        return sfView::NONE;
    }

    public function executeEdit($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
        if (Access::checkPermissions(4, "edit", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $cs = new Criteria();
        $cs->addAnd(ShopsPeer::STATUS_ID, 5, Criteria::NOT_EQUAL);
        $cs->addOR(ShopsPeer::STATUS_ID, 3, Criteria::EQUAL);
        $cs->addOR(ShopsPeer::STATUS_ID, NULL, Criteria::EQUAL);
        $shops = ShopsPeer::doSelect($cs);
        $this->shops = $shops;

        $delivery_id = $request->getParameter("id");
        $c = new Criteria();
        $c->add(DeliveryNotesPeer::NOTE_ID, $delivery_id);
        $delivery_note = DeliveryNotesPeer::doSelectOne($c);
        $this->id = $delivery_note->getNoteId();
        $this->branch = $delivery_note->getBranchNumber();
        $this->company = $delivery_note->getCompanyNumber();
        $this->delivery_date = $delivery_note->getDeliveryDate();
        $this->notes_list = DeliveryNotesPeer::doSelect($c);
        $this->shopid = $delivery_note->getShopId();
        $items = ItemsPeer::doSelect(new Criteria());
        $this->items = $items;
    }

    public function executeUpdate($request) {
        $note_id = $request->getParameter("id");
        $ids = $request->getParameter("dnId");
        $dnItemNo = $request->getParameter("dnItemNo");
        $dnItemQty = $request->getParameter("dnItemQty");
        $delivery_date = $request->getParameter("delivery_date");
        $shop_id = $request->getParameter("shop_id");
        $delete_ids = $request->getParameter("deletednId");
//        echo "cnt ".count($delete_ids);
//        echo " uni ".count(array_unique($dnItemNo));
//        $ids = array_diff($ids, $delete_ids);
//        print_r($ids);
//        die;
        $shop = ShopsPeer::retrieveByPK($shop_id);
        $branchNumber = '7788';
        $companyNumber = $shop->getCompanyNumber();
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        if ($request->isMethod('post')) {
            $cd = new Criteria();
            $cd->clearSelectColumns();
            $cd->add(DeliveryNotesPeer::NOTE_ID, $note_id);
            $cd->addSelectColumn('SUM(' . DeliveryNotesPeer::IS_SYNCED . ') as sumsync');
            $cd->addGroupByColumn(DeliveryNotesPeer::NOTE_ID);
            $d_note = DeliveryNotesPeer::doSelectStmt($cd);
            $row = $d_note->fetch(PDO::FETCH_ASSOC);
//            if($row['sumsync'] > 0){
//               $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Delivery Note is synced.'));
//               $this->redirect('delivery_notes/index');  
//            } 
            if (count($dnItemNo) == count(array_unique($dnItemNo))) {
                for ($i = 0; $i < count($ids); $i++) {
                    $cit = new Criteria();
                    $cit->add(ItemsPeer::ITEM_ID, $dnItemNo[$i], Criteria::EQUAL);
                    $chkinItem = ItemsPeer::doCount($cit);
                    if ($dnItemQty[$i] < 1) {
                        $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Item quantity can\'t be less than 1.'));
                        $this->redirect('delivery_notes/edit?id=' . $note_id);
                        //  $this->redirect('delivery_notes/index');
                    } elseif ($chkinItem == 0) {
                        $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Invalid Item number.'));
                        $this->redirect('delivery_notes/edit?id=' . $note_id);
                        //      $this->redirect('delivery_notes/index');
                    } else {
//                      print_r($ids);die;
                        $cnd = new Criteria();
                        $cnd->add(DeliveryNotesPeer::ID, $ids[$i]);
                        $delivery_note = DeliveryNotesPeer::doSelectOne($cnd);
                        $delivery_note->setItemId($dnItemNo[$i]);
                        $delivery_note->setQuantity($dnItemQty[$i]);
                        $delivery_note->setDeliveryDate($delivery_date);
                        $delivery_note->setBranchNumber($branchNumber);
                        $delivery_note->setCompanyNumber($companyNumber);
                        $delivery_note->setShopId($shop->getId());
                        $delivery_note->setUpdatedBy($user_id);
                        $delivery_note->save();
                    }
                }
                if (count($delete_ids) > 0) {
                    for ($d = 0; $d < count($delete_ids); $d++) {
                        $cndd = new Criteria();
                        $cndd->add(DeliveryNotesPeer::ID, $delete_ids[$d]);
                        $del_note = DeliveryNotesPeer::doSelectOne($cndd);
                        $del_note->delete();
                        //   echo $delete_ids[$d];
                    }
                }
                if (count($delete_ids) == count($ids)) {
                    $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Delivery note ' . $note_id . ' deleted.'));
                    $this->redirect('delivery_notes/index');
                }


                if ($shop->getGcmKey() != "") {
                    new GcmLib("delivery_note", array($shop->getGcmKey()),$shop);
                }

                $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Delivery note updated successfully.'));
                $this->redirect('delivery_notes/edit?id=' . $note_id);
                //    $this->redirect('delivery_notes/index');
            } else {
                $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Items must be different.'));
                $this->redirect('delivery_notes/edit?id=' . $note_id);
                //   $this->redirect('delivery_notes/index');
            }
//          }else{
//              $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Invalid Item number.'));
//              $this->redirect('delivery_notes/edit?id='.$note_id);
//          } 
        }
        return sfView::NONE;
    }

    public function executeAddItem($request) {
        $this->note_id = $request->getParameter('note_id');
        $cnd = new Criteria();
        $cnd->add(DeliveryNotesPeer::NOTE_ID, $this->note_id);
        $this->dnote = DeliveryNotesPeer::doSelectOne($cnd);
    }

    public function executeSaveItem($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        if ($request->isMethod('post')) {
            $itemnum = $request->getParameter('itemnum');
            $qty = $request->getParameter('qyantity');
            $note_id = $request->getParameter('note_id');

            $itmct = new Criteria();
            $itmct->add(ItemsPeer::ITEM_ID, $itemnum , Criteria::EQUAL);
            $itemquentity = ItemsPeer::doCount($itmct);
            if ($itemquentity == 0) {
                $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Item number does not exist in item list.'));
                $this->redirect('delivery_notes/edit?id=' . $note_id);
                //    $this->redirect('delivery_notes/index');
            }


         
            $csc = new Criteria();
            $csc->add(SystemConfigPeer::KEYS, "Check Delivery Items", Criteria::EQUAL);
            $csc->addOr(SystemConfigPeer::ID, 3);
            $dnItem = SystemConfigPeer::doSelectOne($csc);
            if ($dnItem->getValues() == "Yes") {
                $cit = new Criteria();
                $cit->add(ItemsPeer::ITEM_ID, $itemnum, Criteria::EQUAL);
                $chkitem = ItemsPeer::doCount($cit);
                if ($chkitem == 0) {
                    $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Invalid Item Number.'));
                    $this->redirect('delivery_notes/edit?id=' . $note_id);
                    //     $this->redirect('delivery_notes/index');
                }
            } else {
                $cit = new Criteria();
                $cit->add(ItemsPeer::ITEM_ID, $itemnum, Criteria::EQUAL);
                $chkitem = ItemsPeer::doCount($cit);
                if ($chkitem == 0) {
                    $item = array();
                    $item["id"] = $itemnum;
                    $item["ean"] = $itemnum;
                    $item["updated_by"] = $this->getUser()->getAttribute('user_id', '', 'backendsession');
                    itemsLib::populateWebItem($item);
                }
            }
            $cnd = new Criteria();
            $cnd->add(DeliveryNotesPeer::NOTE_ID, $note_id);
            $dnote = DeliveryNotesPeer::doSelectOne($cnd);

            $dnCount = DeliveryNotesPeer::doCount(new Criteria());
            $id = "1" . $dnCount . time();

            $note = new DeliveryNotes();
            $note->setId($id);
            $note->setItemId($itemnum);
            $note->setQuantity($qty);
            $note->setNoteId($note_id);
            $note->setIsSynced(0);
            $note->setBranchNumber($dnote->getBranchNumber());
            $note->setCompanyNumber($dnote->getCompanyNumber());
            $note->setShopId($dnote->getShopId());
            $note->setDeliveryDate($dnote->getDeliveryDate());
            $note->setStatusId(1);
            $note->setUpdatedBy($user_id);
            $note->setGroupId($dnote->getGroupId());
            $note->save();
            $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Item added successfully.'));
            $this->redirect('delivery_notes/edit?id=' . $note_id);
            //   $this->redirect('delivery_notes/index');
        }
    }

    public function executeAdd($request) {
        $items = ItemsPeer::doSelect(new Criteria());
        $this->items = $items;
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
        if (Access::checkPermissions(4, "add", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $cs = new Criteria();
        $cs->addAnd(ShopsPeer::STATUS_ID, 5, Criteria::NOT_EQUAL);
        $cs->addOR(ShopsPeer::STATUS_ID, 3, Criteria::EQUAL);
        $cs->addOR(ShopsPeer::STATUS_ID, NULL, Criteria::EQUAL);
        $shops = ShopsPeer::doSelect($cs);
        $this->shops = $shops;
    }

    public function executeShowItems($request) {
        $term = $request->getParameter("term");
        $cit = new Criteria();
        $cit->add(ItemsPeer::ITEM_ID, '' . $term . '%', Criteria::LIKE);
        $cit->add(ItemsPeer::STATUS_ID, 3);
        $cit->addAscendingOrderByColumn(ItemsPeer::ITEM_ID);
        if (ItemsPeer::doCount($cit) > 0) {
            $cit->setLimit(15);
            $items = ItemsPeer::doSelect($cit);
            foreach ($items as $item) {
                $results[] = array('label' => $item->getItemId());
            }
        } else {
            $results[] = array('label' => "No Item found.");
        }
        echo json_encode($results);
        return sfView::NONE;
    }

    public function executeAddNewNote($request) {
        $note_id = $request->getParameter("dnumber");
        $shop_id = $request->getParameter("shop_id");
        $delivery_date = $request->getParameter("ddate");
//           echo $delivery_date;
        $this->note_id = $note_id;
        $this->shop_id = $shop_id;
        $this->delivery_date = $delivery_date;
    }

    public function executeSaveNewNote($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        if ($request->isMethod('post')) {
            $note_id = $request->getParameter("note_id");
            $shop_id = $request->getParameter("shop_id");
            $shop = ShopsPeer::retrieveByPK($shop_id);
            $branch = $shop->getBranchNumber();
            $co_num = $shop->getCompanyNumber();
            $item_num = $request->getParameter("itemnum");
            $item_qty = $request->getParameter("qyantity");
            $delivery_date = $request->getParameter("ddate");


            if ($note_id != "") {
                $csc = new Criteria();
                $csc->add(SystemConfigPeer::KEYS, "Check Double Delivery Notes", Criteria::EQUAL);
                $csc->addOr(SystemConfigPeer::ID, 2);
                $dnItem = SystemConfigPeer::doSelectOne($csc);
                if ($dnItem->getValues() == "Yes") {
                    $cd = new Criteria();
                    $cd->add(DeliveryNotesPeer::NOTE_ID, $note_id, Criteria::EQUAL);
//                    $cd->add(DeliveryNotesPeer::IS_SYNCED,1 , Criteria::EQUAL);
                    if (DeliveryNotesPeer::doCount($cd) > 0) {
                        $this->getUser()->setFlash('add_error', $this->getContext()->getI18N()->__('Delivery number already exist.'));
                        $this->redirect('delivery_notes/add');
                    }
                }
                $csc = new Criteria();
                $csc->add(SystemConfigPeer::KEYS, "Check Delivery Items", Criteria::EQUAL);
                $csc->addOr(SystemConfigPeer::ID, 3);
                $dnItem = SystemConfigPeer::doSelectOne($csc);
                if ($dnItem->getValues() == "Yes") {
                    $cit = new Criteria();
                    $cit->add(ItemsPeer::ITEM_ID, $item_num, Criteria::EQUAL);
//                    $cit->addAnd(ItemsPeer::STATUS_ID,3, Criteria::EQUAL);
                    $chkitem = ItemsPeer::doCount($cit);
                    if ($chkitem == 0) {
                        $this->getUser()->setFlash('add_error', $this->getContext()->getI18N()->__('Invalid Item Number.'));
                        $this->redirect('delivery_notes/add');
                    }
                } else {
                    $cit = new Criteria();
                    $cit->add(ItemsPeer::ITEM_ID, $item_num, Criteria::EQUAL);
//                    $cit->addAnd(ItemsPeer::STATUS_ID,3, Criteria::EQUAL);
                    $chkitem = ItemsPeer::doCount($cit);
                    if ($chkitem == 0) {
                        $item = array();
                        $item["id"] = $item_num;
                        $item["ean"] = $item_num;
                        $item["updated_by"] = $this->getUser()->getAttribute('user_id', '', 'backendsession');
                        itemsLib::populateWebItem($item);
                    }
                }
                $cdn = new Criteria();
                $cdn->add(DeliveryNotesPeer::NOTE_ID, $note_id, Criteria::EQUAL);
//                   $cdn->add(DeliveryNotesPeer::IS_SYNCED,1 , Criteria::NOT_EQUAL);
                if (DeliveryNotesPeer::doCount($cdn) > 0) {
                    $notes = DeliveryNotesPeer::doSelectOne($cdn);
                    $noteshop = $notes->getShopId();
                    $notebranch = $notes->getBranchNumber();
                } else {
                    $noteshop = "";
                }

                if ($noteshop != "" && $noteshop != $shop_id) {
                    $this->getUser()->setFlash('add_error', $this->getContext()->getI18N()->__('Delivery note ' . $note_id . ' already exist with branch ' . $notebranch . '. You can edit it. <a href="' . sfConfig::get("app_admin_url") . 'delivery_notes/edit/id/' . $note_id . '">Click here</a>'));
                    $this->redirect('delivery_notes/add');
                }

               
                $cdn2 = new Criteria();
                $cdn2->add(DeliveryNotesPeer::NOTE_ID, $note_id, Criteria::EQUAL);
//                   $cdn2->add(DeliveryNotesPeer::IS_SYNCED,1 , Criteria::NOT_EQUAL);
                $cdn2->add(DeliveryNotesPeer::ITEM_ID, $item_num, Criteria::EQUAL);
                $notescount = DeliveryNotesPeer::doCount($cdn2);
                if ($notescount > 0) {
                    $this->getUser()->setFlash('add_error', $this->getContext()->getI18N()->__('Item ' . $item_num . ' already exist in delivery note ' . $note_id . '. You can edit it. <a href="' . sfConfig::get("app_admin_url") . 'delivery_notes/edit/id/' . $note_id . '">Click here</a>'));
                    $this->redirect('delivery_notes/add');
                }
                $cd = new Criteria();
                $cd->clearSelectColumns();
                $cd->addSelectColumn('MAX(' . DeliveryNotesPeer::GROUP_ID . ') as maxgroup');
                $stmt = DeliveryNotesPeer::doSelectStmt($cd);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $max_group = $row['maxgroup'] + 1;

                $dnCount = DeliveryNotesPeer::doCount(new Criteria());
                $id = "1" . $dnCount . time();
                $new_dn = new DeliveryNotes();
                $new_dn->setId($id);
                $new_dn->setNoteId($note_id);
                $new_dn->setBranchNumber($branch);
                $new_dn->setCompanyNumber($co_num);
                $new_dn->setShopId($shop_id);
                $new_dn->setQuantity($item_qty);
                $new_dn->setItemId($item_num);
                $new_dn->setDeliveryDate(date("Y-m-d H:i:s", strtotime($delivery_date)));
                $new_dn->setStatusId(1);
                $new_dn->setUpdatedBy($user_id);
                $new_dn->setGroupId($max_group);
                $new_dn->save();


                $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Delivery note added.'));
                $this->redirect('delivery_notes/edit?id=' . $new_dn->getNoteId());
                //     $this->redirect('delivery_notes/index');
            }
        }
    }

    public function executeDelNotes($request) {
        $note_id = $request->getParameter("id");
        $cn = new Criteria();
        $cn->add(DeliveryNotesPeer::NOTE_ID, $note_id, Criteria::EQUAL);
        $notes = DeliveryNotesPeer::doSelect($cn);
        foreach ($notes as $note) {
            $note->delete();
        }
        $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Delivery note deleted.'));
        $this->redirect('delivery_notes/index');
    }

    public function executeValidateDeliverNote($request) {
        $note_id = $request->getParameter("delivery_number");

        //   $id = $request->getParameter("id");
        $csc = new Criteria();
        $csc->add(SystemConfigPeer::KEYS, "Check Double Delivery Notes", Criteria::EQUAL);
        $csc->addOr(SystemConfigPeer::ID, 2);
        $dnItem = SystemConfigPeer::doSelectOne($csc);
        if ($dnItem->getValues() == "Yes") {
            $cuc = new Criteria();
            $cuc->add(DeliveryNotesPeer::NOTE_ID, $note_id);
            if (DeliveryNotesPeer::doCount($cuc) > 0) {
                echo "false";
            } else {
                echo "true";
            }
        } else {
            echo "true";
        }
        return sfView::NONE;
    }

}
