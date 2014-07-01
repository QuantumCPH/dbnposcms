<?php

/**
 * Role form.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: RoleForm.class.php,v 1.1 2010-05-25 13:15:36 orehman Exp $
 */
class RoleForm extends BaseRoleForm
{
  public function configure()
  {    
        $this->validatorSchema['name'] = new sfValidatorString(
		array('required'=>true),
		array('required'=>'Please enter a role ')
	);
  }
}
