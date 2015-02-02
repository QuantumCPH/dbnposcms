<?php

/**
 * Shops form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseShopsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                             => new sfWidgetFormInputHidden(),
      'name'                           => new sfWidgetFormInput(),
      'branch_number'                  => new sfWidgetFormInput(),
      'company_number'                 => new sfWidgetFormInput(),
      'is_configured'                  => new sfWidgetFormInput(),
      'created_by'                     => new sfWidgetFormInput(),
      'created_at'                     => new sfWidgetFormDateTime(),
      'configured_at'                  => new sfWidgetFormDateTime(),
      'first_login'                    => new sfWidgetFormInput(),
      'password'                       => new sfWidgetFormInput(),
      'status_id'                      => new sfWidgetFormInput(),
      'address'                        => new sfWidgetFormTextarea(),
      'zip'                            => new sfWidgetFormInput(),
      'place'                          => new sfWidgetFormTextarea(),
      'country'                        => new sfWidgetFormInput(),
      'tel'                            => new sfWidgetFormInput(),
      'fax'                            => new sfWidgetFormInput(),
      'item_sync_requested_at'         => new sfWidgetFormDateTime(),
      'item_sync_synced_at'            => new sfWidgetFormDateTime(),
      'pic_sync_requested_at'          => new sfWidgetFormDateTime(),
      'pic_sync_synced_at'             => new sfWidgetFormDateTime(),
      'updated_by'                     => new sfWidgetFormInput(),
      'pin'                            => new sfWidgetFormInput(),
      'user_sync_requested_at'         => new sfWidgetFormDateTime(),
      'user_sync_synced_at'            => new sfWidgetFormDateTime(),
      'updated_at'                     => new sfWidgetFormDateTime(),
      'negative_sale'                  => new sfWidgetFormInput(),
      'language_id'                    => new sfWidgetFormPropelChoice(array('model' => 'Languages', 'add_empty' => true)),
      'time_out'                       => new sfWidgetFormInput(),
      'start_value_sale_receipt'       => new sfWidgetFormInput(),
      'start_value_return_receipt'     => new sfWidgetFormInput(),
      'sale_receipt_format_id'         => new sfWidgetFormPropelChoice(array('model' => 'ReceiptFormats', 'add_empty' => true)),
      'return_receipt_format_id'       => new sfWidgetFormPropelChoice(array('model' => 'ReceiptFormats', 'add_empty' => true)),
      'shop_setting_sync_requested_at' => new sfWidgetFormDateTime(),
      'shop_setting_sync_synced_at'    => new sfWidgetFormDateTime(),
      'voucher_sync_requested_at'      => new sfWidgetFormDateTime(),
      'voucher_sync_synced_at'         => new sfWidgetFormDateTime(),
      'max_day_end_attempts'           => new sfWidgetFormInput(),
      'discount_type_id'               => new sfWidgetFormInput(),
      'discount_value'                 => new sfWidgetFormInput(),
      'gcm_key'                        => new sfWidgetFormInput(),
      'receipt_header_position'        => new sfWidgetFormInput(),
      'receipt_tax_statment_one'       => new sfWidgetFormTextarea(),
      'receipt_tax_statment_two'       => new sfWidgetFormTextarea(),
      'receipt_tax_statment_three'     => new sfWidgetFormTextarea(),
      'receipt_auto_print'             => new sfWidgetFormInput(),
      'start_value_bookout'            => new sfWidgetFormInput(),
      'bookout_format_id'              => new sfWidgetFormInput(),
      'values'                         => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                             => new sfValidatorPropelChoice(array('model' => 'Shops', 'column' => 'id', 'required' => false)),
      'name'                           => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'branch_number'                  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'company_number'                 => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'is_configured'                  => new sfValidatorInteger(array('required' => false)),
      'created_by'                     => new sfValidatorInteger(array('required' => false)),
      'created_at'                     => new sfValidatorDateTime(array('required' => false)),
      'configured_at'                  => new sfValidatorDateTime(array('required' => false)),
      'first_login'                    => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'password'                       => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'status_id'                      => new sfValidatorInteger(array('required' => false)),
      'address'                        => new sfValidatorString(array('required' => false)),
      'zip'                            => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'place'                          => new sfValidatorString(array('required' => false)),
      'country'                        => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'tel'                            => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'fax'                            => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'item_sync_requested_at'         => new sfValidatorDateTime(array('required' => false)),
      'item_sync_synced_at'            => new sfValidatorDateTime(array('required' => false)),
      'pic_sync_requested_at'          => new sfValidatorDateTime(array('required' => false)),
      'pic_sync_synced_at'             => new sfValidatorDateTime(array('required' => false)),
      'updated_by'                     => new sfValidatorInteger(array('required' => false)),
      'pin'                            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'user_sync_requested_at'         => new sfValidatorDateTime(array('required' => false)),
      'user_sync_synced_at'            => new sfValidatorDateTime(array('required' => false)),
      'updated_at'                     => new sfValidatorDateTime(array('required' => false)),
      'negative_sale'                  => new sfValidatorInteger(array('required' => false)),
      'language_id'                    => new sfValidatorPropelChoice(array('model' => 'Languages', 'column' => 'id', 'required' => false)),
      'time_out'                       => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'start_value_sale_receipt'       => new sfValidatorInteger(array('required' => false)),
      'start_value_return_receipt'     => new sfValidatorInteger(array('required' => false)),
      'sale_receipt_format_id'         => new sfValidatorPropelChoice(array('model' => 'ReceiptFormats', 'column' => 'id', 'required' => false)),
      'return_receipt_format_id'       => new sfValidatorPropelChoice(array('model' => 'ReceiptFormats', 'column' => 'id', 'required' => false)),
      'shop_setting_sync_requested_at' => new sfValidatorDateTime(array('required' => false)),
      'shop_setting_sync_synced_at'    => new sfValidatorDateTime(array('required' => false)),
      'voucher_sync_requested_at'      => new sfValidatorDateTime(array('required' => false)),
      'voucher_sync_synced_at'         => new sfValidatorDateTime(array('required' => false)),
      'max_day_end_attempts'           => new sfValidatorInteger(array('required' => false)),
      'discount_type_id'               => new sfValidatorInteger(array('required' => false)),
      'discount_value'                 => new sfValidatorNumber(array('required' => false)),
      'gcm_key'                        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'receipt_header_position'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'receipt_tax_statment_one'       => new sfValidatorString(array('required' => false)),
      'receipt_tax_statment_two'       => new sfValidatorString(array('required' => false)),
      'receipt_tax_statment_three'     => new sfValidatorString(array('required' => false)),
      'receipt_auto_print'             => new sfValidatorInteger(array('required' => false)),
      'start_value_bookout'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'bookout_format_id'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'values'                         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('shops[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Shops';
  }


}
