<?php

/**
 * Reseller form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseResellerForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                            => new sfWidgetFormInputHidden(),
      'name'                          => new sfWidgetFormInput(),
      'status_id'                     => new sfWidgetFormPropelChoice(array('model' => 'Status', 'add_empty' => false)),
      'created_at'                    => new sfWidgetFormDateTime(),
      'user_name'                     => new sfWidgetFormInput(),
      'password'                      => new sfWidgetFormInput(),
      'credit_limit'                  => new sfWidgetFormInput(),
      'email'                         => new sfWidgetFormInput(),
      'contact_number'                => new sfWidgetFormInput(),
      'address'                       => new sfWidgetFormTextarea(),
      'post_code'                     => new sfWidgetFormInput(),
      'resellercommission_package_id' => new sfWidgetFormPropelChoice(array('model' => 'ResellerCommissionPackage', 'add_empty' => false)),
      'balance'                       => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                            => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'id', 'required' => false)),
      'name'                          => new sfValidatorString(array('max_length' => 255)),
      'status_id'                     => new sfValidatorPropelChoice(array('model' => 'Status', 'column' => 'id')),
      'created_at'                    => new sfValidatorDateTime(),
      'user_name'                     => new sfValidatorString(array('max_length' => 255)),
      'password'                      => new sfValidatorString(array('max_length' => 255)),
      'credit_limit'                  => new sfValidatorNumber(),
      'email'                         => new sfValidatorString(array('max_length' => 255)),
      'contact_number'                => new sfValidatorString(array('max_length' => 255)),
      'address'                       => new sfValidatorString(),
      'post_code'                     => new sfValidatorString(array('max_length' => 255)),
      'resellercommission_package_id' => new sfValidatorPropelChoice(array('model' => 'ResellerCommissionPackage', 'column' => 'id')),
      'balance'                       => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('reseller[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Reseller';
  }


}
