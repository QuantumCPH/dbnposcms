<?php

/**
 * Product form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseProductForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                   => new sfWidgetFormInputHidden(),
      'name'                                 => new sfWidgetFormInput(),
      'price'                                => new sfWidgetFormInput(),
      'description'                          => new sfWidgetFormInput(),
      'initial_balance'                      => new sfWidgetFormInput(),
      'registration_fee'                     => new sfWidgetFormInput(),
      'subscription_fee'                     => new sfWidgetFormInput(),
      'created_at'                           => new sfWidgetFormDateTime(),
      'updated_at'                           => new sfWidgetFormDateTime(),
      'is_extra_fill_applied'                => new sfWidgetFormInputCheckbox(),
      'include_in_zerocall'                  => new sfWidgetFormInputCheckbox(),
      'is_in_store'                          => new sfWidgetFormInputCheckbox(),
      'sms_code'                             => new sfWidgetFormInput(),
      'country'                              => new sfWidgetFormInput(),
      'refill'                               => new sfWidgetFormInput(),
      'country_id'                           => new sfWidgetFormInput(),
      'refill_options'                       => new sfWidgetFormInput(),
      'product_order'                        => new sfWidgetFormInput(),
      'product_type_package'                 => new sfWidgetFormInputCheckbox(),
      'product_country_us'                   => new sfWidgetFormInputCheckbox(),
      'billing_product_id'                   => new sfWidgetFormInput(),
      'is_in_b2b'                            => new sfWidgetFormInputCheckbox(),
      'product_type_id'                      => new sfWidgetFormPropelChoice(array('model' => 'ProductType', 'add_empty' => false)),
      'bonus'                                => new sfWidgetFormInput(),
      'sim_type_id'                          => new sfWidgetFormInput(),
      'vendor_id'                            => new sfWidgetFormPropelChoice(array('model' => 'Vendors', 'add_empty' => false)),
      'vat'                                  => new sfWidgetFormInput(),
      'package_description'                  => new sfWidgetFormTextarea(),
      'vending_machine_id'                   => new sfWidgetFormInput(),
      'is_discount_in_percentage'            => new sfWidgetFormInputCheckbox(),
      'discount'                             => new sfWidgetFormInput(),
      'status_id'                            => new sfWidgetFormPropelChoice(array('model' => 'Status', 'add_empty' => true)),
      'commission_value'                     => new sfWidgetFormInput(),
      'commission_value_percentage'          => new sfWidgetFormInputCheckbox(),
      'reseller_commission_value'            => new sfWidgetFormInput(),
      'reseller_commission_value_percentage' => new sfWidgetFormInputCheckbox(),
      'card_purchase_price'                  => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                                   => new sfValidatorPropelChoice(array('model' => 'Product', 'column' => 'id', 'required' => false)),
      'name'                                 => new sfValidatorString(array('max_length' => 50)),
      'price'                                => new sfValidatorNumber(array('required' => false)),
      'description'                          => new sfValidatorString(array('max_length' => 200)),
      'initial_balance'                      => new sfValidatorNumber(array('required' => false)),
      'registration_fee'                     => new sfValidatorNumber(),
      'subscription_fee'                     => new sfValidatorNumber(array('required' => false)),
      'created_at'                           => new sfValidatorDateTime(),
      'updated_at'                           => new sfValidatorDateTime(array('required' => false)),
      'is_extra_fill_applied'                => new sfValidatorBoolean(array('required' => false)),
      'include_in_zerocall'                  => new sfValidatorBoolean(array('required' => false)),
      'is_in_store'                          => new sfValidatorBoolean(array('required' => false)),
      'sms_code'                             => new sfValidatorString(array('max_length' => 2, 'required' => false)),
      'country'                              => new sfValidatorInteger(array('required' => false)),
      'refill'                               => new sfValidatorString(array('max_length' => 400, 'required' => false)),
      'country_id'                           => new sfValidatorInteger(array('required' => false)),
      'refill_options'                       => new sfValidatorString(array('max_length' => 400, 'required' => false)),
      'product_order'                        => new sfValidatorInteger(array('required' => false)),
      'product_type_package'                 => new sfValidatorBoolean(array('required' => false)),
      'product_country_us'                   => new sfValidatorBoolean(array('required' => false)),
      'billing_product_id'                   => new sfValidatorInteger(array('required' => false)),
      'is_in_b2b'                            => new sfValidatorBoolean(array('required' => false)),
      'product_type_id'                      => new sfValidatorPropelChoice(array('model' => 'ProductType', 'column' => 'id')),
      'bonus'                                => new sfValidatorNumber(array('required' => false)),
      'sim_type_id'                          => new sfValidatorInteger(array('required' => false)),
      'vendor_id'                            => new sfValidatorPropelChoice(array('model' => 'Vendors', 'column' => 'id')),
      'vat'                                  => new sfValidatorNumber(),
      'package_description'                  => new sfValidatorString(array('required' => false)),
      'vending_machine_id'                   => new sfValidatorInteger(array('required' => false)),
      'is_discount_in_percentage'            => new sfValidatorBoolean(array('required' => false)),
      'discount'                             => new sfValidatorNumber(array('required' => false)),
      'status_id'                            => new sfValidatorPropelChoice(array('model' => 'Status', 'column' => 'id', 'required' => false)),
      'commission_value'                     => new sfValidatorNumber(array('required' => false)),
      'commission_value_percentage'          => new sfValidatorBoolean(array('required' => false)),
      'reseller_commission_value'            => new sfValidatorNumber(array('required' => false)),
      'reseller_commission_value_percentage' => new sfValidatorBoolean(array('required' => false)),
      'card_purchase_price'                  => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Product';
  }


}
