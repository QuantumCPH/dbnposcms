<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * ItemsSync filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseItemsSyncFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'item_id'              => new sfWidgetFormFilterInput(),
      'description1'         => new sfWidgetFormFilterInput(),
      'description2'         => new sfWidgetFormFilterInput(),
      'description3'         => new sfWidgetFormFilterInput(),
      'supplier_number'      => new sfWidgetFormFilterInput(),
      'supplier_item_number' => new sfWidgetFormFilterInput(),
      'ean'                  => new sfWidgetFormFilterInput(),
      'group'                => new sfWidgetFormFilterInput(),
      'color'                => new sfWidgetFormFilterInput(),
      'size'                 => new sfWidgetFormFilterInput(),
      'buying_price'         => new sfWidgetFormFilterInput(),
      'selling_price'        => new sfWidgetFormFilterInput(),
      'taxation_code'        => new sfWidgetFormFilterInput(),
      'small_pic'            => new sfWidgetFormFilterInput(),
      'large_pic'            => new sfWidgetFormFilterInput(),
      'created_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'synced_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'shop_id'              => new sfWidgetFormFilterInput(),
      'is_synced'            => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'item_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'description1'         => new sfValidatorPass(array('required' => false)),
      'description2'         => new sfValidatorPass(array('required' => false)),
      'description3'         => new sfValidatorPass(array('required' => false)),
      'supplier_number'      => new sfValidatorPass(array('required' => false)),
      'supplier_item_number' => new sfValidatorPass(array('required' => false)),
      'ean'                  => new sfValidatorPass(array('required' => false)),
      'group'                => new sfValidatorPass(array('required' => false)),
      'color'                => new sfValidatorPass(array('required' => false)),
      'size'                 => new sfValidatorPass(array('required' => false)),
      'buying_price'         => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'selling_price'        => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'taxation_code'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'small_pic'            => new sfValidatorPass(array('required' => false)),
      'large_pic'            => new sfValidatorPass(array('required' => false)),
      'created_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'synced_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'shop_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_synced'            => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('items_sync_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ItemsSync';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'item_id'              => 'Number',
      'description1'         => 'Text',
      'description2'         => 'Text',
      'description3'         => 'Text',
      'supplier_number'      => 'Text',
      'supplier_item_number' => 'Text',
      'ean'                  => 'Text',
      'group'                => 'Text',
      'color'                => 'Text',
      'size'                 => 'Text',
      'buying_price'         => 'Number',
      'selling_price'        => 'Number',
      'taxation_code'        => 'Number',
      'small_pic'            => 'Text',
      'large_pic'            => 'Text',
      'created_at'           => 'Date',
      'synced_at'            => 'Date',
      'shop_id'              => 'Number',
      'is_synced'            => 'Boolean',
    );
  }
}
