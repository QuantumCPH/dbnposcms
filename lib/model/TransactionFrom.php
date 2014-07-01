<?php

class TransactionFrom extends BaseTransactionFrom
{
       public function __toString()
    {
      return $this->getName();
    }
}
