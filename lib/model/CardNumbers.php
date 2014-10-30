<?php

class CardNumbers extends BaseCardNumbers
{
    
    public function save(PropelPDO $con = null)
	{
		
	    if (($this->isModified() && $this->isColumnModified(CardNumbersPeer::STATUS)) ||
	    	($this->isNew() && $this->getStatus())
	    	)
	    {
                if(!$this->getStatus()){
	    	$this->setDibsId(NULL);
                }
	    }
	    
	    parent::save($con);
	}
    
    
    
}
