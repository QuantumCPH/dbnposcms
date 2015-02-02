<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Shops filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseShopsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                           => new sfWidgetFormFilterInput(),
      'branch_number'                  => new sfWidgetFormFilterInput(),
      'company_number'                 => new sfWidgetFormFilterInput(),
      'is_configured'                  => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_by'                     => new sfWidgetFormFilterInput(),
      'created_at'                     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'configured_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'first_login'                    => new sfWidgetFormFilterInput(),
      'password'                       => new sfWidgetFormFilterInput(),
      'status_id'                      => new sfWidgetFormFilterInput(),
      'address'                        => new sfWidgetFormFilterInput(),
      'zip'                            => new sfWidgetFormFilterInput(),
      'place'                          => new sfWidgetFormFilterInput(),
      'country'                        => new sfWidgetFormFilterInput(),
      'tel'                            => new sfWidgetFormFilterInput(),
      'fax'                            => new sfWidgetFormFilterInput(),
      'item_sync_requested_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'item_sync_synced_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'pic_sync_requested_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'pic_sync_synced_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_by'                     => new sfWidgetFormFilterInput(),
      'pin'                            => new sfWidgetFormFilterInput(),
      'user_sync_requested_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'user_sync_synced_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'                     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'negative_sale'                  => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'language_id'                    => new sfWidgetFormPropelChoice(array('model' => 'Languages', 'add_empty' => true)),
      'time_out'                       => new sfWidgetFormFilterInput(),
      'start_value_sale_receipt'       => new sfWidgetFormFilterInput(),
      'start_value_return_receipt'     => new sfWidgetFormFilterInput(),
      'sale_receipt_format_id'         => new sfWidgetFormPropelChoice(array('model' => 'ReceiptFormats', 'add_empty' => true)),
      'return_receipt_format_id'       => new sfWidgetFormPropelChoice(array('model' => 'ReceiptFormats', 'add_empty' => true)),
      'shop_setting_sync_requested_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'shop_setting_sync_synced_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'voucher_sync_requested_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'voucher_sync_synced_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'max_day_end_attempts'           => new sfWidgetFormFilterInput(),
      'discount_type_id'               => new sfWidgetFormFilterInput(),
      'discount_value'                 => new sfWidgetFormFilterInput(),
      'gcm_key'                        => new sfWidgetFormFilterInput(),
      'receipt_header_position'        => new sfWidgetFormFilterInput(),
      'receipt_tax_statment_one'       => new sfWidgetFormFilterInput(),
      'receipt_tax_statment_two'       => new sfWidgetFormFilterInput(),
      'receipt_tax_statment_three'     => new sfWidgetFormFilterInput(),
      'receipt_auto_print'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'start_value_bookout'            => new sfWidgetFormFilterInput(),
      'bookout_format_id'              => new sfWidgetFormFilterInput(),
      'values'                         => new sfWidgetFormFilterInput(),
      'vat_value'                      => new sfWidgetFormFilterInput(),
      'currency_id'                    => new sfWidgetFormFilterInput(),
      'promotion_sync_requested_at'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'                           => new sfValidatorPass(array('required' => false)),
      'branch_number'                  => new sfValidatorPass(array('required' => false)),
      'company_number'                 => new sfValidatorPass(array('required' => false)),
      'is_configured'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_by'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'                     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'configured_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'first_login'                    => new sfValidatorPass(array('required' => false)),
      'password'                       => new sfValidatorPass(array('required' => false)),
      'status_id'                      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'address'                        => new sfValidatorPass(array('required' => false)),
      'zip'                            => new sfValidatorPass(array('required' => false)),
      'place'                          => new sfValidatorPass(array('required' => false)),
      'country'                        => new sfValidatorPass(array('required' => false)),
      'tel'                            => new sfValidatorPass(array('required' => false)),
      'fax'                            => new sfValidatorPass(array('required' => false)),
      'item_sync_requested_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'item_sync_synced_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'pic_sync_requested_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'pic_sync_synced_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'pin'                            => new sfValidatorPass(array('required' => false)),
      'user_sync_requested_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'user_sync_synced_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'                     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'negative_sale'                  => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'language_id'                    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Languages', 'column' => 'id')),
      'time_out'                       => new sfValidatorPass(array('required' => false)),
      'start_value_sale_receipt'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'start_value_return_receipt'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sale_receipt_format_id'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ReceiptFormats', 'column' => 'id')),
      'return_receipt_format_id'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ReceiptFormats', 'column' => 'id')),
      'shop_setting_sync_requested_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'shop_setting_sync_synced_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'voucher_sync_requested_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'voucher_sync_synced_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'max_day_end_attempts'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'discount_type_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'discount_value'                 => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'gcm_key'                        => new sfValidatorPass(array('required' => false)),
      'receipt_header_position'        => new sfValidatorPass(array('required' => false)),
      'receipt_tax_statment_one'       => new sfValidatorPass(array('required' => false)),
      'receipt_tax_statment_two'       => new sfValidatorPass(array('required' => false)),
      'receipt_tax_statment_three'     => new sfValidatorPass(array('required' => false)),
      'receipt_auto_print'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'start_value_bookout'            => new sfValidatorPass(array('required' => false)),
      'bookout_format_id'              => new sfValidatorPass(array('required' => false)),
      'values'                         => new sfValidatorPass(array('required' => false)),
      'vat_value'                      => new sfValidatorPass(array('required' => false)),
      'currency_id'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'promotion_sync_requested_at'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('shops_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Shops';
  }

  public function getFields()
  {
    return array(
      'id'                             => 'Number',
      'name'                           => 'Text',
      'branch_number'                  => 'Text',
      'company_number'                 => 'Text',
      'is_configured'                  => 'Boolean',
      'created_by'                     => 'Number',
      'created_at'                     => 'Date',
      'configured_at'                  => 'Date',
      'first_login'                    => 'Text',
      'password'                       => 'Text',
      'status_id'                      => 'Number',
      'address'                        => 'Text',
      'zip'                            => 'Text',
      'place'                          => 'Text',
      'country'                        => 'Text',
      'tel'                            => 'Text',
      'fax'                            => 'Text',
      'item_sync_requested_at'         => 'Date',
      'item_sync_synced_at'            => 'Date',
      'pic_sync_requested_at'          => 'Date',
      'pic_sync_synced_at'             => 'Date',
      'updated_by'                     => 'Number',
      'pin'                            => 'Text',
      'user_sync_requested_at'         => 'Date',
      'user_sync_synced_at'            => 'Date',
      'updated_at'                     => 'Date',
      'negative_sale'                  => 'Boolean',
      'language_id'                    => 'ForeignKey',
      'time_out'                       => 'Text',
      'start_value_sale_receipt'       => 'Number',
      'start_value_return_receipt'     => 'Number',
      'sale_receipt_format_id'         => 'ForeignKey',
      'return_receipt_format_id'       => 'ForeignKey',
      'shop_setting_sync_requested_at' => 'Date',
      'shop_setting_sync_synced_at'    => 'Date',
      'voucher_sync_requested_at'      => 'Date',
      'voucher_sync_synced_at'         => 'Date',
      'max_day_end_attempts'           => 'Number',
      'discount_type_id'               => 'Number',
      'discount_value'                 => 'Number',
      'gcm_key'                        => 'Text',
      'receipt_header_position'        => 'Text',
      'receipt_tax_statment_one'       => 'Text',
      'receipt_tax_statment_two'       => 'Text',
      'receipt_tax_statment_three'     => 'Text',
      'receipt_auto_print'             => 'Boolean',
      'start_value_bookout'            => 'Text',
      'bookout_format_id'              => 'Text',
      'values'                         => 'Text',
      'vat_value'                      => 'Text',
      'currency_id'                    => 'Number',
      'promotion_sync_requested_at'    => 'Text',
    );
  }
}
