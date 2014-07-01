<?php

class PosUserRole extends BasePosUserRole
{
    public function __toString(){
		return $this->getTitle();
	}
}
