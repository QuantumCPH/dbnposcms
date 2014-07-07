<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Currencies filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseCurrenciesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'currency_title'       => new sfWidgetFormFilterInput(),
      'currency_symbol'      => new sfWidgetFormFilterInput(),
      'currency_symbol_code' => new sfWidgetFormFilterInput(),
      'currency_status'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'currency_title'       => new sfValidatorPass(array('required' => false)),
      'currency_symbol'      => new sfValidatorPass(array('required' => false)),
      'currency_symbol_code' => new sfValidatorPass(array('required' => false)),
      'currency_status'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('currencies_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Currencies';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'currency_title'       => 'Text',
      'currency_symbol'      => 'Text',
      'currency_symbol_code' => 'Text',
      'currency_status'      => 'Number',
    );
  }
}
