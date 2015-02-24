<?php

/**
 * PosPermission form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BasePosPermissionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'pos_module_id' => new sfWidgetFormPropelChoice(array('model' => 'PosModules', 'add_empty' => false)),
      'action_name'   => new sfWidgetFormInput(),
      'action_title'  => new sfWidgetFormInput(),
      'position'      => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorPropelChoice(array('model' => 'PosPermission', 'column' => 'id', 'required' => false)),
      'pos_module_id' => new sfValidatorPropelChoice(array('model' => 'PosModules', 'column' => 'id')),
      'action_name'   => new sfValidatorString(array('max_length' => 50)),
      'action_title'  => new sfValidatorString(array('max_length' => 250)),
      'position'      => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('pos_permission[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PosPermission';
  }


}
