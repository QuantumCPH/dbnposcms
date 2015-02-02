<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * OrderPayments filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseOrderPaymentsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'order_id'              => new sfWidgetFormFilterInput(),
      'payment_type_id'       => new sfWidgetFormFilterInput(),
      'amount'                => new sfWidgetFormFilterInput(),
      'shop_id'               => new sfWidgetFormFilterInput(),
      'shop_order_payment_id' => new sfWidgetFormFilterInput(),
      'dyn_syn'               => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'day_start_id'          => new sfWidgetFormFilterInput(),
      'shop_created_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'shop_order_user_id'    => new sfWidgetFormFilterInput(),
      'cc_type_id'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'order_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'payment_type_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'amount'                => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'shop_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_order_payment_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'dyn_syn'               => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'day_start_id'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_created_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'shop_order_user_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'cc_type_id'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('order_payments_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'OrderPayments';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'order_id'              => 'Number',
      'payment_type_id'       => 'Number',
      'amount'                => 'Number',
      'shop_id'               => 'Number',
      'shop_order_payment_id' => 'Number',
      'dyn_syn'               => 'Boolean',
      'created_at'            => 'Date',
      'updated_at'            => 'Date',
      'day_start_id'          => 'Number',
      'shop_created_at'       => 'Date',
      'shop_order_user_id'    => 'Number',
      'cc_type_id'            => 'Number',
    );
  }
}
