<?php

/**
 * saleReports actions.
 *
 * @package    zapnacrm
 * @subpackage saleReports
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class saleReportsActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->sale_reports_list = SaleReportsPeer::doSelect(new Criteria());
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new SaleReportsForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $this->form = new SaleReportsForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($sale_reports = SaleReportsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object sale_reports does not exist (%s).', $request->getParameter('id')));
    $this->form = new SaleReportsForm($sale_reports);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $this->forward404Unless($sale_reports = SaleReportsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object sale_reports does not exist (%s).', $request->getParameter('id')));
    $this->form = new SaleReportsForm($sale_reports);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($sale_reports = SaleReportsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object sale_reports does not exist (%s).', $request->getParameter('id')));
    $sale_reports->delete();

    $this->redirect('saleReports/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $sale_reports = $form->save();

      $this->redirect('saleReports/edit?id='.$sale_reports->getId());
    }
  }
}
