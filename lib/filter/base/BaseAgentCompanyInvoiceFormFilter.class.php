<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * AgentCompanyInvoice filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseAgentCompanyInvoiceFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'invoice_number'        => new sfWidgetFormFilterInput(),
      'agent_company_id'      => new sfWidgetFormPropelChoice(array('model' => 'AgentCompany', 'add_empty' => true)),
      'totalPayment'          => new sfWidgetFormFilterInput(),
      'total_payable_balance' => new sfWidgetFormFilterInput(),
      'current_bill'          => new sfWidgetFormFilterInput(),
      'due_date'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'billing_starting_date' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'billing_ending_date'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'invoice_cost'          => new sfWidgetFormFilterInput(),
      'moms'                  => new sfWidgetFormFilterInput(),
      'invoice_status_id'     => new sfWidgetFormPropelChoice(array('model' => 'InvoiceStatus', 'add_empty' => true)),
      'start_time'            => new sfWidgetFormFilterInput(),
      'end_time'              => new sfWidgetFormFilterInput(),
      'reseller_id'           => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
      'is_prepaid'            => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'manual_invoice_number' => new sfWidgetFormFilterInput(),
      'pdf_file'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'invoice_number'        => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'agent_company_id'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AgentCompany', 'column' => 'id')),
      'totalPayment'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'total_payable_balance' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'current_bill'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'due_date'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'billing_starting_date' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'billing_ending_date'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'invoice_cost'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'moms'                  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'invoice_status_id'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'InvoiceStatus', 'column' => 'id')),
      'start_time'            => new sfValidatorPass(array('required' => false)),
      'end_time'              => new sfValidatorPass(array('required' => false)),
      'reseller_id'           => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Reseller', 'column' => 'id')),
      'is_prepaid'            => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'manual_invoice_number' => new sfValidatorPass(array('required' => false)),
      'pdf_file'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agent_company_invoice_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgentCompanyInvoice';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'invoice_number'        => 'Number',
      'agent_company_id'      => 'ForeignKey',
      'totalPayment'          => 'Number',
      'total_payable_balance' => 'Number',
      'current_bill'          => 'Number',
      'due_date'              => 'Date',
      'created_at'            => 'Date',
      'billing_starting_date' => 'Date',
      'billing_ending_date'   => 'Date',
      'invoice_cost'          => 'Number',
      'moms'                  => 'Number',
      'invoice_status_id'     => 'ForeignKey',
      'start_time'            => 'Text',
      'end_time'              => 'Text',
      'reseller_id'           => 'ForeignKey',
      'is_prepaid'            => 'Boolean',
      'manual_invoice_number' => 'Text',
      'pdf_file'              => 'Text',
    );
  }
}
