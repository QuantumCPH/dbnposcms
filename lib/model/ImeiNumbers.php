<?php

class ImeiNumbers extends BaseImeiNumbers
{
       public function __toString()
	{
		return $this->getImeiNumber();
	}
}
