<?php

/**
 * user actions.
 *
 * @package    zapnacrm
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 5125 2007-09-16 00:53:55Z dwhittle $
 */
class userActions extends sfActions {

    public function handleErrorSave() {
        $this->forward('user', 'edit');
    }

    public function executeLogin($request) {

        $this->loginForm = new LoginForm();

        if ($request->getParameter('new'))
            $this->getUser()->setCulture($request->getParameter('new'));
        else
            $this->getUser()->setCulture($this->getUser()->getCulture());


        if ($request->getMethod() != 'post') {
            $this->loginForm->bind($request->getParameter('login'), $request->getFiles('login'));
            if ($this->loginForm->isValid()) {

                $pin = $this->loginForm->getValue('pin');
                $password = $this->loginForm->getValue('password');

                $c = new Criteria();
                $c->add(UserPeer::PIN, $pin);
                $c->addAnd(UserPeer::PASSWORD, $password);
                $c->addAnd(UserPeer::PIN_STATUS, 3);
                $c->addAnd(UserPeer::STATUS_ID, 3);
                $c->addAnd(UserPeer::ROLE_ID, 0, Criteria::GREATER_THAN);

                /* $c = new Criteria(); 
                  $cton1 = $c->getNewCriterion(UserPeer::ROLE_ID, 0, Criteria::GREATER_THAN);
                  $cton2 = $c->getNewCriterion(UserPeer::IS_SUPER_USER, 1);
                  $cton1->addOr($cton2); // combine them
                  $c->add($cton1); // add to Criteria

                 */

                $user = UserPeer::doSelectOne($c);

                if ($user) {
                    $this->getUser()->setAuthenticated(true);
                    $this->getUser()->setAttribute('user_id', $user->getId(), 'backendsession');
                    $this->getUser()->setAttribute('role_id', $user->getRoleId(), 'backendsession');
                    $this->getUser()->setFlash('message', 'Welcome ' . $user->getName());
                    if ($user->getIsSuperUser()) {
                        $permissions = Access::getAdminPermissions();
                        $this->getUser()->setAttribute('user_permissions', $permissions, 'backendsession');
//                       var_dump($permissions);die;
                    } else {
                        $permissions = Access::getPermissions($user->getRoleId());
                        $this->getUser()->setAttribute('user_permissions', $permissions, 'backendsession');
                    }

                    $pathArray = $request->getPathInfoArray();

                    if (isset($pathArray['HTTP_REFERER']) && $pathArray['HTTP_REFERER'] != '') {
                        if ($pathArray['PATH_INFO'] == '/user/changeCulture/new/de') {

                            $this->redirect('user/dashboard');
                        } elseif ($pathArray['PATH_INFO'] == '/user/login') {
                            $this->redirect('items/index');
                        } else {
                            $this->redirect($pathArray['HTTP_REFERER']);
                        }
                    } else {
                        $this->redirect('items/index');
                    }
                } else {
                    $this->getUser()->setFlash('message', 'You are not Authorized / or you have submitted incorrect Pin or Password');
                }
            }
        }
    }

    public function executeLogout($request) {
        $this->getUser()->getAttributeHolder()->removeNamespace('backendsession');
        $this->getUser()->setAuthenticated(false);
        $this->redirect('@homepage');
    }

    public function executeChangeCulture(sfWebRequest $request) {
        $this->getUser()->setCulture($request->getParameter('new'));

        $pathArray = $request->getPathInfoArray();
        //   var_dump($pathArray);
        //  die;

        if ($pathArray['PATH_INFO'] == '/user/login') {
            $this->redirect('agent_company/index');
        } else {
            if (isset($pathArray['HTTP_REFERER'])) {

                $this->redirect($pathArray['HTTP_REFERER']);
            }
        }
        $this->redirect('agent_company/index');
    }

    public function executeIndex($request) {
        $user_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $current_user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        if ($current_user_id == "" || $user_role == "") {
            $this->redirect($this->redirect($this->targetUrl() . 'user/login'));
        }
        if ($user_role == 1) {
            $cu = new Criteria();
            $cu->add(UserPeer::STATUS_ID, 3);
            $this->user_list = UserPeer::doSelect($cu);
        } else {
            $cu = new Criteria();
//            $cu->add(UserPeer::ID,$current_user_id);
            $cu->add(UserPeer::ROLE_ID, 1, Criteria::NOT_EQUAL);
            $cu->add(UserPeer::STATUS_ID, 3);
            $this->user_list = UserPeer::doSelect($cu);
        }
    }

    public function executeEdit(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// User module id is 2.
        if (Access::checkPermissions(2, "edit", $user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
//           $this->redirect($this->targetUrl().'user/dashboard');
            $this->redirect($request->getReferer());
        }
        $this->redirect_shop_id = $request->getParameter("redirect_shop_id");
        $this->user_id = $user_id;
        $this->curr_user = UserPeer::retrieveByPK($user_id);
        $userid = $request->getParameter("id");
        $user = UserPeer::retrieveByPK($userid);

        if ($user->getIsSuperUser() == 1 && $this->curr_user->getIsSuperUser() == 0) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        $this->user_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $this->pos_user_role = $this->getUser()->getAttribute('pos_user_role_id', '', 'backendsession');

        $sp = new Criteria();
        $sp->add(ShopsPeer::STATUS_ID, 3);
        $this->shops = ShopsPeer::doSelect($sp);

        $spu = new Criteria();
        $spu->add(ShopUsersPeer::STATUS_ID, 3);
        $spu->addAnd(ShopUsersPeer::USER_ID, $userid);
        $spu->addAnd(ShopUsersPeer::IS_PRIMARY, 1);

        if (ShopUsersPeer::doCount($spu) > 0) {
            $this->shopsSelectedUser = ShopUsersPeer::doSelectOne($spu);

            //die($this->shopsSelectedUser->getShopId());

            $cr = new Criteria();
            $cr->addJoin(PosRolePeer::ID, PosShopRolePeer::POS_ROLE_ID, Criteria::LEFT_JOIN);
            $cr->add(PosShopRolePeer::SHOP_ID, $this->shopsSelectedUser->getShopId());
            $this->roles = PosRolePeer::doSelect($cr);
        } else {
            $this->shopsSelectedUser = false;
        }

        $this->user = UserPeer::retrieveByPK($userid);

        $user = $this->user;
        $this->form = new UserForm($user);
        unset($this->form['status_id']);

        if ($request->isMethod('put')) {
            $this->forward404Unless($user = UserPeer::retrieveByPk($request->getParameter('id')));

            $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
            $this->curr_user = UserPeer::retrieveByPK($user_id);

            $editUser = $request->getParameter("user");

            $pos_user_role_id = $request->getParameter("pos_user_role_id");
            $branch = $request->getParameter("branches");
            $pos_super_user = $request->getParameter("pos_super_user") == "on" ? 1 : 0;

            $sp = new Criteria();
            $sp->add(ShopsPeer::STATUS_ID, 3);
            $this->shops = ShopsPeer::doSelect($sp);


            $spu = new Criteria();
            $spu->add(ShopUsersPeer::STATUS_ID, 3);
            $spu->addAnd(ShopUsersPeer::USER_ID, $editUser['id']);
            $spu->addAnd(ShopUsersPeer::IS_PRIMARY, 1);
            if (ShopUsersPeer::doCount($spu) == 1) {
                $this->shopsSelectedUser = ShopUsersPeer::doSelectOne($spu);
            } else {
                $this->shopsSelectedUser = false;
            }


            if (!$this->shopsSelectedUser && $branch != "") {

                $c = new Criteria();
                $c->add(ShopUsersPeer::USER_ID, $user->getId());
                $c->add(ShopUsersPeer::SHOP_ID, $branch);
                $c->add(ShopUsersPeer::STATUS_ID, 3);
                $c->add(ShopUsersPeer::IS_PRIMARY, 0);
                if (ShopUsersPeer::doCount($c) > 0) {
                    $user_shop = ShopUsersPeer::doSelectOne($c);
                } else {
                    $user_shop = new ShopUsers();
                }
                $user_shop->setUserId($user->getId());
                $user_shop->setPosRoleId($pos_user_role_id);
                $user_shop->setPosSuperUser($pos_super_user);
                $user_shop->setShopId($branch);
                $user_shop->setStatusId(3);
                $user_shop->setIsPrimary(1);
                $user_shop->save();
            } elseif ($this->shopsSelectedUser) {

                if ($branch == "") {
                    $this->shopsSelectedUser->setStatusId(5);
                    $this->shopsSelectedUser->setIsPrimary(0);
                    $this->shopsSelectedUser->save();
                }
                if ($branch != "") {
                    $this->shopsSelectedUser->setShopId($branch);
                    $this->shopsSelectedUser->setPosSuperUser($pos_super_user);
                    $this->shopsSelectedUser->setPosRoleId($pos_user_role_id);
                    $this->shopsSelectedUser->save();
                }
            }


            $cc = new Criteria();
            $cc->add(ShopUsersPeer::USER_ID, $editUser['id']);
            $shopUsers = ShopUsersPeer::doSelect($cc);
            $arrayShopIds = "";
            foreach ($shopUsers as $user1) {
                $arrayShopIds[] = $user1->getShopId();
            }


            $sc = new Criteria();
            $sc->add(ShopsPeer::ID, $arrayShopIds, Criteria::IN);
            $sc->addAnd(ShopsPeer::STATUS_ID, 3);
            $sc->addAnd(ShopsPeer::GCM_KEY, null, Criteria::ISNOTNULL);
            if (ShopsPeer::doCount($sc) > 0) {
                $shops = ShopsPeer::doSelect($sc);
                $gcmKeyArray = "";
                foreach ($shops as $shop) {
                    $gcmKeyArray[] = $shop->getGcmKey();
                }
                new GcmLib("user_updated", $gcmKeyArray);
                
            }


//        $countArayBranch = count($arbranches);
//        if ($countArayBranch > 0) {
//
//            $su = new Criteria();
//            $su->add(ShopUsersPeer::USER_ID, $request->getParameter('id'), Criteria::EQUAL);
//            $countUserShop = ShopUsersPeer::doCount($su);
//            if ($countUserShop > 0) {
//                $sushops = ShopUsersPeer::doSelect($su);
//
//                foreach ($sushops as $sushop) {
//                    $sushop->setStatusId(5);
//                    $sushop->save();
//                }
//            }
//
//
//
//
//
//
//
//            foreach ($arbranches as $key) {
//                $countUserShops = 0;
//                $sus = new Criteria();
//                $sus->add(ShopUsersPeer::USER_ID, $request->getParameter('id'), Criteria::EQUAL);
//                $sus->add(ShopUsersPeer::SHOP_ID, $key, Criteria::EQUAL);
//                $countUserShops = ShopUsersPeer::doCount($sus);
//                if ($countUserShops > 0) {
//
//                    $shopUser = ShopUsersPeer::doSelectOne($sus);
//                    $shopUser->setStatusId(3);
//                    $shopUser->save();
//                } else {
//
//                    $shopnew = new ShopUsers();
//                    $shopnew->setUserId($request->getParameter('id'));
//                    $shopnew->setShopId($key);
//                    $shopnew->setStatusId(3);
//                    $shopnew->save();
//                }
//            }
//        } else {
//            $su = new Criteria();
//            $su->add(ShopUsersPeer::USER_ID, $request->getParameter('id'), Criteria::EQUAL);
//            $countUserShop = ShopUsersPeer::doCount($su);
//            if ($countUserShop > 0) {
//                $sushops = ShopUsersPeer::doSelect($su);
//
//                foreach ($sushops as $sushop) {
//                    $sushop->setStatusId(5);
//                    $sushop->save();
//                }
//            }
//        }
            $this->form = new UserForm($user);
            $this->user = $user;
            $this->user_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
            $this->processForm($request, $this->form);
        }
    }

    public function executeView(sfWebRequest $request) {
        $this->user = UserPeer::retrieveByPK($request->getParameter('id'));


        $cu = new Criteria();
//            $cu->add(UserPeer::ID,$current_user_id);
        $cu->addJoin(ShopUsersPeer::USER_ID, UserPeer::ID, Criteria::LEFT_JOIN);
        $cu->addAnd(ShopUsersPeer::USER_ID, $request->getParameter('id'));
        $cu->addAnd(ShopUsersPeer::STATUS_ID, 5, Criteria::NOT_EQUAL);
        $this->user_list = ShopUsersPeer::doSelect($cu);
    }

    public function executeUpdate(sfWebRequest $request) {

        $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
        $this->forward404Unless($user = UserPeer::retrieveByPk($request->getParameter('id')));

        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $this->curr_user = UserPeer::retrieveByPK($user_id);

        $editUser = $request->getParameter("user");

        $pos_user_role_id = $request->getParameter("pos_user_role_id");
        $branch = $request->getParameter("branches");
        $pos_super_user = $request->getParameter("pos_super_user") == "on" ? 1 : 0;

        $sp = new Criteria();
        $sp->add(ShopsPeer::STATUS_ID, 3);
        $this->shops = ShopsPeer::doSelect($sp);


        $spu = new Criteria();
        $spu->add(ShopUsersPeer::STATUS_ID, 3);
        $spu->addAnd(ShopUsersPeer::USER_ID, $editUser['id']);
        $spu->addAnd(ShopUsersPeer::IS_PRIMARY, 1);
        if (ShopUsersPeer::doCount($spu) == 1) {
            $this->shopsSelectedUser = ShopUsersPeer::doSelectOne($spu);
        } else {
            $this->shopsSelectedUser = false;
        }

        if (!$this->shopsSelectedUser && $branch != "") {

            $c = new Criteria();
            $c->add(ShopUsersPeer::USER_ID, $user->getId());
            $c->add(ShopUsersPeer::SHOP_ID, $branch);
            $c->add(ShopUsersPeer::STATUS_ID, 3);
            $c->add(ShopUsersPeer::IS_PRIMARY, 0);
            if (ShopUsersPeer::doCount($c) > 0) {
                $user_shop = ShopUsersPeer::doSelectOne($c);
            } else {
                $user_shop = new ShopUsers();
            }
            $user_shop->setUserId($user->getId());
            $user_shop->setPosRoleId($pos_user_role_id);
            $user_shop->setPosSuperUser($pos_super_user);
            $user_shop->setShopId($branch);
            $user_shop->setStatusId(3);
            $user_shop->setIsPrimary(1);
            $user_shop->save();
        } elseif ($this->shopsSelectedUser) {

            if ($branch == "") {
                $this->shopsSelectedUser->setStatusId(5);
                $this->shopsSelectedUser->setIsPrimary(0);
                $this->shopsSelectedUser->save();
            }
            if ($branch != "") {
                $this->shopsSelectedUser->setShopId($branch);
                $this->shopsSelectedUser->setPosSuperUser($pos_super_user);
                $this->shopsSelectedUser->setPosRoleId($pos_user_role_id);
                $this->shopsSelectedUser->save();
            }
        }

        $this->form = new UserForm($user);
        $this->user = $user;
        $this->user_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $this->processForm($request, $this->form);
    }

    public function executeNew(sfWebRequest $request) {
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $this->user_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $this->pos_user_role = $this->getUser()->getAttribute('pos_user_role_id', '', 'backendsession');
        //// User module id is 2.
        if (Access::checkPermissions(2, "new", $user_id, $this->user_role) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        $this->redirect_shop_id = $request->getParameter("redirect_shop_id");
        $this->user = UserPeer::retrieveByPK($user_id);
        $this->user_id = $user_id;
        $cr = new Criteria();
        if ($this->user_role != 1) {
            $cr->add(RolePeer::ID, 1, Criteria::NOT_EQUAL);
        }
        $this->roles = RolePeer::doSelect($cr);
//        $this->form = new UserForm();  
//        unset($this->form['status_id']);
        if ($this->user_role != 1) {
            $cr->add(PosRolePeer::ID, 1, Criteria::NOT_EQUAL);
        }
        $this->posroles = PosRolePeer::doSelect($cr);
        $sp = new Criteria();
        $sp->add(ShopsPeer::STATUS_ID, 3);
        $this->shops = ShopsPeer::doSelect($sp);
    }

    public function executeCreateUser(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod('post'));
//        $this->form = new UserForm();
        $cuu = new Criteria();
        $usercount = UserPeer::doCount($cuu);
        $_userid = "1" . $usercount . time();
        $username = $request->getParameter("username");
        $sur_name = $request->getParameter("sur_name");
        $address = $request->getParameter("address");
        $zip = $request->getParameter("zip");
        $city = $request->getParameter("city");
        $country = $request->getParameter("country");
        $tel = $request->getParameter("tel");
        $mobile = $request->getParameter("mobile");
        $pin = $request->getParameter("pin");
        $password = $request->getParameter("password");
        $email = $request->getParameter("email");
        $role_id = $request->getParameter("role_id");
        $pos_user_role_id = $request->getParameter("pos_user_role_id");
        $branches = $request->getParameter("branches");
        $is_super_user = $request->getParameter("is_super_user") == "on" ? 1 : 0;
        $pos_super_user = $request->getParameter("pos_super_user") == "on" ? 1 : 0;
        $curr_user_role = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $user = new User();
        $user->setId($_userid);
        $user->setName($username);
        $user->setSurName($sur_name);
        $user->setAddress($address);
        $user->setZip($zip);
        $user->setCity($city);
        $user->setCountry($country);
        $user->setTel($tel);
        $user->setMobile($mobile);
        $user->setPin($pin);
        $user->setPinStatus(3);
        $user->setPassword($password);
        $user->setEmail($email);
        if ($role_id != "") {
            $user->setRoleId($role_id);
        }
        //$user->setPosUserRoleId($pos_user_role_id);
        $user->setStatusId(3);
        $user->setIsSuperUser($is_super_user);
        //$user->setPosSuperUser($pos_super_user);
        $user->setUpdatedBy($curr_user_role);
        $user->save();

        if ($branches != '') {
            $user_shop = new ShopUsers();
            $user_shop->setUserId($user->getId());
            $user_shop->setPosRoleId($pos_user_role_id);
            $user_shop->setPosSuperUser($pos_super_user);
            $user_shop->setShopId($branches);
            $user_shop->setStatusId(3);
            $user_shop->setIsPrimary(1);
            if ($user_shop->save()) {
                $sc = new Criteria();
                $sc->add(ShopsPeer::ID, $branches);
                if (ShopsPeer::doCount($sc)) {
                    $shopObj = ShopsPeer::doSelectOne($sc);
                    if ($shopObj->getGcmKey() != "") {
                        new GcmLib("user_updated", array($shopObj->getGcmKey()),$shopObj);
                    }
                }
            }
        }
//        $this->processForm($request, $this->form);
        $this->getUser()->setFlash('message', 'User info updated');

        if ($request->getParameter("redirect_shop_id") == "") {
            
            $this->redirect(sfConfig::get("app_admin_url") . 'shops/view?id=' . $request->getParameter("redirect_shop_id"));
        } else {
            $this->redirect('user/edit?id=' . $user->getId());
        }
    }
    protected function processForm(sfWebRequest $request, sfForm $form) {

        unset($form['status_id']);
        unset($form['created_at']);
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $loggedin_user = $this->getUser()->getAttribute('user_id', '', 'backendsession');
            $user_role = $this->getUser()->getAttribute('role_id', '', 'backendsession');
            if ($request->getParameter("user[id]") != "" && ($request->getParameter("user[role_id]") != $user_role)) {
                if ($request->getParameter("user[id]") == $loggedin_user) {
                    $user = $form->save();
                    $user->setPinStatus(3);
                    $user->save();
                    $this->redirect('user/logout');
                }

                $user = $form->save();
                $user->setPinStatus(3);
                $user->save();
                $this->getUser()->setFlash('message', 'User info updated');
                if ($request->getParameter("redirect_shop_id") == "") {
                    $this->redirect('user/edit?id=' . $user->getId());
                } else {
                    $this->redirect(sfConfig::get("app_admin_url") . 'shops/view?id=' . $request->getParameter("redirect_shop_id"));
                }
            } else {
                $user = $form->save();
                $user->setPinStatus(3);
                $user->save();
                $this->getUser()->setFlash('message', 'User info updated');
                if ($request->getParameter("redirect_shop_id") == "") {
                    $this->redirect('user/edit?id=' . $user->getId());
                } else {
                    $this->redirect(sfConfig::get("app_admin_url") . 'shops/view?id=' . $request->getParameter("redirect_shop_id"));
                }
            }
        }
//         else {
//            $editUser = $request->getParameter("user");
//            $this->redirect('user/edit?id=' . $editUser['id']);
//        }
    }

    public function executeDelete($request) {
        $loggedin_user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession');
        $user_id = $request->getParameter("id");
        $loggedin_user = UserPeer::retrieveByPK($loggedin_user_id);
        $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        $user = UserPeer::retrieveByPK($user_id);
        //// User module id is 2.
        if (Access::checkPermissions(2, "delete", $loggedin_user_id, $session_role_id) == false) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }
        if ($user->getIsSuperUser() == 1 && $loggedin_user->getIsSuperUser() == 0) {
            $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
            $this->redirect($request->getReferer());
        }

        if ($loggedin_user == $user_id) {
            $this->getUser()->setFlash('err_message', 'You cannot delete your account.');
        } else {
            $user = UserPeer::retrieveByPK($user_id);
            $user->setStatusId(5);
            $user->save();
            $this->getUser()->setFlash('message', 'User has been deleted.');
        }

        $this->redirect('user/index');
    }

    public function executeDashboard() {
        
    }

    public function targetUrl() {
        return sfConfig::get("app_admin_url");
    }

    public function executeValidateEditEmail($request) {
        $email = $request->getParameter("user[email]");
        $userid = $request->getParameter("userid");

        $cuc = new Criteria();
        $cuc->add(UserPeer::EMAIL, $email);
        $cuc->add(UserPeer::ID, $userid);
        if (UserPeer::doCount($cuc) > 0) {
            echo "true";
        } else {
            $cu = new Criteria();
            $cu->add(UserPeer::EMAIL, $email);
            $cu->addAnd(UserPeer::STATUS_ID, 3);
            if (UserPeer::doCount($cu) > 0) {
                echo "false";
            } else {
                echo "true";
            }
        }
        return sfView::NONE;
    }

    public function executeValidateEditPin($request) {
        $pin = $request->getParameter("user[pin]");
        $userid = $request->getParameter("userid");

        $cuc = new Criteria();
        $cuc->add(UserPeer::PIN, $pin);
        $cuc->add(UserPeer::PIN_STATUS, 3);
        $cuc->add(UserPeer::ID, $userid);
        if (UserPeer::doCount($cuc) > 0) {
            echo "true";
        } else {
            $cu = new Criteria();
            $cu->add(UserPeer::PIN, $pin);
            $cu->addAnd(UserPeer::PIN_STATUS, 3);
            $cu->addAnd(UserPeer::STATUS_ID, 3);
            if (UserPeer::doCount($cu) > 0) {
                echo "false";
            } else {
                echo "true";
            }
        }
        return sfView::NONE;
    }

    public function executeValidateEmail($request) {
        $email = $request->getParameter("email");


        $cuc = new Criteria();
        $cuc->add(UserPeer::EMAIL, $email);

        if (UserPeer::doCount($cuc) > 0) {
            echo "false";
        } else {

            echo "true";
        }
        return sfView::NONE;
    }

    public function executeValidatePin($request) {
        $pin = $request->getParameter("pin");


        $cuc = new Criteria();
        $cuc->add(UserPeer::PIN, $pin);
        $cuc->add(UserPeer::PIN_STATUS, 3);
        if (UserPeer::doCount($cuc) > 0) {
            echo "false";
        } else {

            echo "true";
        }
        return sfView::NONE;
    }

    public function executeForgot($request) {
        if ($request->getMethod() != 'post') {
            $email = $request->getParameter("user[email]");

            $cuc = new Criteria();
            $cuc->add(UserPeer::EMAIL, $email);
            $cuc->add(UserPeer::STATUS_ID, 3);
            if (UserPeer::doCount($cuc) > 0) {
                $fpToken = md5(uniqid(mt_rand(), true));
                $fpUser = UserPeer::doSelectOne($cuc);
                $fpUser->setResetPasswordToken($fpToken);
                $fpUser->save();
                $email_content = 'Hello, ' . $fpUser->getName() . '
===============================================

You have requested to reset your password.

To choose a new password, just follow this link: ' . sfConfig::get("app_customer_url") . 'pScripts/resetPassword/token/' . $fpUser->getResetPasswordToken() . '.

Have a great day!';

                emailLib::sendAdminForgotPassworEmail($fpUser, $email_content);
                $this->getUser()->setFlash('reset_message', 'Email sent to your email address.');
            } else {
                
            }
        }
    }

    public function executeValidateEmailOnForgot($request) {
        $email = $request->getParameter("user[email]");

        $cuc = new Criteria();
        $cuc->add(UserPeer::EMAIL, $email);
        $cuc->add(UserPeer::STATUS_ID, 3);
        if (UserPeer::doCount($cuc) > 0) {
            echo "true";
        } else {
            echo "false";
        }
        return sfView::NONE;
    }

    public function executeGetPosRoles(sfWebRequest $request) {
        $shop_id = $request->getParameter("shop_id");
        $cr = new Criteria();
        $cr->addJoin(PosRolePeer::ID, PosShopRolePeer::POS_ROLE_ID, Criteria::LEFT_JOIN);
        $cr->add(PosShopRolePeer::STATUS_ID, 3);
        $cr->add(PosShopRolePeer::SHOP_ID, $shop_id);
        $str = "";
        if (PosRolePeer::doCount($cr) > 0) {
            $posroles = PosRolePeer::doSelect($cr);
            $str.="<option value=''>Please Select</option>";
            foreach ($posroles as $posrole) {
                $str.="<option value='" . $posrole->getId() . "'>" . $posrole->getName() . "</option>";
            }
        } else {
            $str.="<option value=''>No Pos Role Found against this branch</option>";
        }

        echo $str;
        return sfView::NONE;
    }

    public function executeAssignBranch(sfWebRequest $request) {

        $this->user = UserPeer::retrieveByPK($request->getParameter('user_id'));

        $cu = new Criteria();
//            $cu->add(UserPeer::ID,$current_user_id);
        $cu->addAnd(ShopUsersPeer::USER_ID, $request->getParameter('user_id'));
        $cu->addAnd(ShopUsersPeer::STATUS_ID, 3);

        $existing_shop_users = '';

        $shopUsers = ShopUsersPeer::doSelect($cu);

        foreach ($shopUsers as $shopUser) {
            $existing_shop_users[] = $shopUser->getShopId();
        }


        $c = new Criteria();
        $c->add(ShopsPeer::ID, $existing_shop_users, Criteria::NOT_IN);
        $c->add(ShopsPeer::STATUS_ID, 3);
        $c->addAscendingOrderByColumn(ShopsPeer::NAME);
        $this->shops_list = ShopsPeer::doSelect($c);
    }

    public function executeAssignBranchCreate(sfWebRequest $request) {
        $shop_id = $request->getParameter('shop_id');
        $user_id = $request->getParameter('user_id');
        $role_id = $request->getParameter('role_id');
        $pos_super_user = ($request->getParameter('pos_super_user') == 'on') ? 1 : 0;
        $shopUser = new ShopUsers();
        $shopUser->setShopId($shop_id);
        $shopUser->setStatusId(3);
        $shopUser->setPosRoleId($role_id);
        $shopUser->setPosSuperUser($pos_super_user);
        $shopUser->setUserId($user_id);
        $shopUser->save();
        $sc = new Criteria();
        $sc->add(ShopsPeer::ID, $shop_id);
        if (ShopsPeer::doCount($sc)) {
            $shopObj = ShopsPeer::doSelectOne($sc);
            if ($shopObj->getGcmKey() != "") {
                new GcmLib("user_updated", array($shopObj->getGcmKey()),$shopObj);
            }
        }
        $this->getUser()->setFlash('message', 'Branch assigned successfuly.');
        $this->redirect('user/view?id=' . $request->getParameter('user_id'));
    }

}