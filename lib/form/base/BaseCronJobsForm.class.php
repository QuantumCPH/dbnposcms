<?php

/**
 * CronJobs form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseCronJobsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'custom_minutes'       => new sfWidgetFormInput(),
      'minutes'              => new sfWidgetFormInput(),
      'hours'                => new sfWidgetFormInput(),
      'custom_hours'         => new sfWidgetFormInput(),
      'days'                 => new sfWidgetFormInput(),
      'custom_days'          => new sfWidgetFormInput(),
      'months'               => new sfWidgetFormInput(),
      'custom_months'        => new sfWidgetFormInput(),
      'weekdays'             => new sfWidgetFormInput(),
      'custom_weekdays'      => new sfWidgetFormInput(),
      'cron_type_id'         => new sfWidgetFormInput(),
      'job'                  => new sfWidgetFormTextarea(),
      'defination_file_path' => new sfWidgetFormTextarea(),
      'data_file_path'       => new sfWidgetFormTextarea(),
      'job_name'             => new sfWidgetFormInput(),
      'email'                => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorPropelChoice(array('model' => 'CronJobs', 'column' => 'id', 'required' => false)),
      'custom_minutes'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'minutes'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'hours'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'custom_hours'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'days'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'custom_days'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'months'               => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'custom_months'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'weekdays'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'custom_weekdays'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'cron_type_id'         => new sfValidatorInteger(array('required' => false)),
      'job'                  => new sfValidatorString(array('required' => false)),
      'defination_file_path' => new sfValidatorString(array('required' => false)),
      'data_file_path'       => new sfValidatorString(array('required' => false)),
      'job_name'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'email'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cron_jobs[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CronJobs';
  }


}
