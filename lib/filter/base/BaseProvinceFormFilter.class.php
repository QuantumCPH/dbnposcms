<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Province filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseProvinceFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'country_id' => new sfWidgetFormFilterInput(),
      'province'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'country_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'province'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('province_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Province';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'country_id' => 'Number',
      'province'   => 'Text',
    );
  }
}
