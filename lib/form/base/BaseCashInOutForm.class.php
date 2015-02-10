<?php

/**
 * CashInOut form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
<<<<<<< HEAD
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
=======
>>>>>>> aca7f56813d756ed287ec5114b8683530bc6e53e
 */
class BaseCashInOutForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
      'updated_by'    => new sfWidgetFormInput(),
      'shop_id'       => new sfWidgetFormInput(),
      'cash_inout_id' => new sfWidgetFormInput(),
      'day_start_id'  => new sfWidgetFormInput(),
      'amount'        => new sfWidgetFormInput(),
      'description'   => new sfWidgetFormTextarea(),
      'is_synced'     => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorPropelChoice(array('model' => 'CashInOut', 'column' => 'id', 'required' => false)),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
      'updated_by'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'shop_id'       => new sfValidatorInteger(array('required' => false)),
      'cash_inout_id' => new sfValidatorInteger(array('required' => false)),
      'day_start_id'  => new sfValidatorInteger(array('required' => false)),
      'amount'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description'   => new sfValidatorString(array('required' => false)),
      'is_synced'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
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
