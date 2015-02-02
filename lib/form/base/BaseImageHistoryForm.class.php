<?php

/**
 * ImageHistory form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseImageHistoryForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'item_id'         => new sfWidgetFormInput(),
      'created_at'      => new sfWidgetFormDateTime(),
      'image_name'      => new sfWidgetFormInput(),
      'updated_by'      => new sfWidgetFormInput(),
      'image_status_id' => new sfWidgetFormInput(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorPropelChoice(array('model' => 'ImageHistory', 'column' => 'id', 'required' => false)),
      'item_id'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'image_name'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_by'      => new sfValidatorInteger(array('required' => false)),
      'image_status_id' => new sfValidatorInteger(array('required' => false)),
      'updated_at'      => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('image_history[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ImageHistory';
  }


}
