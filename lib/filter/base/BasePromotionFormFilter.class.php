<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Promotion filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BasePromotionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'promotion_title'      => new sfWidgetFormFilterInput(),
      'start_date'           => new sfWidgetFormFilterInput(),
      'end_date'             => new sfWidgetFormFilterInput(),
      'on_all_item'          => new sfWidgetFormFilterInput(),
      'promotion_value'      => new sfWidgetFormFilterInput(),
      'promotion_type'       => new sfWidgetFormFilterInput(),
      'on_all_branch'        => new sfWidgetFormFilterInput(),
      'created_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_by'           => new sfWidgetFormFilterInput(),
      'promotion_status'     => new sfWidgetFormFilterInput(),
      'updated_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'item_id_type'         => new sfWidgetFormFilterInput(),
      'item_id'              => new sfWidgetFormFilterInput(),
      'item_id_to'           => new sfWidgetFormFilterInput(),
      'item_id_from'         => new sfWidgetFormFilterInput(),
      'description1'         => new sfWidgetFormFilterInput(),
      'description2'         => new sfWidgetFormFilterInput(),
      'description3'         => new sfWidgetFormFilterInput(),
      'size'                 => new sfWidgetFormFilterInput(),
      'color'                => new sfWidgetFormFilterInput(),
      'group_type'           => new sfWidgetFormFilterInput(),
      'group_name'           => new sfWidgetFormFilterInput(),
      'group_to'             => new sfWidgetFormFilterInput(),
      'group_from'           => new sfWidgetFormFilterInput(),
      'price_type'           => new sfWidgetFormFilterInput(),
      'price_less'           => new sfWidgetFormFilterInput(),
      'price_greater'        => new sfWidgetFormFilterInput(),
      'price_to'             => new sfWidgetFormFilterInput(),
      'price_from'           => new sfWidgetFormFilterInput(),
      'supplier_number'      => new sfWidgetFormFilterInput(),
      'supplier_item_number' => new sfWidgetFormFilterInput(),
      'branch_id'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'promotion_title'      => new sfValidatorPass(array('required' => false)),
      'start_date'           => new sfValidatorPass(array('required' => false)),
      'end_date'             => new sfValidatorPass(array('required' => false)),
      'on_all_item'          => new sfValidatorPass(array('required' => false)),
      'promotion_value'      => new sfValidatorPass(array('required' => false)),
      'promotion_type'       => new sfValidatorPass(array('required' => false)),
      'on_all_branch'        => new sfValidatorPass(array('required' => false)),
      'created_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by'           => new sfValidatorPass(array('required' => false)),
      'promotion_status'     => new sfValidatorPass(array('required' => false)),
      'updated_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'item_id_type'         => new sfValidatorPass(array('required' => false)),
      'item_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'item_id_to'           => new sfValidatorPass(array('required' => false)),
      'item_id_from'         => new sfValidatorPass(array('required' => false)),
      'description1'         => new sfValidatorPass(array('required' => false)),
      'description2'         => new sfValidatorPass(array('required' => false)),
      'description3'         => new sfValidatorPass(array('required' => false)),
      'size'                 => new sfValidatorPass(array('required' => false)),
      'color'                => new sfValidatorPass(array('required' => false)),
      'group_type'           => new sfValidatorPass(array('required' => false)),
      'group_name'           => new sfValidatorPass(array('required' => false)),
      'group_to'             => new sfValidatorPass(array('required' => false)),
      'group_from'           => new sfValidatorPass(array('required' => false)),
      'price_type'           => new sfValidatorPass(array('required' => false)),
      'price_less'           => new sfValidatorPass(array('required' => false)),
      'price_greater'        => new sfValidatorPass(array('required' => false)),
      'price_to'             => new sfValidatorPass(array('required' => false)),
      'price_from'           => new sfValidatorPass(array('required' => false)),
      'supplier_number'      => new sfValidatorPass(array('required' => false)),
      'supplier_item_number' => new sfValidatorPass(array('required' => false)),
      'branch_id'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('promotion_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Promotion';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'promotion_title'      => 'Text',
      'start_date'           => 'Text',
      'end_date'             => 'Text',
      'on_all_item'          => 'Text',
      'promotion_value'      => 'Text',
      'promotion_type'       => 'Text',
      'on_all_branch'        => 'Text',
      'created_at'           => 'Date',
      'updated_by'           => 'Text',
      'promotion_status'     => 'Text',
      'updated_at'           => 'Date',
      'item_id_type'         => 'Text',
      'item_id'              => 'Number',
      'item_id_to'           => 'Text',
      'item_id_from'         => 'Text',
      'description1'         => 'Text',
      'description2'         => 'Text',
      'description3'         => 'Text',
      'size'                 => 'Text',
      'color'                => 'Text',
      'group_type'           => 'Text',
      'group_name'           => 'Text',
      'group_to'             => 'Text',
      'group_from'           => 'Text',
      'price_type'           => 'Text',
      'price_less'           => 'Text',
      'price_greater'        => 'Text',
      'price_to'             => 'Text',
      'price_from'           => 'Text',
      'supplier_number'      => 'Text',
      'supplier_item_number' => 'Text',
      'branch_id'            => 'Number',
    );
  }
}
