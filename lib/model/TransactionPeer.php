<?php

class TransactionPeer extends BaseTransactionPeer {

    public static $credit_card_types = array(
        '2' => 'Visa/Dankort',
        '18' => 'Visa'
    );

    static public function AssignReceiptNumber(Transaction $transaction, PropelPDO $con = null) {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        if ($transaction->getTransactionStatusId() == 3) {
            $obj = new ReceiptNumbers();
            $obj->setParentId($transaction->getId());
            $obj->setDescription($transaction->getDescription());
            $obj->save();
            $transaction->setReceiptNo($obj->getId());
            $transaction->save();
        }
    }
    
    static public function AssignAgentReceiptNumber(AgentOrder $agent_order, PropelPDO $con = null) {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        if ($agent_order->getStatus() == 3) {
            $obj = new ReceiptNumbers();
            $obj->setParentId($agent_order->getId());
            $obj->setParent("Agent Order");
            $obj->setDescription($agent_order->getOrderDescription());
            $obj->save();
            $agent_order->setReceiptNo($obj->getId());
            $agent_order->save();
        }
    }
       static public function AssignResellerReceiptNumber(ResellerOrder $reseller_order, PropelPDO $con = null) {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        if ($reseller_order->getStatus() == 3) {
            $obj = new ReceiptNumbers();
            $obj->setParentId($reseller_order->getId());
            $obj->setParent("Reseller Order");
            $obj->setDescription($reseller_order->getOrderDescription());
            $obj->save();
            $reseller_order->setReceiptNo($obj->getId());
            $reseller_order->save();
        }
    }
    static public function AssignTopUpReceiptNumber(TopupTransactions $transaction, PropelPDO $con = null) {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        if ($transaction->getStatus() == 3) {
            $obj = new ReceiptNumbers();
            $obj->setParentId($transaction->getId());
            $obj->setDescription("AGent Toptup");
            $obj->save();
            $transaction->setReceiptNo($obj->getId());
            $transaction->save();
        }
    }

}