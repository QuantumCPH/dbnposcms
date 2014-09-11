<?php

/**
 * transactions actions.
 *
 * @package    zapnacrm
 * @subpackage transactions
 * @author     Your name here
 */
class transactionsActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->transactions_list = TransactionsPeer::doSelect(new Criteria());
  }
   public function executeTransaction(sfWebRequest $request) {
        $this->transactions_list = TransactionsPeer::doSelect(new Criteria());
    }
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new TransactionsForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));

    $this->form = new TransactionsForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($transactions = TransactionsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object transactions does not exist (%s).', $request->getParameter('id')));
    $this->form = new TransactionsForm($transactions);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $this->forward404Unless($transactions = TransactionsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object transactions does not exist (%s).', $request->getParameter('id')));
    $this->form = new TransactionsForm($transactions);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($transactions = TransactionsPeer::retrieveByPk($request->getParameter('id')), sprintf('Object transactions does not exist (%s).', $request->getParameter('id')));
    $transactions->delete();

    $this->redirect('transactions/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $transactions = $form->save();

      $this->redirect('transactions/edit?id='.$transactions->getId());
    }
  }
  public function executeOrder(sfWebRequest $request)
  {
      $t=new Criteria();
      
       $t->add(OrdersPeer::STATUS_ID,3);
       $orderconts = OrdersPeer::doCount($t);
       $this->orderconts=$orderconts;
  }
 public function executeReports(sfWebRequest $request)
  {
      
  } 
   public function executeProductNameReport(sfWebRequest $request)
  {
      
  } 
    public function executeProductNameReportData(sfWebRequest $request)
  {
      
  }
   public function executePaymentMethod(sfWebRequest $request)
  {
      
  } 
   public function executeTaxRate(sfWebRequest $request)
  {
      
  } 
    public function executeStaffSale(sfWebRequest $request)
  {
      
  } 
   public function executeStaffDailySale(sfWebRequest $request)
  {
      
  } 
  public function executeMonthlySale(sfWebRequest $request)
  {
      
  } 
    public function executeStockReport(sfWebRequest $request)
  {
        $this->stock_table_id=$request->getParameter('id');
        
        $this->stock=StocksPeer::retrieveByPK($request->getParameter('id'));
  } 
  
    public function executeStockAdjust(sfWebRequest $request) {
        $this->stock_table_id = $request->getParameter('id');

        $this->stock = StocksPeer::retrieveByPK($request->getParameter('id'));

        $st = new Criteria();

        $st->add(StockItemsPeer::STOCK_ID, $request->getParameter('id'));
        $stockItems = StockItemsPeer::doSelect($st);
        $this->stockItems = $stockItems;
    }
 public function executeStockAdjustSubmit(sfWebRequest $request) {
  
        // loop over checked checkboxes
     //    $this->getUser()->setFlash('message', '--A---'); 
        if (isset($_POST['stockItemId']) && $_POST['stockItemId'] != "") {
 // $this->getUser()->setFlash('message', '--B---'); 
               $stockD = StocksPeer::retrieveByPK($request->getParameter('stock_id'));   
            $stocktran=0;
            foreach ($_POST['stockItemId'] as $checkbox) {
           //     $this->getUser()->setFlash('message', '--C---'); 
                //////////////////////////////////////////////////////////////////////////////////////////

   
  
        
        
         //////////////////////////////////////////////////////////       
               
                $stockItem = StockItemsPeer::retrieveByPK($checkbox);
                $items = ItemsPeer::retrieveByPK($stockItem->getCmsItemId());
                  $shop=ShopsPeer::retrieveByPK($stockD->getShopId());
               //     $this->getUser()->setFlash('message', '--C---'); 
                if ($stockItem->getStockValue() > 0) {
                     ///   $this->getUser()->setFlash('message', '--D---'); 
                    if ($stockItem->getStockType() == "positive") {
                      ///    $this->getUser()->setFlash('message', '--E---'); 
                        $parent_type_id = $stockD->getStockId();
                        $transaction_type_id = 10;
                        $parent_type = "Stock In";
                    } else {
                      //    $this->getUser()->setFlash('message', '--FS---'); 
                        $parent_type_id = $stockD->getStockId();
                        $transaction_type_id = 11;
                        $parent_type = "Stock Out";
                    }
                    $transaction = new Transactions();
                    $transaction->setTransactionTypeId($transaction_type_id);
                    $transaction->setShopId($stockD->getShopId());
                    //  $transaction->setShopTransactionId($object->pos_id);
                    $transaction->setQuantity($stockItem->getStockValue());
                    $transaction->setItemId($items->getItemId());
                    $transaction->setShopOrderNumberId(0);
                    $transaction->setShopReceiptNumberId(0);
                    $transaction->setStatusId(3);
                    $transaction->setCreatedAt(time());
                    $transaction->setUpdatedAt(time());
                    $transaction->setDownSync(0);
                    $transaction->setParentType($parent_type);
                    $transaction->setCmsItemId($items->getId());
                    $transaction->setParentTypeId($parent_type_id);
                    $transaction->setSoldPrice(0);
                    $transaction->setDescription1($items->getDescription1());
                    $transaction->setDescription2($items->getDescription2());
                    $transaction->setDescription3($items->getDescription3());
                    $transaction->setSupplierItemNumber($items->getSupplierItemNumber());
                    $transaction->setSupplierNumber($items->getSupplierNumber());
                    $transaction->setEan($items->getEan());

                    $transaction->setColor($items->getColor());
                    $transaction->setGroup($items->getGroup());
                    $transaction->setSize($items->getSize());
                    $transaction->setSellingPrice($items->getSellingPrice());
                    $transaction->setBuyingPrice($items->getBuyingPrice());
                    $transaction->setTaxationCode($items->getTaxationCode());
                    $transaction->setUserId(1);
                    if($transaction->save()){
                        $stocktran=1;
                        
                        $stockItem->setProcessStatus(3);
                        $stockItem->save();
                        
                    }
                }
               
                ///////////////////////////////////////////////////////////
//    echo $checkbox;
            }
            
            if($stocktran){
             if ($shop->getGcmKey() != "") {
                                    new GcmLib("stock_adjust", array($shop->getGcmKey(),$shop));
                                }
                                 $this->getUser()->setFlash('message', 'Stock is adjusted'); 
            
            }
            
        } else {
            $this->getUser()->setFlash('message', 'No Stock Item is slected for adjust');
        }



        $this->redirect('transactions/stockAdjust?id=' . $request->getParameter('stock_id'));

        return sfView::NONE;
    }
  public function executeSaleDetailView(sfWebRequest $request) {
        $this->order_id = $request->getParameter('id');
        $this->branch_number = $request->getParameter('branch_number');

        $sho = new Criteria();
        $sho->add(ShopsPeer::BRANCH_NUMBER, $this->branch_number);
        $shop = ShopsPeer::doSelectOne($sho);

        $st = new Criteria();
        $st->add(TransactionsPeer::ORDER_ID, $this->order_id);
        $st->addAnd(TransactionsPeer::SHOP_ID, $shop->getId());
        $transaction = TransactionsPeer::doSelectOne($st);
        $this->invoice_number = $transaction->getShopReceiptNumberId();
        $tr = new Criteria();

        $tr->add(TransactionsPeer::SHOP_RECEIPT_NUMBER_ID, $transaction->getShopReceiptNumberId());
        $tr->addAnd(TransactionsPeer::STATUS_ID, 3);
        $this->transactions = TransactionsPeer::doSelect($tr);
    }

}
