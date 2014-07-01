<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Reseller filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseResellerFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                          => new sfWidgetFormFilterInput(),
      'status_id'                     => new sfWidgetFormPropelChoice(array('model' => 'Status', 'add_empty' => true)),
      'created_at'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'user_name'                     => new sfWidgetFormFilterInput(),
      'password'                      => new sfWidgetFormFilterInput(),
      'credit_limit'                  => new sfWidgetFormFilterInput(),
      'email'                         => new sfWidgetFormFilterInput(),
      'contact_number'                => new sfWidgetFormFilterInput(),
      'address'                       => new sfWidgetFormFilterInput(),
      'post_code'                     => new sfWidgetFormFilterInput(),
      'resellercommission_package_id' => new sfWidgetFormPropelChoice(array('model' => 'ResellerCommissionPackage', 'add_empty' => true)),
      'balance'                       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'                          => new sfValidatorPass(array('required' => false)),
      'status_id'                     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Status', 'column' => 'id')),
      'created_at'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'user_name'                     => new sfValidatorPass(array('required' => false)),
      'password'                      => new sfValidatorPass(array('required' => false)),
      'credit_limit'                  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'email'                         => new sfValidatorPass(array('required' => false)),
      'contact_number'                => new sfValidatorPass(array('required' => false)),
      'address'                       => new sfValidatorPass(array('required' => false)),
      'post_code'                     => new sfValidatorPass(array('required' => false)),
      'resellercommission_package_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'ResellerCommissionPackage', 'column' => 'id')),
      'balance'                       => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('reseller_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Reseller';
  }

  public function getFields()
  {
    return array(
      'id'                            => 'Number',
      'name'                          => 'Text',
      'status_id'                     => 'ForeignKey',
      'created_at'                    => 'Date',
      'user_name'                     => 'Text',
      'password'                      => 'Text',
      'credit_limit'                  => 'Number',
      'email'                         => 'Text',
      'contact_number'                => 'Text',
      'address'                       => 'Text',
      'post_code'                     => 'Text',
      'resellercommission_package_id' => 'ForeignKey',
      'balance'                       => 'Number',
    );
  }
}
