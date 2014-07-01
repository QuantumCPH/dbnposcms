<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * SmsApi filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseSmsApiFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'     => new sfWidgetFormFilterInput(),
      'status'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'priority' => new sfWidgetFormFilterInput(),
      'detail'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'     => new sfValidatorPass(array('required' => false)),
      'status'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'priority' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'detail'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sms_api_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SmsApi';
  }

  public function getFields()
  {
    return array(
      'id'       => 'Number',
      'name'     => 'Text',
      'status'   => 'Boolean',
      'priority' => 'Number',
      'detail'   => 'Text',
    );
  }
}
