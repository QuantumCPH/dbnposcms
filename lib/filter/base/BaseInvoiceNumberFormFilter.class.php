<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * InvoiceNumber filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseInvoiceNumberFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'invoice_type'             => new sfWidgetFormFilterInput(),
      'agent_company_invoice_id' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'invoice_type'             => new sfValidatorPass(array('required' => false)),
      'agent_company_invoice_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('invoice_number_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'InvoiceNumber';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'invoice_type'             => 'Text',
      'agent_company_invoice_id' => 'Number',
    );
  }
}
