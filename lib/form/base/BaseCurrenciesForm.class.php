<?php

/**
 * Currencies form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseCurrenciesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'currency_title'       => new sfWidgetFormInput(),
      'currency_symbol'      => new sfWidgetFormInput(),
      'currency_symbol_code' => new sfWidgetFormInput(),
      'currency_status'      => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorPropelChoice(array('model' => 'Currencies', 'column' => 'id', 'required' => false)),
      'currency_title'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'currency_symbol'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'currency_symbol_code' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'currency_status'      => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('currencies[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Currencies';
  }


}
