<?php

/**
 * CashInOut form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseCashInOutForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
<<<<<<< HEAD
=======
      'day_start_id'  => new sfWidgetFormInput(),
      'is_synced'     => new sfWidgetFormInput(),
      'description'   => new sfWidgetFormInput(),
      'amount'        => new sfWidgetFormInput(),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
      'updated_by'    => new sfWidgetFormInput(),
      'shop_id'       => new sfWidgetFormInput(),
      'cash_inout_id' => new sfWidgetFormInput(),
<<<<<<< HEAD
      'day_start_id'  => new sfWidgetFormInput(),
      'amount'        => new sfWidgetFormInput(),
      'description'   => new sfWidgetFormTextarea(),
      'is_synced'     => new sfWidgetFormInput(),
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorPropelChoice(array('model' => 'CashInOut', 'column' => 'id', 'required' => false)),
<<<<<<< HEAD
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
      'updated_by'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'shop_id'       => new sfValidatorInteger(array('required' => false)),
      'cash_inout_id' => new sfValidatorInteger(array('required' => false)),
      'day_start_id'  => new sfValidatorInteger(array('required' => false)),
      'amount'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description'   => new sfValidatorString(array('required' => false)),
      'is_synced'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
=======
      'day_start_id'  => new sfValidatorInteger(array('required' => false)),
      'is_synced'     => new sfValidatorInteger(array('required' => false)),
      'description'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'amount'        => new sfValidatorNumber(array('required' => false)),
      'created_at'    => new sfValidatorDateTime(array('required' => false)),
      'updated_at'    => new sfValidatorDateTime(),
      'updated_by'    => new sfValidatorInteger(array('required' => false)),
      'shop_id'       => new sfValidatorInteger(array('required' => false)),
      'cash_inout_id' => new sfValidatorInteger(array('required' => false)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->widgetSchema->setNameFormat('cash_in_out[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CashInOut';
  }


}
