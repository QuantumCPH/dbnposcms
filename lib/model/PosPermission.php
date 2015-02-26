<?php

class PosPermission extends BasePosPermission
{
    public function __toString(){
            return $this->getActionName();
	}
    public function getIsChecked($permission_id,$role_id){
            $cap = new Criteria();
            $cap->add(PosRolePermissionRefPeer::POS_ROLE_ID,$role_id);
            $cap->addAnd(PosRolePermissionRefPeer::POS_PERMISSION_ID,$permission_id);
            $ap_count = PosRolePermissionRefPeer::doCount($cap); 
            if($ap_count > 0){
              return true;
            }else{
              return false;
            }            
        }
}
