<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * CronTypes filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseCronTypesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'title' => new sfWidgetFormFilterInput(),
      'url'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'title' => new sfValidatorPass(array('required' => false)),
      'url'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cron_types_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CronTypes';
  }

  public function getFields()
  {
    return array(
      'id'    => 'Number',
      'title' => 'Text',
      'url'   => 'Text',
    );
  }
}
