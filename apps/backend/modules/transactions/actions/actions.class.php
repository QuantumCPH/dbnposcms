<?php

/**
 * transactions actions.
 *
 * @package    zapnacrm
 * @subpackage transactions
 * @author     Your name here
 */
class transactionsActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->transactions_list = TransactionsPeer::doSelect(new Criteria());
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new TransactionsForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $this->form = new TransactionsForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($transactions = TransactionsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object transactions does not exist (%s).', $request->getParameter('id')));
    $this->form = new TransactionsForm($transactions);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $this->forward404Unless($transactions = TransactionsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object transactions does not exist (%s).', $request->getParameter('id')));
    $this->form = new TransactionsForm($transactions);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($transactions = TransactionsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object transactions does not exist (%s).', $request->getParameter('id')));
    $transactions->delete();

    $this->redirect('transactions/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $transactions = $form->save();

      $this->redirect('transactions/edit?id='.$transactions->getId());
    }
  }
  public function executeOrder(sfWebRequest $request)
  {
      $t=new Criteria();
      
       $t->add(OrdersPeer::STATUS_ID,3);
       $orderconts = OrdersPeer::doCount($t);
       $this->orderconts=$orderconts;
  }
 public function executeReports(sfWebRequest $request)
  {
      
  } 
   public function executeProductNameReport(sfWebRequest $request)
  {
      
  } 
    public function executeProductNameReportData(sfWebRequest $request)
  {
      
  }
   public function executePaymentMethod(sfWebRequest $request)
  {
      
  } 
   public function executeTaxRate(sfWebRequest $request)
  {
      
  } 
    public function executeStaffSale(sfWebRequest $request)
  {
      
  } 
   public function executeStaffDailySale(sfWebRequest $request)
  {
      
  } 
  public function executeMonthlySale(sfWebRequest $request)
  {
      
  } 
}
