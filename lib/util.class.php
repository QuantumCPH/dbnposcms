<?php
	class util extends BaseUtil
	{
	
            	public static function html2pdf($invoiceId,$html_content,$pdfname='',$invoicetype)
		{ // return the path to pdf file
			$html_file_path = '';
			$pdf_file_path = '';
			
                
			try {
				if($pdfname!=''){
                                    $tmpname=$pdfname;
                                }else{
                                    $tmpname=rand(0,9999).'-'.microtime(true);
                                }    
                                
                                
                                if($invoicetype==2){
                                //   echo $tmpname; die;
				$fp = fopen($html_file_path = sfConfig::get('sf_upload_dir').'/companyinvoices/html/'.$tmpname.'.htm', 'w');
				fwrite($fp, $html_content);
				fclose($fp);
                               // $pdf_file_path = sfConfig::get('sf_upload_dir').'/tmp/pdf/'.$tmpname.'.pdf';
			        
                                shell_exec($exec_path = "xvfb-run wkhtmltopdf ".$html_file_path.' '.($pdf_file_path = sfConfig::get('sf_upload_dir').'/companyinvoices/pdf/'.$tmpname.'.pdf'). ' --encoding "ISO 8859-1"  --encoding utf8 -q');
                                   sleep(0.25);
				if(file_exists($pdf_file_path)){
                                  $invoice = AgentCompanyInvoicePeer::retrieveByPK($invoiceId);
                                  $invoice->setPdfFile($tmpname.".pdf");
                                  $invoice->save();
                                }
                                }else{
                                  /////////////////////////////////////////////
                                    
                                  	$fp = fopen($html_file_path = sfConfig::get('sf_upload_dir').'/resellerinvoices/html/'.$tmpname.'.htm', 'w');
				fwrite($fp, $html_content);
				fclose($fp);
                               // $pdf_file_path = sfConfig::get('sf_upload_dir').'/tmp/pdf/'.$tmpname.'.pdf';
                              // $exec_path = "xvfb-run wkhtmltopdf ".$html_file_path.' '.($pdf_file_path = sfConfig::get('sf_upload_dir').'/resellerinvoices/pdf/'.$tmpname.'.pdf'). ' --encoding "ISO 8859-1" --auto-servernum --encoding utf8 -q';
				 
			        shell_exec($exec_path = "xvfb-run wkhtmltopdf ".$html_file_path.' '.($pdf_file_path = sfConfig::get('sf_upload_dir').'/resellerinvoices/pdf/'.$tmpname.'.pdf'). ' --encoding "ISO 8859-1"  --encoding utf8 -q');
                                
                                   sleep(0.25);
				if(file_exists($pdf_file_path)){
                                  $invoice = ResellerInvoicePeer::retrieveByPK($invoiceId);
                                  $invoice->setPdfFile($tmpname.".pdf");
                                  $invoice->save();
                                  
                                }
                                  
                              
                                  
                                  return $invoice->getPdfFile()."<br />";
                                }
				
			}
			catch (Exception $e)
			{
				throw $e;
			}			
			
		}
                             
           	 
            
            
            
            public static function saveTotalPayment($invoice_id, $amount)
		{
			$invoice = AgentCompanyInvoicePeer::retrieveByPK($invoice_id);

			$invoice->setTotalpayment($amount);
			$invoice->save();				
		}
		
		
		public static function saveHtmlToInvoice($invoice_id, $html_content)
		{
			$invoice = AgentCompanyInvoicePeer::retrieveByPK($invoice_id);

			$invoice->setInvoiceHtml($html_content);
			$invoice->save();
		}
		
		public static function getSumForAGroupField($group, $field_to_sum, $is_decimal = true)
		{
			$sum = 0;
		
			if (count($group)>0)
				foreach ($group as $key=>$call_row)
				{
						$sum += $call_row[$field_to_sum];
				}
			if ($is_decimal)
				return number_format($sum, 2);
			else
				return $sum;
		}
		
		public static function getCallSummaryUsageTotal($groups)
		{
			$total = 0;
			if (count($groups)>0)
				foreach ($groups as $group)
				{
					$total += self::getSumForAGroupField($group, 'total_sale_price');
				}
				
			return $total;
		}
	public static function saveHtmlToInvoiceReseller($invoice_id, $html_content)
		{
      
			$invoice = ResellerInvoicePeer::retrieveByPK($invoice_id);
                     
			$invoice->setInvoiceHtml($html_content);
			$invoice->save();
		}	
		
	}
	
?>