<?php
class EntryMeta extends AppModel {
	var $name = 'EntryMeta';
	var $validate = array(
		'entry_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'key' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Entry' => array(
			'className' => 'Entry',
			'foreignKey' => 'entry_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	// DATABASE MODEL...
	var $Type = NULL;
	var $TypeMeta = NULL;
	var $Entry = NULL;
    var $EntryMeta = NULL;
    var $Account = NULL;
    // CURRENT USER DETAIL ...
    var $myCreator = NULL;
	
	public function __construct( $id = false, $table = NULL, $ds = NULL )
	{
		parent::__construct($id, $table, $ds);
		
		// set needed database model ...
		$this->Type = ClassRegistry::init('Type');
		$this->TypeMeta = ClassRegistry::init('TypeMeta');
		$this->Entry = ClassRegistry::init('Entry');
        $this->EntryMeta = $this; // just as alias ...
		$this->Account = ClassRegistry::init('Account');
        
        // set current user ...
        $this->myCreator = $this->getCurrentUser();
	}
	
	/**
	 * retrieve all image types in one indexing array based on that image id as selector
	 * @param string $type contain type attribute of the image (default is image type)
	 * @return array $imgTypeList contains array of image type lists
	 * @public
	 **/
	function embedded_img_meta($type)
	{
		$imgReason = $this->find('all', array(
			'conditions' => array(
				'EntryMeta.key' => 'image_'.$type
			),
            'recursive' => -1
		));
		$imgTypeList[0] = 'jpg';
		foreach ($imgReason as $key20 => $value20)
		{
			$imgTypeList[$value20['EntryMeta']['entry_id']] = $value20['EntryMeta']['value'];			
		}
		return $imgTypeList;
	}
	
    /*
	* Delete files in EntryMeta when a data is to be deleted !!
    */
	function remove_files($myType , $myEntry)
	{
		$haystack = array();
		foreach ($myType['TypeMeta'] as $key => $value) 
		{
			if($value['input_type'] == 'file')
			{
				array_push($haystack , $value['key']);
			}
		}
		
		if(!empty($haystack))
		{
			foreach ($myEntry['EntryMeta'] as $key => $value) 
			{
				if(in_array($value['key'], $haystack))
				{
					deleteFile($value['value']);
				}
			}
		}
	}
    
    function get_diamond_type()
    {
        $query = $this->findAllByKeyAndValue('form-category', 'Diamond');
        $result = array();
        
        foreach($query as $key => $value)
        {
            $result[$value['Entry']['slug']] = $value['Entry']['title'];
        }
        return $result;
    }
    
    function push_product($obj = array(), $myTypeSlug, $title, $description = NULL)
    {
        // BEGIN PROCESS ...
        $query = $this->Entry->find('first', array(
            'conditions' => array(
                'Entry.title'       => $title,
                'Entry.entry_type'  => $myTypeSlug,
            )
        ));
        
        if(empty($query)) // CREATE NEW !!
        {
            $input = array();
            $input['Entry']['entry_type'] = $myTypeSlug;
            $input['Entry']['title'] = $title;
            $input['Entry']['slug'] = get_slug($input['Entry']['title']);
            if(!empty($description))    $input['Entry']['description'] = $description;
            $input['Entry']['created_by'] = $input['Entry']['modified_by'] = $this->myCreator['id'];
            $this->Entry->create();
            $this->Entry->save($input);
            
            $query['Entry']['id'] = $this->Entry->id;
        }
        
        foreach($query['EntryMeta'] as $qKey => $qValue )
        {
            if(substr($qValue['key'] , 0 , 5) == 'form-' && isset($obj[ $shortkey = substr($qValue['key'], 5) ]))
            {
                if(empty($obj[$shortkey])) // DELETE ENTRYMETA ...
                {
                    $this->EntryMeta->delete($qValue['id']);
                }
                else // UPDATE ENTRYMETA ...
                {
                    $this->EntryMeta->id = $qValue['id'];
                    $this->EntryMeta->saveField('value', $obj[$shortkey]);
                    unset($obj[$shortkey]);
                }
            }
        }
        
        // ADD ENTRYMETA ...
        $input = array();
        $input['EntryMeta']['entry_id'] = $query['Entry']['id'];
        foreach($obj as $objKey => $objValue )
        {
            if(!empty($objValue))
            {
                $input['EntryMeta']['key'] = 'form-'.$objKey;
                $input['EntryMeta']['value'] = $objValue;
                $this->EntryMeta->create();
                $this->EntryMeta->save($input);
            }
        }
    }
    
    function push_general_entry(&$title, $entry_type, $complete = FALSE, $terms = array())
    {
        if(empty($title))
        {
            return FALSE;
        }
        
        $query = array();
        if($complete)
        {
            $query = $this->Entry->meta_details(NULL , $entry_type , NULL , NULL , NULL , NULL , $title);
        }
        else
        {
            $query = $this->Entry->find('first', array(
                'conditions' => array(
                    'Entry.title'       => $title,
                    'Entry.entry_type'  => $entry_type,
                ),
                'recursive' => -1
            ));
        }
        
        // check existence ...
        if(empty($query))
        {
            if(count($terms) != count(array_filter($terms)))
            {
                $title = '';
                return FALSE;
            }
            
            $input = array();
            $input['Entry']['entry_type'] = $entry_type;
            $input['Entry']['title'] = $title;
            $input['Entry']['slug'] = get_slug($input['Entry']['title']);
            $input['Entry']['description'] = '[Generated from Excel File]';
            $input['Entry']['created_by'] = $input['Entry']['modified_by'] = $this->myCreator['id'];
            $this->Entry->create();
            $this->Entry->save($input);
            
            $title = $this->Entry->field('slug');
        }
        else
        {
            $title = $query['Entry']['slug'];
        }
        
        return $query;
    }
    
    function sync_product(&$obj = array(), $myTypeSlug)
    {
/*
        if(isset($obj[ $entity = '' ]))
        {}
*/
        if(isset($obj['barcode']))
        {
            if(empty($obj['barcode']))          $obj['barcode'] = 1;
        }
        
        if(isset($obj['vendor_barcode']))
        {
            if(empty($obj['vendor_barcode']))   $obj['vendor_barcode'] = 1;
        }
        
        if(isset($obj[ $entity = 'product_type' ]))
        {
            if(empty($this->push_general_entry($obj[$entity], $entity)))
            {
                $input = array();
                $input['EntryMeta']['entry_id'] = $this->Entry->id;
                $input['EntryMeta']['key'] = 'form-category';
                if($myTypeSlug == 'diamond')
                {
                    $input['EntryMeta']['value'] = 'Diamond';
                }
                $this->EntryMeta->create();
                $this->EntryMeta->save($input);
            }
        }
        
        if(isset($obj[ $entity = 'warehouse' ]))
        {
            $this->push_general_entry($obj[$entity], $entity);
        }
        
        if( isset($obj[ $entity = 'vendor' ]) )
        {
            $query = $this->push_general_entry($obj[$entity], $entity);
            if($query !== FALSE && empty($query))
            {
                if($myTypeSlug == 'diamond' && !empty($obj['vendor_x']))
                {
                    $input = array();
                    $input['EntryMeta']['entry_id'] = $this->Entry->id;
                    $input['EntryMeta']['key'] = 'form-capital_x';
                    $input['EntryMeta']['value'] = $obj['vendor_x'];
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                }
            }
        }
        
        if(isset($obj[ $entity = 'vendor_invoice_code' ]))
        {
            $query = $this->push_general_entry($obj[$entity], $entity, TRUE, array(
                $obj['vendor'], $obj['warehouse']
            ));
            if($query !== FALSE)
            {
                if(empty($query))
                {
                    // register $_SESSION ...
                    array_push($_SESSION['vendor_invoice_code'], $obj[$entity] );

                    // invoice date ...
                    $invdate = '';
                    if(isset($obj['vendor_invoice_date']))
                    {
                        if(empty($obj['vendor_invoice_date']))
                        {
                            $obj['vendor_invoice_date'] = date('m/d/Y');
                        }
                        $invdate = $obj['vendor_invoice_date'];
                    }
                    else
                    {
                        $invdate = date('m/d/Y');
                    }

                    $input = array();
                    $input['EntryMeta']['entry_id'] = $this->Entry->id;
                    $input['EntryMeta']['key'] = 'form-date';
                    $input['EntryMeta']['value'] = $invdate;
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);

                    // invoice vendor ...
                    $input['EntryMeta']['key'] = 'form-vendor';
                    $input['EntryMeta']['value'] = $obj['vendor'];
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                    
                    // invoice warehouse ...
                    $input['EntryMeta']['key'] = 'form-warehouse';
                    $input['EntryMeta']['value'] = $obj['warehouse'];
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);

                }
                else // origin query existed ...
                {
                    if(isset($obj['vendor_invoice_date']))
                    {
                        $obj['vendor_invoice_date'] = $query['EntryMeta']['date'];
                    }
                    
                    $obj['vendor'] = $query['EntryMeta']['vendor'];
                    
                    if(empty($obj['warehouse']))
                    {
                        $obj['warehouse'] = $query['EntryMeta']['warehouse'];
                    }
                }
            }
        }
        
        
    }
    
    function upload_diamond($value = array(), $mySetting = array())
    {
        if(!is_numeric($value[1]))
        {
            return false; // skip record ...
        }
        
        // grouping value ...
        $dmd = array(
            /* WAN DETAIL INFORMATION */
            'product_type'          => ( empty($value[2]) ? 'D' : strtoupper($value[2]) ),
            'barcode'               => round(floatval($value[3]), 2),
            'sell_barcode'          => round(floatval($value[4]), 2),
            'product_status'        => $value[5],            
            'warehouse'             => $value[7],
            
            /* ITEM DESCRIPTION / SPECIFICATIONS */
            'carat'                 => implode(chr(10), array_filter(array($value[13], $value[14], $value[15], $value[16])) ),
            'gold_carat'            => $value[17],
            'gold_weight'           => round(floatval($value[18]), 2),
            
            /* VENDOR & SUPPLIER DETAIL */
            'item_ref_code'         => implode(chr(10), array_filter(array($value[20], $value[21])) ),
            'vendor'                => strtoupper($value[22]),
            'vendor_item_code'      => $value[23],
            'vendor_invoice_code'   => strtoupper($value[24]),
            'vendor_invoice_date'   => ( excelDateToDate($value[25], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'vendor_status'         => $value[26],
            'vendor_note'           => $value[27],
            'vendor_currency'       => ( strtoupper($value[28]) == 'HKD' ? 'HKD' : 'USD' ),
            'vendor_barcode'        => round(floatval($value[29]), 2),
            'vendor_x'              => round(floatval($value[30]), 2),
            'vendor_usd'            => round(floatval($value[31]), 2),
            'vendor_hkd'            => round(floatval($value[32]), 2),
            
            /* SOLD & RETURN REPORT TO VD */
            'report_date'           => ( excelDateToDate($value[38], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'report_type'           => ( strtoupper($value[39]) == 'RR' ? 'RR' : 'SR' ),
            'temp_report'           => ( excelDateToDate($value[40], $rawDate) ? date( $mySetting['date_format'] , $rawDate ) : $value[40] ),
            'return_date'           => ( excelDateToDate($value[41], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'return_detail'         => $value[42],
            'omzet'                 => $value[45],
            
            /* EVERYTHING ABOUT WAN TRANSACTIONS */
            'wholesaler'            => $value[47],
            'client_x'              => ( is_numeric($value[51]) ? round(floatval($value[51]), 2) : round(floatval($value[48]), 2) ),
            'client'                => $value[50],
            'client_invoice_date'   => ( excelDateToDate($value[53], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'client_invoice_code'   => strtoupper($value[54]),
            'total_sold_price'      => round(floatval( str_ireplace( array('USD','US',',') , '', $value[55] ) ), 2),
            'sold_price_usd'        => round(floatval( str_ireplace( array('USD','US',',') , '', $value[56] ) ), 2),
            'sold_price_rp'         => intval($value[57]),
            'rp_rate'               => intval($value[58]),
            
            /* TYPE OF PAYMENT */
            'payment_credit_card'   => $value[60],
            'payment_cicilan'       => $value[61],
            'payment_cash'          => $value[62],
            'payment_checks'        => implode(chr(10), array_filter(array($value[63], $value[64], $value[65], $value[66])) ),
            
            /* HISTORY OF TRANSACTIONS */
            'prev_sold_price'       => ( is_numeric($value[68]) ? ( $value[68]<1000000 ?'USD ':'Rp ').toMoney($value[68], true , true) : $value[68] ),            
            'prev_barcode'          => ( is_numeric($value[69]) ? ( $value[69]<1000000 ?'USD ':'Rp ').toMoney($value[69], true , true) : $value[69] ),
            'prev_sold_note'        => $value[70],
        );
        
        // synchronize product with other entity ...
        $this->sync_product($dmd, 'diamond');
        
        // push product to database ...
        $this->push_product($dmd, 'diamond', $value[1] , $value[59]);
    }
    
    function upload_jewelry($value = array(), $mySetting = array())
    {
        dpr($value);
        return;
    }
}
