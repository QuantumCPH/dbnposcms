<?php

/**
 * OrderPayments form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
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
