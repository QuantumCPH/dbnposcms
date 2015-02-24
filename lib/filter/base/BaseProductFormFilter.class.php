<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Product filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseProductFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                                 => new sfWidgetFormFilterInput(),
      'price'                                => new sfWidgetFormFilterInput(),
      'description'                          => new sfWidgetFormFilterInput(),
      'initial_balance'                      => new sfWidgetFormFilterInput(),
      'registration_fee'                     => new sfWidgetFormFilterInput(),
      'subscription_fee'                     => new sfWidgetFormFilterInput(),
      'created_at'                           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'is_extra_fill_applied'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'include_in_zerocall'                  => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_in_store'                          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'sms_code'                             => new sfWidgetFormFilterInput(),
      'country'                              => new sfWidgetFormFilterInput(),
      'refill'                               => new sfWidgetFormFilterInput(),
      'country_id'                           => new sfWidgetFormFilterInput(),
      'refill_options'                       => new sfWidgetFormFilterInput(),
      'product_order'                        => new sfWidgetFormFilterInput(),
      'product_type_package'                 => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'product_country_us'                   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'billing_product_id'                   => new sfWidgetFormFilterInput(),
      'is_in_b2b'                            => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'product_type_id'                      => new sfWidgetFormPropelChoice(array('model' => 'ProductType', 'add_empty' => true)),
      'bonus'                                => new sfWidgetFormFilterInput(),
      'sim_type_id'                          => new sfWidgetFormFilterInput(),
      'vendor_id'                            => new sfWidgetFormPropelChoice(array('model' => 'Vendors', 'add_empty' => true)),
      'vat'                                  => new sfWidgetFormFilterInput(),
      'package_description'                  => new sfWidgetFormFilterInput(),
      'vending_machine_id'                   => new sfWidgetFormFilterInput(),
      'is_discount_in_percentage'            => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'discount'                             => new sfWidgetFormFilterInput(),
      'status_id'                            => new sfWidgetFormPropelChoice(array('model' => 'Status', 'add_empty' => true)),
      'commission_value'                     => new sfWidgetFormFilterInput(),
      'commission_value_percentage'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'reseller_commission_value'            => new sfWidgetFormFilterInput(),
      'reseller_commission_value_percentage' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'card_purchase_price'                  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'                                 => new sfValidatorPass(array('required' => false)),
      'price'                                => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'description'                          => new sfValidatorPass(array('required' => false)),
      'initial_balance'                      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'registration_fee'                     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'subscription_fee'                     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'created_at'                           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'                           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'is_extra_fill_applied'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'include_in_zerocall'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_in_store'                          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'sms_code'                             => new sfValidatorPass(array('required' => false)),
      'country'                              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'refill'                               => new sfValidatorPass(array('required' => false)),
      'country_id'                           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'refill_options'                       => new sfValidatorPass(array('required' => false)),
      'product_order'                        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product_type_package'                 => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'product_country_us'                   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'billing_product_id'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_in_b2b'                            => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'product_type_id'                      => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ProductType', 'column' => 'id')),
      'bonus'                                => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'sim_type_id'                          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'vendor_id'                            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Vendors', 'column' => 'id')),
      'vat'                                  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'package_description'                  => new sfValidatorPass(array('required' => false)),
      'vending_machine_id'                   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_discount_in_percentage'            => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'discount'                             => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'status_id'                            => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Status', 'column' => 'id')),
      'commission_value'                     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'commission_value_percentage'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'reseller_commission_value'            => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'reseller_commission_value_percentage' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'card_purchase_price'                  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('product_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Product';
  }

  public function getFields()
  {
    return array(
      'id'                                   => 'Number',
      'name'                                 => 'Text',
      'price'                                => 'Number',
      'description'                          => 'Text',
      'initial_balance'                      => 'Number',
      'registration_fee'                     => 'Number',
      'subscription_fee'                     => 'Number',
      'created_at'                           => 'Date',
      'updated_at'                           => 'Date',
      'is_extra_fill_applied'                => 'Boolean',
      'include_in_zerocall'                  => 'Boolean',
      'is_in_store'                          => 'Boolean',
      'sms_code'                             => 'Text',
      'country'                              => 'Number',
      'refill'                               => 'Text',
      'country_id'                           => 'Number',
      'refill_options'                       => 'Text',
      'product_order'                        => 'Number',
      'product_type_package'                 => 'Boolean',
      'product_country_us'                   => 'Boolean',
      'billing_product_id'                   => 'Number',
      'is_in_b2b'                            => 'Boolean',
      'product_type_id'                      => 'ForeignKey',
      'bonus'                                => 'Number',
      'sim_type_id'                          => 'Number',
      'vendor_id'                            => 'ForeignKey',
      'vat'                                  => 'Number',
      'package_description'                  => 'Text',
      'vending_machine_id'                   => 'Number',
      'is_discount_in_percentage'            => 'Boolean',
      'discount'                             => 'Number',
      'status_id'                            => 'ForeignKey',
      'commission_value'                     => 'Number',
      'commission_value_percentage'          => 'Boolean',
      'reseller_commission_value'            => 'Number',
      'reseller_commission_value_percentage' => 'Boolean',
      'card_purchase_price'                  => 'Number',
    );
  }
}
