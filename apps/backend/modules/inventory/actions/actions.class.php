<?php

/**
 * inventory actions.
 *
 * @package    zapnacrm
 * @subpackage inventory
 * @author     Your name here
 */
class inventoryActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $this->inventory_list = InventoryPeer::doSelect(new Criteria());
    }

    public function executeInventorySoldDetail(sfWebRequest $request) {

        $this->item_id = $request->getParameter('id');
        $this->branch_number = $request->getParameter('branch_number');
        $this->close_url = $request->getReferer();
    }

    public function executeDeliveries(sfWebRequest $request) {

        $this->item_id = $request->getParameter('id');
        $this->branch_number = $request->getParameter('branch_number');
        $this->close_url = $request->getReferer();
    }

    public function executeNew(sfWebRequest $request) {
        $this->form = new InventoryForm();
    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post'));

        $this->form = new InventoryForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($inventory = InventoryPeer::retrieveByPk($request->getParameter('id')), sprintf('Object inventory does not exist (%s).', $request->getParameter('id')));
        $this->form = new InventoryForm($inventory);
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
        $this->forward404Unless($inventory = InventoryPeer::retrieveByPk($request->getParameter('id')), sprintf('Object inventory does not exist (%s).', $request->getParameter('id')));
        $this->form = new InventoryForm($inventory);

        $this->processForm($request, $this->form);

        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->forward404Unless($inventory = InventoryPeer::retrieveByPk($request->getParameter('id')), sprintf('Object inventory does not exist (%s).', $request->getParameter('id')));
        $inventory->delete();

        $this->redirect('inventory/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $inventory = $form->save();

            $this->redirect('inventory/edit?id=' . $inventory->getId());
        }
    }

}
