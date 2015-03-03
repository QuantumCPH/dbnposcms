<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Voucher filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseVoucherFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'amount'                      => new sfWidgetFormFilterInput(),
      'created_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'used_amount'                 => new sfWidgetFormFilterInput(),
      'created_shop_id'             => new sfWidgetFormFilterInput(),
      'used_shop_id'                => new sfWidgetFormFilterInput(),
      'created_shop_transaction_id' => new sfWidgetFormFilterInput(),
      'used_shop_transaction_id'    => new sfWidgetFormFilterInput(),
      'parent_id'                   => new sfWidgetFormFilterInput(),
      'shop_created_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'shop_updated_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'shop_used_at'                => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'is_used'                     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_user_id'             => new sfWidgetFormFilterInput(),
      'used_user_id'                => new sfWidgetFormFilterInput(),
      'created_day_start_id'        => new sfWidgetFormFilterInput(),
      'used_day_start_id'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'amount'                      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'created_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'used_amount'                 => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'created_shop_id'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'used_shop_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_shop_transaction_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'used_shop_transaction_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'parent_id'                   => new sfValidatorPass(array('required' => false)),
      'shop_created_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'shop_updated_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'shop_used_at'                => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'is_used'                     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_user_id'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'used_user_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_day_start_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'used_day_start_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('voucher_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Voucher';
  }

  public function getFields()
  {
    return array(
      'id'                          => 'Number',
      'amount'                      => 'Number',
      'created_at'                  => 'Date',
      'updated_at'                  => 'Date',
      'used_amount'                 => 'Number',
      'created_shop_id'             => 'Number',
      'used_shop_id'                => 'Number',
      'created_shop_transaction_id' => 'Number',
      'used_shop_transaction_id'    => 'Number',
      'parent_id'                   => 'Text',
      'shop_created_at'             => 'Date',
      'shop_updated_at'             => 'Date',
      'shop_used_at'                => 'Date',
      'is_used'                     => 'Boolean',
      'created_user_id'             => 'Number',
      'used_user_id'                => 'Number',
      'created_day_start_id'        => 'Number',
      'used_day_start_id'           => 'Number',
    );
  }
}
