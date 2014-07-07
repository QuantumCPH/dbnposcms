<?php

/**
 * shops actions.
 *
 * @package    zapnacrm
 * @subpackage shops
 * @author     Your name here
 */
class shopsActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Shops module id is 5.        
        if (Access::checkPermissions(5, "index", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }







        //  $this->shops_list = ShopsPeer::doSelect();
//      $c=new Criteria();
//    
//     $c->addDescendingOrderByColumn(ShopsPeer::CREATED_AT);
//
//        //set paging
//        $items_per_page = 10; //shouldn't be 0
//        $this->page = $request->getParameter('page');
//        if ($this->page == '')
//            $this->page = 1;
//
//        $pager = new sfPropelPager('Shops', $items_per_page);
//        $pager->setPage($this->page);
//
//        $pager->setCriteria($c);
//
//        $pager->init();
//
//        $this->shops_list = $pager->getResults();
//        $this->total_pages = ceil($pager->getNbResults() / $items_per_page);
//    
    }

//  public function executeNew(sfWebRequest $request)
//  {
//    $this->form = new ShopsForm();
//  }
//
//  public function executeCreate(sfWebRequest $request)
//  {
//    $this->forward404Unless($request->isMethod('post'));
//
//    $this->form = new ShopsForm();
//
//    $this->processForm($request, $this->form);
//
//    $this->setTemplate('new');
//  }
//
//  public function executeEdit(sfWebRequest $request)
//  {
//    $this->forward404Unless($shops = ShopsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object shops does not exist (%s).', $request->getParameter('id')));
//    $this->form = new ShopsForm($shops);
//  }
//
//  public function executeUpdate(sfWebRequest $request)
//  {
//    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
//    $this->forward404Unless($shops = ShopsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object shops does not exist (%s).', $request->getParameter('id')));
//    $this->form = new ShopsForm($shops);
//
//    $this->processForm($request, $this->form);
//
//    $this->setTemplate('edit');
//  }
//
//  public function executeDelete(sfWebRequest $request)
//  {
//    $request->checkCSRFProtection();
//
//    $this->forward404Unless($shops = ShopsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object shops does not exist (%s).', $request->getParameter('id')));
//    $shops->delete();
//
//    $this->redirect('shops/index');
//  }
//
//  protected function processForm(sfWebRequest $request, sfForm $form)
//  {
//    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
//    if ($form->isValid())
//    {
//      $shops = $form->save();
//
//      $this->redirect('shops/edit?id='.$shops->getId());
//    }
//  }


    public function executeView(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Shops module id is 5.        
        if (Access::checkPermissions(5, "view", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        $this->ids = $request->getParameter('id');
        $this->shops = ShopsPeer::retrieveByPK($request->getParameter('id'));



        $cu = new Criteria();
//            $cu->add(UserPeer::ID,$current_user_id);
        $cu->addJoin(ShopUsersPeer::USER_ID, UserPeer::ID, Criteria::LEFT_JOIN);
        $cu->addAnd(ShopUsersPeer::SHOP_ID, $request->getParameter('id'));
        $cu->addAnd(ShopUsersPeer::STATUS_ID, 5, Criteria::NOT_EQUAL);
        $cu->addAnd(UserPeer::STATUS_ID, 3);
        $this->user_list = ShopUsersPeer::doSelect($cu);


        $cs = new Criteria();
        $cs->add(PosShopRolePeer::SHOP_ID, $this->shops->getId());
        $cs->addAnd(PosShopRolePeer::STATUS_ID, 3, Criteria::EQUAL);
        $cs->addJoin(PosRolePeer::ID, PosShopRolePeer::POS_ROLE_ID, Criteria::LEFT_JOIN);
        $this->posRoles = PosRolePeer::doSelect($cs);
    }

    public function executeAdd(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Shops module id is 5.        
        if (Access::checkPermissions(5, "add", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
    }

    public function executeAddSubmit(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $shop = new Shops();
        $shop->setName($request->getParameter('name'));
        $shop->setBranchNumber($request->getParameter('branch_number'));
        $shop->setCompanyNumber($request->getParameter('company_number'));
        $shop->setPassword($request->getParameter('password'));
        $shop->setAddress($request->getParameter('address'));
        $shop->setZip($request->getParameter('zip'));
        $shop->setPlace($request->getParameter('place'));
        $shop->setCountry($request->getParameter('country'));
        $shop->setTel($request->getParameter('tel'));
        $shop->setFax($request->getParameter('fax'));
        $shop->setLanguageId($request->getParameter("languages"));
        $ng = $request->getParameter("negative_sale") == "on" ? "1" : "0";
        $receipt_auto_print = $request->getParameter("receipt_auto_print") == "on" ? "1" : "0";
        $shop->setReceiptAutoPrint($receipt_auto_print);
        $shop->setNegativeSale($ng);
        $shop->setDiscountTypeId($request->getParameter("discount_type_id"));
        $shop->setDiscountValue($request->getParameter("discount_value"));
        $shop->setStartValueSaleReceipt($request->getParameter("sale_receipt"));
        $shop->setStartValueReturnReceipt($request->getParameter("return_receipt"));
        $shop->setSaleReceiptFormatId($request->getParameter("saleFormat"));
        $shop->setReturnReceiptFormatId($request->getParameter("returnFormat"));
        $shop->setTimeOut($request->getParameter("time_out"));

        $shop->setReceiptHeaderPosition($request->getParameter("receipt_header_position"));
        $shop->setReceiptTaxStatmentOne($request->getParameter("receipt_tax_statement_one"));
        $shop->setReceiptTaxStatmentTwo($request->getParameter("receipt_tax_statement_two"));
        $shop->setReceiptTaxStatmentThree($request->getParameter("receipt_tax_statement_three"));

        $shop->setUpdatedBy($user_id);
        $shop->setCreatedBy($user_id);
        $shop->setMaxDayEndAttempts($request->getParameter("max_day_end_attempts"));
        $shop->save();
        ///////////////////Vat Setting ////////////////////
        $setting = SystemConfigPeer::retrieveByPK(6);
        $shop->setVatValue($setting->getValues());
        ///////////////////Currency Setting ////////////////////
        $settingCurrency = SystemConfigPeer::retrieveByPK(7);
        $shop->setCurrencyId($settingCurrency->getValues());
        ////////////////////////////////////////////////////////////////
        $this->getUser()->setFlash('message', 'Branch is added.');
        $this->redirect('shops/index');
    }

    public function executeEdit(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Shops module id is 5.        
        if (Access::checkPermissions(5, "edit", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $this->cancel_url = $request->getReferer();
        $this->ids = $request->getParameter('id');
        $this->shop = ShopsPeer::retrieveByPK($request->getParameter('id'));
    }

    public function executeEditSubmit(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $shop = ShopsPeer::retrieveByPK($request->getParameter('id'));
        $shop->setName($request->getParameter('name'));
        $shop->setBranchNumber($request->getParameter('branch_number'));
        $shop->setCompanyNumber($request->getParameter('company_number'));
        $shop->setPassword($request->getParameter('password'));
        $shop->setAddress($request->getParameter('address'));
        $shop->setZip($request->getParameter('zip'));
        $shop->setPlace($request->getParameter('place'));
        $shop->setCountry($request->getParameter('country'));
        $shop->setTel($request->getParameter('tel'));
        $shop->setFax($request->getParameter('fax'));
        $shop->setStatusId($request->getParameter('status_id'));
        $shop->setUpdatedBy($user_id);
        $shop->setNegativeSale($request->getParameter('negative_sale'));
        $receipt_auto_print = $request->getParameter("receipt_auto_print") == "on" ? "1" : "0";
        $shop->setReceiptAutoPrint($receipt_auto_print);
        $shop->setLanguageId($request->getParameter('languages'));
        $shop->setTimeOut($request->getParameter('time_out'));
        $shop->setStartValueSaleReceipt($request->getParameter('sale_receipt'));
        $shop->setStartValueReturnReceipt($request->getParameter('return_receipt'));
        $shop->setSaleReceiptFormatId($request->getParameter('saleFormat'));
        $shop->setReturnReceiptFormatId($request->getParameter('returnFormat'));
        $shop->setStartValueBookout($request->getParameter('start_value_bookout'));
        $shop->setBookoutFormatId($request->getParameter('bookout_format_id'));
        $shop->setMaxDayEndAttempts($request->getParameter("max_day_end_attempts"));
        $shop->setDiscountTypeId($request->getParameter("discount_type_id"));
        $shop->setDiscountValue($request->getParameter("discount_value"));
        $shop->setReceiptHeaderPosition($request->getParameter("receipt_header_position"));
        $shop->setReceiptTaxStatmentOne($request->getParameter("receipt_tax_statement_one"));
        $shop->setReceiptTaxStatmentTwo($request->getParameter("receipt_tax_statement_two"));
        $shop->setReceiptTaxStatmentThree($request->getParameter("receipt_tax_statement_three"));
        ///////////////////Vat Setting ////////////////////
        $setting = SystemConfigPeer::retrieveByPK(6);
        $shop->setVatValue($setting->getValues());
        ///////////////////Currency Setting ////////////////////
        $settingCurrency = SystemConfigPeer::retrieveByPK(7);
        $shop->setCurrencyId($settingCurrency->getValues());
        ////////////////////////////////////////////////////////////////
        if ($shop->save()) {
            new GcmLib("shop_updated", array($shop->getGcmKey()));
        }
        if ($request->getParameter('status_id') == 5) {
            $sus = new Criteria();
            $sus->addAnd(ShopUsersPeer::SHOP_ID, $shop->getId(), Criteria::EQUAL);
            $countUserShops = ShopUsersPeer::doCount($sus);
            if ($countUserShops > 0) {
                $shopUsers = ShopUsersPeer::doSelect($sus);
                foreach ($shopUsers as $shopUser) {
                    $shopUser->setStatusId(5);
                    $shopUser->save();

                    //// make use inactive 
                    $csu = new Criteria();
                    $csu->add(ShopUsersPeer::USER_ID, $shopUser->getUserId(), Criteria::EQUAL);
                    $csu->addAnd(ShopUsersPeer::STATUS_ID, 5, Criteria::NOT_EQUAL);
                    $usershop = ShopUsersPeer::doCount($csu);
                    if ($usershop == 0) {
                        $cu = new Criteria();
                        $cu->add(UserPeer::ID, $shopUser->getUserId());
                        $ucount = UserPeer::doCount($cu);
                        if ($ucount > 0) {
                            $user = UserPeer::doSelectOne($cu);
                            $user->setStatusId(5);
                            $user->save();
                        }
                    }
                }
            }
        }
        $this->getUser()->setFlash('message', 'Branch is updated.');
        $this->redirect('shops/view?id=' . $request->getParameter('id'));
    }

    public function executeValidateBranch($request) {
        $branch_number = $request->getParameter("branch_number");



        $cuc = new Criteria();
        $cuc->add(ShopsPeer::BRANCH_NUMBER, $branch_number);

        if (ShopsPeer::doCount($cuc) > 0) {

            echo "false";
        } else {

            echo "true";
        }
        return sfView::NONE;
    }

    public function executeValidateEditBranch($request) {
        $branch_number = $request->getParameter("branch_number");


        $id = $request->getParameter("id");

        $cuc = new Criteria();
        $cuc->add(ShopsPeer::BRANCH_NUMBER, $branch_number);
        $cuc->addAnd(ShopsPeer::ID, $id);
        if (ShopsPeer::doCount($cuc) > 0) {
            echo "true";
        } else {
            $cu = new Criteria();
            $cu->add(ShopsPeer::BRANCH_NUMBER, $branch_number);
            //  $cu->addAnd(ShopsPeer::STATUS_ID,3);
            if (ShopsPeer::doCount($cu) > 0) {
                echo "false";
            } else {
                echo "true";
            }
        }
        return sfView::NONE;
    }

    public function executeShopUserDelete($request) {
        $userid = $request->getParameter('userid');

        $sus = new Criteria();
        $sus->add(ShopUsersPeer::USER_ID, $userid, Criteria::EQUAL);
        $sus->addAnd(ShopUsersPeer::SHOP_ID, $request->getParameter('shopid'), Criteria::EQUAL);
        $sus->addAnd(ShopUsersPeer::STATUS_ID, 3, Criteria::EQUAL);
        $countUserShops = ShopUsersPeer::doCount($sus);
        if ($countUserShops > 0) {
//            die("Baran");
            $shopUser = ShopUsersPeer::doSelectOne($sus);
            $shopUser->setStatusId(5);
            if ($shopUser->save()) {
                $sc = new Criteria();
                $sc->add(ShopsPeer::ID, $request->getParameter('shopid'));
                if (ShopsPeer::doCount($sc) > 0) {
                    $shopObj = ShopsPeer::doSelectOne($sc);
                    if ($shopObj->getGcmKey() != "") {
                        new GcmLib("user_updated", array($shopObj->getGcmKey()));
                    }
                }
            }

            //// make use inactive 
//            $csu = new Criteria();
//            $csu->add(ShopUsersPeer::USER_ID, $userid, Criteria::EQUAL);
//            $csu->addAnd(ShopUsersPeer::STATUS_ID, 5, Criteria::NOT_EQUAL);
//            $usershop = ShopUsersPeer::doCount($csu);
//            if ($usershop == 0) {
//                $cu = new Criteria();
//                $cu->add(UserPeer::ID, $userid);
//                $user = UserPeer::doSelectOne($cu);
//                $user->setStatusId(5);
//                $user->save();
//            }
        }
        if ($request->getParameter('return') == 'user') {
            $this->getUser()->setFlash('message', 'Branch Unassigned Successfully.');
            $this->redirect('user/view?id=' . $request->getParameter('userid'));
        } else {
            $this->getUser()->setFlash('message', 'User Unassigned From Branch.');
            $this->redirect('shops/view?id=' . $request->getParameter('shopid'));
        }
        return sfView::NONE;
    }

    public function executeDelete($request) {

        $loggedin_user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $shop_id = $request->getParameter("id");
        $loggedin_user = UserPeer::retrieveByPK($loggedin_user_id);
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Shops module id is 5. 
        if (Access::checkPermissions(5, "delete", $loggedin_user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        $shop = ShopsPeer::retrieveByPK($shop_id);
        if ($shop->getStatusId() == 5) {
            $shop->setStatusId(3);
            $this->getUser()->setFlash('message', 'Branch activated successfully.');
            $shop->setUpdatedBy($loggedin_user_id);
            $shop->save();
        } else {
            $shop->setStatusId(5);
            $this->getUser()->setFlash('message', 'Branch deactivated successfully.');
            $shop->setUpdatedBy($loggedin_user_id);
            $shop->save();
            $sus = new Criteria();
            $sus->addAnd(ShopUsersPeer::SHOP_ID, $shop_id, Criteria::EQUAL);
            $countUserShops = ShopUsersPeer::doCount($sus);
            if ($countUserShops > 0) {
                $shopUsers = ShopUsersPeer::doSelect($sus);
                foreach ($shopUsers as $shopUser) {
                    $shopUser->setStatusId(5);
                    $shopUser->save();

                    //// make use inactive 
                    $csu = new Criteria();
                    $csu->add(ShopUsersPeer::USER_ID, $shopUser->getUserId(), Criteria::EQUAL);
                    $csu->addAnd(ShopUsersPeer::STATUS_ID, 5, Criteria::NOT_EQUAL);
                    $usershop = ShopUsersPeer::doCount($csu);
                    if ($usershop == 0) {
                        $cu = new Criteria();
                        $cu->add(UserPeer::ID, $shopUser->getUserId());
                        $ucount = UserPeer::doCount($cu);
                        if ($ucount > 0) {
                            $user = UserPeer::doSelectOne($cu);
                            $user->setStatusId(5);
                            $user->save();
                        }
                    }
                }
            }
        }





        $this->redirect('shops/index');
    }

    public function executeDeleteShop($request) {

        $loggedin_user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $shop_id = $request->getParameter("id");
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Shops module id is 5. 
        if (Access::checkPermissions(5, "delete", $loggedin_user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        $shop = ShopsPeer::retrieveByPK($shop_id);
        $shop->setStatusId(5);
        $shop->setUpdatedBy($loggedin_user_id);
        $shop->save();


        $sus = new Criteria();
        $sus->addAnd(ShopUsersPeer::SHOP_ID, $shop_id, Criteria::EQUAL);
        $countUserShops = ShopUsersPeer::doCount($sus);
        if ($countUserShops > 0) {
            $shopUsers = ShopUsersPeer::doSelect($sus);
            foreach ($shopUsers as $shopUser) {
                $shopUser->setStatusId(5);
                $shopUser->save();

                $csu = new Criteria();
                $csu->add(ShopUsersPeer::USER_ID, $shopUser->getUserId(), Criteria::EQUAL);
                $csu->addAnd(ShopUsersPeer::STATUS_ID, 5, Criteria::NOT_EQUAL);
                $usershop = ShopUsersPeer::doCount($csu);
                if ($usershop == 0) {
                    $cu = new Criteria();
                    $cu->add(UserPeer::ID, $shopUser->getUserId());
                    $user = UserPeer::doSelectOne($cu);
                    $user->delete();
                }
                $shopUser->delete();
            }
        }
        $shop->delete();
        $this->getUser()->setFlash('message', 'Branch deactivated successfully.');
        $this->redirect('shops/index');
    }

    public function executeAssignExistingUser(sfWebRequest $request) {

        $this->shop = ShopsPeer::retrieveByPK($request->getParameter('shop_id'));
        $cu = new Criteria();
//            $cu->add(UserPeer::ID,$current_user_id);
        $cu->addAnd(ShopUsersPeer::SHOP_ID, $request->getParameter('shop_id'));
        $cu->addAnd(ShopUsersPeer::STATUS_ID, 3);

        $existing_shop_users = '';

        $shopUsers = ShopUsersPeer::doSelect($cu);

        foreach ($shopUsers as $shopUser) {
            $existing_shop_users[] = $shopUser->getUserId();
        }


        $c = new Criteria();
        $c->add(UserPeer::ID, $existing_shop_users, Criteria::NOT_IN);
        $c->add(UserPeer::STATUS_ID, 3);
        $c->addAscendingOrderByColumn(UserPeer::NAME);
        $this->user_list = UserPeer::doSelect($c);


        $cr = new Criteria();
        $cr->addJoin(PosRolePeer::ID, PosShopRolePeer::POS_ROLE_ID, Criteria::LEFT_JOIN);
        $cr->add(PosShopRolePeer::SHOP_ID, $request->getParameter('shop_id'));

        if (PosRolePeer::doCount($cr) > 0) {
            $this->pos_roles_list = PosRolePeer::doSelect($cr);
        } else {
            $this->getUser()->setFlash('access_error', 'Please Assign roles to this branch first');
            $this->redirect('shops/view?id=' . $request->getParameter('shop_id'));
        }
    }

    public function executeAssignExistingUserCreate(sfWebRequest $request) {
        $shop_id = $request->getParameter('shop_id');
        $user_id = $request->getParameter('user_id');
        $role_id = $request->getParameter('role_id');
        $pos_super_user = ($request->getParameter('pos_super_user') == 'on') ? 1 : 0;
        $shopUser = new ShopUsers();
        $shopUser->setShopId($shop_id);
        $shopUser->setStatusId(3);
        $shopUser->setPosRoleId($role_id);
        $shopUser->setPosSuperUser($pos_super_user);
        $shopUser->setUserId($user_id);
        $shopUser->save();
        $sc = new Criteria();
        $sc->add(ShopsPeer::ID, $shop_id);
        if (ShopsPeer::doCount($sc) > 0) {
            $shopObj = ShopsPeer::doSelectOne($sc);
            if ($shopObj->getGcmKey() != "") {
                new GcmLib("user_updated", array($shopObj->getGcmKey()));
            }
        }
        $this->getUser()->setFlash('message', 'User is assigned successfuly.');
        $this->redirect('shops/view?id=' . $request->getParameter('shop_id'));
    }

    public function executeGetDailyCashLog(sfWebRequest $request) {
        $day_start_id = $request->getParameter('id');
        $c = new Criteria();
        $c->add(DayStartsPeer::ID, $day_start_id);
        $this->dayStart = DayStartsPeer::doSelectOne($c);
        $this->denominations = DenominationsPeer::doSelect(new Criteria());
        $this->setLayout(false);
    }

    public function executeGetDailyCashList(sfWebRequest $request) {
        $shop_id = $request->getParameter('shop_id');
        $day_start_date = $request->getParameter('date');
        $c = new Criteria();
        $c->add(DayStartsPeer::DAY_STARTED_AT, $day_start_date . " 00:00:00", Criteria::GREATER_EQUAL);
        $c->addAnd(DayStartsPeer::DAY_STARTED_AT, $day_start_date . " 23:59:59", Criteria::LESS_EQUAL);
        $c->addAnd(DayStartsPeer::SHOP_ID, $shop_id);
        $c->addDescendingOrderByColumn(DayStartsPeer::DAY_STARTED_AT);
        $this->dayStarts = DayStartsPeer::doSelect($c);
        $this->setLayout(false);
    }

}
