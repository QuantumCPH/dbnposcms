<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * CronJobs filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseCronJobsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'custom_minutes'       => new sfWidgetFormFilterInput(),
      'minutes'              => new sfWidgetFormFilterInput(),
      'hours'                => new sfWidgetFormFilterInput(),
      'custom_hours'         => new sfWidgetFormFilterInput(),
      'days'                 => new sfWidgetFormFilterInput(),
      'custom_days'          => new sfWidgetFormFilterInput(),
      'months'               => new sfWidgetFormFilterInput(),
      'custom_months'        => new sfWidgetFormFilterInput(),
      'weekdays'             => new sfWidgetFormFilterInput(),
      'custom_weekdays'      => new sfWidgetFormFilterInput(),
      'cron_type_id'         => new sfWidgetFormFilterInput(),
      'job'                  => new sfWidgetFormFilterInput(),
      'defination_file_path' => new sfWidgetFormFilterInput(),
      'data_file_path'       => new sfWidgetFormFilterInput(),
      'job_name'             => new sfWidgetFormFilterInput(),
      'email'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'custom_minutes'       => new sfValidatorPass(array('required' => false)),
      'minutes'              => new sfValidatorPass(array('required' => false)),
      'hours'                => new sfValidatorPass(array('required' => false)),
      'custom_hours'         => new sfValidatorPass(array('required' => false)),
      'days'                 => new sfValidatorPass(array('required' => false)),
      'custom_days'          => new sfValidatorPass(array('required' => false)),
      'months'               => new sfValidatorPass(array('required' => false)),
      'custom_months'        => new sfValidatorPass(array('required' => false)),
      'weekdays'             => new sfValidatorPass(array('required' => false)),
      'custom_weekdays'      => new sfValidatorPass(array('required' => false)),
      'cron_type_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'job'                  => new sfValidatorPass(array('required' => false)),
      'defination_file_path' => new sfValidatorPass(array('required' => false)),
      'data_file_path'       => new sfValidatorPass(array('required' => false)),
      'job_name'             => new sfValidatorPass(array('required' => false)),
      'email'                => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('cron_jobs_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CronJobs';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'custom_minutes'       => 'Text',
      'minutes'              => 'Text',
      'hours'                => 'Text',
      'custom_hours'         => 'Text',
      'days'                 => 'Text',
      'custom_days'          => 'Text',
      'months'               => 'Text',
      'custom_months'        => 'Text',
      'weekdays'             => 'Text',
      'custom_weekdays'      => 'Text',
      'cron_type_id'         => 'Number',
      'job'                  => 'Text',
      'defination_file_path' => 'Text',
      'data_file_path'       => 'Text',
      'job_name'             => 'Text',
      'email'                => 'Text',
    );
  }
}
