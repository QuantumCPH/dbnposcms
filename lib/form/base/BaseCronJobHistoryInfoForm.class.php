<?php

/**
 * CronJobHistoryInfo form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseCronJobHistoryInfoForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'xml'                 => new sfWidgetFormInput(),
      'csv'                 => new sfWidgetFormInput(),
      'status'              => new sfWidgetFormInput(),
      'message'             => new sfWidgetFormTextarea(),
      'cron_job_history_id' => new sfWidgetFormInput(),
      'created_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorPropelChoice(array('model' => 'CronJobHistoryInfo', 'column' => 'id', 'required' => false)),
      'xml'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'csv'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'              => new sfValidatorInteger(array('required' => false)),
      'message'             => new sfValidatorString(array('required' => false)),
      'cron_job_history_id' => new sfValidatorInteger(array('required' => false)),
      'created_at'          => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cron_job_history_info[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CronJobHistoryInfo';
  }


}
