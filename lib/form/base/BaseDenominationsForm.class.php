<?php

/**
 * Denominations form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseDenominationsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'     => new sfWidgetFormInputHidden(),
      'title'  => new sfWidgetFormInput(),
      'amount' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'     => new sfValidatorPropelChoice(array('model' => 'Denominations', 'column' => 'id', 'required' => false)),
      'title'  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'amount' => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('denominations[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Denominations';
  }


}
