<?php

/**
 * Permission form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BasePermissionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'module_id'    => new sfWidgetFormPropelChoice(array('model' => 'Modules', 'add_empty' => false)),
      'action_name'  => new sfWidgetFormInput(),
      'action_title' => new sfWidgetFormInput(),
      'position'     => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorPropelChoice(array('model' => 'Permission', 'column' => 'id', 'required' => false)),
      'module_id'    => new sfValidatorPropelChoice(array('model' => 'Modules', 'column' => 'id')),
      'action_name'  => new sfValidatorString(array('max_length' => 50)),
      'action_title' => new sfValidatorString(array('max_length' => 250)),
      'position'     => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('permission[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Permission';
  }


}
