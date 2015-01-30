<?php

/**
 * ShopUsers form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseShopUsersForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'user_id'        => new sfWidgetFormInput(),
      'shop_id'        => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'status_id'      => new sfWidgetFormInput(),
      'log_histry'     => new sfWidgetFormTextarea(),
      'pos_role_id'    => new sfWidgetFormInput(),
      'pos_super_user' => new sfWidgetFormInputCheckbox(),
      'is_primary'     => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'ShopUsers', 'column' => 'id', 'required' => false)),
      'user_id'        => new sfValidatorInteger(array('required' => false)),
      'shop_id'        => new sfValidatorInteger(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(array('required' => false)),
      'status_id'      => new sfValidatorInteger(array('required' => false)),
      'log_histry'     => new sfValidatorString(array('required' => false)),
      'pos_role_id'    => new sfValidatorInteger(array('required' => false)),
      'pos_super_user' => new sfValidatorBoolean(array('required' => false)),
      'is_primary'     => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('shop_users[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ShopUsers';
  }


}
