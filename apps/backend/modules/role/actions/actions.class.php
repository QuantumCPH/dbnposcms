<?php

/**
 * role actions.
 *
 * @package    zapnacrm
 * @subpackage role
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 5125 2007-09-16 00:53:55Z dwhittle $
 */

class roleActions extends sfActions
{
    public function executeIndex($request){
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Role module id is 3.        
        if(Access::checkPermissions(3,"index",$user_id,$session_role_id)==false){
           $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
           $this->redirect($request->getReferer());
        }
        
        $user_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $cr = new Criteria();
        if($user_role!=1){ ///admin role
            $cr->add(RolePeer::ID,1, Criteria::NOT_EQUAL);
        }
        $this->role_list = RolePeer::doSelect($cr);        
    }
    
    public function executeEditRole($request){
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Role module id is 3.        
        if(Access::checkPermissions(3,"editRole",$user_id,$session_role_id)==false){
           $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
           $this->redirect($request->getReferer());
        }
        
        $role_id = $request->getParameter("id");
        $role = RolePeer::retrieveByPK($role_id);
        $this->role = $role;
        
        $cm = new Criteria();
        $cm->add(ModulesPeer::ID,3, Criteria::NOT_EQUAL);
        $this->modules = ModulesPeer::doSelect($cm);
        
    }
   public function executeEditProcess($request){
       $permission_ids = $request->getParameter("permissions");
       $role_id = $request->getParameter("role_id");
       $same_permission = array();
       $cr = new Criteria();
       $cr->add(RolePermissionRefPeer::ROLE_ID,$role_id);
       $current_count = RolePermissionRefPeer::doCount($cr);
       if($current_count > 0){
           $current_permissions = RolePermissionRefPeer::doSelect($cr);
           foreach($current_permissions as $current_permission){
               $curr_perm_id =  $current_permission->getPermissionId();
               if(!in_array($curr_perm_id,$permission_ids)){
                 $current_permission->delete();
               }else{
                 $same_permission[] = $curr_perm_id;
               }
           }
            
       }
       for($i=0; $i<count($permission_ids); $i++){
         if(!in_array($permission_ids[$i],$same_permission)){  
           $rp = new RolePermissionRef();
           $rp->setRoleId($role_id);
           $rp->setPermissionId($permission_ids[$i]);
           $rp->save();
         }
       }
       $this->getUser()->setFlash('role_message', $this->getContext()->getI18N()->__('Permission Updated.'));
       
        $current_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $logged_in_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
       $permissions = Access::updatePermissions($current_role,$logged_in_id);
       $user = UserPeer::retrieveByPK($logged_in_id);
       if(!$user->getIsSuperUser()){
          $this->getUser()->setAttribute('user_permissions', "", 'backendsession');
          $this->getUser()->setAttribute('user_permissions', $permissions, 'backendsession'); 
       }
       
       $this->redirect($this->targetUrl().'role/editRole/id/'.$role_id);
       
   }
   
   public function targetUrl(){
       return sfConfig::get("app_admin_url");
   }
   
   public function executeCreateRole($request){
       $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
       $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
       //// Role module id is 3.
       if(Access::checkPermissions(3,"createRole",$user_id,$session_role_id)==false){
           $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
           $this->redirect($request->getReferer());
        }
        
       $cm = new Criteria();
       $cm->add(ModulesPeer::ID,3, Criteria::NOT_EQUAL);
       $this->modules = ModulesPeer::doSelect($cm);
       
       if ($request->isMethod('post')) {
           $permission_ids = $request->getParameter("permissions");
           $roleName = $request->getParameter("roleName");
           if($roleName != ""){
               $role = new Role();
               $role->setName($roleName);
               $role->save();
               
               for($i=0; $i<count($permission_ids); $i++){
                    $rp = new RolePermissionRef();
                    $rp->setRoleId($role->getId());
                    $rp->setPermissionId($permission_ids[$i]);
                    $rp->save();
                    
                }
                $this->getUser()->setFlash('role_message', $this->getContext()->getI18N()->__('Role Created.'));
                $this->redirect($this->targetUrl().'role/editRole/id/'.$role->getId());
           }
       } 
   }
   
     public function executeAddRole($request){
       $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
       $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
       //// Role module id is 3.
       if(Access::checkPermissions(3,"createRole",$user_id,$session_role_id)==false){
           $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
           $this->redirect($request->getReferer());
        }
        
       $cm = new Criteria();
       $cm->add(ModulesPeer::ID,3, Criteria::NOT_EQUAL);
       $this->modules = ModulesPeer::doSelect($cm);
       
       if ($request->isMethod('post')) {
           
           
           $permission_ids = $request->getParameter("permissions");
           $roleName = $request->getParameter("roleName");
           if($roleName != ""){
               $role = new Role();
               $role->setName($roleName);
               $role->save();
               
               for($i=0; $i<count($permission_ids); $i++){
                    $rp = new RolePermissionRef();
                    $rp->setRoleId($role->getId());
                    $rp->setPermissionId($permission_ids[$i]);
                    $rp->save();
                    
                }
                $this->getUser()->setFlash('role_message', $this->getContext()->getI18N()->__('Role Created.'));
                $this->redirect($this->targetUrl().'role/editRole/id/'.$role->getId());
           }
       } 
   }
   
     public function executeUpdateRole($request){
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Role module id is 3.        
        if(Access::checkPermissions(3,"editRole",$user_id,$session_role_id)==false){
           $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
           $this->redirect($request->getReferer());
        }
        
        $role_id = $request->getParameter("id");
        $role = RolePeer::retrieveByPK($role_id);
        $this->role = $role;
        
        $cm = new Criteria();
        $cm->add(ModulesPeer::ID,3, Criteria::NOT_EQUAL);
        $this->modules = ModulesPeer::doSelect($cm);
        
    }
}
