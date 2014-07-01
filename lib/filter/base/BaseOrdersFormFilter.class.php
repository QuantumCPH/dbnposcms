<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Orders filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseOrdersFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'created_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'total_amount'           => new sfWidgetFormFilterInput(),
      'status_id'              => new sfWidgetFormFilterInput(),
      'total_sold_amount'      => new sfWidgetFormFilterInput(),
      'discount_value'         => new sfWidgetFormFilterInput(),
      'discount_type_id'       => new sfWidgetFormFilterInput(),
      'shop_user_id'           => new sfWidgetFormFilterInput(),
      'shop_order_id'          => new sfWidgetFormFilterInput(),
      'shop_id'                => new sfWidgetFormFilterInput(),
      'shop_receipt_number_id' => new sfWidgetFormFilterInput(),
      'updated_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'day_start_id'           => new sfWidgetFormFilterInput(),
      'employee_id'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'created_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'total_amount'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'status_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'total_sold_amount'      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'discount_value'         => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'discount_type_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_user_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_order_id'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_receipt_number_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'updated_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'day_start_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'employee_id'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('orders_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Orders';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'created_at'             => 'Date',
      'total_amount'           => 'Number',
      'status_id'              => 'Number',
      'total_sold_amount'      => 'Number',
      'discount_value'         => 'Number',
      'discount_type_id'       => 'Number',
      'shop_user_id'           => 'Number',
      'shop_order_id'          => 'Number',
      'shop_id'                => 'Number',
      'shop_receipt_number_id' => 'Number',
      'updated_at'             => 'Date',
      'day_start_id'           => 'Number',
      'employee_id'            => 'Number',
    );
  }
}
