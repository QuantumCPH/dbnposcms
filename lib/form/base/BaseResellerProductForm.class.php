<?php

/**
 * ResellerProduct form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseResellerProductForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'reseller_product_id'              => new sfWidgetFormInputHidden(),
      'reseller_id'                      => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => false)),
      'Product_id'                       => new sfWidgetFormPropelChoice(array('model' => 'Product', 'add_empty' => false)),
      'reg_share_value'                  => new sfWidgetFormInput(),
      'is_reg_share_value_pc'            => new sfWidgetFormInputCheckbox(),
      'reg_share_enable'                 => new sfWidgetFormInputCheckbox(),
      'extra_payments_share_value'       => new sfWidgetFormInput(),
      'is_extra_payments_share_value_pc' => new sfWidgetFormInputCheckbox(),
      'extra_payments_share_enable'      => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'reseller_product_id'              => new sfValidatorPropelChoice(array('model' => 'ResellerProduct', 'column' => 'reseller_product_id', 'required' => false)),
      'reseller_id'                      => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'id')),
      'Product_id'                       => new sfValidatorPropelChoice(array('model' => 'Product', 'column' => 'id')),
      'reg_share_value'                  => new sfValidatorNumber(),
      'is_reg_share_value_pc'            => new sfValidatorBoolean(),
      'reg_share_enable'                 => new sfValidatorBoolean(),
      'extra_payments_share_value'       => new sfValidatorNumber(),
      'is_extra_payments_share_value_pc' => new sfValidatorBoolean(),
      'extra_payments_share_enable'      => new sfValidatorBoolean(),
    ));

    $this->widgetSchema->setNameFormat('reseller_product[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ResellerProduct';
  }


}
