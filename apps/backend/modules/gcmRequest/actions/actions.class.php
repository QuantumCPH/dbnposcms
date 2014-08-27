<?php

/**
 * gcmRequest actions.
 *
 * @package    zapnacrm
 * @subpackage gcmRequest
 * @author     Your name here
 */
class gcmRequestActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
       $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Shops module id is 5.        
        if (Access::checkPermissions(13, "index", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
    $this->gcm_request_list = GcmRequestPeer::doSelect(new Criteria());
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new GcmRequestForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $this->form = new GcmRequestForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($gcm_request = GcmRequestPeer::retrieveByPk($request->getParameter('id')), sprintf('Object gcm_request does not exist (%s).', $request->getParameter('id')));
    $this->form = new GcmRequestForm($gcm_request);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $this->forward404Unless($gcm_request = GcmRequestPeer::retrieveByPk($request->getParameter('id')), sprintf('Object gcm_request does not exist (%s).', $request->getParameter('id')));
    $this->form = new GcmRequestForm($gcm_request);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($gcm_request = GcmRequestPeer::retrieveByPk($request->getParameter('id')), sprintf('Object gcm_request does not exist (%s).', $request->getParameter('id')));
    $gcm_request->delete();

    $this->redirect('gcmRequest/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $gcm_request = $form->save();

      $this->redirect('gcmRequest/edit?id='.$gcm_request->getId());
    }
  }
}
