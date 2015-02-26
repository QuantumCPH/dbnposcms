<?php

/**
 * role actions.
 *
 * @package    zapnacrm
 * @subpackage role
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 5125 2007-09-16 00:53:55Z dwhittle $
 */
class pos_roleActions extends sfActions {

    public function executeIndex($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Pos Role module id is 3.        
        if (Access::checkPermissions(3, "index", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        $user_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $cr = new Criteria();
        if ($user_role != 1) { ///admin role
            $cr->add(PosRolePeer::ID, 1, Criteria::NOT_EQUAL);
        }
        $this->role_list = PosRolePeer::doSelect($cr);
    }

    public function executeEditRole($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Role module id is 3.        
        if (Access::checkPermissions(3, "editRole", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        $role_id = $request->getParameter("id");
        $role = PosRolePeer::retrieveByPK($role_id);
        $this->role = $role;

        $this->redirect_shop_id = $request->getParameter("redirect_shop_id");

        $cm = new Criteria();
//        $cm->add(PosModulesPeer::ID,3, Criteria::NOT_EQUAL);
        $this->modules = PosModulesPeer::doSelect($cm);
        $this->shops = ShopsPeer::doSelect(new Criteria());

        $psr = new Criteria();
        $psr->add(PosShopRolePeer::STATUS_ID, 3);
        $psr->addAnd(PosShopRolePeer::POS_ROLE_ID, $role_id);
        $this->shoproles = PosShopRolePeer::doSelect($psr);
    }

    public function executeEditProcess($request) {
        $permission_ids = $request->getParameter("permissions");
        $role_id = $request->getParameter("role_id");
        $rolename = $request->getParameter("roleName");
        $shop_ids = $request->getParameter("shop_id");
        $role = PosRolePeer::retrieveByPK($role_id);
        $role->setName($rolename);
        $role->save();
        $psc = new Criteria();
        $psc->add(PosShopRolePeer::POS_ROLE_ID, $role_id);
        if (PosShopRolePeer::doCount($psc) > 0) {
            $posshoproles = PosShopRolePeer::doSelect($psc);
            foreach ($posshoproles as $posshoprole) {
                $posshoprole->setStatusId(5);
                $posshoprole->save();
            }
        }
        if (count($shop_ids) > 0) {
            foreach ($shop_ids as $shop_id) {
                $psc = new Criteria();
                $psc->add(PosShopRolePeer::SHOP_ID, $shop_id);
                $psc->add(PosShopRolePeer::POS_ROLE_ID, $role_id);
                if (PosShopRolePeer::doCount($psc) > 0) {
                    $pos_shop_role = PosShopRolePeer::doSelectOne($psc);
                    $pos_shop_role->setStatusId(3);
                } else {
                    $pos_shop_role = new PosShopRole();
                    $pos_shop_role->setPosRoleId($role->getId());
                    $pos_shop_role->setShopId($shop_id);
                    $pos_shop_role->setStatusId(3);
                }
                if ($pos_shop_role->save()) {
                    $sc = new Criteria();
                    $sc->add(ShopsPeer::ID, $shop_id);
                    if (ShopsPeer::doCount($sc)) {
                        $shopObj = ShopsPeer::doSelectOne($sc);
                        if ($shopObj->getGcmKey() != "") {
                            new GcmLib("role_updated", array($shopObj->getGcmKey()),$shopObj);
                        }
                    }
                }
            }
        }
        $same_permission = array();
        $cr = new Criteria();
        $cr->add(PosRolePermissionRefPeer::POS_ROLE_ID, $role_id);
        $current_count = PosRolePermissionRefPeer::doCount($cr);
        if ($current_count > 0) {
            $current_permissions = PosRolePermissionRefPeer::doSelect($cr);
            foreach ($current_permissions as $current_permission) {
                $curr_perm_id = $current_permission->getPosPermissionId();
                if (!in_array($curr_perm_id, $permission_ids)) {
                    $current_permission->delete();
                } else {
                    $same_permission[] = $curr_perm_id;
                }
            }
        }
        for ($i = 0; $i < count($permission_ids); $i++) {
            if (!in_array($permission_ids[$i], $same_permission)) {
                $rp = new PosRolePermissionRef();
                $rp->setPosRoleId($role_id);
                $rp->setPosPermissionId($permission_ids[$i]);
                $rp->save();
            }
        }
        $this->getUser()->setFlash('role_message', $this->getContext()->getI18N()->__('Permission Updated.'));


        if ($request->getParameter("redirect_shop_id") == "") {

            $this->redirect($this->targetUrl() . 'pos_role/editRole/id/' . $role_id);
        } else {
            $this->redirect($this->targetUrl() . 'shops/view?id=' . $request->getParameter("redirect_shop_id"));
        }
    }

    public function targetUrl() {
        return sfConfig::get("app_admin_url");
    }

    public function executeCreateRole($request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Role module id is 3.
        if (Access::checkPermissions(3, "createRole", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        $cm = new Criteria();
//       $cm->add(PosModulesPeer::ID,3, Criteria::NOT_EQUAL);
        $this->modules = PosModulesPeer::doSelect($cm);
        $this->shops = ShopsPeer::doSelect(new Criteria());

        $this->redirect_shop_id = $request->getParameter("redirect_shop_id");

        if ($request->isMethod('post')) {
            $permission_ids = $request->getParameter("permissions");
            $roleName = $request->getParameter("roleName");
            $shop_ids = $request->getParameter("shop_id");
            $countrole = PosRolePeer::doCount(new Criteria());
            $posroleid = ($countrole + 1) . time();
            if ($roleName != "") {
                $role = new PosRole();
                $role->setId($posroleid);
                $role->setName($roleName);
                $role->save();
                if (count($shop_ids) > 0) {
                    foreach ($shop_ids as $shop_id) {
                        $pos_shop_role = new PosShopRole();
                        $pos_shop_role->setPosRoleId($role->getId());
                        $pos_shop_role->setShopId($shop_id);
                        $pos_shop_role->setStatusId(3);
                        if ($pos_shop_role->save()) {
                            $sc = new Criteria();
                            $sc->add(ShopsPeer::ID, $shop_id);
                            if (ShopsPeer::doCount($sc)) {
                                $shopObj = ShopsPeer::doSelectOne($sc);
                                if ($shopObj->getGcmKey() != "") {
                                    new GcmLib("role_updated", array($shopObj->getGcmKey()),$shopObj);
                                }
                            }
                        }
                    }
                }


                for ($i = 0; $i < count($permission_ids); $i++) {
                    $rp = new PosRolePermissionRef();
                    $rp->setPosRoleId($role->getId());
                    $rp->setPosPermissionId($permission_ids[$i]);
                    $rp->save();
                }
                $this->getUser()->setFlash('role_message', $this->getContext()->getI18N()->__('Role Created.'));

                if ($request->getParameter("redirect_shop_id") == "") {
                    $this->redirect($this->targetUrl() . 'pos_role/editRole/id/' . $role->getId());
                } else {
                    $this->redirect($this->targetUrl() . 'shops/view?id=' . $request->getParameter("redirect_shop_id"));
                }
            }
        }
    }

}
