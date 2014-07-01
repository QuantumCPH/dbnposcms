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
        $this->promotion_list = PromotionPeer::doSelect(new Criteria());
    }

    public function executeNew(sfWebRequest $request) {
        $this->form = new PromotionForm();
    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post'));

        $promo = new Promotion();
 //       $request->getParameter("dnumber")
        $promo->setPromotionTitle($request->getParameter("promotion_title"));
        $promo->setStartDate(date("Y-m-d H:i:s", strtotime($request->getParameter("start_date"))));
        $promo->setEndDate(date("Y-m-d H:i:s", strtotime($request->getParameter("end_date"))));
        $promo->setPromotionType($request->getParameter("promotion_type"));
        $promo->setPromotionValue($request->getParameter("promotion_value"));
        if($request->getParameter("on_all_item")){
        $promo->setOnAllItem($request->getParameter("on_all_item"));
        }
        $promo->setItemIdType($request->getParameter("item_id_type"));
        $promo->setItemId($request->getParameter("item_id"));
        $promo->setItemIdTo($request->getParameter("item_id_to"));
        $promo->setItemIdFrom($request->getParameter("item_id_from"));
        $promo->setDescription1($request->getParameter("description1"));
        $promo->setDescription2($request->getParameter("description2"));
        $promo->setDescription3($request->getParameter("description3"));
        $promo->setSize($request->getParameter("size"));
         $promo->setColor($request->getParameter("color"));
        $promo->setGroupType($request->getParameter("group_type"));
        $promo->setGroupName($request->getParameter("group_name"));
        $promo->setGroupTo($request->getParameter("group_to"));
        $promo->setGroupFrom($request->getParameter("group_from"));
        $promo->setPriceType($request->getParameter("price_type"));
        $promo->setPriceLess($request->getParameter("price_less"));
        $promo->setPriceGreater($request->getParameter("price_greater"));
        $promo->setPriceTo($request->getParameter("price_to"));
          $promo->setPriceFrom($request->getParameter("price_from"));
        $promo->setSupplierNumber($request->getParameter("supplier_number"));
        $promo->setSupplierItemNumber($request->getParameter("supplier_item_number"));
        $promo->setOnAllBranch($request->getParameter("on_all_branch"));
        
        $promo->setBranchId($request->getParameter("branch_id"));
        $promo->save();


        $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Promotion added successfully.'));

        $this->redirect('promotion/index');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($promotion = PromotionPeer::retrieveByPk($request->getParameter('id')), sprintf('Object promotion does not exist (%s).', $request->getParameter('id')));
        $this->form = new PromotionForm($promotion);
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
        $this->forward404Unless($promotion = PromotionPeer::retrieveByPk($request->getParameter('id')), sprintf('Object promotion does not exist (%s).', $request->getParameter('id')));
        $this->form = new PromotionForm($promotion);

        $this->processForm($request, $this->form);

        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->forward404Unless($promotion = PromotionPeer::retrieveByPk($request->getParameter('id')), sprintf('Object promotion does not exist (%s).', $request->getParameter('id')));
        $promotion->delete();

        $this->redirect('promotion/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $promotion = $form->save();

            $this->redirect('promotion/edit?id=' . $promotion->getId());
        }
    }

}
