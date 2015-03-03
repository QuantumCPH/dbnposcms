<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * SystemConfig filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseSystemConfigFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'keys'   => new sfWidgetFormFilterInput(),
      'values' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'keys'   => new sfValidatorPass(array('required' => false)),
      'values' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('system_config_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SystemConfig';
  }

  public function getFields()
  {
    return array(
      'id'     => 'Number',
      'keys'   => 'Text',
      'values' => 'Text',
    );
  }
}
