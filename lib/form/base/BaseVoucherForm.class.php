<?php

/**
 * Voucher form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseVoucherForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      'amount'                      => new sfWidgetFormInput(),
      'created_at'                  => new sfWidgetFormDateTime(),
      'updated_at'                  => new sfWidgetFormDateTime(),
      'used_amount'                 => new sfWidgetFormInput(),
      'created_shop_id'             => new sfWidgetFormInput(),
      'used_shop_id'                => new sfWidgetFormInput(),
      'created_shop_transaction_id' => new sfWidgetFormInput(),
      'used_shop_transaction_id'    => new sfWidgetFormInput(),
      'parent_id'                   => new sfWidgetFormTextarea(),
      'shop_created_at'             => new sfWidgetFormDateTime(),
      'shop_updated_at'             => new sfWidgetFormDateTime(),
      'shop_used_at'                => new sfWidgetFormDateTime(),
      'is_used'                     => new sfWidgetFormInput(),
      'created_user_id'             => new sfWidgetFormInput(),
      'used_user_id'                => new sfWidgetFormInput(),
      'created_day_start_id'        => new sfWidgetFormInput(),
      'used_day_start_id'           => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorPropelChoice(array('model' => 'Voucher', 'column' => 'id', 'required' => false)),
      'amount'                      => new sfValidatorNumber(),
      'created_at'                  => new sfValidatorDateTime(array('required' => false)),
      'updated_at'                  => new sfValidatorDateTime(array('required' => false)),
      'used_amount'                 => new sfValidatorNumber(array('required' => false)),
      'created_shop_id'             => new sfValidatorInteger(array('required' => false)),
      'used_shop_id'                => new sfValidatorInteger(array('required' => false)),
      'created_shop_transaction_id' => new sfValidatorInteger(array('required' => false)),
      'used_shop_transaction_id'    => new sfValidatorInteger(array('required' => false)),
      'parent_id'                   => new sfValidatorString(array('required' => false)),
      'shop_created_at'             => new sfValidatorDateTime(array('required' => false)),
      'shop_updated_at'             => new sfValidatorDateTime(array('required' => false)),
      'shop_used_at'                => new sfValidatorDateTime(array('required' => false)),
      'is_used'                     => new sfValidatorInteger(array('required' => false)),
      'created_user_id'             => new sfValidatorInteger(array('required' => false)),
      'used_user_id'                => new sfValidatorInteger(array('required' => false)),
      'created_day_start_id'        => new sfValidatorInteger(array('required' => false)),
      'used_day_start_id'           => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('voucher[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Voucher';
  }


}
