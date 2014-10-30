<?php

class AgentUser extends BaseAgentUser
{
      public function __toString(){
        return $this->getUsername();
    }

    
    
    
    
      public function getTopupRevenue()
    {

       $c = new Criteria();
       $c->add(TopupTransactionsPeer::AGENT_USER_ID,$this->getId());
      $c->addAnd(TopupTransactionsPeer::STATUS, 3);
 
       $transactions=TopupTransactionsPeer::doSelect($c);
       //$agent
       //$str=array();

       $sum=0.00;
       $per=0.00;
       foreach($transactions as $transaction){
        $sum = $sum+ $transaction->getProductRegistrationFee();
        //$per=($sum*10)/100;
        $per=$sum;
       }

       return number_format($per,2);

    }
    
    
      public function getTopupCommission()
    {

       $c = new Criteria();
        $c->add(TopupTransactionsPeer::AGENT_USER_ID,$this->getId());
      $c->addAnd(TopupTransactionsPeer::STATUS, 3);
       $transactions=TopupTransactionsPeer::doSelect($c);
       //$agent
       //$str=array();

       $sum=0.00;
       $per=0.00;
       foreach($transactions as $transaction){
        $sum = $sum+ $transaction->getAgentCommission();
        //$per=($sum*10)/100;
        $per=$sum;
       }

       return number_format($per,2);

    }
    
    
}
