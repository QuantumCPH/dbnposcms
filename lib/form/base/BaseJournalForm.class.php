<?php

/**
 * Journal form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseJournalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
<<<<<<< HEAD
      'id'           => new sfWidgetFormInputHidden(),
      'shop_id'      => new sfWidgetFormInput(),
      'updated_at'   => new sfWidgetFormDateTime(),
      'created_at'   => new sfWidgetFormDateTime(),
      'created_date' => new sfWidgetFormInput(),
      'updated_by'   => new sfWidgetFormInput(),
      'journal_id'   => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
=======
<<<<<<< HEAD
      'id'                 => new sfWidgetFormInputHidden(),
      'shop_id'            => new sfWidgetFormInput(),
      'day_starts_journal' => new sfWidgetFormInput(),
      'journal_id'         => new sfWidgetFormInput(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_by'         => new sfWidgetFormInput(),
      'updated_at'         => new sfWidgetFormDateTime(),
      'created_date'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorPropelChoice(array('model' => 'Journal', 'column' => 'id', 'required' => false)),
      'shop_id'            => new sfValidatorInteger(array('required' => false)),
      'day_starts_journal' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'journal_id'         => new sfValidatorInteger(array('required' => false)),
      'created_at'         => new sfValidatorDateTime(array('required' => false)),
      'updated_by'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_at'         => new sfValidatorDateTime(array('required' => false)),
      'created_date'       => new sfValidatorDateTime(array('required' => false)),
=======
      'id'           => new sfWidgetFormInputHidden(),
      'shop_id'      => new sfWidgetFormInput(),
      'updated_at'   => new sfWidgetFormDateTime(),
      'created_at'   => new sfWidgetFormDateTime(),
      'created_date' => new sfWidgetFormInput(),
      'updated_by'   => new sfWidgetFormInput(),
      'journal_id'   => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'id'           => new sfValidatorPropelChoice(array('model' => 'Journal', 'column' => 'id', 'required' => false)),
      'shop_id'      => new sfValidatorInteger(array('required' => false)),
      'updated_at'   => new sfValidatorDateTime(),
      'created_at'   => new sfValidatorDateTime(array('required' => false)),
      'created_date' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_by'   => new sfValidatorInteger(array('required' => false)),
      'journal_id'   => new sfValidatorInteger(array('required' => false)),
<<<<<<< HEAD
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    ));

    $this->widgetSchema->setNameFormat('journal[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Journal';
  }


}
