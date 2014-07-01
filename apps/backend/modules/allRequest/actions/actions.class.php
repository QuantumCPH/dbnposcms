<?php

/**
 * allRequest actions.
 *
 * @package    zapnacrm
 * @subpackage allRequest
 * @author     Your name here
 */
class allRequestActions extends sfActions {

    public function executeAllRequests(sfWebRequest $request) {
        $c = new Criteria();
        $c->addAscendingOrderByColumn(TransactionFromPeer::NAME);
        $this->transactionTypes = TransactionFromPeer::doSelect($c);

        $selectall = $request->getParameter('selectall');
        $this->selectall = $selectall;

        $this->transactionTypeId = $request->getParameter('transaction_type_id');


        $ca = new Criteria();
        if ($selectall != "on") {

            if ($this->transactionTypeId > 0) {
                $ca->addAnd(DibsCallPeer::TRANSACTION_FROM_ID, $this->transactionTypeId);
            }

            if (isset($_REQUEST['startdate']) && $_REQUEST['startdate'] != "") {
                $this->startdate = $request->getParameter('startdate');
                $startdate = $request->getParameter('startdate');
                $startdate = date('Y-m-d', strtotime($startdate)) . " 00:00:00";
                $ca->addAnd(DibsCallPeer::CREATED_AT, $startdate, Criteria::GREATER_THAN);
            } else {
                $this->startdate = date('d-m-Y');
                $ca->addAnd(DibsCallPeer::CREATED_AT, date('Y-m-d') . " 00:00:00", Criteria::GREATER_THAN);
            }
            if (isset($_REQUEST['enddate']) && $_REQUEST['enddate'] != "") {
                $this->enddate = $request->getParameter('enddate');
                $enddate = $request->getParameter('enddate');
                $enddate = date('Y-m-d', strtotime($enddate)) . " 23:59:59";
                $ca->addAnd(DibsCallPeer::CREATED_AT, $enddate, Criteria::LESS_THAN);
            } else {
                $this->enddate = date('d-m-Y');
                $ca->addAnd(DibsCallPeer::CREATED_AT, date('Y-m-d') . " 23:59:59", Criteria::LESS_THAN);
            }
        }

        $ca->addDescendingOrderByColumn(DibsCallPeer::CREATED_AT);
        $this->dibsCalls = DibsCallPeer::doSelect($ca);
    }

}