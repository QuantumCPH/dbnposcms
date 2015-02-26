<?php

/**
 * ResellerOrder form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseResellerOrderForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'reseller_order_id'   => new sfWidgetFormInput(),
      'reseller_id'         => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
      'amount'              => new sfWidgetFormInput(),
      'status'              => new sfWidgetFormInput(),
      'created_at'          => new sfWidgetFormDateTime(),
      'order_description'   => new sfWidgetFormInput(),
      'receipt_no'          => new sfWidgetFormInput(),
      'payment_histry_id'   => new sfWidgetFormInput(),
      'transaction_from_id' => new sfWidgetFormPropelChoice(array('model' => 'TransactionFrom', 'add_empty' => true)),
      'transaction_id'      => new sfWidgetFormInput(),
      'transaction_type_id' => new sfWidgetFormInput(),
      'comments'            => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorPropelChoice(array('model' => 'ResellerOrder', 'column' => 'id', 'required' => false)),
      'reseller_order_id'   => new sfValidatorString(array('max_length' => 5, 'required' => false)),
      'reseller_id'         => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'id', 'required' => false)),
      'amount'              => new sfValidatorNumber(array('required' => false)),
      'status'              => new sfValidatorInteger(array('required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'order_description'   => new sfValidatorInteger(array('required' => false)),
      'receipt_no'          => new sfValidatorInteger(array('required' => false)),
      'payment_histry_id'   => new sfValidatorInteger(array('required' => false)),
      'transaction_from_id' => new sfValidatorPropelChoice(array('model' => 'TransactionFrom', 'column' => 'id', 'required' => false)),
      'transaction_id'      => new sfValidatorInteger(array('required' => false)),
      'transaction_type_id' => new sfValidatorInteger(array('required' => false)),
      'comments'            => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('reseller_order[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ResellerOrder';
  }


}
