<?php

class CardTypes extends BaseCardTypes
{
    	public function __toString(){
		return $this->getName();
	}
}
