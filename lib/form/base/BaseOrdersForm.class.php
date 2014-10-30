<?php

/**
 * Orders form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseOrdersForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'created_at'             => new sfWidgetFormDateTime(),
      'total_amount'           => new sfWidgetFormInput(),
      'status_id'              => new sfWidgetFormInput(),
      'total_sold_amount'      => new sfWidgetFormInput(),
      'discount_value'         => new sfWidgetFormInput(),
      'discount_type_id'       => new sfWidgetFormInput(),
      'shop_user_id'           => new sfWidgetFormInput(),
      'shop_order_id'          => new sfWidgetFormInput(),
      'shop_id'                => new sfWidgetFormInput(),
      'shop_receipt_number_id' => new sfWidgetFormInput(),
      'updated_at'             => new sfWidgetFormDateTime(),
      'day_start_id'           => new sfWidgetFormInput(),
      'employee_id'            => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorPropelChoice(array('model' => 'Orders', 'column' => 'id', 'required' => false)),
      'created_at'             => new sfValidatorDateTime(array('required' => false)),
      'total_amount'           => new sfValidatorNumber(array('required' => false)),
      'status_id'              => new sfValidatorInteger(array('required' => false)),
      'total_sold_amount'      => new sfValidatorNumber(array('required' => false)),
      'discount_value'         => new sfValidatorNumber(array('required' => false)),
      'discount_type_id'       => new sfValidatorInteger(array('required' => false)),
      'shop_user_id'           => new sfValidatorInteger(array('required' => false)),
      'shop_order_id'          => new sfValidatorInteger(array('required' => false)),
      'shop_id'                => new sfValidatorInteger(array('required' => false)),
      'shop_receipt_number_id' => new sfValidatorInteger(array('required' => false)),
      'updated_at'             => new sfValidatorDateTime(array('required' => false)),
      'day_start_id'           => new sfValidatorInteger(array('required' => false)),
      'employee_id'            => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('orders[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Orders';
  }


}
