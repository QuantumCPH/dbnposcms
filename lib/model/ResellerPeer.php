<?php

class ResellerPeer extends BaseResellerPeer
{
     static public function getSortedReseller() {
        $c = new Criteria();
        //$this->getUser()->getAttribute('reseller_id', '', 'resellersession')
        //$sf_user->getAttribute('reseller_id', '', 'resellersession')
        //sfContext::getInstance()->getUser()->getAttribute('reseller_id', '', 'resellersession')
        $c->addAnd(ResellerPeer::ID,sfContext::getInstance()->getUser()->getAttribute('reseller_id', '', 'resellersession'));
        $c->addAscendingOrderByColumn(ResellerPeer::NAME);
        $rs = ResellerPeer::doSelect($c);
        return $rs;
    }
}
