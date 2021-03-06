<?php
class EntriesController extends AppController {
	public $name = 'Entries';
    public $components = array('RequestHandler','Session','Validation','Auth','PhpExcel');
	public $helpers = array('Form', 'Html', 'Js', 'Time', 'Get','Text','Rss');
	
	private $backEndFolder = '/BackEnds/';
    private $generalOrder = 'sort_order DESC';
	
	public function beforeFilter(){
        parent::beforeFilter();
		$this->Auth->allow('index');
    }
    
    public function download_payment($entry_type) // Diamond / Cor Jewelry
    {
        if(empty($entry_type) || empty($this->request->data))
        {
            throw new NotFoundException('Error 404 - Not Found');
        }
        
        set_time_limit(0); // unlimited time limit execution.
        App::import('Vendor', 'excel/worksheet');
        App::import('Vendor', 'excel/workbook');
        
        $myType = $this->Type->findBySlug($entry_type);
        
        $time_start_date = strtotime($this->request->data['start_date']);
        $time_end_date = strtotime($this->request->data['end_date']);
        
        $filename = 'WAN_STATEMENT_'.str_replace(' ', '_', strtoupper($myType['Type']['name'])).'_'.date('dMY', $time_start_date).'_'.date('dMY', $time_end_date);
        
        $excel1995 = getTempFolderPath().$filename.'.xls';
        $excel2007 = getTempFolderPath().$filename.'.xlsx';

        // Creating a workbook
        $workbook = new Workbook($excel1995);
        
        // set index 24 as custom gray color for header table background ...
        $workbook->set_custom_color(24, 242,  242,  242);
        
        // prepare modules ...
        $client_invoice_type = $this->Type->findBySlug($entry_type=='diamond'?'dmd-client-invoice':'cor-client-invoice');
        $client_payment_type = $this->Type->findBySlug($entry_type=='diamond'?'dc-payment':'cc-payment');
        $sr_payment_type = $this->Type->findBySlug($entry_type=='diamond'?'sr-dmd-payment':'sr-cor-payment');
        
        $vendor = array_column( array_column( $this->Entry->findAllByEntryType('vendor') , 'Entry'), 'title', 'slug' );
        $client = array_column( array_column( $this->Entry->findAllByEntryType('client') , 'Entry'), 'title', 'slug' );
        $warehouse = array_column( array_column( $this->Entry->findAllByEntryType('warehouse') , 'Entry'), 'title', 'slug' );
        
        // query all storage entry !!
        $myList = array_map('breakEntryMetas', $this->Entry->findAllById(explode(',', $this->request->data['record'])) );
        foreach($myList as $listKey => $listValue)
        {
            // Creating the worksheet
            $worksheet1 =& $workbook->add_worksheet($listValue['Entry']['title']);
            
            // $worksheet1->hide_gridlines();
            $worksheet1->set_landscape();
            $worksheet1->fit_to_pages(1,0);
            $worksheet1->repeat_rows(5);

            // Set Column width !!
            foreach(array(5, 10, 15, 35, 15, 15, 10, 10, 10, 10, 15 ) as $key => $value)
            {
                $worksheet1->set_column($key, $key, $value);
            }
            
            // Format TITLE !!
            $indexbaris = 0;        
            $worksheet1->write_string($indexbaris,0,$listValue['Entry']['title'].(!empty($listValue['EntryMeta']['kode_warehouse'])?' ('.$listValue['EntryMeta']['kode_warehouse'].')':'').' - '.strtoupper($myType['Type']['name']).' STATEMENT REPORT', $workbook->add_format(array(
                'size' => 12,
                'bold' => 1,
            )) );
            
            $indexbaris += 2;
            $worksheet1->write_string($indexbaris,0,'Start Date: '.date($this->mySetting['date_format'], $time_start_date ), $workbook->add_format(array(
                'size' => 11,
                'bold' => 1,
            )) );        
            $indexbaris++;
            $worksheet1->write_string($indexbaris,0,'End Date: '.date($this->mySetting['date_format'], $time_end_date ), $workbook->add_format(array(
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
            $formatTableHeader->set_text_wrap();

            // set background custom color of the cell method !!
            $formatTableHeader->set_pattern();
            $formatTableHeader->set_fg_color(24);

            foreach(array('No.', 'Tanggal', 'Invoice', 'Keterangan', 'Payer', 'Receiver', 'Payment Type', 'Total Pcs', 'Debit ('.($entry_type=='diamond'?'USD':'GR').')', 'Credit ('.($entry_type=='diamond'?'USD':'GR').')', 'Accumulated Balance') as $key => $value)
            {
                $worksheet1->write_string($indexbaris,$key,$value, $formatTableHeader );
            }

            // ===================== >>
            // BEGIN STATEMENT PROCESS !!
            // ===================== >>
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
            
            $formatmoney =& $workbook->add_format();
            $formatmoney->set_size(10);
            $formatmoney->set_border(1);
            $formatmoney->set_text_wrap();			
            $formatmoney->set_align('center');
            $formatmoney->set_align('vcenter');
            $formatmoney->set_num_format('#,##0.00');
            
            // query all CLIENT invoice from selected WH first !!
            $temp_data = $this->request->data;
            unset($this->request->data);
            $client_invoice = $this->_admin_default($client_invoice_type,0 , NULL , 'warehouse' , $listValue['Entry']['slug'] , NULL , NULL , NULL , NULL , 'manualset')['myList'];
            $this->request->data = $temp_data;
            
            $query = array();
            if(!empty($client_invoice))
            {
                // query all of their client payment children !!
                $this->request->query['invoice'] = array_column(array_column($client_invoice, 'Entry'), 'id');                
                $query = $this->_admin_default($client_payment_type,0 , NULL , NULL , NULL , NULL , NULL , NULL , NULL , 'manualset')['myList'];
                
                // remake $client_invoice for later usage !!
                $client_invoice = array_combine(
                    array_column( array_column( $client_invoice , 'Entry'), 'slug' ), // keys
                    $client_invoice // values
                );
            }
            
            // then, query all SR payment !!
            $this->request->query['warehouse'] = $listValue['Entry']['slug'];
            $query = array_merge($query, $this->_admin_default($sr_payment_type,0 , NULL , NULL , NULL , NULL , NULL , NULL , NULL , 'manualset')['myList']);
            
            // sort all by date !!
            $query = orderby_metavalue($query , 'EntryMeta' , 'date' , 'ASC' , 'datepicker');
            
            // print all the result !!
            $balance = 0;
            foreach($query as $key => $value)
            {
                $indexbaris++;
                $worksheet1->write( $indexbaris , 0 , $key+1 ,$format1);
                $worksheet1->write( $indexbaris, 1, parseExcelDate($value['EntryMeta']['date']), $formatdate );
                
                if($value['Entry']['parent_id'] > 0) // payment from client ...
                {
                    $worksheet1->write_string( $indexbaris, 2, $value['ParentEntry']['title'], $format1);                    
                    $worksheet1->write_string( $indexbaris, 3, $value['Entry']['title'].(!empty($value['Entry']['description'])?chr(10).$value['Entry']['description']:''), $format1);
                    
                    if(strtolower($value['EntryMeta']['statement']) == 'credit')
                    {
                        $worksheet1->write_string( $indexbaris, 4, $client[ $client_invoice[$value['ParentEntry']['slug']]['EntryMeta']['client'] ], $format1); // payer ...
                        $worksheet1->write_string( $indexbaris, 5, $listValue['Entry']['title'], $format1); // receiver ...
                        
                        // jump to balance field !!
                        $balance += $value['EntryMeta']['amount'];
                        $worksheet1->write( $indexbaris, 8, $value['EntryMeta']['amount'], $formatmoney);
                        $worksheet1->write( $indexbaris, 9, '-', $format1);
                    }
                    else
                    {
                        $worksheet1->write_string( $indexbaris, 4, $listValue['Entry']['title'], $format1); // payer ...
                        $worksheet1->write_string( $indexbaris, 5, $client[ $client_invoice[$value['ParentEntry']['slug']]['EntryMeta']['client'] ], $format1); // receiver ...
                        
                        // jump to balance field !!
                        $balance -= $value['EntryMeta']['amount'];
                        $worksheet1->write( $indexbaris, 8, '-', $format1);
                        $worksheet1->write( $indexbaris, 9, $value['EntryMeta']['amount'], $formatmoney);
                    }
                    
                    $worksheet1->write_string( $indexbaris, 6, $value['EntryMeta']['type'], $format1);                    
                    $worksheet1->write_string( $indexbaris, 7, count(explode('|', $value['EntryMeta']['diamond'].$value['EntryMeta']['cor_jewelry'])).' pc', $format1);
                }
                else // Sold Report ...
                {
                    $worksheet1->write_string( $indexbaris, 2, 'SR', $format1);                    
                    $worksheet1->write_string( $indexbaris, 3, $value['Entry']['title'].(!empty($value['Entry']['description'])?chr(10).$value['Entry']['description']:''), $format1);
                    
                    if(strtolower($value['EntryMeta']['statement']) == 'debit')
                    {
                        $worksheet1->write_string( $indexbaris, 4, $warehouse[$value['EntryMeta']['warehouse_payer']], $format1); // payer ...
                        $worksheet1->write_string( $indexbaris, 5, (strtolower($value['EntryMeta']['receiver']) == 'vendor'?$vendor[ $value['EntryMeta']['vendor'] ]:$warehouse[ $value['EntryMeta']['warehouse'] ]) , $format1); // receiver ...
                    }
                    else
                    {
                        $worksheet1->write_string( $indexbaris, 4, (strtolower($value['EntryMeta']['receiver']) == 'vendor'?$vendor[ $value['EntryMeta']['vendor'] ]:$warehouse[ $value['EntryMeta']['warehouse'] ]) , $format1); // payer ...
                        $worksheet1->write_string( $indexbaris, 5, $warehouse[$value['EntryMeta']['warehouse_payer']], $format1); // receiver ...
                    }
                    
                    $worksheet1->write_string( $indexbaris, 6, $value['EntryMeta']['type'], $format1);                    
                    $worksheet1->write_string( $indexbaris, 7, count(explode('|', $value['EntryMeta']['diamond'].$value['EntryMeta']['cor_jewelry'])).' pc', $format1);
                    
                    if(strtolower($value['EntryMeta']['statement']) == 'debit' && $value['EntryMeta']['warehouse_payer'] == $listValue['Entry']['slug'] || strtolower($value['EntryMeta']['statement']) == 'credit' && $value['EntryMeta']['warehouse_payer'] != $listValue['Entry']['slug'])
                    {
                        $balance -= $value['EntryMeta']['amount'];
                        $worksheet1->write( $indexbaris, 8, '-', $format1);
                        $worksheet1->write( $indexbaris, 9, $value['EntryMeta']['amount'], $formatmoney);
                    }
                    else
                    {
                        $balance += $value['EntryMeta']['amount'];
                        $worksheet1->write( $indexbaris, 8, $value['EntryMeta']['amount'], $formatmoney);
                        $worksheet1->write( $indexbaris, 9, '-', $format1);
                    }
                }
                
                $worksheet1->write( $indexbaris, 10, $balance, $formatmoney);
            }
        }
        
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
    
    public function download_invoice($entry_type)
    {
        if(empty($entry_type) || empty($this->request->data))
        {
            throw new NotFoundException('Error 404 - Not Found');
        }
        
        set_time_limit(0); // unlimited time limit execution.
        App::import('Vendor', 'excel/worksheet');
        App::import('Vendor', 'excel/workbook');
        
        $myType = $this->Type->findBySlug($entry_type);
        
        // is it Diamond or Cor Jewelry invoice ?
        $DMD = (strpos($myType['Type']['slug'], 'dmd-')!==FALSE?true:false);

        // is it Vendor or Client invoice ?
        $VENDOR = (strpos($myType['Type']['slug'], '-vendor-')!==FALSE?true:false);
        
        $time_start_date = strtotime($this->request->data['start_date']);
        $time_end_date = strtotime($this->request->data['end_date']);
        
        $filename = 'WAN_'.str_replace(' ', '_', strtoupper($myType['Type']['name'])).'_'.date('dMY', $time_start_date).'_'.date('dMY', $time_end_date);
        
        $excel1995 = getTempFolderPath().$filename.'.xls';
        $excel2007 = getTempFolderPath().$filename.'.xlsx';

        // Creating a workbook
        $workbook = new Workbook($excel1995);
        
        // set index 24 as custom gray color for header table background ...
        $workbook->set_custom_color(24, 242,  242,  242);
        
        // Creating the worksheet
        $worksheet1 =& $workbook->add_worksheet();

        // $worksheet1->hide_gridlines();
        $worksheet1->set_landscape();
        $worksheet1->fit_to_pages(1,0);
        $worksheet1->repeat_rows(0,2);

        // Set Column width !!
        $colwidth = array(
            'dmd-vendor-invoice' => array(5, 20, 10, 10, 15, 10, 10, 15, 10, 50 ),
            'cor-vendor-invoice' => array(5, 20, 10, 10, 15, 15, 10, 70),
            'dmd-client-invoice' => array(5, 15, 10, 15, 15, 15, 10, 10, 10, 10, 40),
            'cor-client-invoice' => array(5, 15, 10, 15, 15, 15, 10, 10, 10, 10, 40),
        );
        
        foreach($colwidth[$entry_type] as $key => $value)
        {
            $worksheet1->set_column($key, $key, $value);
        }

        // Format TITLE !!
        $report_title = array(
            'dmd-vendor-invoice' => 'DIAMOND VENDOR INVOICE',
            'cor-vendor-invoice' => 'COR JEWELRY VENDOR INVOICE',
            'dmd-client-invoice' => 'DIAMOND CLIENT INVOICE',
            'cor-client-invoice' => 'COR JEWELRY CLIENT INVOICE',
        );
        
        $indexbaris = 0;        
        $worksheet1->write_string($indexbaris,0,$report_title[$entry_type].' REPORT ('.date($this->mySetting['date_format'], $time_start_date ).' - '.date($this->mySetting['date_format'], $time_end_date ).')', $workbook->add_format(array(
            'size' => 12,
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
        $formatTableHeader->set_text_wrap();

        // set background custom color of the cell method !!
        $formatTableHeader->set_pattern();
        $formatTableHeader->set_fg_color(24);
        
        $table_header = array(
            'dmd-vendor-invoice' => array('No.', 'Invoice', 'Tanggal', 'Vendor', 'Warehouse', 'Currency', 'HKD Rate / $1 USD', 'Total Price (USD)', 'Total Pcs', 'Diamond Purchased'),
            'cor-vendor-invoice' => array('No.', 'Invoice', 'Tanggal', 'Vendor', 'Warehouse', 'Total Weight (GR)', 'Total Pcs', 'Cor Jewelry Purchased'),
            'dmd-client-invoice' => array('No.', 'Invoice', 'Tanggal', 'Client', 'Wholesaler', 'Salesman', 'Sale Venue', 'Rp Rate / $1 USD', 'Total Price (USD)', 'Total Pcs', 'Diamond Sold'),
            'cor-client-invoice' => array('No.', 'Invoice', 'Tanggal', 'Client', 'Wholesaler', 'Salesman', 'Sale Venue', 'Gold Price / 1GR', 'Total Weight (GR)', 'Total Pcs', 'Cor Jewelry Sold'),
        );

        foreach($table_header[$entry_type] as $key => $value)
        {
            $worksheet1->write_string($indexbaris,$key,$value, $formatTableHeader );
        }
        
        // ===================== >>
        // BEGIN INVOICE PROCESS !!
        // ===================== >>
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
        
        $formatmoney =& $workbook->add_format();
        $formatmoney->set_size(10);
        $formatmoney->set_border(1);
        $formatmoney->set_text_wrap();			
        $formatmoney->set_align('center');
        $formatmoney->set_align('vcenter');
        $formatmoney->set_num_format('#,##0.00');
        
        $formatRP =& $workbook->add_format();
        $formatRP->set_size(10);
        $formatRP->set_border(1);
        $formatRP->set_text_wrap();			
        $formatRP->set_align('center');
        $formatRP->set_align('vcenter');
        $formatRP->set_num_format('_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)');
        
        // prepare modules ...
        if($DMD)
        {
            $product_type = $this->EntryMeta->get_diamond_type();
        }
        
        $temp_order = $_SESSION['order_by'];
        $_SESSION['order_by'] = 'form-date ASC';
        
        // query all invoice entry matched the interval date !!
        $query = $this->_admin_default($myType,0 , NULL , NULL , NULL , NULL , NULL , NULL , NULL , 'manualset')['myList'];
        switch($entry_type)
        {
            case 'dmd-vendor-invoice':
                foreach($query as $key => $value)
                {
                    $indexbaris++;
                    $worksheet1->write( $indexbaris , 0 , $key+1 ,$format1);
                    $worksheet1->write_string( $indexbaris, 1, $value['Entry']['title'], $format1);
                    $worksheet1->write( $indexbaris, 2, parseExcelDate($value['EntryMeta']['date']), $formatdate );
                    $worksheet1->write_string($indexbaris, 3, $this->Entry->findByEntryTypeAndSlug('vendor', $value['EntryMeta']['vendor'])['Entry']['title'], $format1 );
                    $worksheet1->write_string($indexbaris, 4, $this->Entry->findByEntryTypeAndSlug('warehouse', $value['EntryMeta']['warehouse'])['Entry']['title'], $format1 );
                    $worksheet1->write_string($indexbaris, 5, $value['EntryMeta']['currency'], $format1);
                    $worksheet1->write($indexbaris, 6, $value['EntryMeta']['hkd_rate'], $format1);
                    $worksheet1->write($indexbaris, 7, $value['EntryMeta']['total_price'], $formatmoney);
                    $worksheet1->write_string($indexbaris, 8, $value['EntryMeta']['total_pcs'].' pc', $format1);
                    
                    $diamond = $this->EntryMeta->find('all', array(
                        'conditions' => array(
                            'Entry.entry_type' => 'diamond',
                            'EntryMeta.key' => 'form-vendor_invoice_code',
                            'EntryMeta.value' => $value['Entry']['slug'],
                        ),
                        'order' => array('Entry.title ASC'),
                    ));
                    $diamond = $this->EntryMeta->findAllByEntryIdAndKey(array_column(array_column($diamond, 'Entry'), 'id'), 'form-product_type');
                    $diamond = implode(', ', array_map(function($el) use($product_type){ return $el['Entry']['title'].' '.$product_type[$el['EntryMeta']['value']]; }, $diamond));
                    $worksheet1->write_string( $indexbaris, 9, $diamond, $format1);
                }
                break;
            case 'cor-vendor-invoice':
                foreach($query as $key => $value)
                {
                    $indexbaris++;
                    $worksheet1->write( $indexbaris , 0 , $key+1 ,$format1);
                    $worksheet1->write_string( $indexbaris, 1, $value['Entry']['title'], $format1);
                    $worksheet1->write( $indexbaris, 2, parseExcelDate($value['EntryMeta']['date']), $formatdate );
                    $worksheet1->write_string($indexbaris, 3, $this->Entry->findByEntryTypeAndSlug('vendor', $value['EntryMeta']['vendor'])['Entry']['title'], $format1 );
                    $worksheet1->write_string($indexbaris, 4, $this->Entry->findByEntryTypeAndSlug('warehouse', $value['EntryMeta']['warehouse'])['Entry']['title'], $format1 );
                    $worksheet1->write($indexbaris, 5, $value['EntryMeta']['total_weight'], $formatmoney);
                    $worksheet1->write_string($indexbaris, 6, $value['EntryMeta']['total_pcs'].' pc', $format1);
                    
                    $cor = $this->EntryMeta->find('all', array(
                        'conditions' => array(
                            'Entry.entry_type' => 'cor-jewelry',
                            'EntryMeta.key' => 'form-vendor_invoice_code',
                            'EntryMeta.value' => $value['Entry']['slug'],
                        ),
                        'order' => array('Entry.title ASC'),
                    ));
                    $cor = implode(', ', array_column(array_column($cor, 'Entry'), 'title'));
                    $worksheet1->write_string($indexbaris, 7, $cor, $format1);
                }
                break;
            case 'dmd-client-invoice':
                foreach($query as $key => $value)
                {
                    $indexbaris++;
                    $worksheet1->write( $indexbaris , 0 , $key+1 ,$format1);
                    $worksheet1->write_string( $indexbaris, 1, $value['Entry']['title'], $format1);
                    $worksheet1->write( $indexbaris, 2, parseExcelDate($value['EntryMeta']['date']), $formatdate );
                    
                    $client = $this->Entry->findByEntryTypeAndSlug('client', $value['EntryMeta']['client']);
                    if($client['EntryMeta'][0]['key'] == 'form-kode_pelanggan')
                    {
                        $client = $client['Entry']['title'].' ('.$client['EntryMeta'][0]['value'].')';
                    }
                    else
                    {
                        $client = $client['Entry']['title'];
                    }                    
                    $worksheet1->write_string($indexbaris, 3, $client, $format1 );
                    
                    $wholesaler = '-';
                    if(!empty($value['EntryMeta']['wholesaler']))
                    {
                        $wholesaler = $this->Entry->findByEntryTypeAndSlug('client', $value['EntryMeta']['wholesaler']);
                        if($wholesaler['EntryMeta'][0]['key'] == 'form-kode_pelanggan')
                        {
                            $wholesaler = $wholesaler['Entry']['title'].' ('.$wholesaler['EntryMeta'][0]['value'].')';
                        }
                        else
                        {
                            $wholesaler = $wholesaler['Entry']['title'];
                        } 
                    }
                    $worksheet1->write_string($indexbaris, 4, $wholesaler, $format1 );
                    $worksheet1->write_string($indexbaris, 5, (!empty($value['EntryMeta']['salesman'])?$this->Entry->findByEntryTypeAndSlug('salesman', $value['EntryMeta']['salesman'])['Entry']['title']:'-'), $format1 );
                    $worksheet1->write_string($indexbaris, 6, $this->Entry->findByEntryTypeAndSlug($value['EntryMeta']['sale_venue'], $value['EntryMeta']['warehouse'].$value['EntryMeta']['exhibition'])['Entry']['title'], $format1);
                    $worksheet1->write($indexbaris, 7, $value['EntryMeta']['rp_rate'], $formatRP);
                    $worksheet1->write($indexbaris, 8, $value['EntryMeta']['total_price'], $formatmoney);
                    $worksheet1->write_string($indexbaris, 9, $value['EntryMeta']['total_pcs'].' pc', $format1);
                    
                    $diamond = $this->EntryMeta->find('all', array(
                        'conditions' => array(
                            'Entry.entry_type' => 'diamond',
                            'EntryMeta.key' => 'form-client_invoice_code',
                            'EntryMeta.value' => $value['Entry']['slug'],
                        ),
                        'order' => array('Entry.title ASC'),
                    ));
                    $diamond = $this->EntryMeta->findAllByEntryIdAndKey(array_column(array_column($diamond, 'Entry'), 'id'), 'form-product_type');
                    $diamond = implode(', ', array_map(function($el) use($product_type){ return $el['Entry']['title'].' '.$product_type[$el['EntryMeta']['value']]; }, $diamond));
                    $worksheet1->write_string( $indexbaris, 10, $diamond, $format1);
                }
                break;
            case 'cor-client-invoice':
                foreach($query as $key => $value)
                {
                    $indexbaris++;
                    $worksheet1->write( $indexbaris , 0 , $key+1 ,$format1);
                    $worksheet1->write_string( $indexbaris, 1, $value['Entry']['title'], $format1);
                    $worksheet1->write( $indexbaris, 2, parseExcelDate($value['EntryMeta']['date']), $formatdate );
                    
                    $client = $this->Entry->findByEntryTypeAndSlug('client', $value['EntryMeta']['client']);
                    if($client['EntryMeta'][0]['key'] == 'form-kode_pelanggan')
                    {
                        $client = $client['Entry']['title'].' ('.$client['EntryMeta'][0]['value'].')';
                    }
                    else
                    {
                        $client = $client['Entry']['title'];
                    }                    
                    $worksheet1->write_string($indexbaris, 3, $client, $format1 );
                    
                    $wholesaler = '-';
                    if(!empty($value['EntryMeta']['wholesaler']))
                    {
                        $wholesaler = $this->Entry->findByEntryTypeAndSlug('client', $value['EntryMeta']['wholesaler']);
                        if($wholesaler['EntryMeta'][0]['key'] == 'form-kode_pelanggan')
                        {
                            $wholesaler = $wholesaler['Entry']['title'].' ('.$wholesaler['EntryMeta'][0]['value'].')';
                        }
                        else
                        {
                            $wholesaler = $wholesaler['Entry']['title'];
                        } 
                    }
                    $worksheet1->write_string($indexbaris, 4, $wholesaler, $format1 );
                    $worksheet1->write_string($indexbaris, 5, (!empty($value['EntryMeta']['salesman'])?$this->Entry->findByEntryTypeAndSlug('salesman', $value['EntryMeta']['salesman'])['Entry']['title']:'-'), $format1 );
                    $worksheet1->write_string($indexbaris, 6, $this->Entry->findByEntryTypeAndSlug($value['EntryMeta']['sale_venue'], $value['EntryMeta']['warehouse'].$value['EntryMeta']['exhibition'])['Entry']['title'], $format1);
                    $worksheet1->write($indexbaris, 7, $value['EntryMeta']['gold_price'], $formatRP);
                    $worksheet1->write($indexbaris, 8, $value['EntryMeta']['total_weight'], $formatmoney);
                    $worksheet1->write_string($indexbaris, 9, $value['EntryMeta']['total_pcs'].' pc', $format1);
                    
                    $cor = $this->EntryMeta->find('all', array(
                        'conditions' => array(
                            'Entry.entry_type' => 'cor-jewelry',
                            'EntryMeta.key' => 'form-client_invoice_code',
                            'EntryMeta.value' => $value['Entry']['slug'],
                        ),
                        'order' => array('Entry.title ASC'),
                    ));
                    $cor = implode(', ', array_column(array_column($cor, 'Entry'), 'title'));
                    $worksheet1->write_string($indexbaris, 10, $cor, $format1);
                }
                break;
        }
        
        // get back OLD order_by !!
        $_SESSION['order_by'] = $temp_order;

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
    
    public function download_storage($entry_type)
    {
        if(empty($entry_type) || empty($this->request->data))
        {
            throw new NotFoundException('Error 404 - Not Found');
        }
        
        set_time_limit(0); // unlimited time limit execution.
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
            $filename = 'WAN_'.strtoupper($storage['Type']['name']).'_'.date('d.m.y_H.i');
        }
        
        $excel1995 = getTempFolderPath().$filename.'.xls';
        $excel2007 = getTempFolderPath().$filename.'.xlsx';

        // Creating a workbook
        $workbook = new Workbook($excel1995);
        
        // set index 24 as custom gray color for header table background ...
        $workbook->set_custom_color(24, 242,  242,  242);
        
        // prepare modules ...
        $product_type = $this->EntryMeta->get_diamond_type();
        $myType = $this->Type->findBySlug('surat-jalan');
        
        $temp_order = $_SESSION['order_by'];
        $_SESSION['order_by'] = 'form-date ASC';
        
        $statusDict = array('On Process', 'Accepted', 'Repair');    
        
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

            // ===================== >>
            // BEGIN S.J. PROCESS !!
            // ===================== >>
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

            $this->request->query['storage'] = $storage['Type']['slug'];
            $this->request->query['content'] = $listValue['Entry']['slug'];
            $query = $this->_admin_default($myType,0 , NULL , NULL , NULL , NULL , NULL , NULL , NULL , 'manualset')['myList'];
            foreach($query as $key => $value)
            {
                $indexbaris++;
                $worksheet1->write( $indexbaris , 0 , $key+1 ,$format1);
                $worksheet1->write_string( $indexbaris, 1, $value['Entry']['title'], $format1);
                $worksheet1->write( $indexbaris, 2, parseExcelDate($value['EntryMeta']['date']), $formatdate );
                $worksheet1->write_string( $indexbaris, 3, $value['EntryMeta']['delivery_type'] , $format1 );
                
                $invoice = trim($value['EntryMeta']['dmd_vendor_invoice'].$value['EntryMeta']['cor_vendor_invoice'].$value['EntryMeta']['dmd_client_invoice'].$value['EntryMeta']['cor_client_invoice']);
                if(!empty($invoice))
                {
                    $invoice = $this->Entry->findBySlug($invoice)['Entry']['title'];
                }
                else
                {
                    $invoice = '-';
                }
                $worksheet1->write_string( $indexbaris, 4, $invoice , $format1 );
                
                $partners = trim($value['EntryMeta']['client'].$value['EntryMeta']['vendor'].$value['EntryMeta']['salesman']);
                $origin = trim($value['EntryMeta']['warehouse_origin'].$value['EntryMeta']['exhibition_origin']);
                $destination = trim($value['EntryMeta']['warehouse_destination'].$value['EntryMeta']['exhibition_destination']);
                
                $worksheet1->write_string( $indexbaris, 5, $this->Entry->findBySlug(!empty($origin)?$origin:$partners)['Entry']['title'], $format1); // origin place ...
                $worksheet1->write_string( $indexbaris, 6, $this->Entry->findBySlug(!empty($destination)?$destination:$partners)['Entry']['title'], $format1); // destination place ...
                
                $diamond = '-';
                if(!empty($value['EntryMeta']['diamond']))
                {
                    $diamond = $this->Entry->findAllByEntryTypeAndSlug('diamond', explode('|', $value['EntryMeta']['diamond']));
                    $diamond = implode(', ', array_map(function($el) use($product_type){ return $el['Entry']['title'].' '.$product_type[$el['EntryMeta'][0]['value']]; }, $diamond));
                }
                $worksheet1->write_string( $indexbaris, 7, $diamond, $format1);
                
                $cor_jewelry = '-';
                if(!empty($value['EntryMeta']['cor_jewelry']))
                {
                    $cor_jewelry = $this->Entry->findAllByEntryTypeAndSlug('cor-jewelry', explode('|', $value['EntryMeta']['cor_jewelry']));
                    $cor_jewelry = implode(', ', array_column(array_column($cor_jewelry, 'Entry'), 'title'));
                }
                $worksheet1->write_string( $indexbaris, 8, $cor_jewelry, $format1);
                
                $logistic = '-';
                if(!empty($value['EntryMeta']['logistic']))
                {
                    $pecah_logistic = explode('|', $value['EntryMeta']['logistic']);
                    $logistic = $this->Entry->findAllByEntryTypeAndSlug('logistic', array_map(function($el){ return explode('_', $el)[0]; }, $pecah_logistic));
                    $logistic = implode(', ', array_map(function($el1, $el2){ return explode('_', $el2)[1].' '.$el1['Entry']['title']; }, $logistic, $pecah_logistic));
                }
                $worksheet1->write_string( $indexbaris, 9, $logistic, $format1);
                
                // status SJ !!
                $worksheet1->write_string( $indexbaris, 10, $statusDict[$value['Entry']['status']], $format1);
            }
        }
        
        // get back OLD order_by !!
        $_SESSION['order_by'] = $temp_order;

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
    
    public function download_jewelry()
    {
        if(empty($this->request->data))
        {
            throw new NotFoundException('Error 404 - Not Found');
        }
        
        set_time_limit(0); // unlimited time limit execution.
        App::import('Vendor', 'excel/worksheet');
        App::import('Vendor', 'excel/workbook');

        $filename = 'WAN_JEWELRY_';
        $sold_report = false;
        if(!empty($this->request->query['cidm']) && !empty($this->request->query['cidy']))
        {
            $filename .= 'SOLD_'.date("MY", strtotime($this->request->query['cidm'].'/1/'.$this->request->query['cidy']) );
            $sold_report = true;
        }
        else
        {
            $filename .= date('d.m.y_H.i');
        }
        
        $excel1995 = getTempFolderPath().$filename.'.xls';
        $excel2007 = getTempFolderPath().$filename.'.xlsx';

        // Creating a workbook
        $workbook = new Workbook($excel1995);
        // Creating the worksheet
        $worksheet1 =& $workbook->add_worksheet();

        // $worksheet1->hide_gridlines();
        $worksheet1->set_landscape();
        $worksheet1->fit_to_pages(0,0);
        $worksheet1->repeat_rows(0,2);

        $worksheet1->set_column(0, 1, 8);
        $worksheet1->set_column(2, 46, 15);
        $worksheet1->set_column(47, 47, 55);

        // Format for the headings
        $formatot =& $workbook->add_format();
        $formatot->set_size(12);
        $formatot->set_bold();
        $formatot->set_border(1);
        $formatot->set_merge();
        $formatot->set_align('vcenter');
        
        // ==================== >>>
        // write the 1st header ...
        // ==================== >>>
        // set certain index as custom color for header table background ...
        $workbook->set_custom_color(24, 112 , 48 , 160 );
        $workbook->set_custom_color(25, 0 , 176 , 240 );
        $workbook->set_custom_color(26, 255 , 0 , 0 );
        $workbook->set_custom_color(27, 0 , 0 , 0 );
        $workbook->set_custom_color(28, 0 , 176 , 80 );
        $workbook->set_custom_color(29, 128 , 96 , 0 );
        $workbook->set_custom_color(30, 123 , 123 , 123 );
        $colorDict = array();
        $counterColor = 24;
        
        $indexbaris = 1;
        $worksheet1->set_row($indexbaris, 17);

        $worksheet1->write($indexbaris,1,"MERCHANDISE INFO … MERCHANDISE INFO … MERCHANDISE INFO … MERCHANDISE INFO",$formatot);
        for($col=2 ; $col <= 8 ; ++$col)    $worksheet1->write_blank($indexbaris,$col,$formatot);
        
        $colorDict[9] = $counterColor++;
        $worksheet1->write($indexbaris,9,"VENDOR INFO … VENDOR INFO … VENDOR INFO … VENDOR INFO",$formatot);
        for($col=10 ; $col <= 14 ; ++$col)
        {
            $colorDict[$col] = $colorDict[9];
            $worksheet1->write_blank($indexbaris,$col,$formatot);
        }
        
        $colorDict[15] = $counterColor++;
        $worksheet1->write($indexbaris,15,"STATUS BARANG … STATUS BARANG",$formatot);
        for($col=16 ; $col <= 17 ; ++$col)
        {
            $colorDict[$col] = $colorDict[15];
            $worksheet1->write_blank($indexbaris,$col,$formatot);
        }
        
        $colorDict[18] = $counterColor++;
        $worksheet1->write($indexbaris,18,"CLIENT INFO … CLIENT INFO",$formatot);
        for($col=19 ; $col <= 20 ; ++$col)
        {
            $colorDict[$col] = $colorDict[18];
            $worksheet1->write_blank($indexbaris,$col,$formatot);
        }
        
        $colorDict[21] = $counterColor++;
        $worksheet1->write($indexbaris,21,"SR TO WAN",$formatot);
        $colorDict[22] = $colorDict[21];
        $worksheet1->write_blank($indexbaris,22,$formatot);
        
        $colorDict[23] = $counterColor++;
        $worksheet1->write($indexbaris,23,"SOLD INVOICE TO CLIENT … SOLD INVOICE TO CLIENT … SOLD INVOICE TO CLIENT … SOLD INVOICE TO CLIENT … SOLD INVOICE TO CLIENT … SOLD INVOICE TO CLIENT … SOLD INVOICE TO CLIENT",$formatot);
        for($col=24 ; $col <= 37 ; ++$col)
        {
            $colorDict[$col] = $colorDict[23];
            $worksheet1->write_blank($indexbaris,$col,$formatot);
        }
        
        $colorDict[38] = $counterColor++;
        $worksheet1->write($indexbaris,38,"TYPE OF PAYMENT … TYPE OF PAYMENT … TYPE OF PAYMENT … TYPE OF PAYMENT",$formatot);
        for($col=39 ; $col <= 44 ; ++$col)
        {
            $colorDict[$col] = $colorDict[38];
            $worksheet1->write_blank($indexbaris,$col,$formatot);
        }
        
        $colorDict[45] = $counterColor++;
        $worksheet1->write($indexbaris,45,"DESCRIPTION INFO … DESCRIPTION INFO … DESCRIPTION INFO",$formatot);
        for($col=46 ; $col <= 47 ; ++$col)
        {
            $colorDict[$col] = $colorDict[45];
            $worksheet1->write_blank($indexbaris,$col,$formatot);
        }
        
        // ==================== >>>
        // write the 2nd header ...
        // ==================== >>>
        $indexbaris++;
        $worksheet1->set_row($indexbaris, 50);
        
        foreach(array(
            NULL, "NO.", "ITEM CODE #", "TYPE OF GOODS", "BRAND OF GOODS", "COLOR OF GOODS", "ITEM WEIGHT", "GR", "ITEM SIZE",
            "VD", "X", "KET", "VD INV #", "PCS", "GR",
            "WH", "TGL BRG MASUK", "STATUS",
            "CLIENT NAME", "2ND LEVEL CLIENT NAME", "CODE",
            "SR", "DATE",
            "SELL INV DATE".($sold_report?' ▲':''), "SELL INV #", "∑ PCS SOLD", "SOLD 125 (GR)", "X 125", "SOLD 100 (GR)", "X 100", "SOLD 110 (GR)", "X 110", "SOLD 115 (GR)", "X 115", "DISC/RETURN", "∑ SOLD 24K", "INV BALANCE", "GOLD PRICE",
            "PAYMENT : CT (BAHAN LOKAL) & LD", "PAYMENT : ROSOK", "PAYMENT : CHEQUES", "PAYMENT : CASH OR TRANSFER", "PAYMENT : DEBIT OR CREDIT CARD", "PAYMENT : RETURN GOODS", "TOTAL PAYMENT 24K (GR)",
            "CLIENT TOTAL BALANCE", "TRANSACTION HISTORY", "KETERANGAN / DETAIL BARANG",
        ) as $key => $value)
        {
            if(!empty($value))
            {
                $tempformat = array(
                    array('key' => 'size',      'value' => 12 ),
                    array('key' => 'bold',      'value' => 1 ),
                    array('key' => 'border',    'value' => 1 ),
                    array('key' => 'text_wrap', 'value' => 1 ),
                    array('key' => 'align',     'value' => 'center' ),                        
                    array('key' => 'align',     'value' => 'vcenter' ),
                );
                
                if(!empty($colorDict[$key]))
                {
                    // set background custom color of the cell method w/ white text !!
                    array_push($tempformat,
                        array('key' => 'pattern',   'value' => 1 ),
                        array('key' => 'fg_color',  'value' => $colorDict[$key] ),
                        array('key' => 'color',     'value' => 'white' ),
                        array('key' => 'border_color',     'value' => 'white' )
                    );
                }
                
                if(strpos($value, 'SELL INV DATE ▲') === 0)
                {
                    $tempformat[] = array('key' => 'color', 'value' => 'red');
                }
                
                $worksheet1->write($indexbaris, $key ,$value, $workbook->add_format($tempformat) );
            }
        }
        
        // ===================== >>
        // BEGIN CONTENT PROCESS !!
        // ===================== >>
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
        
        $formatIDR =& $workbook->add_format();
        $formatIDR->set_size(10);
        $formatIDR->set_border(1);
        $formatIDR->set_text_wrap();			
        $formatIDR->set_align('center');
        $formatIDR->set_align('vcenter');
        $formatIDR->set_num_format('[$IDR] #,##0');
        
        $formatKurung =& $workbook->add_format();
        $formatKurung->set_size(10);
        $formatKurung->set_border(1);
        $formatKurung->set_text_wrap();			
        $formatKurung->set_align('center');
        $formatKurung->set_align('vcenter');
        $formatKurung->set_num_format('_(* #,##0.00_);_(* (#,##0.00);_(* "-"??_);_(@_)');

        // prepare modules ...
        $product_type = $this->EntryMeta->find('all', array(
            'conditions' => array(
                'Entry.entry_type' => 'product-type',
                'EntryMeta.key' => 'form-category',
                'EntryMeta.value NOT LIKE' => 'Diamond',
            ),
        ));
        $product_type = array_combine(
            array_column( array_column( $product_type , 'Entry'), 'slug' ), // keys
            $product_type // values
        );
        
        $product_brand = array_column( array_column( $this->Entry->findAllByEntryType('product-brand') , 'Entry'), 'title', 'slug' );
        $product_color = array_column( array_column( $this->Entry->findAllByEntryType('product-color') , 'Entry'), 'title', 'slug' );
        
        $vendor = array_column( array_column( $this->Entry->findAllByEntryType('vendor') , 'Entry'), 'title', 'slug' );
        $vendor_invoice = array_map('breakEntryMetas', $this->Entry->findAllByEntryType('cor-vendor-invoice') );
        $vendor_invoice = array_combine(
            array_column( array_column( $vendor_invoice , 'Entry'), 'slug' ), // keys
            $vendor_invoice // values
        );
        
        $warehouse = array_column( array_column( $this->Entry->findAllByEntryType('warehouse') , 'Entry'), 'title', 'slug' );

        $client = array_map('breakEntryMetas', $this->Entry->findAllByEntryType('client') );
        $client = array_combine(
            array_column( array_column( $client , 'Entry'), 'slug' ), // keys
            $client // values
        );
        
        $client_invoice = array_map('breakEntryMetas', $this->Entry->findAllByEntryType('cor-client-invoice') );
        $client_invoice = array_combine(
            array_column( array_column( $client_invoice , 'Entry'), 'slug' ), // keys
            $client_invoice // values
        );
        
        // query cor-jewelry ...
        $query = '';
        if($sold_report)
        {
            $this->request->query['type-alias'] = 'sr-cor-monthly';
            
            $temp_order = $_SESSION['order_by'];
            $_SESSION['order_by'] = 'form-client_invoice_date ASC';
            
            $query = $this->_admin_default($this->Type->findBySlug('cor-jewelry'),0 , NULL , 'product_status' , 'sold' , NULL , NULL , NULL , NULL , 'manualset')['myList'];
            
            $_SESSION['order_by'] = $temp_order;
        }
        else
        {
            $query = array_map('breakEntryMetas', $this->Entry->findAllById(explode(',', $this->request->data['record'])) );
        }
        foreach($query as $key => $value)
        {
            $indexbaris++;
            $wholesaler_name = '';                
            $retailer_name = '';
            if( !empty( $client[ $value['EntryMeta']['client'] ] ) )
            {
                if( $client[ $value['EntryMeta']['client'] ]['EntryMeta']['kategori'] == 'Wholesaler' )
                {
                    $wholesaler_name = $client[ $value['EntryMeta']['client'] ]['Entry']['title'];
                }
                else
                {
                    $retailer_name = $client[ $value['EntryMeta']['client'] ]['Entry']['title'];
                    if( !empty($client[ $value['EntryMeta']['wholesaler'] ]) )
                    {
                        $wholesaler_name = $client[ $value['EntryMeta']['wholesaler'] ]['Entry']['title'];
                    }
                }
            }
            
            // transaction ...
            $sold_before_disc = $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['total_weight'] + $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['disc_adjustment'];
            $total_balance = $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['total_weight'] - $value['EntryMeta']['payment_balance'];
            
            foreach(array(
                /* COR DETAIL INFORMATION */
                NULL,
                ($key+1),
                $value['Entry']['title'],
                $product_type[ $value['EntryMeta']['product_type'] ]['Entry']['title'],
                $product_brand[ $value['EntryMeta']['product_brand'] ],
                $product_color[ $value['EntryMeta']['product_color'] ],
                $value['EntryMeta']['item_weight'],
                'GR',
                $value['EntryMeta']['item_size'],
                
                /* VENDOR INFO */
                $vendor[ $value['EntryMeta']['vendor'] ],
                substr($product_type[ $value['EntryMeta']['product_type'] ]['EntryMeta']['value'], -5, 3),
                'HK',
                $vendor_invoice[ $value['EntryMeta']['vendor_invoice_code'] ]['Entry']['title'],
                $vendor_invoice[ $value['EntryMeta']['vendor_invoice_code'] ]['EntryMeta']['total_pcs'],
                (empty($vendor_invoice[ $value['EntryMeta']['vendor_invoice_code'] ])?'':$vendor_invoice[ $value['EntryMeta']['vendor_invoice_code'] ]['EntryMeta']['total_weight'].' GR'),
                
                /* STATUS BARANG */
                $warehouse[ $value['EntryMeta']['warehouse'] ],
                ( empty($value['EntryMeta']['stock_date'])?'':parseExcelDate($value['EntryMeta']['stock_date']) ),
                $value['EntryMeta']['product_status'],
                
                /* CLIENT INFO */
                $wholesaler_name,
                $retailer_name,
                NULL,
                
                /* SR TO WAN */
                NULL,
                NULL,
                
                /* SOLD INVOICE TO CLIENT */
                ( empty($value['EntryMeta']['client_invoice_date'])?'':parseExcelDate($value['EntryMeta']['client_invoice_date']) ),                
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['Entry']['title'],
                (empty($value['EntryMeta']['client_invoice_pcs'])?'': $value['EntryMeta']['client_invoice_pcs'].' PCS' ),
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['sold_125'],
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['x_125'],
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['sold_100'],
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['x_100'],                
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['sold_110'],
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['x_110'],                
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['sold_115'],
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['x_115'],
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['disc_adjustment'],
                ($sold_before_disc > 0?$sold_before_disc:''),
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ]['EntryMeta']['total_weight'],
                $value['EntryMeta']['gold_price'],
                
                /* TYPE OF PAYMENT */
                $value['EntryMeta']['payment_ct_ld'],
                $value['EntryMeta']['payment_rosok'],
                $value['EntryMeta']['payment_checks'],
                $value['EntryMeta']['payment_cash'],
                $value['EntryMeta']['payment_credit_card'],
                $value['EntryMeta']['payment_return_goods'],
                (empty($value['EntryMeta']['payment_balance'])?'':$value['EntryMeta']['payment_balance'].' GR'),
                
                /* HISTORY OF TRANSACTIONS */
                ($total_balance > 0?$total_balance:''),
                $value['EntryMeta']['transaction_history'],
                $value['Entry']['description'],
            ) as $subkey => $subvalue)
            {
                // ignore first col ...
                if($subkey >= 1)
                {
                    if($subkey == 16 || $subkey == 23) // fixed key for date type ...
                    {
                        $worksheet1->write( $indexbaris , $subkey , $subvalue ,$formatdate);
                    }
                    else if($subkey == 37)
                    {
                        $worksheet1->write( $indexbaris , $subkey , $subvalue ,$formatIDR);
                    }
                    else if($subkey == 6)
                    {
                        $worksheet1->write( $indexbaris , $subkey , $subvalue ,$formatKurung);
                    }
                    else
                    {
                        $worksheet1->write( $indexbaris , $subkey , $subvalue ,$format1);
                    }
                }
            }
        }
        
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
    
    public function download_diamond()
    {
        if(empty($this->request->data))
		{
            throw new NotFoundException('Error 404 - Not Found');
		}
        
        set_time_limit(0); // unlimited time limit execution.
        App::import('Vendor', 'excel/worksheet');
        App::import('Vendor', 'excel/workbook');

        $filename = 'WAN_DIAMOND_';
        $sold_report = false;
        if(!empty($this->request->query['cidm']) && !empty($this->request->query['cidy']))
        {
            $filename .= 'SOLD_'.date("MY", strtotime($this->request->query['cidm'].'/1/'.$this->request->query['cidy']) );
            $sold_report = true;
        }
        else
        {
            $filename .= date('d.m.y_H.i');
        }
        
        $excel1995 = getTempFolderPath().$filename.'.xls';
        $excel2007 = getTempFolderPath().$filename.'.xlsx';

        // Creating a workbook
        $workbook = new Workbook($excel1995);
        // Creating the worksheet
        $worksheet1 =& $workbook->add_worksheet();

        // $worksheet1->hide_gridlines();
        $worksheet1->set_landscape();
        $worksheet1->fit_to_pages(0,0);
        $worksheet1->repeat_rows(0,2);

        $worksheet1->set_column(0, 0, 8);
        $worksheet1->set_column(1, 70, 15);

        // Format for the headings
        $formatot =& $workbook->add_format();
        $formatot->set_size(12);
        $formatot->set_bold();
        $formatot->set_border(1);
        $formatot->set_merge();
        $formatot->set_align('vcenter');

        // ==================== >>>
        // write the 1st header ...
        // ==================== >>>
        $indexbaris = 0;
        $worksheet1->set_row($indexbaris, 18);

        $worksheet1->write($indexbaris,1,"WAN DETAIL INFORMATION (商品详细信息) … WAN DETAIL INFORMATION (商品详细信息) … WAN DETAIL INFORMATION (商品详细信息) … WAN DETAIL INFORMATION (商品详细信息)",$formatot);
        for($col=2 ; $col <= 11 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,13,"ITEM DESCRIPTION / SPECIFICATIONS … (项目描述/规格) … (项目描述/规格)",$formatot);
        for($col=14 ; $col <= 18 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,22,"VENDOR & SUPPLIER DETAIL (供应商和供应商的详细信息) … VENDOR & SUPPLIER DETAIL (供应商和供应商的详细信息) … VENDOR & SUPPLIER DETAIL (供应商和供应商的详细信息)",$formatot);
        for($col=23 ; $col <= 36 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,38,"SOLD & RETURN REPORT TO VD (出售及向供应商退回报告)",$formatot);
        for($col=39 ; $col <= 42 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,47,"EVERYTHING ABOUT WAN TRANSACTIONS (卖出交易的完整信息) … EVERYTHING ABOUT WAN TRANSACTIONS (卖出交易的完整信息) … EVERYTHING ABOUT WAN TRANSACTIONS (卖出交易的完整信息)",$formatot);
        for($col=48 ; $col <= 66 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,68,"HISTORY OF TRANSACTIONS (交易历史)",$formatot);
        for($col=69 ; $col <= 70 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        // ==================== >>>
        // write the 2nd header ...
        // ==================== >>>
        $indexbaris++;
        $worksheet1->set_row($indexbaris, 18);

        $worksheet1->write($indexbaris,1,"MERCHANDISE INFO (商品信息) … MERCHANDISE INFO (商品信息) … MERCHANDISE INFO (商品信息)",$formatot);
        for($col=2 ; $col <= 8 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,9,"FEE (费) … FEE (费) … FEE (费)",$formatot);
        for($col=10 ; $col <= 11 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,13,"DIAMOND DESCRIPTION / SPEC (钻石总重)",$formatot);
        for($col=14 ; $col <= 16 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,17,"GOLD (金重)",$formatot);
        $worksheet1->write_blank($indexbaris,18,$formatot);

        $worksheet1->write($indexbaris,22,"VENDOR INVOICE DETAIL … (供应商发票的详细信息)",$formatot);
        for($col=23 ; $col <= 27 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,28,"VENDOR PRICE (CAPITAL) (资本价格)",$formatot);
        for($col=29 ; $col <= 32 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,33,"PAYMENT TO VENDOR (向供应商付款)",$formatot);
        for($col=34 ; $col <= 36 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,38,"SOLD REPORT (销售报告)",$formatot);
        for($col=39 ; $col <= 40 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,41,"RETURN REPORT (将报表)",$formatot);
        $worksheet1->write_blank($indexbaris,42,$formatot);

        $worksheet1->write($indexbaris,43,"SALES REPORT (销售报告)",$formatot);
        for($col=44 ; $col <= 45 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,47,"CLIENT TRANSACTION DETAIL (客户信息) … CLIENT TRANSACTION DETAIL (客户信息)",$formatot);
        for($col=48 ; $col <= 52 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,53,"SOLD INV 出售发票",$formatot);
        $worksheet1->write_blank($indexbaris,54,$formatot);

        $worksheet1->write($indexbaris,55,"SELLING PRICE (售价) … SELLING PRICE (售价) … SELLING PRICE (售价)",$formatot);
        for($col=56 ; $col <= 59 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,60,"TYPE OF PAYMENT (描述支付) … TYPE OF PAYMENT (描述支付) … TYPE OF PAYMENT (描述支付)",$formatot);
        for($col=61 ; $col <= 66 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        $worksheet1->write($indexbaris,68,"PREVIOUS SOLD INFO",$formatot);
        for($col=69 ; $col <= 70 ; ++$col) $worksheet1->write_blank($indexbaris,$col,$formatot);

        // ==================== >>>
        // write the 3rd header ...
        // ==================== >>>
        $indexbaris++;
        $worksheet1->set_row($indexbaris, 100);

        // set certain index as custom color for header table background ...
        $workbook->set_custom_color(24, 175, 171, 171);            
        $workbook->set_custom_color(25, 219, 219, 219);
        $workbook->set_custom_color(26, 251, 228, 213);
        $workbook->set_custom_color(27, 255, 255, 255);
        $workbook->set_custom_color(28, 222, 235, 246);
        $workbook->set_custom_color(29, 255, 231, 153);

        $counterColor = 23;
        foreach(array(
            NULL, "WAN # 项目编号", "TYPE 类型", "BARCODE 价格标签", "SELL BRCD (价格标签)", "STATUS IN WAN (状态)", "ADDITIONAL NOTE 附加说明", "WH", "√", "FEE (费)", "DATE (日期)", "PAID (高薪)",
            NULL, "CARAT (总克拉)", "CARAT (总克拉)", "CARAT (总克拉)", "CARAT (总克拉)", "%", "GRAM (总克)",
            NULL, "ITEM REFERENCE CODE 项目参考代码", "ITEM REFERENCE CODE 项目参考代码 (X2)", "VENDOR 供应商", "VENDOR ITEM # 供应商伴奏编号", "VENDOR INV # 供应商发票号", "VD DATE 发票日期", "STATUS WITH VENDOR", "NOTE 附加信息", "USD HKD", "BARCODE (价格)", "X", "USD (美元)", "HKD (港元)", "PAID FACTORY", "PAID DATE", "PAID 2ND VENDOR", "PAID DATE",
            NULL, "SR DATE (报告日期)", "SR  RR", "TEMP/R (临时报告)", "DATE (返程日期)", "RETURN DETAIL (返回的信息)", "SALES NAME", "COMMISION (回扣)", "OMZET",
            NULL, "CLIENT NAME (客户名称) WHOLESALE (批发)", "SELL X", "CD 守则", "CLIENT NAME (客户名称) RETAIL", "SELL X", "INPUT DATA", "SOLD DT".($sold_report?' ▲':'')." 销售日期", "S INV # 发票号码", "TOTAL (合計)", "USD (美元)", "RUPIAH (卢比)", "RATE", "CLIENT OUTSTANDING (未偿还余额)", "CREDIT CARD (信用卡)", "CICILAN 债务 (HSBC PERMATA CITI)   3-6-12 MONTHS", "CASH (现金) TRANSFER (银行汇款) DEBIT CARD (借记卡)", "CHECKS (检查) OR OTHERS (ADDITIONAL INFO) 或其他类型的付款方式", "CHECKS (检查) OR OTHERS (ADDITIONAL INFO) 或其他类型的付款方式", "CHECKS (检查) OR OTHERS (ADDITIONAL INFO) 或其他类型的付款方式", "CHECKS (检查) OR OTHERS (ADDITIONAL INFO) 或其他类型的付款方式",
            NULL, "PREV SOLD PRICE (成交价历史)", "PREV BARCODE", "PREVIOUS SOLD NOTE (注：关于交易历史)",
        ) as $key => $value)
        {
            if(!empty($value))
            {
                $tempformat = array(
                    array('key' => 'size',      'value' => 12 ),
                    array('key' => 'bold',      'value' => 1 ),
                    array('key' => 'border',    'value' => 1 ),
                    array('key' => 'text_wrap', 'value' => 1 ),
                    array('key' => 'align',     'value' => 'center' ),                        
                    array('key' => 'align',     'value' => 'vcenter' ),

                    // set background custom color of the cell method !!
                    array('key' => 'pattern',   'value' => 1 ),
                    array('key' => 'fg_color',  'value' => $counterColor ),
                );
                
                if(strpos($value, 'SOLD DT ▲') === 0)
                {
                    $tempformat[] = array('key' => 'color', 'value' => 'red');
                }
                
                $worksheet1->write($indexbaris, $key ,$value, $workbook->add_format($tempformat) );
            }
            else
            {
                $counterColor++;
            }
        }

        // ===================== >>
        // BEGIN CONTENT PROCESS !!
        // ===================== >>
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
        $product_type = $this->EntryMeta->get_diamond_type();
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
        $query = '';
        if($sold_report)
        {
            $this->request->query['type-alias'] = 'sr-dmd-monthly';
            
            $temp_order = $_SESSION['order_by'];
            $_SESSION['order_by'] = 'form-client_invoice_date ASC';
            
            $query = $this->_admin_default($this->Type->findBySlug('diamond'),0 , NULL , 'product_status' , 'sold' , NULL , NULL , NULL , NULL , 'manualset')['myList'];
            
            $_SESSION['order_by'] = $temp_order;
        }
        else
        {
            $query = array_map('breakEntryMetas', $this->Entry->findAllById(explode(',', $this->request->data['record'])) );
        }
        foreach($query as $key => $value)
        {
            $indexbaris++;
            $carat = array_map('trim', explode(chr(10), $value['EntryMeta']['carat'] ) );
            $irc = array_map('trim', explode(chr(10), $value['EntryMeta']['item_ref_code'] ) );
            $payment_checks = array_filter(array_map('trim', explode(chr(10), $value['EntryMeta']['payment_checks'] ) ));

            $wholesaler_name = '';
            $wholesaler_x = '';                
            $wholesaler_code = '';                
            $retailer_name = '';
            $retailer_x = '';
            if( !empty( $client[ $value['EntryMeta']['client'] ] ) )
            {
                if( $client[ $value['EntryMeta']['client'] ]['EntryMeta']['kategori'] == 'Wholesaler' )
                {
                    $wholesaler_name = $client[ $value['EntryMeta']['client'] ]['Entry']['title'];
                    $wholesaler_x = $value['EntryMeta']['client_x'];                        
                    $wholesaler_code = $client[ $value['EntryMeta']['client'] ]['EntryMeta']['kode_pelanggan'];
                }
                else
                {
                    $retailer_name = $client[ $value['EntryMeta']['client'] ]['Entry']['title'];
                    $retailer_x = $value['EntryMeta']['client_x'];

                    if( !empty($client[ $value['EntryMeta']['wholesaler'] ]) )
                    {
                        $wholesaler_name = $client[ $value['EntryMeta']['wholesaler'] ]['Entry']['title'];
                        $wholesaler_x = ( empty($client[ $value['EntryMeta']['wholesaler'] ]['EntryMeta']['diamond_sell_x']) ?$retailer_x: $client[ $value['EntryMeta']['wholesaler'] ]['EntryMeta']['diamond_sell_x'] );
                        $wholesaler_code = $client[ $value['EntryMeta']['wholesaler'] ]['EntryMeta']['kode_pelanggan'];
                    }
                }
            }

            foreach(array(
                /* WAN DETAIL INFORMATION */
                ($key+1),
                $value['Entry']['title'],
                $product_type[ $value['EntryMeta']['product_type'] ],
                ( $value['EntryMeta']['barcode'] > 1 ?$value['EntryMeta']['barcode']:''),
                $value['EntryMeta']['sell_barcode'],
                $value['EntryMeta']['product_status'],
                NULL,
                $warehouse[ $value['EntryMeta']['warehouse'] ],
                NULL,NULL,NULL,NULL,NULL,

                /* ITEM DESCRIPTION / SPECIFICATIONS */
                $carat[0], $carat[1], $carat[2], implode(chr(10), array_slice($carat, 3) ),
                $value['EntryMeta']['gold_carat'],
                (empty($value['EntryMeta']['gold_weight'])?'':$value['EntryMeta']['gold_weight'].' GR'),
                NULL,
                $irc[0], implode(chr(10), array_slice($irc, 1) ),

                /* VENDOR & SUPPLIER DETAIL */
                $vendor[ $value['EntryMeta']['vendor'] ],
                $value['EntryMeta']['vendor_item_code'],
                $vendor_invoice[ $value['EntryMeta']['vendor_invoice_code'] ],
                (empty($value['EntryMeta']['vendor_invoice_date'])?'':parseExcelDate( $value['EntryMeta']['vendor_invoice_date'] )),
                $value['EntryMeta']['vendor_note'],
                NULL,
                $value['EntryMeta']['vendor_currency'],
                ( $value['EntryMeta']['vendor_barcode'] > 1 ?$value['EntryMeta']['vendor_barcode']:''),
                $value['EntryMeta']['vendor_x'],
                $value['EntryMeta']['vendor_usd'],
                $value['EntryMeta']['vendor_hkd'],
                NULL,NULL,NULL,NULL,NULL,

                /* SOLD & RETURN REPORT TO VD */
                ( empty($value['EntryMeta']['report_date']) ?'': parseExcelDate( $value['EntryMeta']['report_date'] ) ),
                $value['EntryMeta']['report_type'],
                $value['EntryMeta']['temp_report'],
                ( empty($value['EntryMeta']['return_date']) ?'': parseExcelDate( $value['EntryMeta']['return_date'] ) ),
                $value['EntryMeta']['return_detail'],
                NULL,NULL,
                $value['EntryMeta']['omzet'],
                NULL,

                /* EVERYTHING ABOUT WAN TRANSACTIONS */
                $wholesaler_name,
                $wholesaler_x,
                $wholesaler_code,
                $retailer_name,
                $retailer_x,
                parseExcelDate( $value['Entry']['created'] ),
                ( empty($value['EntryMeta']['client_invoice_date'])?'':parseExcelDate($value['EntryMeta']['client_invoice_date']) ),
                $client_invoice[ $value['EntryMeta']['client_invoice_code'] ],
                (empty($value['EntryMeta']['total_sold_price'])?'':'USD '.$value['EntryMeta']['total_sold_price']),
                (empty($value['EntryMeta']['sold_price_usd'])?'':'USD '.$value['EntryMeta']['sold_price_usd']),
                $value['EntryMeta']['sold_price_rp'],
                $value['EntryMeta']['rp_rate'],
                $value['Entry']['description'],

                /* TYPE OF PAYMENT */
                $value['EntryMeta']['payment_credit_card'],
                $value['EntryMeta']['payment_cicilan'],
                $value['EntryMeta']['payment_cash'],
                $payment_checks[0], $payment_checks[1], $payment_checks[2], implode(chr(10), array_slice($payment_checks, 3) ),
                NULL,

                /* HISTORY OF TRANSACTIONS */
                $value['EntryMeta']['prev_sold_price'],
                $value['EntryMeta']['prev_barcode'],
                $value['EntryMeta']['prev_sold_note'],
            ) as $subkey => $subvalue)
            {
                if($subkey == 25 || $subkey == 38 || $subkey == 41 || $subkey == 52 || $subkey == 53) // fixed key for date type ...
                {
                    $worksheet1->write( $indexbaris , $subkey , $subvalue ,$formatdate);
                }
                else if($subkey == 57 || $subkey == 58)
                {
                    $worksheet1->write( $indexbaris , $subkey , $subvalue ,$formatRP);
                }
                else
                {
                    $worksheet1->write( $indexbaris , $subkey , $subvalue ,$format1);
                }
            }
        }

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
    
    function index() // front End view !!
	{
		throw new NotFoundException('Error 404 - Not Found');
	}
	
	function change_status($id, $status = NULL , $localcall = NULL)
	{
		$this->autoRender = false;
		$data = $this->Entry->findById($id);		
		$data_change = ( is_null($status) ? ($data['Entry']['status']==0?1:0)   : $status );
		$this->Entry->id = $id;
		$this->Entry->saveField('status', $data_change);

		if(empty($localcall))
		{
			if ($this->request->is('ajax'))
			{
				echo $data_change;
			}
			else
			{
				header("Location: ".$_SESSION['now']);
				exit;
			}
		}
	}
	
	/**
	 * delete entry
	 * @param integer $id contains id of the entry
	 * @return void
	 * @public
	 **/
	function delete($id = null, $localcall = NULL) 
	{
		$this->autoRender = FALSE;
		if (!$id) 
		{
			if(empty($localcall))
			{
				$this->Session->setFlash('Invalid id for entry', 'failed');
				header("Location: ".$_SESSION['now']);
				exit;
			}
			else
			{
				return false;
			}
		}
		
		$title = $this->meta_details(NULL , NULL , NULL , $id);        
        $statushapus = true;
        
        // Parent Type !!
		if($title['Entry']['parent_id'] > 0)
		{
			if($title['Entry']['entry_type'] == '')
			{
                // ADDITIONAL FUNCTION HERE AFTER DELETE RECORD !!
                // ...............
                // ===================================================== >>
			}
		}
		else // if this is a single / parent entry ...
		{
			if($title['Entry']['entry_type'] == '')
			{
                // ADDITIONAL FUNCTION HERE AFTER DELETE RECORD !!
                // ...............
                // ===================================================== >>
			}
		}
        
        if($statushapus)
        {
            // delete all the children !!
            $children = $this->Entry->findAllByParentId($id);
            foreach ($children as $key => $value) 
            {
                $this->EntryMeta->remove_files( $this->Type->findBySlug($value['Entry']['entry_type']) , $value );
                $this->EntryMeta->deleteAll(array('EntryMeta.entry_id' => $value['Entry']['id']));
            }
            $this->Entry->deleteAll(array('Entry.parent_id' => $id));

            // delete the entry !!
            $this->EntryMeta->remove_files( $this->Type->findBySlug($title['Entry']['entry_type']) , $title );
            $this->EntryMeta->deleteAll(array('EntryMeta.entry_id' => $id));
            $this->Entry->delete($id);

            if(empty($localcall))
            {
                $this->Session->setFlash($title['Entry']['title'].' has been deleted', 'success');
            }
        }
        
        if(empty($localcall))
        {
            header("Location: ".$_SESSION['now']);
            exit;
        }
        else
        {
            return $statushapus;
        }
	}

	/**
	* display images info may have been used or not on pop up
	* @param integer $id get media id
	* @return void
	**/	
	public function mediaused($id=NULL)
	{
		$this->autoRender = FALSE;
		if($id!=NULL)
		{	
			// check for direct media_id in Entries...
			$result = $this->Entry->find('all' , array(
				'conditions' => array(
					'Entry.main_image' => $id,
					'Entry.entry_type <>' => 'media'
				),
				'order' => array('Entry.'.$this->generalOrder)
			));
			
			foreach ($result as $key => $value) 
			{
				echo '"' . $value['Entry']['entry_type'] . '" - ' . $value['Entry']['title'] . '
';
			}
			
			// check for image used in EntryMeta too !!
			$temp = $this->TypeMeta->findAllByInputType("image");
			foreach ($temp as $key => $value) 
			{
				$tempDetail = $this->EntryMeta->find("all" , array(
					"conditions" => array(
						"EntryMeta.key" => $value['TypeMeta']['key'],
						"EntryMeta.value" => $id
					)
				));
				foreach ($tempDetail as $key10 => $value10) 
				{
					echo '"' . $value10['Entry']['entry_type'] . '" - ' . $value10['Entry']['title'] . '
';
				}
			}
			
			// CHECK FOR HAVING CHILD IMAGE OR NOT !!
			$temp = $this->Entry->findAllByParentId($id);
			foreach ($temp as $key => $value) 
			{
				$state = 0;
				$searchEntryMeta = $this->EntryMeta->findAllByValue($value['Entry']['id']);
				foreach ($searchEntryMeta as $key10 => $value10) 
				{
					$testImage = $this->TypeMeta->find('count' , array(
						"conditions" => array(
							"TypeMeta.input_type" => "image",
							"TypeMeta.key" => $value10['EntryMeta']['key'],
							"Type.slug" => $value10['Entry']['entry_type']
						)
					));
					if(!empty($testImage))
					{
						$state = 1;
						echo '"' . $value10['Entry']['entry_type'] . '" - ' . $value10['Entry']['title'] . '
';
					}
				}
				if($state == 0)
				{
					// DELETE THIS CHILD IMAGE !!
					$this->Entry->deleteMedia($value['Entry']['id']);
				}
			}
		}
	}
	
	/**
	 * delete image from media library
	 * @param integer $id contains id of the image entry
	 * @return void
	 * @public
	 **/
	function deleteMedia($id = null)
	{
		$this->autoRender = FALSE;
		if ($id==NULL)
		{
			$this->Session->setFlash('Invalid ID Media','failed');
		}
		else 
		{
			//////////// FIND MEDIA NAME BEFORE DELETED ////////////
			$media_name = $this->Entry->findById($id);
			if($this->Entry->deleteMedia($id))
			{				
				$this->Session->setFlash('Media "'.$media_name['Entry']['title'].'" has been deleted','success');
			}
		}
		header("Location: ".$_SESSION['now']);
		exit;
	}
    
    function _get_template($myTypeSlug = NULL , $myChildTypeSlug = NULL )
    {
        $myTemplate = '';
        if(empty($myChildTypeSlug))
        {
            if(strpos($myTypeSlug , '-invoice') !== FALSE)
            {
                $myTemplate = 'custom-invoice';
            }
            else if(strpos($myTypeSlug , '-payment') !== FALSE) // SR / RR
            {
                $myTemplate = 'custom-payment';
            }
            else
            {
                $myTemplate = $myTypeSlug;
            }
        }
        else
        {
            if(strpos($myChildTypeSlug , '-payment') !== FALSE)
            {
                $myTemplate = 'custom-payment';
            }
            else
            {
                $myTemplate = $myChildTypeSlug;
            }
        }
        return $myTemplate;
    }
    
    /**
	 * Bulk inject process to certain module from uploaded excel file.
	 * @param array $filepath contains fullpath of excel file.
     * @param array $myTypeSlug contains type slug of module related.
	 * @return void
	 * @public
	 **/
    function read_excel($filepath, $myTypeSlug)
    {
        set_time_limit(0); // unlimited time limit execution.
        ini_set('memory_limit', '-1'); // unlimited memory limit to process batch.
        ob_implicit_flush(true);
        ob_end_flush();
                
        /**  Define how many rows we want for each "chunk" and other helper variable  **/
        $chunkSize = $counterRow = 50;
        $maxCols = ( $myTypeSlug == 'diamond' ? 71 : 48 );
        $printSpace = array('', '&nbsp;','&nbsp;&nbsp;', '&nbsp;&nbsp;&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;');
        $intervalSpace = count($printSpace) - 1;
        
        // save just created invoice (slug) from Excel !!
        $_SESSION['vendor_invoice_code'] = array();
        $_SESSION['client_invoice_code'] = array();
        
        // BEGIN MAIN PROCESS !!
        $this->PhpExcel->setExcelReader($filepath);
        /**  Loop to read our worksheet in "chunk size" blocks  **/
        for ($startRow = 4, $counterChunk = 0; $counterRow >= $chunkSize ; $startRow += $chunkSize, ++$counterChunk)
        {
            // Firstly, print loading process ...
            dpr('Processing Excel record : '.$startRow.' - '.($startRow + $chunkSize - 1).' '.$printSpace[abs( (floor($counterChunk / $intervalSpace) % 2) * $intervalSpace - ($counterChunk % $intervalSpace) )].'... Please wait a moment ... RAM: '.(memory_get_peak_usage(true)/(1024*1024)).' MB');
            echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
            
            // begin load worksheet ...
            $this->PhpExcel->loadWorksheet($filepath , $startRow , $chunkSize , empty($counterChunk) );
            for($counterRow = 0 ; $counterRow < $chunkSize && ($value = $this->PhpExcel->getTableData($maxCols)) ; ++$counterRow )
            {
                // trim all value first !!
                $value = array_map('trim', $value);
                
                if($myTypeSlug == 'diamond')
                {
                    $this->EntryMeta->upload_diamond($value, $this->mySetting);
                }
                else if($myTypeSlug == 'cor-jewelry')
                {
                    $this->EntryMeta->upload_jewelry($value, $this->mySetting);
                }
            }
            $this->PhpExcel->freeMemory();
        }
        // END OF MAIN PROCESS !!
        
        // unset temp $_SESSION ...
        unset($_SESSION['vendor_invoice_code']);
        unset($_SESSION['client_invoice_code']);
    }
    
	/**
	 * target route for querying to get list of entries.
	 * @return void
	 * @public
	 **/
	function admin_index() 
	{
        // DEFINE THE ORDER...
		if(!empty($this->request->data['order_by']))
		{	
			switch ($this->request->data['order_by']) 
			{
                case 'by_order':
                    unset($_SESSION['order_by']);
                    break;
				case 'z_to_a':
					$_SESSION['order_by'] = 'title DESC';
					break;
				case 'a_to_z':
					$_SESSION['order_by'] = 'title ASC';
					break;
				case 'latest_first':
					$_SESSION['order_by'] = 'created DESC';
					break;
				case 'oldest_first':
					$_SESSION['order_by'] = 'created ASC';
					break;	
				default:
					$_SESSION['order_by'] = $this->request->data['order_by'];
					break;
			}
		}		
		// END OF DEFINE THE ORDER...
		
		if($this->request->params['type'] == 'pages')
		{
			// manually set pages data !!
			$myType['Type']['name'] = 'Pages';
			$myType['Type']['slug'] = 'pages';
			$myType['Type']['parent_id'] = 0;
		}
		else
		{
			$myType = $this->Type->findBySlug($this->request->params['type']);
		}
		// if this action is going to view the CHILD list...
		if(!empty($this->request->params['entry']))
		{
			$myEntry = $this->meta_details($this->request->params['entry']);
			if(!empty($this->request->query['type']))
			{				
				$myChildTypeSlug = $this->request->query['type'];
			}
			else 
			{
				$myChildTypeSlug = $myType['Type']['slug'];
			}
		}

		// ==================================== >> UPLOAD BATCH PROCESS FILE !!
        if(!empty($this->request->data['fileurl']))
		{
            error_reporting(E_ALL ^ E_NOTICE);
            if(empty($this->request->data['fileurl']['error']) && !empty($this->request->data['fileurl']['tmp_name']))
            {
                // firstly, set possible warning message ...
                $this->Session->setFlash('Batch Process has been interrupted due to server timeout.<br>Please contact your administrator and try again.', 'failed');
                
                $this->read_excel($this->request->data['fileurl']['tmp_name'], $myType['Type']['slug']);
                $this->Session->setFlash('Batch Process from uploaded excel file has been executed successfully.','success');
                redirectUsingScript($_SERVER['REQUEST_URI']);
            }
            else
            {
                $this->Session->setFlash('Batch Process <strong>could not be executed</strong> due to some error from uploaded excel file.<br>Please contact the administrator and try again.','failed');
            }
		}
        // ========== FORM SUBMIT BULK ACTION ============
		if(!empty($this->request->data['action']))
		{
			$pecah = explode(',', $this->request->data['record']);
			
			if($this->request->data['action'] == 'active')
			{
				foreach ($pecah as $key => $value) 
				{
					$this->change_status($value, 1 , 'localcall');
				}
				$this->Session->setFlash('Your selection data status has been set as <strong>COMPLETE</strong> successfully.','success');
			}
			else if($this->request->data['action'] == 'disable')
			{
				foreach ($pecah as $key => $value) 
				{
					$this->change_status($value, 0 , 'localcall');
				}
				$this->Session->setFlash('Your selection data status has been set as <strong>PENDING</strong> successfully.','success');
			}
			else if($this->request->data['action'] == 'delete')
			{
				foreach ($pecah as $key => $value) 
				{
					$this->delete($value , 'localcall');
				}
				$this->Session->setFlash('Your selection data has been <strong>deleted</strong> successfully.','success');
			}
			else
			{
				$this->Session->setFlash('There\'s no bulk action process to be executed. Please try again.','failed');
			}
		}

		// this general action is one for all...
        if(strpos($myChildTypeSlug , '-payment') !== false)
		{
			$this->request->params['page'] = 0; // must be one full page !!
            
            $tempOrder = (isset($_SESSION['order_by'])? $_SESSION['order_by'] : '' );
            $_SESSION['order_by'] = 'form-date asc';
		}
        else if($myType['Type']['slug'] == 'diamond' || $myType['Type']['slug'] == 'cor-jewelry')
        {
            // default MONTHLY SOLD REPORT order_by ...
            if(strpos($this->request->query['type-alias'], '-monthly') !== false && !$this->request->is('ajax') )
            {
                $_SESSION['order_by'] = 'form-client_invoice_date asc';
            }
            
            // check for eligible products access (WH Employee) !!
            if(strtolower($this->user['Role']['name']) == 'warehouse employee' && strpos($this->user['UserMeta']['eligible_products'], $myType['Type']['slug']) === false )
            {
                $this->set('staticRecordTemplate', 1);
            }
            
            // search mode is "by field" on admin_header.ctp !!
            $this->set('search_by_field', 1);
        }
        
        // query diamond product_type ...
        if($myType['Type']['slug'] == 'surat-jalan' || $myType['Type']['slug'] == 'sr-dmd-payment' || $myChildTypeSlug == 'dc-payment')
        {
            $this->set('diamondType', $this->EntryMeta->get_diamond_type() );
        }
        
        // always void "lang" query !!
        unset($this->request->query['lang']);
        
		$this->_admin_default($myType , $this->request->params['page'] , $myEntry , $this->request->query['key'] , $this->request->query['value'] , $myChildTypeSlug , $this->request->data['search_by'] , $this->request->query['popup'] , strtolower($this->request->query['lang']));
        
        $myTemplate = $this->_get_template($myType['Type']['slug'], $myChildTypeSlug);
        
		// send to each appropriate view
		$str = substr(WWW_ROOT, 0 , strlen(WWW_ROOT)-1); // buang DS trakhir...
		$str = substr($str, 0 , strripos($str, DS)+1); // buang webroot...
		$src = $str.'View'.str_replace('/', DS, $this->backEndFolder).$myTemplate.'.ctp';
        
        if(file_exists($src))
		{
			$this->render($this->backEndFolder.$myTemplate);
		}
		else
		{
			$this->render('admin_default');
		}
        
        // get back $_SESSION['order_by'] if any ...
        if(isset($tempOrder))
        {
            $_SESSION['order_by'] = $tempOrder;
        }
	}
	
	/**
	* target route for adding new entry
	* @return void
	* @public
	**/
	function admin_index_add()
	{
		if($this->request->params['type'] == 'pages')
		{
			
			if($this->user['role_id'] > 1)
			{
				throw new NotFoundException('Error 404 - Not Found');
			}
			// manually set pages data !!
			$myType['Type']['name'] = 'Pages';			
			$myType['Type']['slug'] = 'pages';
			$myType['Type']['parent_id'] = 0;
		}
		else
		{
			$myType = $this->Type->findBySlug($this->request->params['type']);
		}
		
		// if this action is going to add CHILD list...
		if(!empty($this->request->params['entry']))
		{
			$myEntry = $this->meta_details($this->request->params['entry']);
			if(!empty($this->request->query['type']))
			{				
				$myChildTypeSlug = $this->request->query['type'];
			}
			else 
			{
				$myChildTypeSlug = $myType['Type']['slug'];
			}
		}
        
        // ========== SRID POST ACTION ============
        if(empty($myChildTypeSlug) && strpos($myType['Type']['slug'] , '-payment') !== FALSE) // SR / RR
        {
            if(!empty($_POST['srid']))
            {
                $srid = array_map('breakEntryMetas', $this->Entry->findAllById(explode(',', $_POST['srid'])) );
                
                if($myType['Type']['slug'] == 'sr-dmd-payment')
                {
                    $srid = array_map(function($value){ return $value['Entry']['slug'].'_'.$value['EntryMeta']['vendor_usd']; }, $srid);
                }
                else
                {
                    $srid = array_map(function($value){ return $value['Entry']['slug'].'_'.$value['EntryMeta']['item_weight']; }, $srid);
                }
                
                $this->set('srid', implode('|', $srid) );

                // delete temporary POST data ...
                unset($_POST);
                unset($this->request->data);
            }
        }
        
		// main add function ...
		$this->_admin_default_add(($myType['Type']['slug']=='pages'?NULL:$myType) , $myEntry , $myChildTypeSlug);
        
        $myTemplate = $this->_get_template($myType['Type']['slug'], $myChildTypeSlug).'_add';
		
		// send to each appropriate view
		$str = substr(WWW_ROOT, 0 , strlen(WWW_ROOT)-1); // buang DS trakhir...
		$str = substr($str, 0 , strripos($str, DS)+1); // buang webroot...
		$src = $str.'View'.str_replace('/', DS, $this->backEndFolder).$myTemplate.'.ctp';
		
		// add / edit must use the same view .ctp, but with different action !!
		if(file_exists($src))
		{
			$this->render($this->backEndFolder.$myTemplate);
		}
		else
		{
			$this->render('admin_default_add');
		}
	}
	
	/**
	* target route for editing certain entry based on passed url parameter
	* @return void
	* @public
	**/
	function admin_index_edit()
	{	
		if($this->request->params['type'] == 'pages')
		{
			// manually set pages data !!
			$myType['Type']['name'] = 'Pages';
			$myType['Type']['slug'] = 'pages';
			$myType['Type']['parent_id'] = 0;
		}
		else
		{
			$myType = $this->Type->findBySlug($this->request->params['type']);
		}
        
		$myEntry = $this->meta_details($this->request->params['entry'] , (!empty($this->request->query['type'])?$this->request->query['type']:$myType['Type']['slug']) );
		
        if(empty($myEntry))
        {
            throw new NotFoundException('Error 404 - Not Found');
        }
		
		// if this action is going to edit CHILD list...
		if(!empty($this->request->params['entry_parent']))
		{	
			$myParentEntry = $this->meta_details($this->request->params['entry_parent']);
			if(!empty($this->request->query['type']))
			{				
				$myChildTypeSlug = $this->request->query['type'];
			}
			else 
			{
				$myChildTypeSlug = $myType['Type']['slug'];
			}
		}
        
        // check for eligible products access (WH Employee) !!
        if( ($myType['Type']['slug'] == 'diamond' || $myType['Type']['slug'] == 'cor-jewelry') && strtolower($this->user['Role']['name']) == 'warehouse employee' && strpos($this->user['UserMeta']['eligible_products'], $myType['Type']['slug']) === false )
        {
            $this->set('staticRecordTemplate', 1);
        }
		
		// main edit function ...
		$this->_admin_default_edit(($myType['Type']['slug']=='pages'?NULL:$myType) , $myEntry , $myParentEntry , $myChildTypeSlug , strtolower($this->request->query['lang']));
		
		$myTemplate = $this->_get_template($myType['Type']['slug'], $myChildTypeSlug).'_add';
        
		// send to each appropriate view
		$str = substr(WWW_ROOT, 0 , strlen(WWW_ROOT)-1); // buang DS trakhir...
		$str = substr($str, 0 , strripos($str, DS)+1); // buang webroot...
		$src = $str.'View'.str_replace('/', DS, $this->backEndFolder).$myTemplate.'.ctp';
		
		// add / edit must use the same view .ctp, but with different action !!
		if(file_exists($src))
		{
			$this->render($this->backEndFolder.$myTemplate);
		}
		else
		{
			$this->render('admin_default_add');
		}
	}
	
	/**
	* querying to get a bunch of entries based on parameter given (core function)
	* @param array $myType contains record query result of database type
	* @param integer $paging[optional] contains selected page of lists you want to retrieve
	* @param array $myEntry[optional] contains record query result of the parent Entry (used if want to search certain child Entry)
	* @param string $myMetaKey[optional] contains specific key that entries must have
	* @param string $myMetaValue[optional] contains specific value from certain key that entries must have
	* @param string $myChildTypeSlug[optional] contains slug of child type database (used if want to search certain child Entry)
	* @param string $searchMe[optional] contains search string that existed in bunch of entries requested
	* @param string $popup[optional] contains how this entry is representated
	* @param string $lang[optional] contains language of the entries that want to be retrieved
	* @param boolean $manualset[optional] set TRUE if you want set data variable to view file OUT OF this function, otherwise set FALSE
	* @return array $data certain bunch of entries you'd requested
	* @public
	**/
	public function _admin_default($myType = array(),$paging = NULL , $myEntry = array() , $myMetaKey = NULL , $myMetaValue = NULL , $myChildTypeSlug = NULL , $searchMe = NULL , $popup = NULL , $lang = NULL , $manualset = NULL)
	{
        set_time_limit(120); // 2 MINUTES time limit execution.
        if(is_null($paging))
		{
			$paging = 1;
		}
		if(!empty($popup) || $this->request->is('ajax'))
		{
			$this->layout = 'ajax';
			$data['stream'] = (isset($this->request->query['stream'])?$this->request->query['stream']:NULL);
            $data['alias'] = (isset($this->request->query['alias'])?$this->request->query['alias']:NULL);
		}	
		if ($this->request->is('ajax') && empty($popup) || $popup == "ajax" || !empty($searchMe)) 
		{	
			$data['isAjax'] = 1;
			if($searchMe != NULL || !empty($lang) && !empty($this->request->params['admin']) )
			{
				$data['search'] = "yes";
			}			
			if($searchMe != NULL)
			{
				$searchMe = trim($searchMe);
				if(empty($searchMe))
				{
					unset($_SESSION['searchMe']);
				}
				else
				{
					$_SESSION['searchMe'] = array('value' => $searchMe);
                    if(!empty($this->request->data['field_by']))
                    {
                        $_SESSION['searchMe']['key'] = $this->request->data['field_by'];
                    }
				}
			}
		} 
		else 
		{
			$data['isAjax'] = 0;
			unset($_SESSION['searchMe']);
		}
        
        // USING ENGLISH LANGUAGE ONLY !!
        if(empty($_SESSION['lang']))
        {
            $_SESSION['lang'] = 'en';
        }
        
		$data['myType'] = $myType;
		$data['paging'] = $paging;
		$data['popup'] = $popup;
		if(!empty($myEntry))
		{			
			$data['myEntry'] = $myEntry;
			$myChildType = $this->Type->findBySlug($myChildTypeSlug);
			$data['myChildType'] = $myChildType;
		}
        
        // $_SESSION['order_by'] Validation !!
        $myAutomaticValidation = (empty($myChildType)?$myType['TypeMeta']:$myChildType['TypeMeta']);
        if($this->mySetting['table_view']=='complex' && substr($_SESSION['order_by'] , 0 , 5) == 'form-')
        {
            $innerFieldMeta = FALSE;
            foreach( $myAutomaticValidation as $key => $value)
            {
                if(substr($value['key'],0,5) == 'form-' && stripos($_SESSION['order_by'] , $value['key'] ) !== FALSE)
                {
                    $innerFieldMeta = $value['input_type'];
                    $innerFieldMetaNumeric = strpos($value['validation'], 'is_numeric'); // for order by type !!
                    break;
                }                    
            }
            if(empty($innerFieldMeta))
            {
                unset($_SESSION['order_by']);
            }
        }
        
        // SEARCH IF GALLERY MODE IS TURN ON / OFF ...
        $data['gallery'] = $this->Entry->checkGalleryType($myAutomaticValidation);
        
		// set page title
		$this->setTitle(empty($myEntry)?$myType['Type']['name']:$myEntry['Entry']['title']);
		
		// set paging session...
		$countPage = $this->countListPerPage;
		if(!empty($paging))
		{
			if(empty($this->request->params['admin'])) // front-end
			{
				foreach($myAutomaticValidation as $key => $value) 
				{
					if($value['key'] == 'pagination')
					{
						$countPage = $value['value'];
						break;
					}
				}
			}
			else // back-end
			{
				if($myType['Type']['slug']=='media')
				{
					$countPage = $this->mediaPerPage;
                    unset($_SESSION['order_by']);
				}
			}
		}
		
		// our list conditions... --------------------------------------------------------------////
		if(empty($myEntry))
		{
			$options['conditions'] = array('Entry.entry_type' => $myType['Type']['slug']);
            if($myType['Type']['parent_id'] <= 0)
			{
				$options['conditions']['Entry.parent_id'] = 0;
			}
		}
		else
		{
			$options['conditions'] = array(
				'Entry.entry_type' => $myChildTypeSlug,
				'Entry.parent_id' => $myEntry['Entry']['id']
			);
		}

		if($myType['Type']['slug'] != 'media')
		{
			$data['language'] = $_SESSION['lang'];
		}
        
        // ========================================= >>
		// FIND LAST MODIFIED !!
		// ========================================= >>
		$options['order'] = array('Entry.modified DESC');
		$data['lastModified'] = $this->Entry->find('first' , $options);

		// ======================================== >>
		// JOIN TABLE & ADDITIONAL FILTERING METHOD !!
		// ======================================== >>
        if(!empty($this->user['storage'])) // WAREHOUSE EMPLOYEE !!
        {
/*
            if($myType['Type']['slug'] == 'client')
            {
                $wh_cond = array(array('Entry.created_by' => $this->user['id']));
                foreach($this->user['storage'] as $key => $value)
                {
                    $wh_cond[] = array('CONCAT("|", SUBSTRING_INDEX(SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-'.$value['entry_type'].'=", -1), "{#}", 1), "|") LIKE' => '%|'.$value['slug'].'|%');
                }
                
                $options['conditions'][] = array('OR' => $wh_cond);
            }
*/
        }
        
        if($myType['Type']['slug'] == 'logistic')
        {
            if(!empty($this->request->query['storage']) && !empty($this->request->query['content']) )
            {
                array_push($options['conditions'], array(
                    'CAST(SUBSTRING_INDEX(CONCAT("|", SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-'.$this->request->query['storage'].'=", -1)), "|'.$this->request->query['content'].'_", -1) AS SIGNED) >=' => 1
                ));
            }
        }
        else if($myType['Type']['slug'] == 'diamond' || $myType['Type']['slug'] == 'cor-jewelry')
        {
            if(!empty($this->request->query['storage']) && !empty($this->request->query['content']) )
            {
                array_push($options['conditions'], array('SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-'.$this->request->query['storage'].'=", -1) LIKE' => $this->request->query['content'].'{#}%') );
                
                array_push($options['conditions'], array('OR' => array(
                    array('SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-product_status=", -1) LIKE' => 'kllg%'),
                    array('SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-product_status=", -1) LIKE' => ($this->request->query['storage'] == 'exhibition'?'exhibition':'stock').'%')
                )));
            }
            
            // custom SOLD REPORT monthly search ...
            if(strpos($this->request->query['type-alias'], '-monthly') !== false)
            {
                if(empty($this->request->query['cidm']))    $this->request->query['cidm'] = date('n');
                if(empty($this->request->query['cidy']))    $this->request->query['cidy'] = date('Y');
                
                array_push($options['conditions'], array('SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-client_invoice_date=", -1) LIKE' => sprintf("%02d",$this->request->query['cidm']).'/__/'.$this->request->query['cidy'].'{#}%') );
            }
        }
        else if($myType['Type']['slug'] == 'surat-jalan')
        {
            if(!empty($this->request->query['storage']) && !empty($this->request->query['content']) )
            {
                array_push($options['conditions'], array('OR' => array(
                    array('SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-'.$this->request->query['storage'].'_origin=", -1) LIKE' => $this->request->query['content'].'{#}%'),
                    array('SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-'.$this->request->query['storage'].'_destination=", -1) LIKE' => $this->request->query['content'].'{#}%'),
                )));
            }
        }
        else if($myType['Type']['slug'] == 'sr-dmd-payment' || $myType['Type']['slug'] == 'sr-cor-payment')
        {
            if(!empty($this->request->query['warehouse']))
            {
                $options['conditions'][] = array('OR' => array(
                    array('SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-warehouse_payer=", -1) LIKE' => $this->request->query['warehouse'].'{#}%'),
                    array('SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-warehouse=", -1) LIKE' => $this->request->query['warehouse'].'{#}%'),
                ));
                
                $options['conditions'][] = array('Entry.status' => 1);
                $options['conditions'][] = array('EntryMeta.key_value NOT LIKE' => '%{#}form-loan_period=%');
                $options['conditions'][] = array('OR' => array(
                    array('EntryMeta.key_value NOT LIKE' => '%{#}form-checks_date=%'),
                    array('STR_TO_DATE( SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-checks_date=", -1) ,"%m/%d/%Y") <=' => date('Y-m-d')),
                ));
            }
        }
        else if($myType['Type']['slug'] == 'dc-payment' || $myType['Type']['slug'] == 'cc-payment')
        {
            if(!empty($this->request->query['invoice']))
            {
                $options['conditions'][] = array('Entry.parent_id' => $this->request->query['invoice']);
                
                $options['conditions'][] = array('Entry.status' => 1);
                $options['conditions'][] = array('EntryMeta.key_value NOT LIKE' => '%{#}form-loan_period=%');
                $options['conditions'][] = array('OR' => array(
                    array('EntryMeta.key_value NOT LIKE' => '%{#}form-checks_date=%'),
                    array('STR_TO_DATE( SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-checks_date=", -1) ,"%m/%d/%Y") <=' => date('Y-m-d')),
                ));
            }
        }
        
        // GENERAL QUERY WITH INTERVAL DATE ...
        if(!empty($this->request->data['start_date']) && !empty($this->request->data['end_date']))
        {
            array_push($options['conditions'], array(
                'STR_TO_DATE( SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-date=", -1) ,"%m/%d/%Y") BETWEEN ? AND ?' => array(
                    date('Y-m-d', strtotime($this->request->data['start_date']) ),
                    date('Y-m-d', strtotime($this->request->data['end_date']) )
                )
            ));
        }
        
        // SPECIAL THREAT FOR $myMetaKey !!
        if(empty($myMetaKey))
        {
            // second check, is there any field that is a required one?
            $validation = implode('|', array_column($myAutomaticValidation , 'validation' ) );
            if(strpos($validation, 'not_empty') !== FALSE)
            {
                $myMetaKey = 'dummy';
            }
        }
        // END OF SPECIAL THREAT ...
        
        if( !empty($myMetaKey) )
		{
            $myMetaKey = array_map('trim', explode('|', $myMetaKey));
            $myMetaValue = array_map('trim', explode('|', $myMetaValue));
            
            foreach($myMetaKey as $tempKey => $tempValue)
            {
                if(!empty($tempValue) && !empty($myMetaValue[$tempKey]))
                {
                    $myMetaNot = '';
                    if(substr($myMetaValue[$tempKey] , 0 , 1) == '!')
                    {
                        $myMetaValue[$tempKey] = substr($myMetaValue[$tempKey] , 1);
                        $myMetaNot = 'NOT';
                    }
                    
                    array_push($options['conditions'], array(
                        'REPLACE(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(EntryMeta.key_value, "{#}form-'.$tempValue.'=", -1), "{#}", 1) , "-" , " "),"_"," ") '.$myMetaNot.' LIKE' => '%'.string_unslug($myMetaValue[$tempKey]).'%'
                    ));
                    
                    unset($myMetaKey[$tempKey]);
                }
            }
            
            $myMetaKey = array_filter($myMetaKey);
            if(!empty($myMetaKey))
            {
                $options['conditions']['NOT'] = array_map(function($value){ return array('EntryMeta.key_value LIKE' => '%{#}form-'.$value.'=%'); }, $myMetaKey);
            }
		}
        
		if(!empty($_SESSION['searchMe']))
		{
            if(!empty($_SESSION['searchMe']['key']))
            {
                if(substr($_SESSION['searchMe']['key'], 0, 5) == 'form-')
                {
                    $options['conditions'][] = array(
                        'REPLACE(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(EntryMeta.key_value, "{#}'.$_SESSION['searchMe']['key'].'=", -1), "{#}", 1) , "-" , " "),"_"," ") LIKE' => '%'.string_unslug($_SESSION['searchMe']['value']).'%'
                    );
                }
                else // title / description ...
                {
                    $options['conditions'][] = array('Entry.'.$_SESSION['searchMe']['key'].' LIKE' => '%'.$_SESSION['searchMe']['value'].'%');
                }
            }
            else // normal search ...
            {
                $options['conditions']['OR'] = array(
                    array('Entry.title LIKE' => '%'.$_SESSION['searchMe']['value'].'%'), 
                    array('Entry.description LIKE' => '%'.$_SESSION['searchMe']['value'].'%'),
                );

                if($this->mySetting['table_view']=='complex')
                {
                    array_push($options['conditions']['OR'] , array('REPLACE(REPLACE(EntryMeta.key_value , "-" , " "),"_"," ") LIKE' => '%'.string_unslug($_SESSION['searchMe']['value']).'%') );
                }
            }
		}
        
        // ========================================= >>
        // FINAL SORT based on certain criteria !!
        // ========================================= >>
        if(!empty($innerFieldMeta))
        {
            $explodeSorting = explode(' ', $_SESSION['order_by']);
            if($innerFieldMeta == 'gallery')    $explodeSorting[0] = 'count-'.$explodeSorting[0];
            
            $sqlOrderValue = 'LTRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(EntryMeta.key_value, "{#}'.$explodeSorting[0].'=", -1), "{#}", 1))';
            if(strpos($innerFieldMeta, 'datetime') !== FALSE)
            {
                $sqlOrderValue = 'STR_TO_DATE('.$sqlOrderValue.', "%m/%d/%Y %H:%i")';
            }
            else if(strpos($innerFieldMeta, 'date') !== FALSE)
            {
                $sqlOrderValue = 'STR_TO_DATE('.$sqlOrderValue.', "%m/%d/%Y")';
            }
            else if($innerFieldMetaNumeric !== FALSE)
            {
                $sqlOrderValue = 'CAST('.$sqlOrderValue.' AS SIGNED)';
            }
            
            $options['order'] = array($sqlOrderValue.' '.$explodeSorting[1]);
        }
        else 
        {
            $options['order'] = array('Entry.'.(empty($_SESSION['order_by'])?$this->generalOrder:$_SESSION['order_by']));
        }
        
        if(strpos( serialize($options) , 'EntryMeta.key_value') !== FALSE)
		{
            $options['joins'] = array(array(
				'table' => '(SELECT EntryMeta.entry_id, CONCAT("{#}", GROUP_CONCAT(EntryMeta.key, "=", EntryMeta.value ORDER BY EntryMeta.id SEPARATOR "{#}"), "{#}") as key_value FROM cms_entry_metas as EntryMeta GROUP BY EntryMeta.entry_id)',
	            'alias' => 'EntryMeta',
	            'type' => 'LEFT',
	            'conditions' => array('Entry.id = EntryMeta.entry_id')
			));
		}
        
        // ========================================= >>
		// EXECUTE MAIN QUERY !!
		// ========================================= >>
        $data['totalList'] = 0;
		$data['myList'] = array();
        
        // CUSTOM REQUEST BY WAN !!
        if(!empty($this->request->query) || $myType['Type']['slug'] == 'media' || $this->user['role_id'] == 1)
        {
            $data['noNeedToSearch'] = 1;
        }
        
        if(!empty($_SESSION['searchMe']) || !empty($data['noNeedToSearch']) )
        {
            $data['totalList'] = $this->Entry->find('count' ,$options);
            
            if($paging >= 1)
            {
                $options['limit'] = $countPage;
                $options['page'] = $paging;
            }
            
            $data['myList'] = array_map('breakEntryMetas', $this->Entry->find('all' ,$options));
        }
        
        // check for image is used for this entries or not ??
		$data['imageUsed'] = (empty(array_filter(array_column(array_column($data['myList'], 'Entry'), 'main_image')))?0:1);
        
        // set New countPage
		$data['countPage'] = $newCountPage = ceil($data['totalList'] / $countPage);
		
		// set the paging limitation...
		$left_limit = 1;
		$right_limit = 5;
		if($newCountPage <= 5)
		{
			$right_limit = $newCountPage;
		}
		else
		{
			$left_limit = $paging-2;
			$right_limit = $paging+2;
			if($left_limit < 1)
			{
				$left_limit = 1;
				$right_limit = 5;
			}
			else if($right_limit > $newCountPage)
			{
				$right_limit = $newCountPage;
				$left_limit = $newCountPage - 4;
			}			
		}
		$data['left_limit'] = $left_limit;
		$data['right_limit'] = $right_limit;
		
		// for image input type reason...
		$data['myImageTypeList'] = $this->EntryMeta->embedded_img_meta('type');
		
		// --------------------------------------------- LANGUAGE OPTION LINK ------------------------------------------ //
		if(!empty($myEntry) && count($this->mySetting['language']) > 1)
		{
			$temp100 = $this->Entry->find('all' , array(
				'conditions' => array(
					'Entry.lang_code LIKE' => '%-'.substr($myEntry['Entry']['lang_code'], 3)
				),
                'recursive' => -1
			));
            
            foreach ($temp100 as $key => $value) 
			{
				$parent_language[ substr($value['Entry']['lang_code'], 0,2) ] = $value['Entry']['slug'];
			}
			$data['parent_language'] = $parent_language;
		}
		// ------------------------------------------ END OF LANGUAGE OPTION LINK -------------------------------------- //

		if(empty($manualset))
		{
			$this->set('data' , $data);
		}
		
		return $data;
	}

	/**
	* add new entry 
	* @param array $myType contains record query result of database type
	* @param array $myEntry[optional] contains record query result of the selected Entry
	* @param string $myChildTypeSlug[optional] contains slug of child type database (used if want to search certain child Entry)
	* @return void
	* @public
	**/
	function _admin_default_add($myType = array() , $myEntry = array() , $myChildTypeSlug = NULL , $lang_code = NULL , $prefield_slug = NULL)
	{
		$myChildType = $this->Type->findBySlug($myChildTypeSlug);
		$data['myType'] = $myType;
		$data['myParentEntry'] = $myEntry;
		$data['myChildType'] = $myChildType;
        
        // SEARCH IF GALLERY MODE IS TURN ON / OFF ...
        $myAutomaticValidation = (empty($myEntry)?$myType['TypeMeta']:$myChildType['TypeMeta']);
        $data['gallery'] = $this->Entry->checkGalleryType($myAutomaticValidation);
        
		// for image input type reason...
		$data['myImageTypeList'] = $this->EntryMeta->embedded_img_meta('type');
		// --------------------------------------------- LANGUAGE OPTION LINK ------------------------------------------ //
		if(!empty($myEntry) && count($this->mySetting['language']) > 1)
		{
			$temp100 = $this->Entry->find('all' , array(
				'conditions' => array(
					'Entry.lang_code LIKE' => '%-'.substr($myEntry['Entry']['lang_code'], 3)
				),
                'recursive' => -1
			));
			foreach ($temp100 as $key => $value) 
			{
				$parent_language[ substr($value['Entry']['lang_code'], 0,2) ] = $value['Entry']['slug'];
			}
			$data['parent_language'] = $parent_language;
		}
		$data['lang'] = 'en'; // USING ENGLISH LANGUAGE ONLY !!
		// ------------------------------------------ END OF LANGUAGE OPTION LINK -------------------------------------- //
		
		if(empty($prefield_slug))
		{
			$this->setTitle('Add New '.(empty($myEntry)?(empty($myType)?'Pages':$myType['Type']['name']):$myEntry['Entry']['title']));
			$this->set('data' , $data);
		}
		
		// if form submit is taken...
		if (!empty($this->request->data)) 
		{
//            dpr($this->request->data);
//            exit;
            
            if(empty($lang_code) && !empty($myEntry) && substr($myEntry['Entry']['lang_code'], 0,2) != $this->request->data['language'])
			{
				$myEntry = $this->Entry->findByLangCode($this->request->data['language'].substr($myEntry['Entry']['lang_code'], 2));
			}	
			// PREPARE DATA !!	
			$this->request->data['Entry']['title'] = $this->Entry->get_serial_title($myType['Type']['slug'], $this->request->data['Entry'][0]['value'], $this->request->data['EntryMeta'] );
			$this->request->data['Entry']['description'] = $this->request->data['Entry'][1]['value'];
			$this->request->data['Entry']['main_image'] = $this->request->data['Entry'][2]['value'];
			if(isset($this->request->data['Entry'][3]['value']))
			{
				$this->request->data['Entry']['status'] = $this->request->data['Entry'][3]['value'];
			}
			
			// set the type of this entry...
			$this->request->data['Entry']['entry_type'] = (empty($myEntry)?(empty($myType)?'pages':$myType['Type']['slug']):$myChildType['Type']['slug']);
			// generate slug from title...			
			$this->request->data['Entry']['slug'] = $this->get_slug($this->request->data['Entry']['title']);
			// write my creator...			
			$this->request->data['Entry']['created_by'] = $this->user['id'];
			$this->request->data['Entry']['modified_by'] = $this->user['id'];
			// write time created manually !!
			$nowDate = $this->getNowDate();
			$this->request->data['Entry']['created'] = $nowDate;
			$this->request->data['Entry']['modified'] = $nowDate;
			// set parent_id
			$this->request->data['Entry']['parent_id'] = (empty($myEntry)?0:$myEntry['Entry']['id']);
			$this->request->data['Entry']['lang_code'] = strtolower(empty($lang_code)?$this->request->data['language']:$lang_code);
			
			// PREPARE FOR ADDITIONAL LINK OPTIONS !!
			$myChildTypeLink = (!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?'?type='.$myChildType['Type']['slug']:'');
			$myTranslation = (empty($myChildTypeLink)?'?':'&').'lang='.substr($this->request->data['Entry']['lang_code'], 0,2);
			
			// now for validation !!
			$this->Entry->set($this->request->data);
			if($this->Entry->validates())
			{
			    // --------------------------------- NOW for add / validate the details of this entry !!!
				$myDetails = $this->request->data['EntryMeta'];
				$errMsg = "";
                
				foreach ($myDetails as $key => $value) 
				{
                    if($value['input_type']=='file' && !empty($_FILES[$value['key']]['name']))
                    {
                        $value['value'] = $_FILES[$value['key']]['name'];
                    }
                    else if($value['input_type']=='multibrowse')
                    {
                        $value['value'] = array_unique(array_filter($value['value']));
                    }

					// firstly DO checking validation from view layout !!!
					$myValid = explode('|', $value['validation']);
					foreach ($myValid as $key10 => $value10) 
					{
						$tempMsg = $this->Validation->blazeValidate( $value['value'] ,$value10 , $value['key']);
						$errMsg .= ( strpos($errMsg, $tempMsg) === FALSE ?$tempMsg:"");
					}
					// secondly DO checking validation from database !!!
					foreach ($myAutomaticValidation as $key2 => $value2) // check for validation for each attribute key... 
					{
						if($value['key'] == $value2['key']) // if find the same key...
						{
							$myValid = explode('|' , $value2['validation']);
							foreach ($myValid as $key3 => $value3) 
							{
								$tempMsg = $this->Validation->blazeValidate($value['value'],$value3 , $value['key']);
								$errMsg .= ( strpos($errMsg, $tempMsg) === FALSE ?$tempMsg:"");
							}
							break;
						}
					}
				}
				// LAST CHECK ERROR MESSAGE !!
				if(!empty($errMsg))
				{
					$this->Session->setFlash($errMsg,'failed');
					return;
				}
                
                // CUSTOM CHECKER FOR WAREHOUSE EMPLOYEE ROLE !!
                if(!empty($this->user['storage']))
                {
                    if($myType['Type']['slug'] == 'surat-jalan')
                    {
                        $invalid_storage = true;
                        $breaked_data = breakEntryMetas($this->request->data);
                        
                        foreach($this->user['storage'] as $key => $value)
                        {
                            if($breaked_data['EntryMeta'][ $value['entry_type'].'_origin' ] == $value['slug'])
                            {
                                $invalid_storage = false;
                                break;
                            }
                        }
                        
                        if($invalid_storage)
                        {
                            $alert_tail = implode(', ', array_column($this->user['storage'], 'title'));
                            $this->Session->setFlash('Pembuatan Surat Jalan tidak valid!<br>Akun Anda hanya diperbolehkan untuk menambahkan Surat Jalan dengan tempat asal di mana Anda ditugaskan ('.$alert_tail.'). Silahkan cek dan ulangi kembali.','failed');
					        return;
                        }
                    }
                    else if(strpos($myType['Type']['slug'], '-invoice') !== false)
                    {
                        $invalid_storage = true;
                        $breaked_data = breakEntryMetas($this->request->data);
                        
                        foreach($this->user['storage'] as $key => $value)
                        {
                            if($breaked_data['EntryMeta'][ $value['entry_type'] ] == $value['slug'])
                            {
                                $invalid_storage = false;
                                break;
                            }
                        }
                        
                        if($invalid_storage)
                        {
                            $alert_tail = implode(', ', array_column($this->user['storage'], 'title'));
                            $this->Session->setFlash('Pembuatan Invoice tidak valid!<br>Akun Anda hanya diperbolehkan untuk menambahkan Invoice dengan Warehouse / Pameran tempat di mana Anda ditugaskan ('.$alert_tail.'). Silahkan cek dan ulangi kembali.','failed');
					        return;
                        }
                    }
                    else if($myType['Type']['slug'] == 'sr-dmd-payment' || $myType['Type']['slug'] == 'sr-cor-payment')
                    {
                        $invalid_storage = true;
                        $breaked_data = breakEntryMetas($this->request->data);
                        
                        foreach($this->user['storage'] as $key => $value)
                        {
                            if($breaked_data['EntryMeta']['warehouse_payer'] == $value['slug'])
                            {
                                $invalid_storage = false;
                                break;
                            }
                        }
                        
                        if($invalid_storage)
                        {
                            $alert_tail = implode(', ', array_column($this->user['storage'], 'title'));
                            $this->Session->setFlash('Metode Pembayaran tidak valid!<br>Akun Anda hanya diperbolehkan untuk melakukan pembayaran dengan <a class="underline" href="#warehouse-payer">Warehouse Payer (pihak pembayar)</a> di mana Anda ditugaskan ('.$alert_tail.'). Silahkan cek dan ulangi kembali.','failed');
					        return;
                        }
                    }
                }
				// ------------------------------------- end of entry details...
				$this->Entry->create();
				$this->Entry->save($this->request->data);
                $this->request->data['Entry'][0]['slug'] = $this->Entry->field('slug');
                $newEntryId = $this->Entry->id;
                if($data['gallery'])
                {   
                    foreach (array_reverse($this->request->data['Entry']['image']) as $key => $value) 
                    {
                        $myImage = $this->Entry->findById($value);
                        
                        $input = array();
                        $input['Entry']['entry_type'] = $this->request->data['Entry']['entry_type'];
                        $input['Entry']['title'] = $myImage['Entry']['title'];
                        $input['Entry']['slug'] = $this->get_slug($myImage['Entry']['title']);
                        $input['Entry']['main_image'] = $value;
                        $input['Entry']['parent_id'] = $newEntryId;
                        $input['Entry']['created_by'] = $this->user['id'];
                        $input['Entry']['modified_by'] = $this->user['id'];
                        $this->Entry->create();
                        $this->Entry->save($input);
                    }
                }

                if(!empty($this->request->data['Entry']['fieldimage']))
                {
                	foreach ($this->request->data['Entry']['fieldimage'] as $fieldkey => $fieldvalue) 
                	{
                		foreach (array_reverse($fieldvalue) as $key => $value) 
                		{
                			$myImage = $this->Entry->findById($value);

                			$input = array();                        
	                        $input['Entry']['entry_type'] = $fieldkey;
	                        $input['Entry']['title'] = $myImage['Entry']['title'];
	                        $input['Entry']['slug'] = $this->get_slug($myImage['Entry']['title']);
	                        $input['Entry']['main_image'] = $value;
	                        $input['Entry']['parent_id'] = $newEntryId;
	                        $input['Entry']['created_by'] = $this->user['id'];
	                        $input['Entry']['modified_by'] = $this->user['id'];
	                        $this->Entry->create();
	                        $this->Entry->save($input);
                		}
                	}
                }

				$this->request->data['EntryMeta']['entry_id'] = $newEntryId;
				foreach ($myDetails as $key => $value)
				{	
					if(!empty($value['value']) && substr($value['key'], 0,5) == 'form-')
					{
						$this->request->data['EntryMeta']['key'] = $value['key'];
						if($value['input_type'] == 'image' && isset($value['w']) && isset($value['h']))
						{
							$this->request->data['EntryMeta']['value'] = $this->Entry->makeChildImageEntry($value,(empty($myEntry)?$myType:$myChildType));
						}
						else if($value['input_type'] == 'multibrowse')
						{
                            $value['value'] = array_unique(array_filter($value['value']));
                            if(!empty($value['total'])) // special mode...
                            {
                                foreach($value['value'] as $nanokey => $nanovalue)
                                {
                                    $value['value'][$nanokey] .= '_'.$value['total'][$nanokey];
                                }
                            }
							$this->request->data['EntryMeta']['value'] = implode('|', $value['value'] );
						}
						else
						{
							$this->request->data['EntryMeta']['value'] = ($value['input_type'] == 'checkbox'?implode("|",$value['value']):$value['value']);
						}
						
                        // ONE MORE CHECKER STEP !!
                        if(strpos($value['validation'], 'is_numeric') !== FALSE)
                        {
                            $value['value'] = (float)$value['value'];
                        }
                        if(!empty($value['value']))
                        {
                            $this->EntryMeta->create();
						    $this->EntryMeta->save($this->request->data);
                        }
					}
				}
				
				// Upload File !!
				if(isset($_FILES))
				{
					foreach ($_FILES as $key => $value) 
					{
						if(!empty($value['name']))
						{
							$value['name'] = getValidFileName($value['name']);
							uploadFile($value);
							// Save data to EntryMeta !!
							$this->request->data['EntryMeta']['key'] = $key;
							$this->request->data['EntryMeta']['value'] = $value['name'];
							$this->EntryMeta->create();
							$this->EntryMeta->save($this->request->data);
						}
					}
				}
                
                // reorder Entry.sort_order that just be translated !!
                if(!empty($lang_code))
                {
                    $this->Entry->_reorderAfterTranslate($lang_code);
                }
                
                // ---------- ADD SHIPPING ID OR SOMETHING ELSE RELATED !! ------------- //
				$this->_add_update_id_meta($myType['Type']['slug'] , $myChildTypeSlug , $myEntry);
                
				// NOW finally setFlash ^^
				$this->Session->setFlash($this->request->data['Entry']['title'].' has been added.','success');
				if($this->request->params['admin']==1)
				{
					$newEntrySlug = $this->Entry->checkRemainingLang($newEntryId , $this->mySetting);
					$this->redirect(array('action' => (empty($myType)?'pages':$myType['Type']['slug']).(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']).($newEntrySlug?'/edit/'.$newEntrySlug.$myChildTypeLink:$myChildTypeLink.$myTranslation) ));
				}
				else
				{
					$this->redirect( redirectSessionNow($_SESSION['now']) );
				}
			}
			else 
			{
				$this->_setFlashInvalidFields($this->Entry->invalidFields());
			}
		}
	}
    
    /*
    * last action before setFlash from add/update selected Entry...
    */
    function _add_update_id_meta($myTypeSlug , $myChildTypeSlug = NULL , $myParentEntry = array() , $myEntry = array())
	{
		// $this->request->data['EntryMeta']['entry_id'] => not needed to be set, coz it's already set in parent function !!
        $this->request->data = breakEntryMetas($this->request->data);
        $this->request->data['Entry']['id'] = $this->request->data['EntryMeta']['entry_id'];
        $this->request->data['imagePath'] = $this->get_linkpath();
        
        // ADDITIONAL FUNCTION HERE AFTER INSERT / UPDATE RECORD !!
        // ...............
        // ===================================================== >>
        
        /*
        note 1: pada saat pengiriman barang retur, sistem akan otomatis mengurangi total pcs,
                total item sent & total price / weight dari invoice yg berkaitan ...
        */
        
        if($myTypeSlug == 'surat-jalan')
        {
            if($myEntry['Entry']['status'] != 1) // executed ONLY IF still not accepted ...
            {
                $this->EntryMeta->update_surat_jalan($this->request->data, $myEntry);
            }
        }
        else if(strpos($myTypeSlug, '-invoice') !== FALSE)
        {
            if(empty($myChildTypeSlug))
            {
                if(empty($myEntry)) // ADD MODE ONLY !!
                {
                    $this->EntryMeta->update_product_by_invoice($myTypeSlug , $this->request->data);
                }
            }
            else if(strpos($myChildTypeSlug, '-payment') !== FALSE)
            {
                // JUST ONCE WHEN PAYMENT STATUS IS COMPLETED !!
                if($myEntry['Entry']['status'] != 1 && $this->request->data['Entry'][3]['value'] == 1)
                {
                    $this->Entry->update_invoice_payment($myParentEntry, $this->request->data);
                }
            }
        }
        else if(strpos($myTypeSlug, '-payment') !== FALSE) // SR / RR
        {
            // JUST ONCE WHEN PAYMENT STATUS IS COMPLETED !!
            if($myEntry['Entry']['status'] != 1 && $this->request->data['Entry'][3]['value'] == 1)
            {
                $this->Entry->update_invoice_payment(NULL, $this->request->data);
            }
        }
	}

	/**
	* update certain entry 
	* @param array $myType contains record query result of database type
	* @param array $myEntry contains record query result of the selected Entry
	* @param array $myParentEntry[optional] contains record query result of the parent Entry (used if want to search certain child Entry) 
	* @param string $myChildTypeSlug[optional] contains slug of child type database (used if want to search certain child Entry)
	* @return array $result a selected entry with all of its attributes you'd requested
	* @public
	**/
	function _admin_default_edit($myType = array() , $myEntry = array() , $myParentEntry = array() , $myChildTypeSlug = NULL , $lang = NULL)
	{
		if ($this->request->is('ajax')) 
		{	
			$this->layout = 'ajax';
			$data['isAjax'] = 1;
		} 
		else 
		{
			$data['isAjax'] = 0;
		}	
		$this->setTitle('Edit '.$myEntry['Entry']['title']);
		$myChildType = $this->Type->findBySlug($myChildTypeSlug);
		$data['myType'] = $myType;		
		$data['myEntry'] = $myEntry;
		$data['myParentEntry'] = $myParentEntry;
		$data['myChildType'] = $myChildType;
        
        // SEARCH IF GALLERY MODE IS TURN ON / OFF ...
        $myAutomaticValidation = (empty($myParentEntry)?$myType['TypeMeta']:$myChildType['TypeMeta']);
        $data['gallery'] = $this->Entry->checkGalleryType($myAutomaticValidation);

        // FIRSTLY, sorting our (image / entry) children !!
        if(!empty($data['myEntry']['ChildEntry']))
        {
            $options = array(
	            'conditions' => array(
	                'Entry.parent_id' => $myEntry['Entry']['id']
	            ),
	            'order' => array('Entry.'.$this->generalOrder )
	        );

	        $data['myEntry']['ChildEntry'] = array_map('breakEntryMetas', $this->Entry->find('all', $options) );
        }
        
		// for image input type reason...
		$data['myImageTypeList'] = $this->EntryMeta->embedded_img_meta('type');
		// --------------------------------------------- LANGUAGE OPTION LINK ------------------------------------------ //
		$lang_opt = $this->Entry->find('all' , array(
			'conditions' => array(
				'Entry.lang_code LIKE' => '%-'.substr($myEntry['Entry']['lang_code'], 3)
			)
		));
		foreach ($lang_opt as $key => $value) 
		{
			$language_link[substr($value['Entry']['lang_code'], 0,2)] = $value['Entry']['slug'];
		}
		$data['language_link'] = $language_link;
		$data['lang'] = $lang;
		if(!empty($myParentEntry) && count($this->mySetting['language']) > 1)
		{
			$temp100 = $this->Entry->find('all' , array(
				'conditions' => array(
					'Entry.lang_code LIKE' => '%-'.substr($myParentEntry['Entry']['lang_code'], 3)
				),
                'recursive' => -1
			));
			foreach ($temp100 as $key => $value) 
			{
				$parent_language[ substr($value['Entry']['lang_code'], 0,2) ] = $value['Entry']['slug'];
			}
			$data['parent_language'] = $parent_language;
		}
		// ------------------------------------------ END OF LANGUAGE OPTION LINK -------------------------------------- //
		$this->set('data' , $data);
		
		// if form submit is taken...
		if (!empty($this->request->data))
		{
            if(empty($lang))
			{
				$this->request->data['Entry']['title'] = $this->request->data['Entry'][0]['value'];
                $this->request->data['Entry']['description'] = $this->request->data['Entry'][1]['value'];
				$this->request->data['Entry']['main_image'] = $this->request->data['Entry'][2]['value'];
				if(isset($this->request->data['Entry'][3]['value']))
				{
					$this->request->data['Entry']['status'] = $this->request->data['Entry'][3]['value'];
				}
				
				// write my modifier ID...				
				$this->request->data['Entry']['modified_by'] = $this->user['id'];

				// write time modified manually !!
				$nowDate = $this->getNowDate();
				$this->request->data['Entry']['modified'] = $nowDate;
				
				// PREPARE FOR ADDITIONAL LINK OPTIONS !!
				$myChildTypeLink = (!empty($myParentEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?'?type='.$myChildType['Type']['slug']:'');
				$myTranslation = (empty($myChildTypeLink)?'?':'&').'lang='.substr($myEntry['Entry']['lang_code'], 0,2);
				
				// now for validation !!
				$this->Entry->set($this->request->data);
				if($this->Entry->validates())
				{		
					// --------------------------------- NOW for validating the details of this entry !!!
					$errMsg = "";
					$myDetails = $this->request->data['EntryMeta'];
					foreach ($myDetails as $key => $value) 
					{
						if($value['input_type']=='file')				{continue;}
						else if($value['input_type']=='multibrowse')	{$value['value'] = array_unique(array_filter($value['value']));}
							
						// firstly DO checking validation from view layout !!!
						$myValid = explode('|', $value['validation']);
						foreach ($myValid as $key10 => $value10) 
						{
							$tempMsg = $this->Validation->blazeValidate($value['value'],$value10 , $value['key']);
							$errMsg .= ( strpos($errMsg, $tempMsg) === FALSE ?$tempMsg:"");
						}
						// secondly DO checking validation from database !!!
						foreach ($myAutomaticValidation as $key2 => $value2) // check for validation for each attribute key... 
						{
							if($value['key'] == $value2['key']) // if find the same key...
							{					
								$myValid = explode('|' , $value2['validation']);
								foreach ($myValid as $key3 => $value3) 
								{
									$tempMsg = $this->Validation->blazeValidate($value['value'],$value3 , $value['key']);
									$errMsg .= ( strpos($errMsg, $tempMsg) === FALSE ?$tempMsg:"");
								}
								break;
							}
						}
					}
					// LAST CHECK ERROR MESSAGE !!
					if(!empty($errMsg))
					{
						$this->Session->setFlash($errMsg,'failed');
						return;
					}
					// ------------------------------------- end of entry details...
					$this->Entry->id = $myEntry['Entry']['id'];
					$this->Entry->save($this->request->data);
				
                    // SKIP ENTRYMETA PROCESS ON SOME ENTRY_TYPE !!!
                if(!($myEntry['Entry']['entry_type'] == 'surat-jalan' || strpos($myType['Type']['slug'], '-invoice') !== FALSE ))
                {
                    $galleryId = $myEntry['Entry']['id'];
                    if($data['gallery'])
                    {
                        // delete all the child image, and then add again !!
                        $this->Entry->deleteAll(array('Entry.parent_id' => $galleryId,'Entry.entry_type' => $myEntry['Entry']['entry_type']));
                        
                        foreach (array_reverse($this->request->data['Entry']['image']) as $key => $value) 
                        {
                            $myImage = $this->Entry->findById($value);
                            
                            $input = array();
                            $input['Entry']['entry_type'] = $myEntry['Entry']['entry_type'];
                            $input['Entry']['title'] = $myImage['Entry']['title'];
                            $input['Entry']['slug'] = $this->get_slug($myImage['Entry']['title']);
                            $input['Entry']['main_image'] = $value;
                            $input['Entry']['parent_id'] = $galleryId;
                            $input['Entry']['created_by'] = $this->user['id'];
                            $input['Entry']['modified_by'] = $this->user['id'];
                            $this->Entry->create();
                            $this->Entry->save($input);
                        }
                    }

                    // delete all the attributes, and then add again !!
					$this->EntryMeta->deleteAll(array(
						'EntryMeta.entry_id' => $myEntry['Entry']['id'] ,
						'OR' => array(
							array('EntryMeta.key LIKE' => 'form-%'),
							array('EntryMeta.key LIKE' => 'count-form-%'),
						)
					));

                    // delete all the field child image, and then add again !!
                    $this->Entry->deleteAll(array('Entry.parent_id' => $galleryId,'Entry.entry_type LIKE' => 'form-%'));

                    if(!empty($this->request->data['Entry']['fieldimage']))
	                {
	                	foreach ($this->request->data['Entry']['fieldimage'] as $fieldkey => $fieldvalue) 
	                	{
	                		foreach (array_reverse($fieldvalue) as $key => $value) 
	                		{
	                			$myImage = $this->Entry->findById($value);

	                			$input = array();                        
		                        $input['Entry']['entry_type'] = $fieldkey;
		                        $input['Entry']['title'] = $myImage['Entry']['title'];
		                        $input['Entry']['slug'] = $this->get_slug($myImage['Entry']['title']);
		                        $input['Entry']['main_image'] = $value;
		                        $input['Entry']['parent_id'] = $galleryId;
		                        $input['Entry']['created_by'] = $this->user['id'];
		                        $input['Entry']['modified_by'] = $this->user['id'];
		                        $this->Entry->create();
		                        $this->Entry->save($input);
	                		}
	                	}
	                }

	                // Insert New EntryMeta ...
					$this->request->data['EntryMeta']['entry_id'] = $myEntry['Entry']['id'];
					foreach ($myDetails as $key => $value)
					{	
						if(!empty($value['value']) && substr($value['key'], 0,5) == 'form-')
						{
							if($value['input_type'] == 'file' && !empty($_FILES[$value['key']]['name']))
							{
								$_FILES[$value['key']]['value'] = $value['value'];
							}
							else
							{
								$this->request->data['EntryMeta']['key'] = $value['key'];
								if($value['input_type'] == 'image' && isset($value['w']) && isset($value['h']))
								{
									$this->request->data['EntryMeta']['value'] = $this->Entry->makeChildImageEntry($value,(empty($myParentEntry)?$myType:$myChildType));
								}
								else if($value['input_type'] == 'multibrowse')
								{
                                    $value['value'] = array_unique(array_filter($value['value']));
                                    if(!empty($value['total'])) // special mode...
                                    {
                                        foreach($value['value'] as $nanokey => $nanovalue)
                                        {
                                            $value['value'][$nanokey] .= '_'.$value['total'][$nanokey];
                                        }
                                    }
									$this->request->data['EntryMeta']['value'] = implode('|', $value['value'] );
								}
								else
								{
									$this->request->data['EntryMeta']['value'] = ($value['input_type'] == 'checkbox'?implode("|",$value['value']):$value['value']);
								}
                                
                                // ONE MORE CHECKER STEP !!
                                if(strpos($value['validation'], 'is_numeric') !== FALSE)
                                {
                                    $value['value'] = (float)$value['value'];
                                }
                                if(!empty($value['value']))
                                {
                                    $this->EntryMeta->create();
                                    $this->EntryMeta->save($this->request->data);
                                }
							}
						}
					}

					// Upload File !!
					if(isset($_FILES))
					{
						foreach ($_FILES as $key => $value) 
						{
							if(!empty($value['name']))
							{
								if(!empty($value['value']))
								{
									deleteFile($value['value']);
								}
								
								$value['name'] = getValidFileName($value['name']);
								uploadFile($value);
								// Save data to EntryMeta !!
								$this->request->data['EntryMeta']['key'] = $key;
								$this->request->data['EntryMeta']['value'] = $value['name'];
								$this->EntryMeta->create();
								$this->EntryMeta->save($this->request->data);
							}
						}
					}    
                }
                    
                    // --------- UPDATE SHIPPING ID OR SOMETHING ELSE RELATED !! ----------- //
				    $this->_add_update_id_meta($myType['Type']['slug'] , $myChildTypeSlug , $myParentEntry , $myEntry);
                    
					$this->Session->setFlash($this->request->data['Entry']['title'].' has been updated.','success');
					if($this->request->params['admin']==1)
					{
						$newEntrySlug = $this->Entry->checkRemainingLang($myEntry['Entry']['id'] , $this->mySetting);
						$this->redirect(array('action' => (empty($myType)?'pages':$myType['Type']['slug']).(empty($myParentEntry)?'':'/'.$myParentEntry['Entry']['slug']).($newEntrySlug?'/edit/'.$newEntrySlug.$myChildTypeLink:$myChildTypeLink.$myTranslation) ));
					}
					else
					{
						$this->redirect( redirectSessionNow($_SESSION['now']) );
					}
				}
				else 
				{	
					$this->_setFlashInvalidFields($this->Entry->invalidFields());
					return;
				}
			}
			else // ADD NEW TRANSLATION LANGUAGE !!
			{	
				$this->_admin_default_add($myType , $myParentEntry , $myChildTypeSlug , $lang.substr( $myEntry['Entry']['lang_code'] , 2) , $myEntry['Entry']['slug']);
			}
		}
		return $data;
	}
	
	/**
	 * blueimp jQuery plugin function for initialize upload media image purpose
	 * @return void
	 * @public
	 **/
	public function UploadHandler()
	{
		$this->autoRender = FALSE;
		App::import('Vendor', 'uploadhandler');
		$upload_handler = new UploadHandler();
		
		$info = $upload_handler->post();
		
		// update database...
		if(isset($info[0]->name) && (!isset($info[0]->error)))
		{
			$path_parts = pathinfo($info[0]->name);
			$filename = $path_parts['filename'];
			$mytype = strtolower($path_parts['extension']);

			// CHECK FILE ALREADY EXISTS OR NOT ?
			$checkmedia = $this->meta_details(NULL , 'media' , NULL , NULL , NULL , NULL , $filename);
			if( !empty($this->mySetting['custom-overwrite_image']) && !empty($checkmedia) && $checkmedia['EntryMeta']['image_type'] == $mytype)
			{
				$this->request->data['Entry'] = $checkmedia['Entry'];
				$myid = $checkmedia['Entry']['id'];

				// REMOVE OLD IMAGE FILE !!
				unlink(WWW_ROOT.'img'.DS.'upload'.DS.$myid.'.'.$mytype);

				// DELETE ENTRY METAS TOO !!
				$this->EntryMeta->deleteAll(array('EntryMeta.entry_id' => $myid));
			}
			else // create new data !!
			{
				// set the type of this entry...
				$this->request->data['Entry']['entry_type'] = 'media';
				$this->request->data['Entry']['title'] = $filename;
				// generate slug from title...
				$this->request->data['Entry']['slug'] = $this->get_slug($this->request->data['Entry']['title']);
				// write my creator...
				
				$this->request->data['Entry']['created_by'] = $this->user['id'];
				$this->request->data['Entry']['modified_by'] = $this->user['id'];
				$this->Entry->create();
				$this->Entry->save($this->request->data);
				
				$myid = $this->Entry->id;
			}

			// rename the filename...
			rename( WWW_ROOT.'img'.DS.'upload'.DS.'original'.DS.$info[0]->name , WWW_ROOT.'img'.DS.'upload'.DS.'original'.DS.$myid.'.'.$mytype);
			
			// now generate for display and thumb image according to the media settings...
			$myType = $this->Type->findBySlug($this->request->data['Type']['slug']);
			$myMediaSettings = $this->Entry->getMediaSettings($myType);
			
			// save the image type...			
			$this->request->data['EntryMeta']['entry_id'] = $myid;
			$this->request->data['EntryMeta']['key'] = 'image_type';
			$this->request->data['EntryMeta']['value'] = $mytype;
			$this->EntryMeta->create();
			$this->EntryMeta->save($this->request->data);
			// save the image size...
			$this->request->data['EntryMeta']['key'] = 'image_size';
			$this->request->data['EntryMeta']['value'] = $this->Entry->createDisplay($myid , $mytype , $myMediaSettings);
			$this->EntryMeta->create();
			$this->EntryMeta->save($this->request->data);
			
			// REMOVE ORIGINAL IMAGE FILE !!
			unlink(WWW_ROOT.'img'.DS.'upload'.DS.'original'.DS.$myid.'.'.$mytype);
		}
	}

	/**
	 * generate upload popup for uploading image to media library
 	 * @param string $myTypeSlug contains from what database type this function is called(used for media settings arrangements)
	 * @return void
	 * @public
	 **/
	public function upload_popup($myTypeSlug = NULL)
	{			
		$this->layout = 'ajax';	
		$this->set('myTypeSlug' , $myTypeSlug);
	}
	
	/**
	 * generate upload popup form for selecting image from media library
	 * @param integer $paging[optional] contains selected page of lists you want to retrieve
	 * @param string $myCaller[optional] contains type of method this popup is called
 	 * @param string $myTypeSlug[optional] contains from what database type this function is called(used for media settings arrangements)
	 * @return void
	 * @public
	 **/
	public function media_popup_single($paging = NULL , $mycaller = NULL , $myTypeSlug = NULL)
	{
		$this->setTitle("Media Library");
		$this->layout = ($this->request->is('ajax')?'ajax':'cms_blankpage');

		if(is_null($paging))
        {
            $paging = 1;
        }
        $this->set('paging' , $paging);
		$this->set('isAjax' , (is_null($mycaller) && is_null($myTypeSlug)?1:0) );		
		$this->set('myTypeSlug' , $myTypeSlug);
		
		// DEFINE MY TYPE CROP !!
		if(!empty($myTypeSlug))
		{
			$temp = $this->Type->findBySlug($myTypeSlug);
			$crop = -1;
			foreach ($temp['TypeMeta'] as $key => $value) 
			{
				if($value['key'] == 'display_crop')
				{
					$crop = $value['value'];
				}
			}
			$this->set('crop' , $crop);
		}
		
		$countPage = $this->mediaPerPage;
		
		$options['conditions'] = array(
			'Entry.entry_type' => 'media',
			'Entry.parent_id' => 0
		);
		$resultTotalList = $this->Entry->find('count' , $options);
		$this->set('totalList' , $resultTotalList);
		
		$options['order'] = array('Entry.'.$this->generalOrder);
		$options['offset'] = ($paging-1) * $countPage;
		$options['limit'] = $countPage;
		$mysql = $this->Entry->find('all' ,$options);
		$this->set('myList' , $mysql);
		
		// set New countPage
		$newCountPage = ceil($resultTotalList / $countPage);
		$this->set('countPage' , $newCountPage);
		
		// set the paging limitation...
		$left_limit = 1;
		$right_limit = 5;
		if($newCountPage <= 5)
		{
			$right_limit = $newCountPage;
		}
		else
		{
			$left_limit = $paging-2;
			$right_limit = $paging+2;
			if($left_limit < 1)
			{
				$left_limit = 1;
				$right_limit = 5;
			}
			else if($right_limit > $newCountPage)
			{
				$right_limit = $newCountPage;
				$left_limit = $newCountPage - 4;
			}			
		}
		$this->set('left_limit' , $left_limit);
		$this->set('right_limit' , $right_limit);
		
		// set mycaller...
		if(is_null($mycaller))
		{
			$this->set('mycaller' , '0');
		}
		else
		{
			$this->set('mycaller' , $mycaller);
		}		
	}	
	
	function update_slug()
	{		
		$this->autoRender = FALSE;
		$slug = $this->Entry->get_valid_slug(    $this->get_slug($this->request->data['slug'])   ,  $this->request->data['id']  );
		$this->Entry->id = $this->request->data['id'];
		$this->Entry->saveField('slug' , $slug);
		echo $slug;
	}
	
	/**
	 * re-order entry sort_order for entries view order through ajax
	 * @return void
	 * @public
	 **/
	function reorder_list()
	{
		$this->autoRender = FALSE;
		$this->Entry->_reorderList( explode(',', $this->request->data['src_order'] ) , explode(',', $this->request->data['dst_order'] ) , $this->request->data['lang'] );
	}
	
	// imported from GET Helpers !!
	function meta_details($slug = NULL , $entry_type = NULL , $parentId = NULL , $id = NULL , $ordering = NULL , $lang = NULL , $title = NULL)
	{
		return $this->Entry->meta_details($slug , $entry_type , $parentId , $id , $ordering , $lang , $title ); // default is from BACK-END called !!
	}

	function admin_backup()
	{
		$mode = $this->request->params['mode'];

		$myTitle = "Backup Database & Files";
		$this->setTitle($myTitle);
		$this->set('myTitle' , $myTitle);

		if($mode == "clean")
		{			
			$this->Setting->cleanDatabase();
			$this->Session->setFlash('Database has been cleaned successfully.', 'success');
			$this->redirect (array('action' => 'backup'));
		}
		else if($mode == "backup-files") // uploaded files
		{
			$filename = 'files-'.get_slug($this->mySetting['title']).'-'.date('d-m-Y').'.zip';
			if(!Zip('files', $filename)) // if zipping failed, then reload page and let the admin try again.
			{
				$this->render($this->backEndFolder."backup-restore");
			}
		}
        else if($mode == "backup-img") // uploaded img/upload...
		{
			$filename = 'images-'.get_slug($this->mySetting['title']).'-'.date('d-m-Y').'.zip';
			if(!Zip('img/upload', $filename)) // if zipping failed, then reload page and let the admin try again.
			{
				$this->render($this->backEndFolder."backup-restore");
			}
		}
		else if($mode == "backup") // database ...
		{
			$this->layout = "sql";
			$this->set('sql' , $this->Setting->backup_tables($this->get_db_host() , $this->get_db_user() , $this->get_db_password() , $this->get_db_name()));
			$this->render($this->backEndFolder."sql");
		}
		else if($mode == "restore")
		{	
			$ext = pathinfo($this->request->data['fileurl']['name'], PATHINFO_EXTENSION);
			if(strtolower($ext) == "sql")
			{
				$message = $this->Setting->executeSql($this->get_db_host(),$this->get_db_user() , $this->get_db_password() , $this->get_db_name(),$this->request->data['fileurl']['tmp_name']);
				if($message == "success")
				{
					$this->Session->setFlash('Database restoration success.', 'success');
				}
				else
				{
					$this->Session->setFlash($message, 'failed');
				}
			}
			else
			{
				$this->Session->setFlash('File extension invalid.', 'failed');
			}
			$this->redirect (array('action' => 'backup'));
		}
		else // JUST VIEWING OPTIONS !!
		{
			$this->render($this->backEndFolder."backup-restore");
		}
	}
}