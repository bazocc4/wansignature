<?php
class EntryMetasController extends AppController {
	var $name = 'EntryMetas';
	public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow('cronjob_checks', 'cronjob_cicilan');
    }
    
    /* Cronjob Function ( run daily @ 03.00 AM ) !! */
    public function cronjob_checks()
    {
        $query = $this->EntryMeta->find('all', array(
            'conditions' => array(
                'Entry.status' => 1, // check for CEK LUNAS only ...
                'EntryMeta.key' => 'form-checks_date',
                'STR_TO_DATE( EntryMeta.value ,"%m/%d/%Y")' => date('Y-m-d'),
            )
        ));
        
        foreach($query as $key => $value)
        {
            $this->Entry->update_invoice_payment(
                $this->Entry->meta_details(NULL , NULL , NULL , $value['Entry']['parent_id'] ),
                $this->Entry->meta_details(NULL , NULL , NULL , $value['Entry']['id'] )
            );
        }
        
        // end of cronjob !!
        exit;
    }
    
    public function cronjob_cicilan()
    {
        
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
