<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * DibsCall filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
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
<<<<<<< HEAD
      'call_post_data' => new sfWidgetFormFilterInput(),
=======
<<<<<<< HEAD
=======
      'call_post_data' => new sfWidgetFormFilterInput(),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    ));

    $this->setValidators(array(
      'callurl'        => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'decrypted_data' => new sfValidatorPass(array('required' => false)),
      'call_response'  => new sfValidatorPass(array('required' => false)),
<<<<<<< HEAD
      'call_post_data' => new sfValidatorPass(array('required' => false)),
=======
<<<<<<< HEAD
=======
      'call_post_data' => new sfValidatorPass(array('required' => false)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
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
<<<<<<< HEAD
      'call_post_data' => 'Text',
=======
<<<<<<< HEAD
=======
      'call_post_data' => 'Text',
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    );
  }
}
