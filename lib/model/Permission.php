<?php

class Permission extends BasePermission
{
	public function __toString(){
//		return $this->getModuleName().'::'.$this->getActionName();
            return $this->getActionName();
	}
        
        public function getIsChecked($permission_id,$role_id){
            $cap = new Criteria();
            $cap->add(RolePermissionRefPeer::ROLE_ID,$role_id);
            $cap->addAnd(RolePermissionRefPeer::PERMISSION_ID,$permission_id);
            $ap_count = RolePermissionRefPeer::doCount($cap); 
            if($ap_count > 0){
              return true;
            }else{
              return false;
            }            
        }
}
