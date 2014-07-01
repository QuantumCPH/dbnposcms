<?php

/**
 * AgentPaymentHistory form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseAgentPaymentHistoryForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'agent_id'                   => new sfWidgetFormInput(),
      'customer_id'                => new sfWidgetFormInput(),
      'expenese_type'              => new sfWidgetFormInput(),
      'order_description'          => new sfWidgetFormInput(),
      'amount'                     => new sfWidgetFormInput(),
      'remaining_balance'          => new sfWidgetFormInput(),
      'created_at'                 => new sfWidgetFormDateTime(),
      'credit_limit'               => new sfWidgetFormInput(),
      'company_available_balance'  => new sfWidgetFormInput(),
      'company_actual_balance'     => new sfWidgetFormInput(),
      'commission'                 => new sfWidgetFormInput(),
      'debit'                      => new sfWidgetFormInput(),
      'credit'                     => new sfWidgetFormInput(),
      'transaction_id'             => new sfWidgetFormPropelChoice(array('model' => 'TopupTransactions', 'add_empty' => true)),
      'reseller_id'                => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
      'reseller_credit_limit'      => new sfWidgetFormInput(),
      'reseller_available_balance' => new sfWidgetFormInput(),
      'reseller_actual_balance'    => new sfWidgetFormInput(),
      'reseller_commission'        => new sfWidgetFormInput(),
      'reseller_credit'            => new sfWidgetFormInput(),
      'reseller_debit'             => new sfWidgetFormInput(),
      'transaction_from_id'        => new sfWidgetFormPropelChoice(array('model' => 'TransactionFrom', 'add_empty' => true)),
      'transaction_type_id'        => new sfWidgetFormPropelChoice(array('model' => 'TransactionType', 'add_empty' => true)),
      'transaction_section_id'     => new sfWidgetFormPropelChoice(array('model' => 'TransactionSection', 'add_empty' => true)),
      'description'                => new sfWidgetFormInput(),
      'comments'                   => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorPropelChoice(array('model' => 'AgentPaymentHistory', 'column' => 'id', 'required' => false)),
      'agent_id'                   => new sfValidatorInteger(),
      'customer_id'                => new sfValidatorInteger(),
      'expenese_type'              => new sfValidatorInteger(),
      'order_description'          => new sfValidatorInteger(array('required' => false)),
      'amount'                     => new sfValidatorNumber(),
      'remaining_balance'          => new sfValidatorNumber(),
      'created_at'                 => new sfValidatorDateTime(),
      'credit_limit'               => new sfValidatorNumber(array('required' => false)),
      'company_available_balance'  => new sfValidatorNumber(array('required' => false)),
      'company_actual_balance'     => new sfValidatorNumber(array('required' => false)),
      'commission'                 => new sfValidatorNumber(array('required' => false)),
      'debit'                      => new sfValidatorNumber(array('required' => false)),
      'credit'                     => new sfValidatorNumber(array('required' => false)),
      'transaction_id'             => new sfValidatorPropelChoice(array('model' => 'TopupTransactions', 'column' => 'id', 'required' => false)),
      'reseller_id'                => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'id', 'required' => false)),
      'reseller_credit_limit'      => new sfValidatorNumber(array('required' => false)),
      'reseller_available_balance' => new sfValidatorNumber(array('required' => false)),
      'reseller_actual_balance'    => new sfValidatorNumber(array('required' => false)),
      'reseller_commission'        => new sfValidatorNumber(array('required' => false)),
      'reseller_credit'            => new sfValidatorNumber(array('required' => false)),
      'reseller_debit'             => new sfValidatorNumber(array('required' => false)),
      'transaction_from_id'        => new sfValidatorPropelChoice(array('model' => 'TransactionFrom', 'column' => 'id', 'required' => false)),
      'transaction_type_id'        => new sfValidatorPropelChoice(array('model' => 'TransactionType', 'column' => 'id', 'required' => false)),
      'transaction_section_id'     => new sfValidatorPropelChoice(array('model' => 'TransactionSection', 'column' => 'sectionId', 'required' => false)),
      'description'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'comments'                   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agent_payment_history[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgentPaymentHistory';
  }


}
