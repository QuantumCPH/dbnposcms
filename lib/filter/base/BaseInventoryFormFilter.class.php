<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Inventory filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseInventoryFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'shop_id'        => new sfWidgetFormFilterInput(),
      'cms_item_id'    => new sfWidgetFormFilterInput(),
      'total'          => new sfWidgetFormFilterInput(),
      'sold'           => new sfWidgetFormFilterInput(),
      'book_out'       => new sfWidgetFormFilterInput(),
      'returned'       => new sfWidgetFormFilterInput(),
      'available'      => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'item_id'        => new sfWidgetFormFilterInput(),
      'delivery_count' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'shop_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'cms_item_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'total'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sold'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'book_out'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'returned'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'available'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'item_id'        => new sfValidatorPass(array('required' => false)),
      'delivery_count' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('inventory_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Inventory';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'shop_id'        => 'Number',
      'cms_item_id'    => 'Number',
      'total'          => 'Number',
      'sold'           => 'Number',
      'book_out'       => 'Number',
      'returned'       => 'Number',
      'available'      => 'Number',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
      'item_id'        => 'Text',
      'delivery_count' => 'Number',
    );
  }
}
