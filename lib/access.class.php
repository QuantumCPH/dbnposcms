<?php

class Access {
    
    public static function getPermissions($role_id){
        $permissions = array();
        
        $cup = new Criteria();
        $cup->add(RolePermissionRefPeer::ROLE_ID,$role_id);
        $cup->addJoin(PermissionPeer::ID,  RolePermissionRefPeer::PERMISSION_ID, Criteria::LEFT_JOIN);        
        if(PermissionPeer::doCount($cup) > 0){
            $user_permissions  = PermissionPeer::doSelect($cup);
            foreach($user_permissions as $permission){
                $permissions[] = $permission->getModuleId()."_".$permission->getActionName();
            }
        }
//        $this->getUser()->setAttribute('user_permissions', $permissions, 'backendsession');
        return $permissions;
    }
    
    public static function updatePermissions($role_id,$logged_in_user){
//        $logged_in_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $user = UserPeer::retrieveByPK($logged_in_user);
        if(!$user->getIsSuperUser()){
            $permissions = array();        
            $cup = new Criteria();
            $cup->add(RolePermissionRefPeer::ROLE_ID,$role_id);
            $cup->addJoin(PermissionPeer::ID,  RolePermissionRefPeer::PERMISSION_ID, Criteria::LEFT_JOIN);        
            if(PermissionPeer::doCount($cup) > 0){
                $user_permissions  = PermissionPeer::doSelect($cup);
                foreach($user_permissions as $permission){
                    $permissions[] = $permission->getModuleId()."_".$permission->getActionName();
                }
            }
//            $this->getUser()->setAttribute('user_permissions', "", 'backendsession');
//            $this->getUser()->setAttribute('user_permissions', $permissions, 'backendsession');
            return $permissions;
        }
    }
    
    public static function checkPermissions($module_id,$action_name,$user_id,$session_role_id){
        
        $user = UserPeer::retrieveByPK($user_id);
        if(!$user->getIsSuperUser()){
            $role_id = $user->getRoleId();
//            $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
            if($role_id != $session_role_id) {
//                $this->getUser()->getAttributeHolder()->removeNamespace('backendsession');
//                $this->getUser()->setAuthenticated(false);
//                $this->getUser()->setFlash('role_changed', $this->getContext()->getI18N()->__('Role changed by admin.'));
//                $this->redirect(sfConfig::get("app_admin_url"));
            }

            $cup = new Criteria();
            $cup->add(RolePermissionRefPeer::ROLE_ID,$role_id);
            $cup->addJoin(PermissionPeer::ID,  RolePermissionRefPeer::PERMISSION_ID, Criteria::LEFT_JOIN);    
            $cup->addAnd(PermissionPeer::MODULE_ID, $module_id);
            $cup->addAnd(PermissionPeer::ACTION_NAME,$action_name);
            if(PermissionPeer::doCount($cup) > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    public static function getAdminPermissions(){
        $permissions = array();        
        $cup = new Criteria();       
        if(PermissionPeer::doCount($cup) > 0){
            $user_permissions  = PermissionPeer::doSelect($cup);
            foreach($user_permissions as $permission){
                $permissions[] = $permission->getModuleId()."_".$permission->getActionName();
            }
        }
//        $this->getUser()->setAttribute('user_permissions', $permissions, 'backendsession');
        return $permissions;
    }
}
?>
