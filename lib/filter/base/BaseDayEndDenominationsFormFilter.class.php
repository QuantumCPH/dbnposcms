<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * DayEndDenominations filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseDayEndDenominationsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'denomination_id' => new sfWidgetFormFilterInput(),
      'day_end_id'      => new sfWidgetFormFilterInput(),
      'count'           => new sfWidgetFormFilterInput(),
      'amount'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'denomination_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'day_end_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'count'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'amount'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('day_end_denominations_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DayEndDenominations';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'denomination_id' => 'Number',
      'day_end_id'      => 'Number',
      'count'           => 'Number',
      'amount'          => 'Number',
    );
  }
}
