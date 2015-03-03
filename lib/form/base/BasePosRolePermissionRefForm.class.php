<?php

/**
 * PosRolePermissionRef form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BasePosRolePermissionRefForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'pos_role_id'       => new sfWidgetFormInput(),
      'pos_permission_id' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorPropelChoice(array('model' => 'PosRolePermissionRef', 'column' => 'id', 'required' => false)),
      'pos_role_id'       => new sfValidatorInteger(),
      'pos_permission_id' => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('pos_role_permission_ref[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PosRolePermissionRef';
  }


}
