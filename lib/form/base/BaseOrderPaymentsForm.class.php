<?php

/**
 * OrderPayments form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseOrderPaymentsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'order_id'              => new sfWidgetFormInput(),
      'payment_type_id'       => new sfWidgetFormInput(),
      'amount'                => new sfWidgetFormInput(),
      'shop_id'               => new sfWidgetFormInput(),
      'shop_order_payment_id' => new sfWidgetFormInput(),
      'dyn_syn'               => new sfWidgetFormInputCheckbox(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'day_start_id'          => new sfWidgetFormInput(),
      'shop_created_at'       => new sfWidgetFormDateTime(),
      'shop_order_user_id'    => new sfWidgetFormInput(),
      'cc_type_id'            => new sfWidgetFormInput(),
<<<<<<< HEAD
      'change_value'          => new sfWidgetFormInput(),
      'change_type'           => new sfWidgetFormInput(),
      'shop_order_id'         => new sfWidgetFormInput(),
      'promotion_ids'         => new sfWidgetFormInput(),
=======
      'change_type'           => new sfWidgetFormInput(),
      'change_value'          => new sfWidgetFormInput(),
      'shop_order_id'         => new sfWidgetFormInput(),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorPropelChoice(array('model' => 'OrderPayments', 'column' => 'id', 'required' => false)),
      'order_id'              => new sfValidatorInteger(array('required' => false)),
      'payment_type_id'       => new sfValidatorInteger(array('required' => false)),
      'amount'                => new sfValidatorNumber(array('required' => false)),
      'shop_id'               => new sfValidatorInteger(array('required' => false)),
      'shop_order_payment_id' => new sfValidatorInteger(array('required' => false)),
      'dyn_syn'               => new sfValidatorBoolean(array('required' => false)),
      'created_at'            => new sfValidatorDateTime(array('required' => false)),
      'updated_at'            => new sfValidatorDateTime(array('required' => false)),
      'day_start_id'          => new sfValidatorInteger(array('required' => false)),
      'shop_created_at'       => new sfValidatorDateTime(array('required' => false)),
      'shop_order_user_id'    => new sfValidatorInteger(array('required' => false)),
      'cc_type_id'            => new sfValidatorInteger(array('required' => false)),
<<<<<<< HEAD
      'change_value'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'change_type'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'shop_order_id'         => new sfValidatorInteger(array('required' => false)),
      'promotion_ids'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
=======
      'change_type'           => new sfValidatorInteger(array('required' => false)),
      'change_value'          => new sfValidatorNumber(array('required' => false)),
      'shop_order_id'         => new sfValidatorInteger(array('required' => false)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->widgetSchema->setNameFormat('order_payments[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'OrderPayments';
  }


}
