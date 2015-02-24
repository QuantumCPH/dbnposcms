<?php

/**
 * users actions.
 *
 * @package    zapnacrm
 * @subpackage users
 * @author     Your name here
 */
class usersActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        
    }

    public function executeView(sfWebRequest $request) {
        $this->ids = $request->getParameter('id');
        $this->users = UsersPeer::retrieveByPK($request->getParameter('id'));
    }

    public function executeAdd(sfWebRequest $request) {
        
    }

    public function executeAddSubmit(sfWebRequest $request) {
        $posUser = new Users();
        $posUser->setLogin($request->getParameter('login'));
        $posUser->setPassword($request->getParameter('password'));
        $posUser->setFirstName($request->getParameter('first_name'));
        $posUser->setLastName($request->getParameter('last_name'));
        $posUser->setEmail($request->getParameter('email'));
        $posUser->setMobile($request->getParameter('mobile'));
        $posUser->setUserTypeId($request->getParameter('user_type_id'));

        $posUser->save();
        $this->redirect('users/index');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->ids = $request->getParameter('id');
        $this->user = UsersPeer::retrieveByPK($request->getParameter('id'));
    }

    public function executeEditSubmit(sfWebRequest $request) {
        $posUser = UsersPeer::retrieveByPK($request->getParameter('id'));
        $posUser->setLogin($request->getParameter('login'));
        $posUser->setPassword($request->getParameter('password'));
        $posUser->setFirstName($request->getParameter('first_name'));
        $posUser->setLastName($request->getParameter('last_name'));
        $posUser->setEmail($request->getParameter('email'));
        $posUser->setMobile($request->getParameter('mobile'));
        $posUser->setUserTypeId($request->getParameter('user_type_id'));
        $posUser->save();
        $this->redirect('users/index');
    }

}
