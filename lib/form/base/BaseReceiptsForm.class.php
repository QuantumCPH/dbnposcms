<?php

/**
 * Receipts form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseReceiptsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'shop_id'    => new sfWidgetFormInput(),
<<<<<<< HEAD
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
      'updated_by' => new sfWidgetFormInput(),
=======
      'updated_at' => new sfWidgetFormDateTime(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_by' => new sfWidgetFormInput(),
      'receipt_id' => new sfWidgetFormInput(),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorPropelChoice(array('model' => 'Receipts', 'column' => 'id', 'required' => false)),
      'shop_id'    => new sfValidatorInteger(array('required' => false)),
<<<<<<< HEAD
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
      'updated_by' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
=======
      'updated_at' => new sfValidatorDateTime(),
      'created_at' => new sfValidatorDateTime(array('required' => false)),
      'updated_by' => new sfValidatorInteger(array('required' => false)),
      'receipt_id' => new sfValidatorInteger(array('required' => false)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->widgetSchema->setNameFormat('receipts[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Receipts';
  }


}
