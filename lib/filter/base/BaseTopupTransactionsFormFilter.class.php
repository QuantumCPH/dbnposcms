<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * TopupTransactions filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseTopupTransactionsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'agent_company_id'         => new sfWidgetFormFilterInput(),
      'agent_user_id'            => new sfWidgetFormFilterInput(),
      'vendor_id'                => new sfWidgetFormFilterInput(),
      'vender_name'              => new sfWidgetFormFilterInput(),
      'product_id'               => new sfWidgetFormFilterInput(),
      'product_name'             => new sfWidgetFormFilterInput(),
      'product_vat'              => new sfWidgetFormFilterInput(),
      'product_price'            => new sfWidgetFormFilterInput(),
      'customer_mobile_number'   => new sfWidgetFormFilterInput(),
      'created_at'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'agent_email'              => new sfWidgetFormFilterInput(),
      'agent_company_name'       => new sfWidgetFormFilterInput(),
      'agent_user_name'          => new sfWidgetFormFilterInput(),
      'agent_commission'         => new sfWidgetFormFilterInput(),
      'product_registration_fee' => new sfWidgetFormFilterInput(),
      'status'                   => new sfWidgetFormFilterInput(),
      'card_number'              => new sfWidgetFormFilterInput(),
      'receipt_no'               => new sfWidgetFormFilterInput(),
      'card_type_id'             => new sfWidgetFormPropelChoice(array('model' => 'CardTypes', 'add_empty' => true)),
      'vat_on_commission'        => new sfWidgetFormFilterInput(),
      'transaction_from_id'      => new sfWidgetFormPropelChoice(array('model' => 'TransactionFrom', 'add_empty' => true)),
      'reseller_id'              => new sfWidgetFormFilterInput(),
      'reseller_name'            => new sfWidgetFormFilterInput(),
      'reseller_email'           => new sfWidgetFormFilterInput(),
      'reseller_contact_number'  => new sfWidgetFormFilterInput(),
      'dibs_call_id'             => new sfWidgetFormPropelChoice(array('model' => 'DibsCall', 'add_empty' => true)),
      'reseller_commission'      => new sfWidgetFormFilterInput(),
      'reseller_vat_commission'  => new sfWidgetFormFilterInput(),
      'imei_number_id'           => new sfWidgetFormFilterInput(),
      'imei_number'              => new sfWidgetFormFilterInput(),
      'card_purchase_price'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'agent_company_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'agent_user_id'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'vendor_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'vender_name'              => new sfValidatorPass(array('required' => false)),
      'product_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product_name'             => new sfValidatorPass(array('required' => false)),
      'product_vat'              => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'product_price'            => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'customer_mobile_number'   => new sfValidatorPass(array('required' => false)),
      'created_at'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'agent_email'              => new sfValidatorPass(array('required' => false)),
      'agent_company_name'       => new sfValidatorPass(array('required' => false)),
      'agent_user_name'          => new sfValidatorPass(array('required' => false)),
      'agent_commission'         => new sfValidatorPass(array('required' => false)),
      'product_registration_fee' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'status'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'card_number'              => new sfValidatorPass(array('required' => false)),
      'receipt_no'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'card_type_id'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CardTypes', 'column' => 'id')),
      'vat_on_commission'        => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'transaction_from_id'      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TransactionFrom', 'column' => 'id')),
      'reseller_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'reseller_name'            => new sfValidatorPass(array('required' => false)),
      'reseller_email'           => new sfValidatorPass(array('required' => false)),
      'reseller_contact_number'  => new sfValidatorPass(array('required' => false)),
      'dibs_call_id'             => new sfValidatorPropelChoice(array('required' => false, 'model' => 'DibsCall', 'column' => 'id')),
      'reseller_commission'      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'reseller_vat_commission'  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'imei_number_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'imei_number'              => new sfValidatorPass(array('required' => false)),
      'card_purchase_price'      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('topup_transactions_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'TopupTransactions';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'agent_company_id'         => 'Number',
      'agent_user_id'            => 'Number',
      'vendor_id'                => 'Number',
      'vender_name'              => 'Text',
      'product_id'               => 'Number',
      'product_name'             => 'Text',
      'product_vat'              => 'Number',
      'product_price'            => 'Number',
      'customer_mobile_number'   => 'Text',
      'created_at'               => 'Date',
      'agent_email'              => 'Text',
      'agent_company_name'       => 'Text',
      'agent_user_name'          => 'Text',
      'agent_commission'         => 'Text',
      'product_registration_fee' => 'Number',
      'status'                   => 'Number',
      'card_number'              => 'Text',
      'receipt_no'               => 'Number',
      'card_type_id'             => 'ForeignKey',
      'vat_on_commission'        => 'Number',
      'transaction_from_id'      => 'ForeignKey',
      'reseller_id'              => 'Number',
      'reseller_name'            => 'Text',
      'reseller_email'           => 'Text',
      'reseller_contact_number'  => 'Text',
      'dibs_call_id'             => 'ForeignKey',
      'reseller_commission'      => 'Number',
      'reseller_vat_commission'  => 'Number',
      'imei_number_id'           => 'Number',
      'imei_number'              => 'Text',
      'card_purchase_price'      => 'Number',
    );
  }
}
