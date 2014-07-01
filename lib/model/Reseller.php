<?php

class Reseller extends BaseReseller
{
    public function __toString()
	{
		return $this->getName();
	}
        
         public function getResellerActualBalance()
    {
        
            
       $rsbalance=$this->getBalance();
        

       return number_format($rsbalance,2);


       
    }
        public function getResellerAvailableBalance()
    {
        
       
       
       $reavibalance=$this->getBalance()-($this->getCreditLimit());
       return number_format($reavibalance,2);
    
    }  
     
      public function getTopupRevenue()
    {

       $c = new Criteria();
       $c->add(TopupTransactionsPeer::RESELLER_ID,$this->getId());
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
        $c->add(TopupTransactionsPeer::RESELLER_ID,$this->getId());
      $c->addAnd(TopupTransactionsPeer::STATUS, 3);
       $transactions=TopupTransactionsPeer::doSelect($c);
       //$agent
       //$str=array();

       $sum=0.00;
       $per=0.00;
       foreach($transactions as $transaction){
        $sum = $sum+ $transaction->getResellerCommission();
        //$per=($sum*10)/100;
        $per=$sum;
       }

       return number_format($per,2);

    }
  /////////////////////////////Reseller Commission/////////////////////////////////////////////  
    
      public function save(PropelPDO $con = null)
  {
       
      parent::save($con);
    

    $pcr = new Criteria();
    $pcr->add(ResellerProductPeer::RESELLER_ID, $this->getId());
    $agentProductCount = ResellerProductPeer::doCount($pcr);
    
  if($agentProductCount>0){
      
  }else{
        $c = new Criteria();
        $c->add(ProductPeer::RESELLER_COMMISSION_VALUE, 0,Criteria::GREATER_THAN);
        $productCount = ProductPeer::doCount($c);
            
        if($productCount>0){
             $products = ProductPeer::doSelect($c);
          foreach($products as $product) {
              
            $agentp = new ResellerProduct();
                $agentp->setResellerId($this->getId());
                $agentp->setProductId($product->getId());
                $agentp->setExtraPaymentsShareValue($product->getResellerCommissionValue());
                $agentp->setIsExtraPaymentsShareValuePc($product->getResellerCommissionValuePercentage());
                $agentp->setExtraPaymentsShareEnable(1);
                $agentp->save();   
                
          } 
           
        
        }
  
  
  }
  
   
      return parent::save($con);
  } 
    
    
    //////////////////////////////////////////////////////////////////////////////////////////////
    
}
