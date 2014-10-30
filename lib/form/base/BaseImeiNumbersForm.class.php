<?php

/**
 * ImeiNumbers form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseImeiNumbersForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'imei_number' => new sfWidgetFormInput(),
      'name'        => new sfWidgetFormInput(),
      'used_status' => new sfWidgetFormInputCheckbox(),
      'comment'     => new sfWidgetFormInput(),
      'created_at'  => new sfWidgetFormDateTime(),
      'user_id'     => new sfWidgetFormPropelChoice(array('model' => 'AgentUser', 'add_empty' => true)),
      'company_id'  => new sfWidgetFormPropelChoice(array('model' => 'AgentCompany', 'add_empty' => true)),
      'reseller_id' => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
      'app_version' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorPropelChoice(array('model' => 'ImeiNumbers', 'column' => 'id', 'required' => false)),
      'imei_number' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'name'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'used_status' => new sfValidatorBoolean(array('required' => false)),
      'comment'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'user_id'     => new sfValidatorPropelChoice(array('model' => 'AgentUser', 'column' => 'id', 'required' => false)),
      'company_id'  => new sfValidatorPropelChoice(array('model' => 'AgentCompany', 'column' => 'id', 'required' => false)),
      'reseller_id' => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'id', 'required' => false)),
      'app_version' => new sfValidatorString(array('max_length' => 45, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('imei_numbers[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ImeiNumbers';
  }


}
