<script type="text/javascript">
 
    $(document).ready(function() {
        $(":file").filestyle({classButton: "btn btn-primary",classInput: "filenamefld"});
         jQuery('.amt').click(function(){
          $('.amt').button('complete');
        });
        } );
    </script>
    <div class="container bs-docs-container itemslist">
<h1><img src="<?php echo sfConfig::get('app_web_url').'images/import_over.png'?>" />&nbsp;Export</h1> 
  <form action="itemExportSubmit" method="post" enctype="multipart/form-data">
      <div class="regForm">
<?php if ($sf_user->hasFlash('file_error')): ?>
                    <div class="alert alert-danger" style="display: block;">
                        <?php echo $sf_user->getFlash('file_error') ?>
                    </div>
                <?php endif; ?>
          <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
  <table  class="table table-striped">
      
   <tr>
   <td style="width:14%">Definition file(xml)</td>
      <td> <input   type="file" name="defile"   class="btn btn-default"></td>
   </tr>
    
     <tr>
   <td> </td>
      <td> <input   type="submit" name="Submit" value="Submit"  data-complete-text="Submitting..."   class="im-ex-btn amt"></td>
   </tr>
     
    
    </table>
      </div>
    </form>
</div>
