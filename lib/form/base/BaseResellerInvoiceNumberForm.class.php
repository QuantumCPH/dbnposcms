<?php

/**
 * ResellerInvoiceNumber form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseResellerInvoiceNumberForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'invoice_type'        => new sfWidgetFormInput(),
      'reseller_invoice_id' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorPropelChoice(array('model' => 'ResellerInvoiceNumber', 'column' => 'id', 'required' => false)),
      'invoice_type'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'reseller_invoice_id' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('reseller_invoice_number[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ResellerInvoiceNumber';
  }


}
