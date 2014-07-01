<?php

/**
 * TopupTransactions form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseTopupTransactionsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'agent_company_id'         => new sfWidgetFormInput(),
      'agent_user_id'            => new sfWidgetFormInput(),
      'vendor_id'                => new sfWidgetFormInput(),
      'vender_name'              => new sfWidgetFormInput(),
      'product_id'               => new sfWidgetFormInput(),
      'product_name'             => new sfWidgetFormInput(),
      'product_vat'              => new sfWidgetFormInput(),
      'product_price'            => new sfWidgetFormInput(),
      'customer_mobile_number'   => new sfWidgetFormInput(),
      'created_at'               => new sfWidgetFormDateTime(),
      'agent_email'              => new sfWidgetFormInput(),
      'agent_company_name'       => new sfWidgetFormInput(),
      'agent_user_name'          => new sfWidgetFormInput(),
      'agent_commission'         => new sfWidgetFormInput(),
      'product_registration_fee' => new sfWidgetFormInput(),
      'status'                   => new sfWidgetFormInput(),
      'card_number'              => new sfWidgetFormInput(),
      'receipt_no'               => new sfWidgetFormInput(),
      'card_type_id'             => new sfWidgetFormPropelChoice(array('model' => 'CardTypes', 'add_empty' => true)),
      'vat_on_commission'        => new sfWidgetFormInput(),
      'transaction_from_id'      => new sfWidgetFormPropelChoice(array('model' => 'TransactionFrom', 'add_empty' => true)),
      'reseller_id'              => new sfWidgetFormInput(),
      'reseller_name'            => new sfWidgetFormInput(),
      'reseller_email'           => new sfWidgetFormInput(),
      'reseller_contact_number'  => new sfWidgetFormInput(),
      'dibs_call_id'             => new sfWidgetFormPropelChoice(array('model' => 'DibsCall', 'add_empty' => true)),
      'reseller_commission'      => new sfWidgetFormInput(),
      'reseller_vat_commission'  => new sfWidgetFormInput(),
      'imei_number_id'           => new sfWidgetFormInput(),
      'imei_number'              => new sfWidgetFormInput(),
      'card_purchase_price'      => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorPropelChoice(array('model' => 'TopupTransactions', 'column' => 'id', 'required' => false)),
      'agent_company_id'         => new sfValidatorInteger(array('required' => false)),
      'agent_user_id'            => new sfValidatorInteger(array('required' => false)),
      'vendor_id'                => new sfValidatorInteger(array('required' => false)),
      'vender_name'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'product_id'               => new sfValidatorInteger(array('required' => false)),
      'product_name'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'product_vat'              => new sfValidatorNumber(array('required' => false)),
      'product_price'            => new sfValidatorNumber(array('required' => false)),
      'customer_mobile_number'   => new sfValidatorString(array('max_length' => 25, 'required' => false)),
      'created_at'               => new sfValidatorDateTime(),
      'agent_email'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'agent_company_name'       => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'agent_user_name'          => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'agent_commission'         => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'product_registration_fee' => new sfValidatorNumber(array('required' => false)),
      'status'                   => new sfValidatorInteger(array('required' => false)),
      'card_number'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'receipt_no'               => new sfValidatorInteger(array('required' => false)),
      'card_type_id'             => new sfValidatorPropelChoice(array('model' => 'CardTypes', 'column' => 'id', 'required' => false)),
      'vat_on_commission'        => new sfValidatorNumber(array('required' => false)),
      'transaction_from_id'      => new sfValidatorPropelChoice(array('model' => 'TransactionFrom', 'column' => 'id', 'required' => false)),
      'reseller_id'              => new sfValidatorInteger(array('required' => false)),
      'reseller_name'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'reseller_email'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'reseller_contact_number'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'dibs_call_id'             => new sfValidatorPropelChoice(array('model' => 'DibsCall', 'column' => 'id', 'required' => false)),
      'reseller_commission'      => new sfValidatorNumber(array('required' => false)),
      'reseller_vat_commission'  => new sfValidatorNumber(array('required' => false)),
      'imei_number_id'           => new sfValidatorInteger(array('required' => false)),
      'imei_number'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'card_purchase_price'      => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('topup_transactions[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TopupTransactions';
  }


}
