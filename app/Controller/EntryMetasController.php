<?php
class EntryMetasController extends AppController {
	var $name = 'EntryMetas';
	public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow('cronjob_reminder');
    }
    
    /*
        Cronjob Function ( run daily @ 03.00 AM ) !!
    */
    public function cronjob_reminder()
    {
        // open E-mail library stream !!
        App::uses('CakeEmail', 'Network/Email');
        
        $this->_checksWithdrawal();
        
        $this->_cicilanPerBulan();
        
        // end of cronjob !!
        exit;
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
            $myParentEntry = $this->Entry->meta_details(NULL , NULL , NULL , $value['Entry']['parent_id'] );
            $data = $this->Entry->meta_details(NULL , NULL , NULL , $value['Entry']['id'] );
            $this->Entry->update_invoice_payment($myParentEntry, $data);
            
            array_push($invoice, '('.sprintf("%02d", $key+1).') INV# <a href="'.$this->get_host_name().'admin/entries/'.$myParentEntry['Entry']['entry_type'].'/'.$myParentEntry['Entry']['slug'].'?type='.$data['Entry']['entry_type'].'">'.strtoupper($myParentEntry['Entry']['title']).' ('.$data['Entry']['title'].')</a>');
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
                
                $mybody .= '('.sprintf("%02d", $key+1).') INV# <a href="'.$this->get_host_name().'admin/entries/'.$value['ParentEntry']['entry_type'].'/'.$value['ParentEntry']['slug'].'?type='.$value['Entry']['entry_type'].'">'.strtoupper($value['ParentEntry']['title']).' ('.$value['Entry']['title'].')</a>';
                
                $price = round($value['EntryMeta']['amount'] / $value['EntryMeta']['loan_period'], 2);
                
                $mybody .= ' : Amount $'.toMoney($price, true, true);
                
                if(!empty($value['EntryMeta']['loan_interest_rate']))
                {
                    $price += round($value['EntryMeta']['loan_interest_rate'] * $price / 100, 2);
                    
                    $mybody .= ' + charge ('.$value['EntryMeta']['loan_interest_rate'].'% / month) = $'.toMoney($price, true, true);
                }
                
                $mybody .= ' <a href="'.$this->get_host_name().'admin/entries/'.$value['ParentEntry']['entry_type'].'/'.$value['ParentEntry']['slug'].'/add?type='.$value['Entry']['entry_type'].'">#Add this '.ordinalSuffix($bulanke).' monthly payment record.</a><br>';
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
            return;
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
            'bank'         => $query['EntryMeta']['bank']
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
        
        $myParentEntry = $this->Entry->meta_details(NULL , NULL , NULL , $query['ParentEntry']['id']);
        $this->Entry->update_invoice_payment($myParentEntry, $data );
        
        // redirect to init url ...
        $this->Session->setFlash('Withdrawal fund checks has been executed successfully.','success');
        $this->redirect( redirectSessionNow($_SESSION['now']) );
    }
}
