<?php

/**
 * bookoutNotes actions.
 *
 * @package    zapnacrm
 * @subpackage bookoutNotes
 * @author     khan muhammad
 */
class bookoutNotesActions extends sfActions
{
  public function executeIndex(sfWebRequest $request) {
       $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession'); 
       $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
       if(Access::checkPermissions(4,"index",$user_id,$session_role_id)==false){
           $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
           $this->redirect($request->getReferer());
        }
        $searchFld = $request->getParameter('searchFld');


        $c = new Criteria();

        if ($searchFld) {
            $c->add(BookoutNotesPeer::NOTE_ID, $searchFld . "%", Criteria::LIKE);
        }
        $c->addGroupByColumn (BookoutNotesPeer::NOTE_ID);
        $c->setLimit(1000);

        $this->notes_list = BookoutNotesPeer::doSelect($c);
        $this->notesCount = BookoutNotesPeer::doCount(new Criteria());
    }
 public function executeView($request) {
       $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession'); 
       $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
       if(Access::checkPermissions(4,"view",$user_id,$session_role_id)==false){
           $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
           $this->redirect($request->getReferer());
        }
        $delivery_id = $request->getParameter("id");
        $c = new Criteria();
        $c->add(BookoutNotesPeer::NOTE_ID, $delivery_id);
        if(BookoutNotesPeer::doCount($c)==0){
            $this->redirect('bookoutNotes/index');
        }
        $cd = new Criteria();
        $cd->clearSelectColumns();
        $cd->add(BookoutNotesPeer::NOTE_ID, $delivery_id);
        $cd->addSelectColumn('SUM(' . BookoutNotesPeer::IS_RECEIVED . ') as sumreceived');
        $cd->addGroupByColumn(BookoutNotesPeer::NOTE_ID);
        $d_note = BookoutNotesPeer::doSelectStmt($cd);
        $row = $d_note->fetch(PDO::FETCH_ASSOC);
        $this->edit_del = false;
        if($row['sumreceived'] == 0){
            $this->edit_del = ture;
        }
        $delivery_note = BookoutNotesPeer::doSelectOne($c);
        $this->dn = $delivery_note;
        $this->id = $delivery_note->getNoteId();
        $this->branch = $delivery_note->getBranchNumber();
        $this->company = $delivery_note->getCompanyNumber();
        $this->delivery_date = $delivery_note->getDeliveryDate();
        $this->notes_list = BookoutNotesPeer::doSelect($c);
        
    }
   
    public function executeEdit($request) {
       $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession'); 
       $session_role_id = $this->getUser()->getAttribute('role_id', '', 'backendsession');
        //// Delivery notes module id is 4.
       if(Access::checkPermissions(4,"edit",$user_id,$session_role_id)==false){
           $this->getUser()->setFlash('access_error', $this->getContext()->getI18N()->__('Access Denied.'));
           $this->redirect($request->getReferer());
        }
        $cs  = new Criteria();
        $cs->addAnd(ShopsPeer::STATUS_ID,5, Criteria::NOT_EQUAL);
        $cs->addOR(ShopsPeer::STATUS_ID,3, Criteria::EQUAL);
        $cs->addOR(ShopsPeer::STATUS_ID,NULL, Criteria::EQUAL);
        $shops = ShopsPeer::doSelect($cs);
        $this->shops = $shops;
        
        $delivery_id = $request->getParameter("id");
        $c = new Criteria();
        $c->add(BookoutNotesPeer::NOTE_ID, $delivery_id);
        $delivery_note = BookoutNotesPeer::doSelectOne($c);
        $this->id = $delivery_note->getNoteId();
        $this->branch = $delivery_note->getBranchNumber();
        $this->company = $delivery_note->getCompanyNumber();
        $this->delivery_date = $delivery_note->getDeliveryDate();
        $this->notes_list = BookoutNotesPeer::doSelect($c);
        $this->shopid = $delivery_note->getShopId();
        $this->branchNumber = $delivery_note->getBranchNumber();
        $items = ItemsPeer::doSelect(new Criteria());
        $this->items = $items;
        
    }
       public function executeUpdate($request) {
        $note_id =  $request->getParameter("id");
        $ids = $request->getParameter("dnId");
        $dnItemNo  = $request->getParameter("dnItemNo");
        $dnItemQty = $request->getParameter("dnReceivedQuantity");
        $dndnrply = $request->getParameter("dnreplyComent");
    
        $delivery_date = $request->getParameter("delivery_date");
        $shop_id = $request->getParameter("shop_id");
        $delete_ids = $request->getParameter("deletednId");
//        echo "cnt ".count($delete_ids);
//        echo " uni ".count(array_unique($dnItemNo));
//        $ids = array_diff($ids, $delete_ids);
//        print_r($ids);
//        die;
        $shop = ShopsPeer::retrieveByPK($shop_id);
        $branchNumber = $shop->getBranchNumber();
        $companyNumber = $shop->getCompanyNumber();
        $user_id = $this->getUser()->getAttribute('user_id', '', 'backendsession'); 
        if($request->isMethod('post')){
            $cd = new Criteria();
            $cd->clearSelectColumns();
            $cd->add(BookoutNotesPeer::NOTE_ID, $note_id);
            $cd->addSelectColumn('SUM(' . BookoutNotesPeer::IS_SYNCED . ') as sumsync');
            $cd->addGroupByColumn(BookoutNotesPeer::NOTE_ID);
            $d_note = BookoutNotesPeer::doSelectStmt($cd);
            $row = $d_note->fetch(PDO::FETCH_ASSOC);            
            if($row['sumsync'] > 0){
               $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Bookout Note is synced.'));
               $this->redirect('bookoutNotes/index');  
            } 
            if(count($dnItemNo) ==  count(array_unique($dnItemNo))){
                $valusesave=0;
              for($i=0; $i<count($ids); $i++){
                  $cit = new Criteria();
                  $cit->add(ItemsPeer::ITEM_ID,$dnItemNo[$i],Criteria::EQUAL);
                  $chkinItem = ItemsPeer::doCount($cit);
                  if($dnItemQty[$ids[$i]] < 1){
                     $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Item quantity can\'t be less than 1.'));
                    // $this->redirect('bookoutNotes/edit?id='.$note_id); 
                        $this->redirect('bookoutNotes/index'); 
                  }elseif($chkinItem==0){
                        $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Invalid Item number.'));
                      //  $this->redirect('bookoutNotes/edit?id='.$note_id);
                            $this->redirect('bookoutNotes/index'); 
                  }else{
//                      print_r($ids);die;
                    $cnd = new Criteria();
                    $cnd->add(BookoutNotesPeer::ID,$ids[$i]);
                    $delivery_note = BookoutNotesPeer::doSelectOne($cnd);
                    $delivery_note->setItemId($dnItemNo[$i]);
                    $delivery_note->setReceivedQuantity($dnItemQty[$ids[$i]]);
                     $delivery_note->setReplyComment($dndnrply[$i]);
                    $delivery_note->setDeliveryDate($delivery_date);
                    $delivery_note->setBranchNumber($branchNumber);
                    $delivery_note->setCompanyNumber($companyNumber);
                    $delivery_note->setShopId($shop->getId());
                     $delivery_note->setStatusId(3);
                      $delivery_note->setIsReceived(1);
                      $delivery_note->setReceivedAt(date("Y-m-d H:i:s"));
                    $delivery_note->setUpdatedBy($user_id);
                   // $delivery_note->save(); 
                    
                     if ($delivery_note->save()) {
           $valusesave=1;
           
        }
                    //////////////////////////////////
                    
                 }  
              }
/////////////////////////////////////////////////////////////////////////////////   
                if ($valusesave) {
  if ($shop->getGcmKey() != "") {
                new GcmLib("bookout_updated", array($shop->getGcmKey()));
            }
                }
////////////////////////////////////////////////////////////////////////////////              
              $this->getUser()->setFlash('message', $this->getContext()->getI18N()->__('Bookout note updated successfully.'));
              
           //  $this->redirect('bookoutNotes/view?id='.$note_id); 
              $this->redirect('bookoutNotes/index'); 
            }else{
              $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Items must be different.'));
            //  $this->redirect('bookoutNotes/edit?id='.$note_id);
                  $this->redirect('bookoutNotes/index'); 
            }
//          }else{
//              $this->getUser()->setFlash('edit_error', $this->getContext()->getI18N()->__('Invalid Item number.'));
//              $this->redirect('bookoutNotes/edit?id='.$note_id);
//          } 
        }
        return sfView::NONE;
    }
}
