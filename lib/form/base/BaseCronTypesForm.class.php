<?php

/**
 * CronTypes form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseCronTypesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'    => new sfWidgetFormInputHidden(),
      'title' => new sfWidgetFormInput(),
      'url'   => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'    => new sfValidatorPropelChoice(array('model' => 'CronTypes', 'column' => 'id', 'required' => false)),
      'title' => new sfValidatorString(array('max_length' => 45, 'required' => false)),
      'url'   => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cron_types[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CronTypes';
  }


}
