<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * CronJobHistoryInfo filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseCronJobHistoryInfoFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'xml'                 => new sfWidgetFormFilterInput(),
      'csv'                 => new sfWidgetFormFilterInput(),
      'status'              => new sfWidgetFormFilterInput(),
      'message'             => new sfWidgetFormFilterInput(),
      'cron_job_history_id' => new sfWidgetFormFilterInput(),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'xml'                 => new sfValidatorPass(array('required' => false)),
      'csv'                 => new sfValidatorPass(array('required' => false)),
      'status'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'message'             => new sfValidatorPass(array('required' => false)),
      'cron_job_history_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('cron_job_history_info_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CronJobHistoryInfo';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'xml'                 => 'Text',
      'csv'                 => 'Text',
      'status'              => 'Number',
      'message'             => 'Text',
      'cron_job_history_id' => 'Number',
      'created_at'          => 'Date',
    );
  }
}
