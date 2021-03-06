<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * DibsCall filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseDibsCallFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'callurl'        => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'decrypted_data' => new sfWidgetFormFilterInput(),
      'call_response'  => new sfWidgetFormFilterInput(),
      'call_post_data' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'callurl'        => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'decrypted_data' => new sfValidatorPass(array('required' => false)),
      'call_response'  => new sfValidatorPass(array('required' => false)),
      'call_post_data' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dibs_call_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DibsCall';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'callurl'        => 'Text',
      'created_at'     => 'Date',
      'decrypted_data' => 'Text',
      'call_response'  => 'Text',
      'call_post_data' => 'Text',
    );
  }
}
