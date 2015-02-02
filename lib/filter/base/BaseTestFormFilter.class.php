<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Test filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseTestFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                   => new sfWidgetFormFilterInput(),
      'cvr_number'             => new sfWidgetFormFilterInput(),
      'ean_number'             => new sfWidgetFormFilterInput(),
      'address'                => new sfWidgetFormFilterInput(),
      'post_code'              => new sfWidgetFormFilterInput(),
      'country_id'             => new sfWidgetFormFilterInput(),
      'city_id'                => new sfWidgetFormFilterInput(),
      'contact_name'           => new sfWidgetFormFilterInput(),
      'email'                  => new sfWidgetFormFilterInput(),
      'head_phone_number'      => new sfWidgetFormFilterInput(),
      'fax_number'             => new sfWidgetFormFilterInput(),
      'website'                => new sfWidgetFormFilterInput(),
      'status_id'              => new sfWidgetFormFilterInput(),
      'company_size_id'        => new sfWidgetFormFilterInput(),
      'company_type_id'        => new sfWidgetFormFilterInput(),
      'customer_type_id'       => new sfWidgetFormFilterInput(),
      'cpr_number'             => new sfWidgetFormFilterInput(),
      'apartment_form_id'      => new sfWidgetFormFilterInput(),
      'invoice_method_id'      => new sfWidgetFormFilterInput(),
      'account_manager_id'     => new sfWidgetFormFilterInput(),
      'agent_company_id'       => new sfWidgetFormFilterInput(),
      'created_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'confirmed_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'sim_card_dispatch_date' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'package_id'             => new sfWidgetFormFilterInput(),
      'send_letter'            => new sfWidgetFormFilterInput(),
      'send_email'             => new sfWidgetFormFilterInput(),
      'send_specification'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'                   => new sfValidatorPass(array('required' => false)),
      'cvr_number'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ean_number'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'address'                => new sfValidatorPass(array('required' => false)),
      'post_code'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'country_id'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'city_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'contact_name'           => new sfValidatorPass(array('required' => false)),
      'email'                  => new sfValidatorPass(array('required' => false)),
      'head_phone_number'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'fax_number'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'website'                => new sfValidatorPass(array('required' => false)),
      'status_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'company_size_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'company_type_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'customer_type_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'cpr_number'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'apartment_form_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'invoice_method_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'account_manager_id'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'agent_company_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'confirmed_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'sim_card_dispatch_date' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'package_id'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'send_letter'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'send_email'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'send_specification'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('test_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Test';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'name'                   => 'Text',
      'cvr_number'             => 'Number',
      'ean_number'             => 'Number',
      'address'                => 'Text',
      'post_code'              => 'Number',
      'country_id'             => 'Number',
      'city_id'                => 'Number',
      'contact_name'           => 'Text',
      'email'                  => 'Text',
      'head_phone_number'      => 'Number',
      'fax_number'             => 'Number',
      'website'                => 'Text',
      'status_id'              => 'Number',
      'company_size_id'        => 'Number',
      'company_type_id'        => 'Number',
      'customer_type_id'       => 'Number',
      'cpr_number'             => 'Number',
      'apartment_form_id'      => 'Number',
      'invoice_method_id'      => 'Number',
      'account_manager_id'     => 'Number',
      'agent_company_id'       => 'Number',
      'created_at'             => 'Date',
      'confirmed_at'           => 'Date',
      'sim_card_dispatch_date' => 'Date',
      'package_id'             => 'Number',
      'send_letter'            => 'Number',
      'send_email'             => 'Number',
      'send_specification'     => 'Number',
    );
  }
}
