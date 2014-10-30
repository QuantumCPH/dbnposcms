<?php

class ImeiNumbersPeer extends BaseImeiNumbersPeer {

    static public function getUnusedImeiNumbers() {

        $c = new Criteria();
        $c->addAnd(ImeiNumbersPeer::USED_STATUS, 1, Criteria::NOT_EQUAL);
        $c->addAscendingOrderByColumn(ImeiNumbersPeer::IMEI_NUMBER);
        $rs = ImeiNumbersPeer::doSelect($c);
        return $rs;
    }

    static public function getUsedImeiNumbers() {
        //  $c->addAnd(AgentCompanyPeer::RESELLER_ID,sfContext::getInstance()->getUser()->getAttribute('reseller_id', '', 'resellersession'));
        $c = new Criteria();
        $c->addJoin(ImeiNumbersPeer::ID, AgentUserPeer::IMEI_NUMBER_ID, Criteria::LEFT_JOIN);
        $c->addJoin(AgentUserPeer::AGENT_COMPANY_ID, AgentCompanyPeer::ID, Criteria::LEFT_JOIN);
        $c->addAnd(AgentCompanyPeer::RESELLER_ID, sfContext::getInstance()->getUser()->getAttribute('reseller_id', '', 'resellersession'));

        $c->addAscendingOrderByColumn(ImeiNumbersPeer::IMEI_NUMBER);
        $rs = ImeiNumbersPeer::doSelect($c);
        return $rs;
    }

}
