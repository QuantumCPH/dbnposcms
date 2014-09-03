<?php

/**
 * voucher actions.
 *
 * @package    zapnacrm
 * @subpackage voucher
 * @author     Your name here
 */
class voucherActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $this->voucher_list = VoucherPeer::doSelect(new Criteria());
    }

    public function executeNew(sfWebRequest $request) {
        $this->form = new VoucherForm();
    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post'));

        $this->form = new VoucherForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($voucher = VoucherPeer::retrieveByPk($request->getParameter('id')), sprintf('Object voucher does not exist (%s).', $request->getParameter('id')));
        $this->form = new VoucherForm($voucher);
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
        $this->forward404Unless($voucher = VoucherPeer::retrieveByPk($request->getParameter('id')), sprintf('Object voucher does not exist (%s).', $request->getParameter('id')));
        $this->form = new VoucherForm($voucher);

        $this->processForm($request, $this->form);

        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->forward404Unless($voucher = VoucherPeer::retrieveByPk($request->getParameter('id')), sprintf('Object voucher does not exist (%s).', $request->getParameter('id')));
        $voucher->delete();

        $this->redirect('voucher/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $voucher = $form->save();

            $this->redirect('voucher/edit?id=' . $voucher->getId());
        }
    }

   
    public function executeView(sfWebRequest $request) {
       $id = $request->getParameter("id");

        $c = new Criteria();
//      $c->add(VoucherPeer::id, "${username}%", Criteria::LIKE);
        $c->add(VoucherPeer::ID, $id);
        if (VoucherPeer::doCount($c) == 0) {
            $this->getUser()->setFlash('access_error', "Voucher does not exisits");
            $this->redirect('voucher/index');
        }

        $this->parent = VoucherPeer::doSelectOne($c);

       $parentId= $this->parent->getParentId();
        $parentIds = explode(",", $parentId);
        $vc = new Criteria();
//         $vc->add(VoucherPeer::PARENT_ID, "%" . $this->parent->getParentId(), Criteria::LIKE);
//          $vc->addOr(VoucherPeer::ID,$parentIds , Criteria::IN);
        $criterion = $c->getNewCriterion(VoucherPeer::PARENT_ID, "%" . $this->parent->getParentId(), Criteria::LIKE);
$criterion->addOr($c->getNewCriterion(VoucherPeer::ID,$parentIds , Criteria::IN));
$vc->add($criterion);
        
     //   $vc->add(VoucherPeer::PARENT_ID, "%" . $this->parent->getParentId(), Criteria::LIKE);
        $vc->addDescendingOrderByColumn (VoucherPeer::SHOP_CREATED_AT);
        
        
        
        
        
        if (VoucherPeer::doCount($vc) == 0) {
            $this->getUser()->setFlash('access_error', "Voucher doesn't have child");
            $this->redirect('voucher/index');
        }

        
        
        
        
        
        $this->vouchers = VoucherPeer::doSelect($vc);
    }

}
