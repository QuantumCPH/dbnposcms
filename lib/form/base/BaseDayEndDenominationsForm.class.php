<?php

/**
 * DayEndDenominations form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseDayEndDenominationsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'denomination_id' => new sfWidgetFormInput(),
      'day_end_id'      => new sfWidgetFormInput(),
      'count'           => new sfWidgetFormInput(),
      'amount'          => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorPropelChoice(array('model' => 'DayEndDenominations', 'column' => 'id', 'required' => false)),
      'denomination_id' => new sfValidatorInteger(array('required' => false)),
      'day_end_id'      => new sfValidatorInteger(array('required' => false)),
      'count'           => new sfValidatorInteger(array('required' => false)),
      'amount'          => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('day_end_denominations[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DayEndDenominations';
  }


}
