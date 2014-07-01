<?php

/**
 * system_config actions.
 *
 * @package    zapnacrm
 * @subpackage system_config
 * @author     Your name here
 */
class system_configActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->system_config_list = SystemConfigPeer::doSelect(new Criteria());
  }

  public function executeAdd(sfWebRequest $request)
  {
    $loggedin_user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
    $user = UserPeer::retrieveByPK($loggedin_user_id);      
    if($user->getIsSuperUser()==false){
        $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
        $this->redirect($request->getReferer());
    }  
    $this->form = new SystemConfigForm();
  }

  public function executeAddNew(sfWebRequest $request){
      if ($request->isMethod('post')) { 
        $key = $request->getParameter("keys");
        $value = $request->getParameter("values");
        $setting = new SystemConfig();
        $setting->setKeys($key);
        $setting->setValues($value);
        $setting->save();
        $this->getUser()->setFlash('message', 'Settings added');         
      }
      $this->redirect('system_config/edit?id='.$setting->getId());
  }
  
  public function executeEdit(sfWebRequest $request){
      $loggedin_user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $user = UserPeer::retrieveByPK($loggedin_user_id);      
        if($user->getIsSuperUser()==false){
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
      $setting_id = $request->getParameter("id");
      $this->setting = SystemConfigPeer::retrieveByPK($setting_id);        
      $setting = $this->setting;
      
  }
  
  public function executeUpdate(sfWebRequest $request){
      if ($request->isMethod('post')) { 
        $id = $request->getParameter("id");  
        $key = $request->getParameter("keys");
        $value = $request->getParameter("values");
        $setting = SystemConfigPeer::retrieveByPK($id);
        $setting->setKeys($key);
        $setting->setValues($value);
        $setting->save();
        $this->getUser()->setFlash('message', 'Settings updated');         
      }
      $this->redirect('system_config/edit?id='.$setting->getId());
  }
  
  public function executeValidateKeys($request){
      $keys = $request->getParameter("keys");
       $cuc = new Criteria();
       $cuc->add(SystemConfigPeer::KEYS,$keys);      
       if(SystemConfigPeer::doCount($cuc)>0){
           echo "false";
       }else{
            echo "true";
       }
       return sfView::NONE;
   }
}
