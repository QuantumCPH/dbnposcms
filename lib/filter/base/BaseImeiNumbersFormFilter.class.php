<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * ImeiNumbers filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseImeiNumbersFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'imei_number' => new sfWidgetFormFilterInput(),
      'name'        => new sfWidgetFormFilterInput(),
      'used_status' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'comment'     => new sfWidgetFormFilterInput(),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'user_id'     => new sfWidgetFormPropelChoice(array('model' => 'AgentUser', 'add_empty' => true)),
      'company_id'  => new sfWidgetFormPropelChoice(array('model' => 'AgentCompany', 'add_empty' => true)),
      'reseller_id' => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
      'app_version' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'imei_number' => new sfValidatorPass(array('required' => false)),
      'name'        => new sfValidatorPass(array('required' => false)),
      'used_status' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'comment'     => new sfValidatorPass(array('required' => false)),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'user_id'     => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AgentUser', 'column' => 'id')),
      'company_id'  => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AgentCompany', 'column' => 'id')),
      'reseller_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Reseller', 'column' => 'id')),
      'app_version' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('imei_numbers_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ImeiNumbers';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'imei_number' => 'Text',
      'name'        => 'Text',
      'used_status' => 'Boolean',
      'comment'     => 'Text',
      'created_at'  => 'Date',
      'user_id'     => 'ForeignKey',
      'company_id'  => 'ForeignKey',
      'reseller_id' => 'ForeignKey',
      'app_version' => 'Text',
    );
  }
}
