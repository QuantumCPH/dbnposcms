<?php

/**
 * promotion actions.
 *
 * @package    zapnacrm
 * @subpackage promotion
 * @author     Your name here
 */
class promotionActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
        if (Access::checkPermissions(12, "index", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $this->promotion_list = PromotionPeer::doSelect(new Criteria());
    }

    public function executeNew(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
        if (Access::checkPermissions(12, "new", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $cit = new Criteria();
        $cit->add(ShopsPeer::STATUS_ID, 3);
        $cit->addAscendingOrderByColumn(ShopsPeer::BRANCH_NUMBER);
        $this->shops = ShopsPeer::doSelect($cit);
    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post'));
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $idss = "";
        if ($request->getParameter("on_all_branch") == 1) {
            $cit = new Criteria();

            $cit->add(ShopsPeer::STATUS_ID, 3);
            $cit->addAscendingOrderByColumn(ShopsPeer::BRANCH_NUMBER);
            $shops = ShopsPeer::doSelect($cit);

            foreach ($shops as $shop) {
                if (isset($idss) && $idss != "") {
                    $idss = $idss . "," . $shop->getId();
                } else {
                    $idss = $shop->getId();
                }
            }
        } else {

            foreach ($_REQUEST['branch_id'] as $selectedOption) {
                if (isset($idss) && $idss != "") {
                    $idss = $idss . "," . $selectedOption;
                } else {
                    $idss = $selectedOption;
                }
            }
        }
        $promo = new Promotion();
        //       $request->getParameter("dnumber")
        $promo->setPromotionTitle($request->getParameter("promotion_title"));
        $promo->setStartDate(date("Y-m-d H:i:s", strtotime($request->getParameter("start_date"))));
        $promo->setEndDate(date("Y-m-d H:i:s", strtotime($request->getParameter("end_date"))));
        $promo->setPromotionType($request->getParameter("promotion_type"));
        $promo->setPromotionValue($request->getParameter("promotion_value"));

        $promo->setOnAllItem($request->getParameter("on_all_item"));

        $promo->setItemIdType($request->getParameter("item_id_type"));
        if ($request->getParameter("item_id_type") == 1) {
            $promo->setItemId("");
            $promo->setItemIdTo("");
            $promo->setItemIdFrom("");
        } elseif ($request->getParameter("item_id_type") == 2) {
            $promo->setItemId($request->getParameter("item_id"));
            $promo->setItemIdTo("");
            $promo->setItemIdFrom("");
        } else {
            $promo->setItemId("");
            $promo->setItemIdTo($request->getParameter("item_id_to"));
            $promo->setItemIdFrom($request->getParameter("item_id_from"));
        }

        $promo->setDescription1($request->getParameter("description1"));
        $promo->setDescription2($request->getParameter("description2"));
        $promo->setDescription3($request->getParameter("description3"));
        $promo->setSize($request->getParameter("size"));
        $promo->setColor($request->getParameter("color"));
        $promo->setGroupType($request->getParameter("group_type"));
        if ($request->getParameter("group_type") == 1) {
            $promo->setGroupName("");
            $promo->setGroupTo("");
            $promo->setGroupFrom("");
        } elseif ($request->getParameter("group_type") == 2) {
            $promo->setGroupName($request->getParameter("group_name"));
            $promo->setGroupTo("");
            $promo->setGroupFrom("");
        } else {
            $promo->setGroupName("");
            $promo->setGroupTo($request->getParameter("group_to"));
            $promo->setGroupFrom($request->getParameter("group_from"));
        }
        $promo->setPriceType($request->getParameter("price_type"));
        if ($request->getParameter("price_type") == 1) {

            $promo->setPriceLess("");
            $promo->setPriceGreater("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
        } elseif ($request->getParameter("price_type") == 2) {
            $promo->setPriceGreater("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
            $promo->setPriceLess($request->getParameter("price_less"));
        } elseif ($request->getParameter("price_type") == 3) {
            $promo->setPriceLess("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
            $promo->setPriceGreater($request->getParameter("price_greater"));
        } else {
            $promo->setPriceLess("");
            $promo->setPriceGreater("");
            $promo->setPriceTo($request->getParameter("price_to"));
            $promo->setPriceFrom($request->getParameter("price_from"));
        }

        $promo->setSupplierNumber($request->getParameter("supplier_number"));
        $promo->setSupplierItemNumber($request->getParameter("supplier_item_number"));
        $promo->setOnAllBranch($request->getParameter("on_all_branch"));
        $promo->setBranchId($idss);
        $promo->setCreatedAt(date("Y-m-d H:i:s"));
        $promo->setUpdatedBy($user_id);
        $promo->setPromotionStatus(3);
        if ($promo->save()) {

            $branchIds = explode(",", $promo->getBranchId());
            foreach ($branchIds as $branchId) {
                $shop = ShopsPeer::retrieveByPK($branchId);
                if ($shop->getGcmKey() != "") {
                    new GcmLib("promotion", array($shop->getGcmKey()),$shop);
                }
            }
        }
/////////////////////////////////promotion Log section /////////////////////////////////////
        $promolog = new PromotionLog();
        $promolog->setPromotionId($promo->getId());
        $promolog->setPromotionTitle($request->getParameter("promotion_title"));
        $promolog->setStartDate(date("Y-m-d H:i:s", strtotime($request->getParameter("start_date"))));
        $promolog->setEndDate(date("Y-m-d H:i:s", strtotime($request->getParameter("end_date"))));
        $promolog->setPromotionType($request->getParameter("promotion_type"));
        $promolog->setPromotionValue($request->getParameter("promotion_value"));

        $promolog->setOnAllItem($request->getParameter("on_all_item"));

        $promo->setItemIdType($request->getParameter("item_id_type"));
        if ($request->getParameter("item_id_type") == 1) {
            $promo->setItemId("");
            $promo->setItemIdTo("");
            $promo->setItemIdFrom("");
        } elseif ($request->getParameter("item_id_type") == 2) {
            $promo->setItemId($request->getParameter("item_id"));
            $promo->setItemIdTo("");
            $promo->setItemIdFrom("");
        } else {
            $promo->setItemId("");
            $promo->setItemIdTo($request->getParameter("item_id_to"));
            $promo->setItemIdFrom($request->getParameter("item_id_from"));
        }

        $promo->setDescription1($request->getParameter("description1"));
        $promo->setDescription2($request->getParameter("description2"));
        $promo->setDescription3($request->getParameter("description3"));
        $promo->setSize($request->getParameter("size"));
        $promo->setColor($request->getParameter("color"));
        $promo->setGroupType($request->getParameter("group_type"));
        if ($request->getParameter("group_type") == 1) {
            $promo->setGroupName("");
            $promo->setGroupTo("");
            $promo->setGroupFrom("");
        } elseif ($request->getParameter("group_type") == 2) {
            $promo->setGroupName($request->getParameter("group_name"));
            $promo->setGroupTo("");
            $promo->setGroupFrom("");
        } else {
            $promo->setGroupName("");
            $promo->setGroupTo($request->getParameter("group_to"));
            $promo->setGroupFrom($request->getParameter("group_from"));
        }
        $promo->setPriceType($request->getParameter("price_type"));
        if ($request->getParameter("price_type") == 1) {

            $promo->setPriceLess("");
            $promo->setPriceGreater("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
        } elseif ($request->getParameter("price_type") == 2) {
            $promo->setPriceGreater("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
            $promo->setPriceLess($request->getParameter("price_less"));
        } elseif ($request->getParameter("price_type") == 3) {
            $promo->setPriceLess("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
            $promo->setPriceGreater($request->getParameter("price_greater"));
        } else {
            $promo->setPriceLess("");
            $promo->setPriceGreater("");
            $promo->setPriceTo($request->getParameter("price_to"));
            $promo->setPriceFrom($request->getParameter("price_from"));
        }


        $promolog->setSupplierNumber($request->getParameter("supplier_number"));
        $promolog->setSupplierItemNumber($request->getParameter("supplier_item_number"));
        $promolog->setOnAllBranch($request->getParameter("on_all_branch"));
        $promolog->setBranchId($idss);
        $promolog->setCreatedAt(date("Y-m-d H:i:s"));
        $promolog->setUpdatedBy($user_id);
        $promolog->setPromotionStatus(3);
        $promolog->save();

        ///////////////////////////////////////////////////////////////////////////////////////////////////   
        $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Promotion added successfully.'));

        $this->redirect('promotion/index');
    }

    public function executeEdit(sfWebRequest $request) {

        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');

        if (Access::checkPermissions(12, "edit", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $this->forward404Unless($promotion = PromotionPeer::retrieveByPk($request->getParameter('id')), sprintf('Object promotion does not exist (%s).', $request->getParameter('id')));
        $this->promotion = $promotion;
        $cit = new Criteria();
        $cit->add(ShopsPeer::STATUS_ID, 3);
        $cit->addAscendingOrderByColumn(ShopsPeer::BRANCH_NUMBER);
        $this->shops = ShopsPeer::doSelect($cit);
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
        $this->forward404Unless($promotion = PromotionPeer::retrieveByPk($request->getParameter('id')), sprintf('Object promotion does not exist (%s).', $request->getParameter('id')));
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');

        $promo = $promotion;
 if ($promo->getOnAllBranch() == 1) {
            $cit = new Criteria();

        //    $cit->add(ShopsPeer::STATUS_ID, 3);
            $cit->addAscendingOrderByColumn(ShopsPeer::BRANCH_NUMBER);
            $shops = ShopsPeer::doSelect($cit);

            foreach ($shops as $shop) {
                if (isset($idss) && $idss != "") {
                    $idss = $idss . "," . $shop->getId();
                } else {
                    $idss = $shop->getId();
                }
            }
        } else {

            
                
                    $idss = $promo->getBranchId();
                 
            
        }
        
        $promo->setPromotionTitle($request->getParameter("promotion_title"));
        $promo->setStartDate(date("Y-m-d H:i:s", strtotime($request->getParameter("start_date"))));
        $promo->setEndDate(date("Y-m-d H:i:s", strtotime($request->getParameter("end_date"))));
        $promo->setPromotionType($request->getParameter("promotion_type"));
        $promo->setPromotionValue($request->getParameter("promotion_value"));

        $promo->setOnAllItem($request->getParameter("on_all_item"));

        $promo->setItemIdType($request->getParameter("item_id_type"));
        if ($request->getParameter("item_id_type") == 1) {
            $promo->setItemId("");
            $promo->setItemIdTo("");
            $promo->setItemIdFrom("");
        } elseif ($request->getParameter("item_id_type") == 2) {
            $promo->setItemId($request->getParameter("item_id"));
            $promo->setItemIdTo("");
            $promo->setItemIdFrom("");
        } else {
            $promo->setItemId("");
            $promo->setItemIdTo($request->getParameter("item_id_to"));
            $promo->setItemIdFrom($request->getParameter("item_id_from"));
        }

        $promo->setDescription1($request->getParameter("description1"));
        $promo->setDescription2($request->getParameter("description2"));
        $promo->setDescription3($request->getParameter("description3"));
        $promo->setSize($request->getParameter("size"));
        $promo->setColor($request->getParameter("color"));
        $promo->setGroupType($request->getParameter("group_type"));
        if ($request->getParameter("group_type") == 1) {
            $promo->setGroupName("");
            $promo->setGroupTo("");
            $promo->setGroupFrom("");
        } elseif ($request->getParameter("group_type") == 2) {
            $promo->setGroupName($request->getParameter("group_name"));
            $promo->setGroupTo("");
            $promo->setGroupFrom("");
        } else {
            $promo->setGroupName("");
            $promo->setGroupTo($request->getParameter("group_to"));
            $promo->setGroupFrom($request->getParameter("group_from"));
        }
        $promo->setPriceType($request->getParameter("price_type"));
        if ($request->getParameter("price_type") == 1) {

            $promo->setPriceLess("");
            $promo->setPriceGreater("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
        } elseif ($request->getParameter("price_type") == 2) {
            $promo->setPriceGreater("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
            $promo->setPriceLess($request->getParameter("price_less"));
        } elseif ($request->getParameter("price_type") == 3) {
            $promo->setPriceLess("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
            $promo->setPriceGreater($request->getParameter("price_greater"));
        } else {
            $promo->setPriceLess("");
            $promo->setPriceGreater("");
            $promo->setPriceTo($request->getParameter("price_to"));
            $promo->setPriceFrom($request->getParameter("price_from"));
        }

        $promo->setSupplierNumber($request->getParameter("supplier_number"));
        $promo->setSupplierItemNumber($request->getParameter("supplier_item_number"));

 $promo->setBranchId($idss);
        $promo->setUpdatedBy($user_id);
        $promo->setPromotionStatus($request->getParameter("promotion_status"));
        if ($promo->save()) {

            $branchIds = explode(",", $promo->getBranchId());
            foreach ($branchIds as $branchId) {
                $shop = ShopsPeer::retrieveByPK($branchId);
                if ($shop->getGcmKey() != "") {
                    new GcmLib("promotion", array($shop->getGcmKey()),$shop);
                }
            }
        }
/////////////////////////////////promotion Log section /////////////////////////////////////
        $promolog = new PromotionLog();
        $promolog->setPromotionId($promo->getId());
        $promolog->setPromotionTitle($request->getParameter("promotion_title"));
        $promolog->setStartDate(date("Y-m-d H:i:s", strtotime($request->getParameter("start_date"))));
        $promolog->setEndDate(date("Y-m-d H:i:s", strtotime($request->getParameter("end_date"))));
        $promolog->setPromotionType($request->getParameter("promotion_type"));
        $promolog->setPromotionValue($request->getParameter("promotion_value"));

        $promolog->setOnAllItem($request->getParameter("on_all_item"));

                $promo->setItemIdType($request->getParameter("item_id_type"));
        if ($request->getParameter("item_id_type") == 1) {
            $promo->setItemId("");
            $promo->setItemIdTo("");
            $promo->setItemIdFrom("");
        } elseif ($request->getParameter("item_id_type") == 2) {
            $promo->setItemId($request->getParameter("item_id"));
            $promo->setItemIdTo("");
            $promo->setItemIdFrom("");
        } else {
            $promo->setItemId("");
            $promo->setItemIdTo($request->getParameter("item_id_to"));
            $promo->setItemIdFrom($request->getParameter("item_id_from"));
        }

        $promo->setDescription1($request->getParameter("description1"));
        $promo->setDescription2($request->getParameter("description2"));
        $promo->setDescription3($request->getParameter("description3"));
        $promo->setSize($request->getParameter("size"));
        $promo->setColor($request->getParameter("color"));
        $promo->setGroupType($request->getParameter("group_type"));
        if ($request->getParameter("group_type") == 1) {
            $promo->setGroupName("");
            $promo->setGroupTo("");
            $promo->setGroupFrom("");
        } elseif ($request->getParameter("group_type") == 2) {
            $promo->setGroupName($request->getParameter("group_name"));
            $promo->setGroupTo("");
            $promo->setGroupFrom("");
        } else {
            $promo->setGroupName("");
            $promo->setGroupTo($request->getParameter("group_to"));
            $promo->setGroupFrom($request->getParameter("group_from"));
        }
        $promo->setPriceType($request->getParameter("price_type"));
        if ($request->getParameter("price_type") == 1) {

            $promo->setPriceLess("");
            $promo->setPriceGreater("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
        } elseif ($request->getParameter("price_type") == 2) {
            $promo->setPriceGreater("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
            $promo->setPriceLess($request->getParameter("price_less"));
        } elseif ($request->getParameter("price_type") == 3) {
            $promo->setPriceLess("");
            $promo->setPriceTo("");
            $promo->setPriceFrom("");
            $promo->setPriceGreater($request->getParameter("price_greater"));
        } else {
            $promo->setPriceLess("");
            $promo->setPriceGreater("");
            $promo->setPriceTo($request->getParameter("price_to"));
            $promo->setPriceFrom($request->getParameter("price_from"));
        }


        $promolog->setSupplierNumber($request->getParameter("supplier_number"));
        $promolog->setSupplierItemNumber($request->getParameter("supplier_item_number"));
        $promolog->setOnAllBranch($promo->getOnAllBranch());
        $promolog->setBranchId($promo->getBranchId());
        $promolog->setCreatedAt(date("Y-m-d H:i:s"));
        $promolog->setUpdatedBy($user_id);
        $promolog->setPromotionStatus($request->getParameter("promotion_status"));
        $promolog->save();

        ///////////////////////////////////////////////////////////////////////////////////////////////////   

        $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Promotion updated successfully.'));

        $this->redirect('promotion/index');
    }

    public function executeView(sfWebRequest $request) {

        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');

//        if (Access::checkPermissions(12, "view", $user_id, $session_role_id) == false) {
//            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
//            $this->redirect($request->getReferer());
//        }
        $this->forward404Unless($promotion = PromotionPeer::retrieveByPk($request->getParameter('id')), sprintf('Object promotion does not exist (%s).', $request->getParameter('id')));
        $this->promotion = $promotion;
        $cit = new Criteria();
        $cit->add(ShopsPeer::STATUS_ID, 3);
        $cit->addAscendingOrderByColumn(ShopsPeer::BRANCH_NUMBER);
        $this->shops = ShopsPeer::doSelect($cit);
    }

    public function executeViewLog(sfWebRequest $request) {

        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');

//        if (Access::checkPermissions(12, "viewLog", $user_id, $session_role_id) == false) {
//            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
//            $this->redirect($request->getReferer());
//        }
        $this->forward404Unless($promotion = PromotionLogPeer::retrieveByPk($request->getParameter('id')), sprintf('Object promotion does not exist (%s).', $request->getParameter('id')));
        $this->promotion = $promotion;
        $cit = new Criteria();
        $cit->add(ShopsPeer::STATUS_ID, 3);
        $cit->addAscendingOrderByColumn(ShopsPeer::BRANCH_NUMBER);
        $this->shops = ShopsPeer::doSelect($cit);
    }

}
