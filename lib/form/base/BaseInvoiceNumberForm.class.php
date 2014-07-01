<?php

/**
 * InvoiceNumber form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseInvoiceNumberForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'invoice_type'             => new sfWidgetFormInput(),
      'agent_company_invoice_id' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorPropelChoice(array('model' => 'InvoiceNumber', 'column' => 'id', 'required' => false)),
      'invoice_type'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'agent_company_invoice_id' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('invoice_number[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'InvoiceNumber';
  }


}
