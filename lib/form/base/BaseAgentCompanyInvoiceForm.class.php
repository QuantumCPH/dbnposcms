<?php

/**
 * AgentCompanyInvoice form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseAgentCompanyInvoiceForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'invoice_number'        => new sfWidgetFormInput(),
      'agent_company_id'      => new sfWidgetFormPropelChoice(array('model' => 'AgentCompany', 'add_empty' => false)),
      'totalPayment'          => new sfWidgetFormInput(),
      'total_payable_balance' => new sfWidgetFormInput(),
      'current_bill'          => new sfWidgetFormInput(),
      'due_date'              => new sfWidgetFormDateTime(),
      'created_at'            => new sfWidgetFormDateTime(),
      'billing_starting_date' => new sfWidgetFormDateTime(),
      'billing_ending_date'   => new sfWidgetFormDateTime(),
      'invoice_cost'          => new sfWidgetFormInput(),
      'moms'                  => new sfWidgetFormInput(),
      'invoice_status_id'     => new sfWidgetFormPropelChoice(array('model' => 'InvoiceStatus', 'add_empty' => false)),
      'start_time'            => new sfWidgetFormInput(),
      'end_time'              => new sfWidgetFormInput(),
      'reseller_id'           => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
      'is_prepaid'            => new sfWidgetFormInputCheckbox(),
      'manual_invoice_number' => new sfWidgetFormInput(),
      'pdf_file'              => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorPropelChoice(array('model' => 'AgentCompanyInvoice', 'column' => 'id', 'required' => false)),
      'invoice_number'        => new sfValidatorNumber(array('required' => false)),
      'agent_company_id'      => new sfValidatorPropelChoice(array('model' => 'AgentCompany', 'column' => 'id')),
      'totalPayment'          => new sfValidatorNumber(),
      'total_payable_balance' => new sfValidatorNumber(array('required' => false)),
      'current_bill'          => new sfValidatorNumber(array('required' => false)),
      'due_date'              => new sfValidatorDateTime(),
      'created_at'            => new sfValidatorDateTime(),
      'billing_starting_date' => new sfValidatorDateTime(),
      'billing_ending_date'   => new sfValidatorDateTime(),
      'invoice_cost'          => new sfValidatorInteger(array('required' => false)),
      'moms'                  => new sfValidatorNumber(array('required' => false)),
      'invoice_status_id'     => new sfValidatorPropelChoice(array('model' => 'InvoiceStatus', 'column' => 'id')),
      'start_time'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'end_time'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'reseller_id'           => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'id', 'required' => false)),
      'is_prepaid'            => new sfValidatorBoolean(array('required' => false)),
      'manual_invoice_number' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'pdf_file'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agent_company_invoice[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgentCompanyInvoice';
  }


}
