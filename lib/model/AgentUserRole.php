<?php

class AgentUserRole extends BaseAgentUserRole
{
      public function __toString(){
        return $this->getName();
    }

}
