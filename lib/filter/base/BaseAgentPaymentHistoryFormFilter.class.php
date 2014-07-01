<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * AgentPaymentHistory filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseAgentPaymentHistoryFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'agent_id'                   => new sfWidgetFormFilterInput(),
      'customer_id'                => new sfWidgetFormFilterInput(),
      'expenese_type'              => new sfWidgetFormFilterInput(),
      'order_description'          => new sfWidgetFormFilterInput(),
      'amount'                     => new sfWidgetFormFilterInput(),
      'remaining_balance'          => new sfWidgetFormFilterInput(),
      'created_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'credit_limit'               => new sfWidgetFormFilterInput(),
      'company_available_balance'  => new sfWidgetFormFilterInput(),
      'company_actual_balance'     => new sfWidgetFormFilterInput(),
      'commission'                 => new sfWidgetFormFilterInput(),
      'debit'                      => new sfWidgetFormFilterInput(),
      'credit'                     => new sfWidgetFormFilterInput(),
      'transaction_id'             => new sfWidgetFormPropelChoice(array('model' => 'TopupTransactions', 'add_empty' => true)),
      'reseller_id'                => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
      'reseller_credit_limit'      => new sfWidgetFormFilterInput(),
      'reseller_available_balance' => new sfWidgetFormFilterInput(),
      'reseller_actual_balance'    => new sfWidgetFormFilterInput(),
      'reseller_commission'        => new sfWidgetFormFilterInput(),
      'reseller_credit'            => new sfWidgetFormFilterInput(),
      'reseller_debit'             => new sfWidgetFormFilterInput(),
      'transaction_from_id'        => new sfWidgetFormPropelChoice(array('model' => 'TransactionFrom', 'add_empty' => true)),
      'transaction_type_id'        => new sfWidgetFormPropelChoice(array('model' => 'TransactionType', 'add_empty' => true)),
      'transaction_section_id'     => new sfWidgetFormPropelChoice(array('model' => 'TransactionSection', 'add_empty' => true)),
      'description'                => new sfWidgetFormFilterInput(),
      'comments'                   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'agent_id'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'customer_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'expenese_type'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'order_description'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'amount'                     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'remaining_balance'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'created_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'credit_limit'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'company_available_balance'  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'company_actual_balance'     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'commission'                 => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'debit'                      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'credit'                     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'transaction_id'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TopupTransactions', 'column' => 'id')),
      'reseller_id'                => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Reseller', 'column' => 'id')),
      'reseller_credit_limit'      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'reseller_available_balance' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'reseller_actual_balance'    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'reseller_commission'        => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'reseller_credit'            => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'reseller_debit'             => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'transaction_from_id'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TransactionFrom', 'column' => 'id')),
      'transaction_type_id'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TransactionType', 'column' => 'id')),
      'transaction_section_id'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TransactionSection', 'column' => 'sectionId')),
      'description'                => new sfValidatorPass(array('required' => false)),
      'comments'                   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agent_payment_history_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgentPaymentHistory';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'agent_id'                   => 'Number',
      'customer_id'                => 'Number',
      'expenese_type'              => 'Number',
      'order_description'          => 'Number',
      'amount'                     => 'Number',
      'remaining_balance'          => 'Number',
      'created_at'                 => 'Date',
      'credit_limit'               => 'Number',
      'company_available_balance'  => 'Number',
      'company_actual_balance'     => 'Number',
      'commission'                 => 'Number',
      'debit'                      => 'Number',
      'credit'                     => 'Number',
      'transaction_id'             => 'ForeignKey',
      'reseller_id'                => 'ForeignKey',
      'reseller_credit_limit'      => 'Number',
      'reseller_available_balance' => 'Number',
      'reseller_actual_balance'    => 'Number',
      'reseller_commission'        => 'Number',
      'reseller_credit'            => 'Number',
      'reseller_debit'             => 'Number',
      'transaction_from_id'        => 'ForeignKey',
      'transaction_type_id'        => 'ForeignKey',
      'transaction_section_id'     => 'ForeignKey',
      'description'                => 'Text',
      'comments'                   => 'Text',
    );
  }
}
