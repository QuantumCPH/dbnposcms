<?php

/**
 * RolePermissionRef form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseRolePermissionRefForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'role_id'       => new sfWidgetFormInput(),
      'permission_id' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorPropelChoice(array('model' => 'RolePermissionRef', 'column' => 'id', 'required' => false)),
      'role_id'       => new sfValidatorInteger(),
      'permission_id' => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('role_permission_ref[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolePermissionRef';
  }


}
