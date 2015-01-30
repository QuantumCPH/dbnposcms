<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Denominations filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseDenominationsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'title'  => new sfWidgetFormFilterInput(),
      'amount' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'title'  => new sfValidatorPass(array('required' => false)),
      'amount' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('denominations_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Denominations';
  }

  public function getFields()
  {
    return array(
      'id'     => 'Number',
      'title'  => 'Text',
      'amount' => 'Number',
    );
  }
}
