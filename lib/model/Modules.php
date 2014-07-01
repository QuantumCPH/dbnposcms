<?php

class Modules extends BaseModules
{
    public function __toString(){
		return $this->getTitle();
	}
}
