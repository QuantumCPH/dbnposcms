<?php

class ReceiptFormats extends BaseReceiptFormats
{
  public function __toString(){
	return $this->getTitle();
    }
}
