<?php

/**
 * AgentCompanyNetBalance form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseAgentCompanyNetBalanceForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'bill_start'  => new sfWidgetFormDateTime(),
      'bill_end'    => new sfWidgetFormDateTime(),
      'net_balance' => new sfWidgetFormInput(),
      'company_id'  => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorPropelChoice(array('model' => 'AgentCompanyNetBalance', 'column' => 'id', 'required' => false)),
      'bill_start'  => new sfValidatorDateTime(array('required' => false)),
      'bill_end'    => new sfValidatorDateTime(array('required' => false)),
      'net_balance' => new sfValidatorNumber(array('required' => false)),
      'company_id'  => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agent_company_net_balance[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgentCompanyNetBalance';
  }


}
