<?php

/**
 * Users form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseUsersForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'login'        => new sfWidgetFormInput(),
      'password'     => new sfWidgetFormInput(),
      'first_name'   => new sfWidgetFormInput(),
      'last_name'    => new sfWidgetFormInput(),
      'email'        => new sfWidgetFormInput(),
      'mobile'       => new sfWidgetFormInput(),
      'user_type_id' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorPropelChoice(array('model' => 'Users', 'column' => 'id', 'required' => false)),
      'login'        => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'password'     => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'first_name'   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'last_name'    => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'email'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'mobile'       => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'user_type_id' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Users';
  }


}
