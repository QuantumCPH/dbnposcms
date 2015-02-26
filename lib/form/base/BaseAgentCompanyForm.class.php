<?php

/**
 * AgentCompany form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseAgentCompanyForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      'name'                        => new sfWidgetFormInput(),
      'first_name'                  => new sfWidgetFormInput(),
      'middle_name'                 => new sfWidgetFormInput(),
      'last_name'                   => new sfWidgetFormInput(),
      'cvr_number'                  => new sfWidgetFormInput(),
      'ean_number'                  => new sfWidgetFormInput(),
      'kimarin_agent_id'            => new sfWidgetFormInput(),
      'nationality_id'              => new sfWidgetFormInput(),
      'address'                     => new sfWidgetFormInput(),
      'post_code'                   => new sfWidgetFormInput(),
      'country_id'                  => new sfWidgetFormInput(),
      'province_id'                 => new sfWidgetFormInput(),
      'city_id'                     => new sfWidgetFormInput(),
      'contact_name'                => new sfWidgetFormInput(),
      'email'                       => new sfWidgetFormInput(),
      'mobile_number'               => new sfWidgetFormInput(),
      'head_phone_number'           => new sfWidgetFormInput(),
      'fax_number'                  => new sfWidgetFormInput(),
      'website'                     => new sfWidgetFormInput(),
      'status_id'                   => new sfWidgetFormInput(),
      'company_type_id'             => new sfWidgetFormInput(),
      'product_detail'              => new sfWidgetFormInput(),
      'commission_period_id'        => new sfWidgetFormInput(),
      'account_manager_id'          => new sfWidgetFormInput(),
      'created_at'                  => new sfWidgetFormDateTime(),
      'agent_commission_package_id' => new sfWidgetFormPropelChoice(array('model' => 'AgentCommissionPackage', 'add_empty' => false)),
      'sms_code'                    => new sfWidgetFormInput(),
      'is_prepaid'                  => new sfWidgetFormInputCheckbox(),
      'balance'                     => new sfWidgetFormInput(),
      'invoice_method_id'           => new sfWidgetFormInput(),
      'comments'                    => new sfWidgetFormTextarea(),
      'credit_limit'                => new sfWidgetFormInput(),
      'reseller_id'                 => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorPropelChoice(array('model' => 'AgentCompany', 'column' => 'id', 'required' => false)),
      'name'                        => new sfValidatorString(array('max_length' => 255)),
      'first_name'                  => new sfValidatorString(array('max_length' => 255)),
      'middle_name'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'last_name'                   => new sfValidatorString(array('max_length' => 255)),
      'cvr_number'                  => new sfValidatorString(array('max_length' => 50)),
      'ean_number'                  => new sfValidatorInteger(array('required' => false)),
      'kimarin_agent_id'            => new sfValidatorInteger(array('required' => false)),
      'nationality_id'              => new sfValidatorInteger(array('required' => false)),
      'address'                     => new sfValidatorString(array('max_length' => 255)),
      'post_code'                   => new sfValidatorString(array('max_length' => 255)),
      'country_id'                  => new sfValidatorInteger(array('required' => false)),
      'province_id'                 => new sfValidatorInteger(array('required' => false)),
      'city_id'                     => new sfValidatorInteger(array('required' => false)),
      'contact_name'                => new sfValidatorString(array('max_length' => 150, 'required' => false)),
      'email'                       => new sfValidatorString(array('max_length' => 255)),
      'mobile_number'               => new sfValidatorString(array('max_length' => 255)),
      'head_phone_number'           => new sfValidatorString(array('max_length' => 50)),
      'fax_number'                  => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'website'                     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status_id'                   => new sfValidatorInteger(array('required' => false)),
      'company_type_id'             => new sfValidatorInteger(array('required' => false)),
      'product_detail'              => new sfValidatorInteger(array('required' => false)),
      'commission_period_id'        => new sfValidatorInteger(array('required' => false)),
      'account_manager_id'          => new sfValidatorInteger(array('required' => false)),
      'created_at'                  => new sfValidatorDateTime(array('required' => false)),
      'agent_commission_package_id' => new sfValidatorPropelChoice(array('model' => 'AgentCommissionPackage', 'column' => 'id')),
      'sms_code'                    => new sfValidatorString(array('max_length' => 4, 'required' => false)),
      'is_prepaid'                  => new sfValidatorBoolean(array('required' => false)),
      'balance'                     => new sfValidatorNumber(array('required' => false)),
      'invoice_method_id'           => new sfValidatorInteger(array('required' => false)),
      'comments'                    => new sfValidatorString(array('required' => false)),
      'credit_limit'                => new sfValidatorNumber(array('required' => false)),
      'reseller_id'                 => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'id', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('agent_company[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'AgentCompany';
  }


}
