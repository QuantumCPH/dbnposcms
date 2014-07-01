<?php

/**
 * AgentUser form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseAgentUserForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'agent_company_id'   => new sfWidgetFormPropelChoice(array('model' => 'AgentCompany', 'add_empty' => false)),
      'username'           => new sfWidgetFormInput(),
      'password'           => new sfWidgetFormInput(),
      'status_id'          => new sfWidgetFormPropelChoice(array('model' => 'Status', 'add_empty' => false)),
      'created_at'         => new sfWidgetFormDateTime(),
      'comments'           => new sfWidgetFormTextarea(),
      'unique_id'          => new sfWidgetFormInput(),
      'pin_code'           => new sfWidgetFormInput(),
      'imei_number_id'     => new sfWidgetFormPropelChoice(array('model' => 'ImeiNumbers', 'add_empty' => true)),
      'agent_user_role_id' => new sfWidgetFormPropelChoice(array('model' => 'AgentUserRole', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorPropelChoice(array('model' => 'AgentUser', 'column' => 'id', 'required' => false)),
      'agent_company_id'   => new sfValidatorPropelChoice(array('model' => 'AgentCompany', 'column' => 'id')),
      'username'           => new sfValidatorString(array('max_length' => 150)),
      'password'           => new sfValidatorString(array('max_length' => 150)),
      'status_id'          => new sfValidatorPropelChoice(array('model' => 'Status', 'column' => 'id')),
      'created_at'         => new sfValidatorDateTime(array('required' => false)),
      'comments'           => new sfValidatorString(array('required' => false)),
      'unique_id'          => new sfValidatorString(array('max_length' => 10)),
      'pin_code'           => new sfValidatorString(array('max_length' => 255)),
      'imei_number_id'     => new sfValidatorPropelChoice(array('model' => 'ImeiNumbers', 'column' => 'id', 'required' => false)),
      'agent_user_role_id' => new sfValidatorPropelChoice(array('model' => 'AgentUserRole', 'column' => 'id', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agent_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgentUser';
  }


}
