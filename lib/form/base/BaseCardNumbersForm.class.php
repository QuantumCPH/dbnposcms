<?php

/**
 * CardNumbers form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseCardNumbersForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'card_number'         => new sfWidgetFormInput(),
      'card_serial'         => new sfWidgetFormInput(),
      'card_price'          => new sfWidgetFormInput(),
      'status'              => new sfWidgetFormInputCheckbox(),
      'created_at'          => new sfWidgetFormDateTime(),
      'used_at'             => new sfWidgetFormDateTime(),
      'customer_mobile'     => new sfWidgetFormInput(),
      'product_id'          => new sfWidgetFormPropelChoice(array('model' => 'Product', 'add_empty' => false)),
      'comments'            => new sfWidgetFormInput(),
      'card_type_id'        => new sfWidgetFormPropelChoice(array('model' => 'CardTypes', 'add_empty' => true)),
      'agent_company_id'    => new sfWidgetFormPropelChoice(array('model' => 'AgentCompany', 'add_empty' => true)),
      'agent_user_id'       => new sfWidgetFormPropelChoice(array('model' => 'AgentUser', 'add_empty' => true)),
      'used_by'             => new sfWidgetFormInput(),
      'customer_id'         => new sfWidgetFormInput(),
      'card_purchase_price' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorPropelChoice(array('model' => 'CardNumbers', 'column' => 'id', 'required' => false)),
      'card_number'         => new sfValidatorString(array('max_length' => 255)),
      'card_serial'         => new sfValidatorString(array('max_length' => 255)),
      'card_price'          => new sfValidatorInteger(),
      'status'              => new sfValidatorBoolean(),
      'created_at'          => new sfValidatorDateTime(),
      'used_at'             => new sfValidatorDateTime(),
      'customer_mobile'     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'product_id'          => new sfValidatorPropelChoice(array('model' => 'Product', 'column' => 'id')),
      'comments'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'card_type_id'        => new sfValidatorPropelChoice(array('model' => 'CardTypes', 'column' => 'id', 'required' => false)),
      'agent_company_id'    => new sfValidatorPropelChoice(array('model' => 'AgentCompany', 'column' => 'id', 'required' => false)),
      'agent_user_id'       => new sfValidatorPropelChoice(array('model' => 'AgentUser', 'column' => 'id', 'required' => false)),
      'used_by'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'customer_id'         => new sfValidatorInteger(array('required' => false)),
      'card_purchase_price' => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('card_numbers[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CardNumbers';
  }


}
