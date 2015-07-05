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
            if(!empty($objValue) && substr($objKey, 0, 5) != 'form-')
            {
                $input['EntryMeta']['key'] = 'form-'.$objKey;
                $input['EntryMeta']['value'] = $objValue;
                $this->EntryMeta->create();
                $this->EntryMeta->save($input);
            }
        }
    }
    
    function push_general_entry(&$title, $entry_type, $complete = FALSE, $terms = array(), $description = NULL)
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
            $input['Entry']['description'] = (empty($description)?'[Generated from Excel File]':$description);
            $input['Entry']['created_by'] = $input['Entry']['modified_by'] = $this->myCreator['id'];
            $this->Entry->create();
            $this->Entry->save($input);
            
            $title = $this->Entry->field('slug');
        }
        else
        {
            $title = $query['Entry']['slug'];
            $this->Entry->id = $query['Entry']['id'];
            if(!empty($description) && stripos($query['Entry']['description'], $description) === FALSE)
            {
                $this->Entry->saveField('description', $query['Entry']['description'].chr(10).$description);
            }
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
            if(empty($obj['barcode']))          $obj['barcode'] = (empty($obj['sell_barcode'])?1:floor($obj['sell_barcode']));
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
            $query = $this->push_general_entry($obj[$entity], $entity, TRUE);
            if($query !== FALSE)
            {
                // get / push capital X
                if($myTypeSlug == 'diamond')
                {
                    if(empty($obj['vendor_x']) && !empty($query['EntryMeta']['capital_x']) )
                    {
                        $obj['vendor_x'] = $query['EntryMeta']['capital_x'];
                    }
                    else if(!empty($obj['vendor_x']) && empty($query['EntryMeta']['capital_x']))
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
        }
        
        if(isset($obj[ $entity = 'vendor_invoice_code' ]))
        {
            $query = $this->push_general_entry($obj[$entity], ($myTypeSlug=='diamond'?'dmd-vendor-invoice':'cor-vendor-invoice') , TRUE, array($obj['vendor'], $obj['warehouse'], (empty($obj['return_date'])?1:0) ), $obj['vendor_note']);
            if($query !== FALSE)
            {
                if(empty($query))
                {
                    // register $_SESSION ...
                    if(empty($obj['vendor_pcs']) && empty($obj['vendor_gr']))
                    {
                        array_push($_SESSION['vendor_invoice_code'], $obj[$entity] );
                    }

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
                    
                    // invoice total Pcs ...
                    $input['EntryMeta']['key'] = 'form-total_pcs';
                    $input['EntryMeta']['value'] = ( empty($obj['vendor_pcs']) ? 1 : $obj['vendor_pcs'] );
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                    
                    // invoice total Item Sent (with same value as total PCS) ...
                    $input['EntryMeta']['key'] = 'form-total_item_sent';
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                    
                    if($myTypeSlug == 'diamond')
                    {
                        // currency ...
                        $input['EntryMeta']['key'] = 'form-currency';
                        $input['EntryMeta']['value'] = $obj['vendor_currency'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                        
                        // HKD rate ...
                        $hkd_rate = 7.75;
                        if(!empty($obj['vendor_usd']) && !empty($obj['vendor_hkd']))
                        {
                            $hkd_rate = round($obj['vendor_hkd'] / $obj['vendor_usd'] , 2);
                        }
                        else // query database rate ...
                        {
                            $query_rate = $this->Entry->meta_details(NULL , 'usd-rate' , NULL , NULL , NULL , NULL , 'HKD');
                            if(!empty($query_rate))
                            {
                                $hkd_rate = $query_rate['EntryMeta']['rate_value'];
                            }
                        }
                        $input['EntryMeta']['key'] = 'form-hkd_rate';
                        $input['EntryMeta']['value'] = $hkd_rate;
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                        
                        // Total Price ...
                        if(empty($obj['vendor_usd']))
                        {
                            $obj['vendor_usd'] = round($obj['vendor_barcode'] * (empty($obj['vendor_x'])?1:$obj['vendor_x']) / ( $obj['vendor_currency'] == 'HKD' ? $hkd_rate : 1 ), 2);
                        }
                        $input['EntryMeta']['key'] = 'form-total_price';
                        $input['EntryMeta']['value'] = $obj['vendor_usd'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                    }
                    else if($myTypeSlug == 'cor-jewelry')
                    {
                        // Total Weight ...
                        $input['EntryMeta']['key'] = 'form-total_weight';
                        $input['EntryMeta']['value'] = (empty($obj['vendor_gr'])?$obj['item_weight']:$obj['vendor_gr']);
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                    }
                    
                    // invoice payment balance (with same value as total price / total weight) ...
                    $input['EntryMeta']['key'] = 'form-payment_balance';
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
                    
                    if(in_array($obj['vendor_invoice_code'], $_SESSION['vendor_invoice_code']) && empty($obj['return_date']))
                    {
                        foreach($query['EntryMeta'] as $tempKey => $tempValue)
                        {
                            if($tempValue['key'] == 'form-total_pcs' || $tempValue['key'] == 'form-total_item_sent')
                            {
                                $this->EntryMeta->id = $tempValue['id'];
                                $this->EntryMeta->saveField('value', $tempValue['value'] + 1);
                            }
                            else if($tempValue['key'] == 'form-total_price' || $myTypeSlug == 'diamond' && $tempValue['key'] == 'form-payment_balance')
                            {
                                if(empty($obj['vendor_usd']))
                                {
                                    $obj['vendor_usd'] = round($obj['vendor_barcode'] * (empty($obj['vendor_x'])?1:$obj['vendor_x']) / ( $obj['vendor_currency'] == 'HKD' ? $query['EntryMeta']['hkd_rate'] : 1 ), 2);
                                }

                                $this->EntryMeta->id = $tempValue['id'];
                                $this->EntryMeta->saveField('value', $tempValue['value'] + $obj['vendor_usd']);
                            }
                            else if($tempValue['key'] == 'form-total_weight' || $myTypeSlug == 'cor-jewelry' && $tempValue['key'] == 'form-payment_balance')
                            {
                                $this->EntryMeta->id = $tempValue['id'];
                                $this->EntryMeta->saveField('value', $tempValue['value'] + $obj['item_weight']);
                            }
                        }
                    }
                }
            }
        }
        
        if( isset($obj[ $entity = 'wholesaler' ]) )
        {
            $query = $this->push_general_entry($obj[$entity], 'client', TRUE);
            if($query !== FALSE)
            {
                $input = array();
                $input['EntryMeta']['entry_id'] = $this->Entry->id;
                    
                // kode pelanggan ...
                if(!empty($obj['form-kode_pelanggan']) && empty($query['EntryMeta']['kode_pelanggan']))
                {
                    $input['EntryMeta']['key'] = 'form-kode_pelanggan';
                    $input['EntryMeta']['value'] = $obj['form-kode_pelanggan'];
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                }
                
                // pernah ambil dari WH mana saja ...
                if(!empty($obj['warehouse']))
                {
                    if(empty($query['EntryMeta']['warehouse']))
                    {
                        $input['EntryMeta']['key'] = 'form-warehouse';
                        $input['EntryMeta']['value'] = $obj['warehouse'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                    }
                    else // update record ...
                    {
                        // check WH already existed or not ...
                        if(strpos( '|'.$query['EntryMeta']['warehouse'].'|', '|'.$obj['warehouse'].'|' ) === FALSE)
                        {
                            $this->EntryMeta->id = $query['EntryMeta'][array_search('form-warehouse', array_column($query['EntryMeta'], 'key'))]['id'];
                            $this->EntryMeta->saveField('value', $query['EntryMeta']['warehouse'].'|'.$obj['warehouse'] );
                        }
                    }
                }
                
                // client X
                if($myTypeSlug == 'diamond')
                {
                    if(empty($query['EntryMeta']['diamond_sell_x']))
                    {
                        $wholesale_x = ( empty($obj['form-diamond_sell_x']) ? $obj['client_x'] : $obj['form-diamond_sell_x'] );
                        if(!empty($wholesale_x))
                        {
                            $input['EntryMeta']['key'] = 'form-diamond_sell_x';
                            $input['EntryMeta']['value'] = $wholesale_x;
                            $this->EntryMeta->create();
                            $this->EntryMeta->save($input);
                        }
                    }
                }
                
                if(empty($query))
                {
                    // kategori pelanggan ...
                    $input['EntryMeta']['key'] = 'form-kategori';
                    $input['EntryMeta']['value'] = 'Wholesaler';
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                }
            }
        }
        
        if(isset($obj[ $entity = 'client_x' ]))
        {
            if(empty($obj[$entity]))    $obj[$entity] = $obj['form-diamond_sell_x'];
        }
        
        if(isset($obj[ $entity = 'client' ]))
        {
            $query = $this->push_general_entry($obj[$entity], $entity, TRUE);
            if($query !== FALSE)
            {
                $input = array();
                $input['EntryMeta']['entry_id'] = $this->Entry->id;
                
                // pernah ambil dari WH mana saja ...
                if(!empty($obj['warehouse']))
                {
                    if(empty($query['EntryMeta']['warehouse']))
                    {
                        $input['EntryMeta']['key'] = 'form-warehouse';
                        $input['EntryMeta']['value'] = $obj['warehouse'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                    }
                    else // update record ...
                    {
                        // check WH already existed or not ...
                        if(strpos( '|'.$query['EntryMeta']['warehouse'].'|', '|'.$obj['warehouse'].'|' ) === FALSE)
                        {
                            $this->EntryMeta->id = $query['EntryMeta'][array_search('form-warehouse', array_column($query['EntryMeta'], 'key'))]['id'];
                            $this->EntryMeta->saveField('value', $query['EntryMeta']['warehouse'].'|'.$obj['warehouse'] );
                        }
                    }
                }
                
                // client X
                if($myTypeSlug == 'diamond')
                {
                    if(empty($query['EntryMeta']['diamond_sell_x']) && !empty($obj['client_x']))
                    {
                        $input['EntryMeta']['key'] = 'form-diamond_sell_x';
                        $input['EntryMeta']['value'] = $obj['client_x'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                    }
                    else if(!empty($query['EntryMeta']['diamond_sell_x']) && empty($obj['client_x']))
                    {
                        $obj['client_x'] = $query['EntryMeta']['diamond_sell_x'];
                    }
                }
                
                // wholesaler ...
                if(empty($query['EntryMeta']['wholesaler']) && !empty($obj['wholesaler']))
                {
                    $input['EntryMeta']['key'] = 'form-wholesaler';
                    $input['EntryMeta']['value'] = $obj['wholesaler'];
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                }
                else if(!empty($query['EntryMeta']['wholesaler']) && empty($obj['wholesaler']))
                {
                    $obj['wholesaler'] = $query['EntryMeta']['wholesaler'];
                }
                
                if(empty($query))
                {
                    // kategori pelanggan ...
                    $input['EntryMeta']['key'] = 'form-kategori';
                    $input['EntryMeta']['value'] = (empty($obj['wholesaler'])?'End User':'Retailer');
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                }
            }
            else // ambil wholesaler sbg client ...
            {
                $obj['client'] = $obj['wholesaler'];
                unset($obj['wholesaler']);
            }
        }
        
        if(isset($obj[ $entity = 'client_invoice_code' ]))
        {
            $query = $this->push_general_entry($obj[$entity], ($myTypeSlug=='diamond'?'dmd-client-invoice':'cor-client-invoice') , TRUE, array($obj['client'], $obj['warehouse'] ), $obj['form-description']);
            if($query !== FALSE)
            {
                $input = array();
                $input['EntryMeta']['entry_id'] = $this->Entry->id;
                
                // invoice wholesaler ...
                if(empty($query['EntryMeta']['wholesaler']))
                {
                    if(!empty($obj['wholesaler']))
                    {
                        $input['EntryMeta']['key'] = 'form-wholesaler';
                        $input['EntryMeta']['value'] = $obj['wholesaler'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                    }
                }
                else
                {
                    $obj['wholesaler'] = $query['EntryMeta']['wholesaler'];
                }
                
                if(empty($query))
                {
                    // register $_SESSION ...
                    if(empty($obj['client_invoice_pcs']) && empty($obj['client_invoice_sold_24k']))
                    {
                        array_push($_SESSION['client_invoice_code'], $obj[$entity] );
                    }

                    // invoice date ...
                    $invdate = '';
                    if(isset($obj['client_invoice_date']))
                    {
                        if(empty($obj['client_invoice_date']))
                        {
                            $obj['client_invoice_date'] = date('m/d/Y');
                        }
                        $invdate = $obj['client_invoice_date'];
                    }
                    else
                    {
                        $invdate = date('m/d/Y');
                    }
                    
                    $input['EntryMeta']['key'] = 'form-date';
                    $input['EntryMeta']['value'] = $invdate;
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);

                    // invoice client ...
                    $input['EntryMeta']['key'] = 'form-client';
                    $input['EntryMeta']['value'] = $obj['client'];
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                    
                    // invoice sale venue ...
                    $input['EntryMeta']['key'] = 'form-sale_venue';
                    $input['EntryMeta']['value'] = 'Warehouse';
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                    
                    // invoice warehouse ...
                    $input['EntryMeta']['key'] = 'form-warehouse';
                    $input['EntryMeta']['value'] = $obj['warehouse'];
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                    
                    // invoice total Pcs ...
                    $input['EntryMeta']['key'] = 'form-total_pcs';
                    $input['EntryMeta']['value'] = ( empty($obj['client_invoice_pcs']) ? 1 : $obj['client_invoice_pcs'] );
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                    
                    // invoice total Item Sent (with same value as total PCS) ...
                    $input['EntryMeta']['key'] = 'form-total_item_sent';
                    $this->EntryMeta->create();
                    $this->EntryMeta->save($input);
                    
                    if($myTypeSlug == 'diamond')
                    {
                        // IDR rate ...
                        if(empty($obj['rp_rate']))
                        {
                            // query database rate ...
                            $query_rate = $this->Entry->meta_details(NULL , 'usd-rate' , NULL , NULL , NULL , NULL , 'IDR');
                            $obj['rp_rate'] = ( empty($query_rate) ? 12800 : $query_rate['EntryMeta']['rate_value'] );
                        }
                        $input['EntryMeta']['key'] = 'form-rp_rate';
                        $input['EntryMeta']['value'] = $obj['rp_rate'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                        
                        // Total Price ...
                        if(empty($obj['total_sold_price']))
                        {
                            $obj['total_sold_price'] = ( empty($obj['sell_barcode']) ? $obj['barcode'] : $obj['sell_barcode'] ) * (empty($obj['client_x'])?1:$obj['client_x']);
                        }
                        $input['EntryMeta']['key'] = 'form-total_price';
                        $input['EntryMeta']['value'] = $obj['total_sold_price'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                        
                        // invoice payment balance (CUSTOM CASE: even if 0, record must be created) ...
                        $input['EntryMeta']['key'] = 'form-payment_balance';
                        $input['EntryMeta']['value'] = $obj['sold_price_usd'] + round($obj['sold_price_rp'] / $obj['rp_rate'], 2);
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                    }
                }
                else // origin query existed ...
                {
                    if(isset($obj['client_invoice_date']))
                    {
                        $obj['client_invoice_date'] = $query['EntryMeta']['date'];
                    }
                    
                    $obj['client'] = $query['EntryMeta']['client'];
                    
                    if(empty($obj['warehouse']))
                    {
                        $obj['warehouse'] = $query['EntryMeta']['warehouse'];
                    }
                    
                    if(isset($obj['rp_rate']) && empty($obj['rp_rate']))
                    {
                        $obj['rp_rate'] = $query['EntryMeta']['rp_rate'];
                    }
                    
                    if(in_array($obj['client_invoice_code'], $_SESSION['client_invoice_code']))
                    {
                        foreach($query['EntryMeta'] as $tempKey => $tempValue)
                        {
                            if($tempValue['key'] == 'form-total_pcs' || $tempValue['key'] == 'form-total_item_sent')
                            {
                                $this->EntryMeta->id = $tempValue['id'];
                                $this->EntryMeta->saveField('value', $tempValue['value'] + 1);
                            }
                            else if($tempValue['key'] == 'form-total_price')
                            {
                                if(empty($obj['total_sold_price']))
                                {
                                    $obj['total_sold_price'] = ( empty($obj['sell_barcode']) ? $obj['barcode'] : $obj['sell_barcode'] ) * (empty($obj['client_x'])?1:$obj['client_x']);
                                }

                                $this->EntryMeta->id = $tempValue['id'];
                                $this->EntryMeta->saveField('value', $tempValue['value'] + $obj['total_sold_price']);
                            }
                            else if($tempValue['key'] == 'form-payment_balance')
                            {
                                if($myTypeSlug == 'diamond')
                                {
                                    $paybal = $obj['sold_price_usd'] + round($obj['sold_price_rp'] / $obj['rp_rate'], 2);
                                    if(!empty($paybal))
                                    {
                                        $this->EntryMeta->id = $tempValue['id'];
                                        $this->EntryMeta->saveField('value', $tempValue['value'] + $paybal);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } // end of entity client_invoice_code ...
    }
    
    function upload_diamond($value = array(), $mySetting = array())
    {
        $test_title = intval($value[1]);
        if(empty($test_title))
        {
            return false; // skip record ...
        }
        
        // renew title !!
        $value[1] = substr( $test_title + 1000000 , 1);
        
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
            'form-diamond_sell_x'   => round(floatval($value[48]), 2),
            'form-kode_pelanggan'   => strtoupper($value[49]),
            'client'                => $value[50],
            'client_x'              => round(floatval($value[51]), 2),
            'client_invoice_date'   => ( excelDateToDate($value[53], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'client_invoice_code'   => strtoupper($value[54]),
            'total_sold_price'      => round(floatval( str_ireplace( array('USD','US',',') , '', $value[55] ) ), 2),
            'sold_price_usd'        => round(floatval( str_ireplace( array('USD','US',',') , '', $value[56] ) ), 2),
            'sold_price_rp'         => intval($value[57]),
            'rp_rate'               => intval($value[58]),
            'form-description'      => $value[59], // client outstanding ...
            
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
        $this->push_product($dmd, 'diamond', $value[1] , $dmd['form-description']);
    }
    
    function upload_jewelry($value = array(), $mySetting = array())
    {
        dpr($value);
        return;
    }
}
