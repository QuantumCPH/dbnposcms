<?php

class AgentCompanyPeer extends BaseAgentCompanyPeer
{

   static public function getSortedAgentCompanies() {
        $c = new Criteria();
        //$this->getUser()->getAttribute('reseller_id', '', 'resellersession')
        //$sf_user->getAttribute('reseller_id', '', 'resellersession')
        //sfContext::getInstance()->getUser()->getAttribute('reseller_id', '', 'resellersession')
        $c->addAnd(AgentCompanyPeer::RESELLER_ID,sfContext::getInstance()->getUser()->getAttribute('reseller_id', '', 'resellersession'));
        $c->addAscendingOrderByColumn(AgentCompanyPeer::NAME);
        $rs = AgentCompanyPeer::doSelect($c);
        return $rs;
    }

}
