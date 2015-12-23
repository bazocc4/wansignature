<?php
class EntryMetasController extends AppController {
	var $name = 'EntryMetas';
	public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow('cron');
    }
    
    /*
        Cron Jobs Function ( run daily @ 03.00 AM ) !!
    */
    public function cron($fakeauth)
    {
        if($fakeauth == '169e781bd52860b584879cbe117085da596238f3') // hash pass admin code
		{
			set_time_limit(0);
            ini_set('memory_limit', '-1'); // unlimited memory limit for cron jobs process.

            // open E-mail library stream !!
            App::uses('CakeEmail', 'Network/Email');

            $this->_checksAlmostDue();

            $this->_checksWithdrawal();

            $this->_cicilanPerBulan();
		}
		else if($fakeauth == 'test') // test cron jobs function !!
		{
            $sql = $this->Setting->findByKey('custom-pagination');
			$this->Setting->id = $sql['Setting']['id'];
			$this->Setting->saveField('value' , $sql['Setting']['value'] + 1);
		}
		else
		{
			throw new NotFoundException('Error 404 - Not Found');
		}
        
        // end of cron jobs !!
        dpr("Cron Jobs SUCCESS! (".date('Y-m-d H:i:s').")");
        exit;
    }
    
    public function _checksAlmostDue($days = 1)
    {
        $myTimeStamp = strtotime('+ '.$days.' day');
        $query = $this->EntryMeta->find('all', array(
            'conditions' => array(
                'EntryMeta.key' => 'form-checks_date',
                'STR_TO_DATE( EntryMeta.value ,"%m/%d/%Y")' => date('Y-m-d', $myTimeStamp ),
            )
        ));
        
        if(!empty($query))
        {
            $duedate = date($this->mySetting['date_format'], $myTimeStamp );
            
            // Create the message !!
            $subject = 'WAN System - Checks Almost Due ('.$duedate.') Alert Reminder';

            $header = "<strong>== Checks Almost Due Reminder ==</strong><br/><br/>";
            $header .= "Dear Administrator,<br/>You are receiving this E-mail to remind about some invoice, having payment checks that almost due on <span style='color:red'>".$duedate."</span> as follow :<br/>";

            $footer = "<br/>NB: Please cross-check those payment checks first before their due date.<br/>In any case if some checks are not right, you can cancel those checks by deleting checks data on the website.<br/>Thank you for your attention.";

            // compose the message body !!
            $mybody = $header;
            foreach($query as $key => $value)
            {
                if($value['Entry']['parent_id'] > 0)
                {
                    $myParentEntry = $this->Entry->findById($value['Entry']['parent_id']);
                
                    $mybody .= '('.sprintf("%02d", $key+1).') INV# <a href="'.$this->get_host_name().'admin/entries/'.$myParentEntry['Entry']['entry_type'].'/'.$myParentEntry['Entry']['slug'].'?type='.$value['Entry']['entry_type'].'">'.strtoupper($myParentEntry['Entry']['title']).' ('.$value['Entry']['title'].')</a>';
                }
                else
                {
                    $mybody .= '('.sprintf("%02d", $key+1).') <a href="'.$this->get_host_name().'admin/entries/'.$value['Entry']['entry_type'].'">'.strtoupper($value['Entry']['title']).'</a>';
                }
                
                if(empty($value['Entry']['status']))
                {
                    $mybody .= ' [Cek Titip]';
                }
                
                $mybody .= '<br>';
            }
            $mybody .= $footer;

            // Execute E-mail ...
            $Email = new CakeEmail();
            try{
                $Email->from(array('reminder@wansignature.com'=>'WAN Reminder System'))
                      ->to( array_map("trim" , explode(',' , $this->mySetting['custom-email_admin'] )) )
                      ->subject($subject)
                      ->emailFormat('html')
                      ->template('default','default')
                      ->send($mybody);
            } catch(Exception $e){}
        }
    }
    
    public function _checksWithdrawal()
    {
        $query = $this->EntryMeta->find('all', array(
            'conditions' => array(
                'Entry.status' => 1, // check for CEK LUNAS only ...
                'EntryMeta.key' => 'form-checks_date',
                'STR_TO_DATE( EntryMeta.value ,"%m/%d/%Y")' => date('Y-m-d'),
            )
        ));
        
        $invoice = array();
        foreach($query as $key => $value)
        {
            $myParentEntry = NULL;
            $data = $this->Entry->meta_details(NULL , NULL , NULL , $value['Entry']['id'] );
            
            if($value['Entry']['parent_id'] > 0)
            {
                $myParentEntry = $this->Entry->meta_details(NULL , NULL , NULL , $value['Entry']['parent_id'] );
                array_push($invoice, '('.sprintf("%02d", $key+1).') INV# <a href="'.$this->get_host_name().'admin/entries/'.$myParentEntry['Entry']['entry_type'].'/'.$myParentEntry['Entry']['slug'].'?type='.$data['Entry']['entry_type'].'">'.strtoupper($myParentEntry['Entry']['title']).' ('.$data['Entry']['title'].')</a>');
            }
            else
            {
                array_push($invoice, '('.sprintf("%02d", $key+1).') <a href="'.$this->get_host_name().'admin/entries/'.$data['Entry']['entry_type'].'">'.strtoupper($data['Entry']['title']).'</a>');
            }
            
            $this->Entry->update_invoice_payment($myParentEntry, $data);
        }
        
        if(!empty($invoice))
        {
            // Create the message !!
            $subject = 'WAN System - Checks Withdrawal ('.date($this->mySetting['date_format']).') Alert Reminder';

            $header = "<strong>== Checks Withdrawal Today Reminder ==</strong><br/><br/>";
            $header .= "Dear Administrator,<br/>You are receiving this E-mail to remind about some invoice, having payment checks that can be withdrawn / is due today as follow :<br/>";

            $footer = "<br/>NB: WAN System had already automatically updated payment balance for each invoice related.<br/>Thank you for your attention.";

            // compose the message body !!
            $mybody = $header;
            $mybody .= implode('<br>', $invoice )."<br>";
            $mybody .= $footer;

            // Execute E-mail ...
            $Email = new CakeEmail();
            try{
                $Email->from(array('reminder@wansignature.com'=>'WAN Reminder System'))
                      ->to( array_map("trim" , explode(',' , $this->mySetting['custom-email_admin'] )) )
                      ->subject($subject)
                      ->emailFormat('html')
                      ->template('default','default')
                      ->send($mybody);
            } catch(Exception $e){}
        }
    }
    
    public function _cicilanPerBulan()
    {
        $query = 'SELECT L.entry_id FROM cms_entry_metas as L, cms_entry_metas as D WHERE 
        L.entry_id = D.entry_id AND 
        L.key = "form-loan_period" AND 
        D.key = "form-date" AND 
        ( CURDATE() BETWEEN DATE_ADD(STR_TO_DATE(D.value, "%m/%d/%Y"), INTERVAL 1 MONTH) AND DATE_ADD(STR_TO_DATE(D.value, "%m/%d/%Y"), INTERVAL L.value MONTH) ) AND 
        ( DAYOFMONTH(CURDATE()) = DAYOFMONTH(STR_TO_DATE(D.value, "%m/%d/%Y")) OR 
        LAST_DAY(CURDATE()) = CURDATE() AND 
        DAYOFMONTH(CURDATE()) < DAYOFMONTH(STR_TO_DATE(D.value, "%m/%d/%Y")) )';
        
        $result = $this->EntryMeta->query($query);
        if(!empty($result))
        {
            $query = array_map('breakEntryMetas', $this->Entry->findAllById(array_column(array_column($result, 'L'), 'entry_id')));
            
            // Create the message !!
            $subject = 'WAN System - Loan Installment Withdrawal ('.date($this->mySetting['date_format']).') Alert Reminder';

            $header = "<strong>== Loan Installment Withdrawal (pencairan dana cicilan) Today Reminder ==</strong><br/><br/>";
            $header .= "Dear Administrator,<br/>You are receiving this E-mail to remind about some invoice, having loan installment that can be withdrawn today as follow :<br/>";

            $footer = "<br/>NB: For each invoice above, please add monthly loan installment payment record <strong>ONLY IF</strong> the payer had already made a (cash / transfer) payment.";
            $footer .= "<br/>Thank you for your attention.";

            // compose the message body !!
            $mybody = $header;
            $nowDate = getdate();
            foreach($query as $key => $value)
            {
                $loandate = getdate(strtotime($value['EntryMeta']['date']));
                $bulanke = ($nowDate['mon'] - $loandate['mon'] + 12) % 12;
                
                if( !empty($value['ParentEntry']) )
                {
                    $mybody .= '('.sprintf("%02d", $key+1).') INV# <a href="'.$this->get_host_name().'admin/entries/'.$value['ParentEntry']['entry_type'].'/'.$value['ParentEntry']['slug'].'?type='.$value['Entry']['entry_type'].'">'.strtoupper($value['ParentEntry']['title']).' ('.$value['Entry']['title'].')</a>';
                }
                else
                {
                    $mybody .= '('.sprintf("%02d", $key+1).') <a href="'.$this->get_host_name().'admin/entries/'.$value['Entry']['entry_type'].'">'.strtoupper($value['Entry']['title']).'</a>';
                }
                
                $price = round($value['EntryMeta']['amount'] / $value['EntryMeta']['loan_period'], 2);
                
                $mybody .= ' : Amount $'.toMoney($price, true, true);
                
                if(!empty($value['EntryMeta']['loan_interest_rate']))
                {
                    $price += round($value['EntryMeta']['loan_interest_rate'] * $price / 100, 2);
                    
                    $mybody .= ' + charge ('.$value['EntryMeta']['loan_interest_rate'].'% / month) = $'.toMoney($price, true, true);
                }
                
                if( !empty($value['ParentEntry']) )
                {
                    $mybody .= ' <a href="'.$this->get_host_name().'admin/entries/'.$value['ParentEntry']['entry_type'].'/'.$value['ParentEntry']['slug'].'/add?type='.$value['Entry']['entry_type'].'&cicilan='.$value['Entry']['id'].'&amount='.$price.'&mo='.$bulanke.'">#Add this '.ordinalSuffix($bulanke).' monthly payment record.</a>'.($bulanke == $value['EntryMeta']['loan_period']?' (last installment)':'').'<br>';
                }
                else
                {
                    $mybody .= ' <a href="'.$this->get_host_name().'admin/entries/'.$value['Entry']['entry_type'].'/add?cicilan='.$value['Entry']['id'].'&amount='.$price.'&mo='.$bulanke.'">#Add this '.ordinalSuffix($bulanke).' monthly payment record.</a>'.($bulanke == $value['EntryMeta']['loan_period']?' (last installment)':'').'<br>';
                }
            }
            
            $mybody .= $footer;

            // Execute E-mail ...
            $Email = new CakeEmail();
            try{
                $Email->from(array('reminder@wansignature.com'=>'WAN Reminder System'))
                      ->to( array_map("trim" , explode(',' , $this->mySetting['custom-email_admin'] )) )
                      ->subject($subject)
                      ->emailFormat('html')
                      ->template('default','default')
                      ->send($mybody);
            } catch(Exception $e){}
        }
    }
    
    function deleteTempThumbnails()
    {
    	$this->autoRender = FALSE;
    	$files = glob('img/upload/thumbnails/*'); // get all file names
		foreach($files as $file){ // iterate files
		  if(is_file($file) && strtolower(basename($file)) != 'empty')
		    unlink($file); // delete file
		}
    }
    
    public function withdraw_checks($id)
    {
        if(empty($id))
        {
            throw new NotFoundException('Error 404 - Not Found');
        }
        
        $now = date('m/d/Y');
        
        $query = $this->Entry->meta_details(NULL , NULL , NULL , $id);
        
        $dbkey_haystack = array_column($query['EntryMeta'], 'key');
        $dbkey = array_search('form-checks_date', $dbkey_haystack);
        
        $this->EntryMeta->id = $query['EntryMeta'][$dbkey]['id'];
        $this->EntryMeta->saveField('value', $now );
        
        $datediff = strtotime( $query['EntryMeta'][$dbkey]['value'] ) - strtotime($now);
        $datediff = floor($datediff/(60*60*24)); // convert from seconds into days ...
        
        $new_charge = round($this->mySetting['custom-bunga_cek'] * $datediff * $query['EntryMeta']['amount'] / 3000, 2);
        
        // add new payment ...
        $input = array();
        $input['Entry']['entry_type'] = $query['Entry']['entry_type'];
        $input['Entry']['title'] = 'Checks ('.date_converter($query['EntryMeta'][$dbkey]['value'], $this->mySetting['date_format']).') Withdrawal Charge';
        $input['Entry']['description'] = $query['Entry']['title'];
        $input['Entry']['slug'] = get_slug($input['Entry']['title']);
        $input['Entry']['parent_id'] = $query['Entry']['parent_id'];
        $input['Entry']['created_by'] = $input['Entry']['modified_by'] = $this->user['id'];
        $this->Entry->create();
        $this->Entry->save($input);
        
        // push data EntryMeta ...
        $pushdata = array(
            'date'         => $now,
            'statement'    => ( $query['EntryMeta']['statement'] == 'Debit' ? 'Credit' : 'Debit' ),
            'type'         => 'Cash',
            'hkd_rate'     => $query['EntryMeta']['hkd_rate'],
            'rp_rate'      => $query['EntryMeta']['rp_rate'],
            'gold_price'   => $query['EntryMeta']['gold_price'],
            'amount'       => $new_charge,
            'bank'         => $query['EntryMeta']['bank'],
            
            // SR RR payment ...
            'warehouse_payer'   => $query['EntryMeta']['warehouse_payer'],
            'receiver'          => $query['EntryMeta']['receiver'],
            'vendor'            => $query['EntryMeta']['vendor'],
            'warehouse'         => $query['EntryMeta']['warehouse'],
        );
        $input['EntryMeta']['entry_id'] = $this->Entry->id;
        foreach($pushdata as $key => $value)
        {
            if(!empty($value))
            {
                $input['EntryMeta']['key'] = 'form-'.$key;
                $input['EntryMeta']['value'] = $value;
                $this->EntryMeta->create();
                $this->EntryMeta->save($input);
            }
        }
        
        // update balance ...
        $data = array();
        $data['Entry'][0]['value'] = $input['Entry']['title'];
        $data['Entry'][1]['value'] = $input['Entry']['description'];
        $data['Entry'][3]['value'] = 1; // Completed ...
        $data['EntryMeta'] = $pushdata;
        
        // renew amount ...
        $data['EntryMeta']['amount'] -= $query['EntryMeta']['amount'];
        
        $myParentEntry = NULL;
        if($query['ParentEntry']['id'] > 0)
        {
            $myParentEntry = $this->Entry->meta_details(NULL , NULL , NULL , $query['ParentEntry']['id']);
        }
        $this->Entry->update_invoice_payment($myParentEntry, $data );
        
        // redirect to init url ...
        $this->Session->setFlash('Withdrawal fund checks has been executed successfully.','success');
        $this->redirect( redirectSessionNow($_SESSION['now']) );
    }
    
    public function download_storage($entry_type)
    {
        if(empty($entry_type) || empty($this->request->data))
        {
            throw new NotFoundException('Error 404 - Not Found');
        }
        
        App::import('Vendor', 'excel/worksheet');
        App::import('Vendor', 'excel/workbook');
        
        $storage = $this->Type->findBySlug($entry_type);
        
        $time_start_date = '';
        $time_end_date = '';
        if(!empty($this->request->data['start_date']) && !empty($this->request->data['end_date']))
        {
            $time_start_date = strtotime($this->request->data['start_date']);
            $time_end_date = strtotime($this->request->data['end_date']);
            $filename = 'WAN_'.strtoupper($storage['Type']['name']).'_'.date('dMY', $time_start_date).'_'.date('dMY', $time_end_date);
        }
        else
        {
            $filename = 'WAN_'.strtoupper($storage['Type']['name']).'_'.date('dmy_Hi');
        }
        
        $excel1995 = getTempFolderPath().$filename.'.xls';
        $excel2007 = getTempFolderPath().$filename.'.xlsx';

        // Creating a workbook
        $workbook = new Workbook($excel1995);
        
        // set index 24 as custom gray color for header table background ...
        $workbook->set_custom_color(24, 242,  242,  242);
        
        // query all storage entry !!
        $myList = array_map('breakEntryMetas', $this->Entry->findAllById(explode(',', $this->request->data['record'])) );
        foreach($myList as $listKey => $listValue)
        {
            // prepare interval date first !!
            $start_date = '';
            $end_date = '';
            if(!empty($time_start_date) && !empty($time_end_date))
            {
                $start_date = date($this->mySetting['date_format'], $time_start_date );
                $end_date = date($this->mySetting['date_format'], $time_end_date );
            }
            else // grab from EntryMeta !!
            {
                if(!empty($listValue['EntryMeta']['start_date']))
                {
                    $start_date = date($this->mySetting['date_format'], strtotime($listValue['EntryMeta']['start_date']) );
                }
                
                if(!empty($listValue['EntryMeta']['end_date']))
                {
                    $end_date = date($this->mySetting['date_format'], strtotime($listValue['EntryMeta']['end_date']) );
                }
            }
            
            // Creating the worksheet
            $worksheet1 =& $workbook->add_worksheet($listValue['Entry']['title']);
            
            // $worksheet1->hide_gridlines();
            $worksheet1->set_landscape();
            $worksheet1->fit_to_pages(1,0);
            $worksheet1->repeat_rows(10);

            // Set Column width !!
            foreach(array(5, 15, 10, 15, 15, 15, 15, 20, 20, 15, 10 ) as $key => $value)
            {
                $worksheet1->set_column($key, $key, $value);
            }
            
            // Format TITLE !!
            $indexbaris = 0;        
            $worksheet1->write_string($indexbaris,0,strtoupper($storage['Type']['name']).' REPORT', $workbook->add_format(array(
                'size' => 12,
                'bold' => 1,
            )) );
            
            $indexbaris += 2;
            $worksheet1->write_string($indexbaris,0,'Start Date: '.$start_date, $workbook->add_format(array(
                'size' => 11,
                'bold' => 1,
            )) );        
            $indexbaris++;
            $worksheet1->write_string($indexbaris,0,'End Date: '.$end_date, $workbook->add_format(array(
                'size' => 11,
                'bold' => 1,
            )) );
            
            $indexbaris += 2;
            $worksheet1->write_string($indexbaris,0,$storage['Type']['name'].' Name: '.$listValue['Entry']['title'].(!empty($listValue['EntryMeta']['kode_warehouse'])?' ('.$listValue['EntryMeta']['kode_warehouse'].')':''), $workbook->add_format(array(
                'size' => 11,
                'bold' => 1,
            )) );
            $indexbaris++;
            $worksheet1->write_string($indexbaris,0,'Address: '.str_replace(chr(10), ', ', $listValue['EntryMeta']['alamat']), $workbook->add_format(array(
                'size' => 11,
                'bold' => 1,
            )) );
            $indexbaris++;
            $worksheet1->write_string($indexbaris,0,'Phone: '.$listValue['EntryMeta']['telepon'], $workbook->add_format(array(
                'size' => 11,
                'bold' => 1,
            )) );
            
            $empString = '';
            if(!empty($listValue['EntryMeta']['warehouse_employee']))
            {
                $employee = $this->Account->findAllById(explode('|', $listValue['EntryMeta']['warehouse_employee']));
                foreach($employee as $key => $value)
                {
                    if($key > 0)
                    {
                        $empString .= ', ';
                    }
                    
                    $empString .= $value['User']['firstname'].' '.$value['User']['lastname'];
                }
            }
            
            $indexbaris++;
            $worksheet1->write_string($indexbaris,0,'Employee (PIC): '.$empString, $workbook->add_format(array(
                'size' => 11,
                'bold' => 1,
            )) );

            $indexbaris += 2;
            // write the header ...
            $worksheet1->set_row($indexbaris, 30 );

            $formatTableHeader =& $workbook->add_format();
            $formatTableHeader->set_size(11);
            $formatTableHeader->set_align('center');
            $formatTableHeader->set_align('vcenter');
            $formatTableHeader->set_bold();
            $formatTableHeader->set_border(1);

            // set background custom color of the cell method !!
            $formatTableHeader->set_pattern();
            $formatTableHeader->set_fg_color(24);

            foreach(array('No.', 'Surat Jalan', 'Tanggal', 'Jenis Pengiriman', 'Invoice', 'Tempat Asal', 'Tujuan Kirim', 'Diamond', 'Cor Jewelry', 'Logistic', 'Status') as $key => $value)
            {
                $worksheet1->write_string($indexbaris,$key,$value, $formatTableHeader );
            }

            

            
            
        }
        
        

        // ===================== >>
        // BEGIN CONTENT PROCESS !!
        // ===================== >>
/*
        $format1 =& $workbook->add_format();
        $format1->set_size(10);
        $format1->set_border(1);
        $format1->set_text_wrap();
        $format1->set_align('center');
        $format1->set_align('vcenter');

        $formatdate =& $workbook->add_format();
        $formatdate->set_size(10);
        $formatdate->set_border(1);
        $formatdate->set_text_wrap();			
        $formatdate->set_align('center');
        $formatdate->set_align('vcenter');
        $formatdate->set_num_format('d-mmm-yy'); // 7-AUG-15

        $formatRP =& $workbook->add_format();
        $formatRP->set_size(10);
        $formatRP->set_border(1);
        $formatRP->set_text_wrap();			
        $formatRP->set_align('center');
        $formatRP->set_align('vcenter');
        $formatRP->set_num_format('_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)');

        // prepare modules ...
        $product_type = $this->EntryMeta->find('all', array(
            'conditions' => array(
                'Entry.entry_type' => 'product-type',
                'EntryMeta.key' => 'form-category',
                'EntryMeta.value' => 'Diamond',
            ),
        ));
        $product_type = array_column( array_column($product_type, 'Entry'), 'title', 'slug' );
        $warehouse = array_column( array_column( $this->Entry->findAllByEntryType('warehouse') , 'Entry'), 'title', 'slug' );
        $vendor = array_column( array_column( $this->Entry->findAllByEntryType('vendor') , 'Entry'), 'title', 'slug' );
        $vendor_invoice = array_column( array_column( $this->Entry->findAllByEntryType('dmd-vendor-invoice') , 'Entry'), 'title', 'slug' );

        $client = array_map('breakEntryMetas', $this->Entry->findAllByEntryType('client') );
        $client = array_combine(
            array_column( array_column( $client , 'Entry'), 'slug' ), // keys
            $client // values
        );
        $client_invoice = array_column( array_column( $this->Entry->findAllByEntryType('dmd-client-invoice') , 'Entry'), 'title', 'slug' );

        // query diamond ...
        $query = array_map('breakEntryMetas', $this->Entry->findAllById(explode(',', $this->request->data['record'])) );
*/
        
        // process the query phase !!!
        // ...........................
        // ...........................

        $workbook->close();
        // convert Excel version 5.0 to Excel 2007...
        convertExcelVersion($excel1995 , $excel2007);
        // HTTP headers for new Excel 2007 output buffer ...
        promptDownloadFile($excel2007);
        // delete temp files ...
        unlink($excel1995);
        unlink($excel2007);
        exit;
    }
}