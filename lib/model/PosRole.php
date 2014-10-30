<?php

class PosRole extends BasePosRole
{
    public function __toString(){
            return $this->getName();
	}
}
