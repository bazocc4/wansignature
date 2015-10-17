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
			'conditions' => array('EntryMeta.key' => 'image_'.$type),
            'recursive' => -1
		));
        
        $imgTypeList = array_column(array_column($imgReason, 'EntryMeta'), 'value', 'entry_id');
		$imgTypeList[0] = 'jpg';
        
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
        return array_column(array_column($query, 'Entry'), 'title', 'slug');
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
        else // update Entry description if any...
        {
            if(!empty($description) && stripos($query['Entry']['description'], $description) === FALSE)
            {
                $this->Entry->id = $query['Entry']['id'];
                $this->Entry->saveField('description', $query['Entry']['description'].chr(10).$description);
            }
        }
        
        // PROCESS ENTRYMETA ...
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
                    if($qValue['value'] != $obj[$shortkey])
                    {
                        $this->EntryMeta->id = $qValue['id'];
                        $this->EntryMeta->saveField('value', $obj[$shortkey]);
                    }
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
        
        $entry_type = get_slug($entry_type);
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
            if(!empty($description))    $input['Entry']['description'] = $description;
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
        
        if(isset($obj['sell_barcode']))
        {
            if(empty($obj['sell_barcode']) && $obj['barcode'] > 1)
            {
                $obj['sell_barcode'] = ceil($obj['barcode']);
            }
        }
        
        if(isset($obj['vendor_barcode']))
        {
            if(empty($obj['vendor_barcode']))   $obj['vendor_barcode'] = 1;
        }
        
        if(isset($obj[ $entity = 'item_weight' ]))
        {
            if(empty($obj[$entity]))            $obj[$entity] = 1;
        }
        
        $product_type_cat = '';
        if(isset($obj[ $entity = 'product_type' ]))
        {
            $query = $this->push_general_entry($obj[$entity], $entity, TRUE);
            if(empty($query))
            {
                $input = array();
                $input['EntryMeta']['entry_id'] = $this->Entry->id;
                $input['EntryMeta']['key'] = 'form-category';
                if($myTypeSlug == 'diamond')
                {
                    $input['EntryMeta']['value'] = 'Diamond';
                }
                else // cor-jewelry ...
                {
                    if(strpos($obj[$entity], 'italy') !== FALSE)
                    {
                        $input['EntryMeta']['value'] = 'Italy (125%)';
                        
                        // add kode type ...
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $input['EntryMeta']['entry_id'],
                            'key'       => 'form-kode_type',
                            'value'     => '9', // default type code ...
                        )));
                    }
                    else if(strpos($obj[$entity], 'korea') !== FALSE)
                    {
                        $input['EntryMeta']['value'] = 'Korea (100%)';
                        
                        // add kode type ...
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $input['EntryMeta']['entry_id'],
                            'key'       => 'form-kode_type',
                            'value'     => '9', // default type code ...
                        )));
                    }
                    else if(strpos($obj[$entity], '3d') !== FALSE)
                    {
                        $input['EntryMeta']['value'] = '999 3D (115%)';
                    }
                    else // 999 simple ...
                    {
                        $input['EntryMeta']['value'] = '999 Simple (110%)';
                    }
                }
                $this->EntryMeta->create();
                $this->EntryMeta->save($input);
                
                $query['EntryMeta']['category'] = $input['EntryMeta']['value'];
            }
            
            // for later use ...
            $product_type_cat = $query['EntryMeta']['category'];
        }
        
        if(isset($obj[ $entity = 'product_brand' ]))
        {
            $query = $this->push_general_entry($obj[$entity], $entity);
            if(empty($query))
            {
                $input = array();
                $input['EntryMeta']['entry_id'] = $this->Entry->id;
                $input['EntryMeta']['key'] = 'form-kode_brand';
                $input['EntryMeta']['value'] = '9'; // default brand code ...
                $this->EntryMeta->create();
                $this->EntryMeta->save($input);
            }
        }
        
        if(isset($obj[ $entity = 'product_color' ]))
        {
            $query = $this->push_general_entry($obj[$entity], $entity);
            if(empty($query))
            {
                $input = array();
                $input['EntryMeta']['entry_id'] = $this->Entry->id;
                $input['EntryMeta']['key'] = 'form-kode_color';
                $input['EntryMeta']['value'] = '9'; // default color code ...
                $this->EntryMeta->create();
                $this->EntryMeta->save($input);
            }
        }
        
        if(isset($obj[ $entity = 'warehouse' ]))
        {
            // update product_status to STOCK if have same value as warehouse ...
            if( !empty($obj[$entity]) && (empty($obj['product_status']) || strtolower($obj['product_status']) == strtolower($obj[$entity])) )
            {
                $obj['product_status'] = 'STOCK';
            }
            
            $this->push_general_entry($obj[$entity], $entity);
        }
        
        if( isset($obj[ $entity = 'vendor' ]) )
        {
            $query = $this->push_general_entry($obj[$entity], $entity, TRUE);
            if($query !== FALSE && $myTypeSlug == 'diamond')
            {
                // get / push capital X
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
                else // cor-jewelry ...
                {
                    $x_label = intval(substr($product_type_cat, strrpos($product_type_cat, '(') + 1 ));
                    
                    if(empty($query['EntryMeta']['x_'.$x_label]) && !empty($obj['client_x']) )
                    {
                        $input['EntryMeta']['key'] = 'form-x_'.$x_label;
                        $input['EntryMeta']['value'] = $obj['client_x'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
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
                else // cor-jewelry ...
                {
                    $x_label = intval(substr($product_type_cat, strrpos($product_type_cat, '(') + 1 ));
                    
                    if(empty($query['EntryMeta']['x_'.$x_label]) && !empty($obj['client_x']) )
                    {
                        $input['EntryMeta']['key'] = 'form-x_'.$x_label;
                        $input['EntryMeta']['value'] = $obj['client_x'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                    }
                    else if(!empty($query['EntryMeta']['x_'.$x_label]) && empty($obj['client_x']) )
                    {
                        $obj['client_x'] = $query['EntryMeta']['x_'.$x_label];
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
            $query = $this->push_general_entry($obj[$entity], ($myTypeSlug=='diamond'?'dmd-client-invoice':'cor-client-invoice') , TRUE, array($obj['client'], $obj['warehouse'] ), ($myTypeSlug=='diamond'? $obj['form-description'] : '' ) );
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
                            $obj['rp_rate'] = ( empty($query_rate) ? 13000 : $query_rate['EntryMeta']['rate_value'] );
                        }
                        $input['EntryMeta']['key'] = 'form-rp_rate';
                        $input['EntryMeta']['value'] = $obj['rp_rate'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                        
                        // Total Price ...
                        if(empty($obj['total_sold_price']))
                        {
                            $obj['total_sold_price'] = round( ( empty($obj['sell_barcode']) ? $obj['barcode'] : $obj['sell_barcode'] ) * (empty($obj['client_x'])?1:$obj['client_x']), 2);
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
                    else // cor-jewelry ...
                    {
                        if(empty($obj['gold_price']))
                        {
                            // query database rate ...
                            $db_idr = $this->Entry->meta_details(NULL , 'usd-rate' , NULL , NULL , NULL , NULL , 'IDR');
                            $db_gold = $this->Entry->meta_details(NULL , 'usd-rate' , NULL , NULL , NULL , NULL , 'gold bar%');
                            
                            if(empty($db_idr) || empty($db_gold))
                            {
                                $obj['gold_price'] = 500000;
                            }
                            else
                            {
                                $obj['gold_price'] = round($db_idr['EntryMeta']['rate_value'] / $db_gold['EntryMeta']['rate_value']);
                            }
                        }
                        $input['EntryMeta']['key'] = 'form-gold_price';
                        $input['EntryMeta']['value'] = $obj['gold_price'];
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                        
                        // Sold X
                        foreach(array(
                            'form-sold_125', 'form-x_125', 'form-sold_100', 'form-x_100', 'form-sold_110', 'form-x_110', 'form-sold_115', 'form-x_115', 'form-disc_adjustment'
                        ) as $sold_key => $sold_value)
                        {
                            if(!empty($obj[$sold_value]))
                            {
                                $input['EntryMeta']['key'] = $sold_value;
                                $input['EntryMeta']['value'] = $obj[$sold_value];
                                $this->EntryMeta->create();
                                $this->EntryMeta->save($input);
                            }
                        }
                        
                        // Total Weight ...
                        $input['EntryMeta']['key'] = 'form-total_weight';                        
                        if(!empty($obj['client_invoice_sold_24k']))
                        {
                            $input['EntryMeta']['value'] = $obj['client_invoice_sold_24k'];
                        }
                        else
                        {
                            if(empty($obj['client_x']))
                            {
                                $obj['client_x'] = intval(substr($product_type_cat, strrpos($product_type_cat, '(') + 1 )) / 100;
                            }
                            
                            $input['EntryMeta']['value'] = round($obj['item_weight'] * $obj['client_x'], 2);
                        }
                        $this->EntryMeta->create();
                        $this->EntryMeta->save($input);
                        
                        // invoice payment balance ...
                        $input['EntryMeta']['key'] = 'form-payment_balance';
                        if(!empty($obj['payment_balance']) && !empty($obj['client_invoice_sold_24k']) )
                        {
                            $input['EntryMeta']['value'] = $obj['payment_balance'];
                        }
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
                    if(!empty($query['EntryMeta']['wholesaler']))
                    {
                        $obj['wholesaler'] = $query['EntryMeta']['wholesaler'];
                    }
                    
                    if(empty($obj['warehouse']))
                    {
                        $obj['warehouse'] = $query['EntryMeta']['warehouse'];
                    }
                    
                    if(isset($obj['rp_rate']) && empty($obj['rp_rate']))
                    {
                        $obj['rp_rate'] = $query['EntryMeta']['rp_rate'];
                    }
                    
                    if(isset($obj['gold_price']) && empty($obj['gold_price']))
                    {
                        $obj['gold_price'] = $query['EntryMeta']['gold_price'];
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
                                    $obj['total_sold_price'] = round( ( empty($obj['sell_barcode']) ? $obj['barcode'] : $obj['sell_barcode'] ) * (empty($obj['client_x'])?1:$obj['client_x']), 2);
                                }

                                $this->EntryMeta->id = $tempValue['id'];
                                $this->EntryMeta->saveField('value', $tempValue['value'] + $obj['total_sold_price']);
                            }
                            else if($tempValue['key'] == 'form-payment_balance' && $myTypeSlug == 'diamond')
                            {
                                $paybal = $obj['sold_price_usd'] + round($obj['sold_price_rp'] / $obj['rp_rate'], 2);
                                if(!empty($paybal))
                                {
                                    $this->EntryMeta->id = $tempValue['id'];
                                    $this->EntryMeta->saveField('value', $tempValue['value'] + $paybal);
                                }
                            }
                            else if($tempValue['key'] == 'form-total_weight' || $tempValue['key'] == 'form-payment_balance' && $myTypeSlug == 'cor-jewelry')
                            {
                                if(empty($obj['client_x']))
                                {
                                    $obj['client_x'] = intval(substr($product_type_cat, strrpos($product_type_cat, '(') + 1 )) / 100;
                                }
                                $this->EntryMeta->id = $tempValue['id'];
                                $this->EntryMeta->saveField('value', $tempValue['value'] + round($obj['item_weight'] * $obj['client_x'], 2) );
                            }
                        }
                    }
                }
            }
        } // end of entity client_invoice_code ...
    }
    
    function upload_jewelry($value = array(), $mySetting = array())
    {
        // check product_type and item_weight ?
        if(empty($value[3]) || !is_numeric($value[1+5]) )
        {
            return false; // skip record ...
        }
        
        $test_title = trim($value[2], 'G');
        if(!is_numeric($test_title))
        {
            // auto generate product code !!
            $value[2] = $this->Entry->get_serial_title('cor-jewelry', NULL, array(
                array('key' => 'form-product_brand' , 'value' => $value[4] ),   // (I)
                array('key' => 'form-product_type' , 'value' => $value[3] ),    // (II)
                array('key' => 'form-product_color' , 'value' => $value[5] ),   // (III)
            ), FALSE );
        }
        
        // grouping value ...
        $cor = array(
            /* COR DETAIL INFORMATION */
            'product_type'          => $value[3],
            'product_brand'         => $value[4],
            'product_color'         => $value[5], // NEW FIELD !!!
            'item_weight'           => round(floatval($value[1+5]), 2),
            'item_size'             => intval($value[1+7]),
            
            /* VENDOR INFO */
            'vendor'                => strtoupper($value[1+8]),
            'vendor_invoice_code'   => strtoupper($value[1+11]),
            'vendor_pcs'            => intval($value[1+12]),
            'vendor_gr'             => round(floatval($value[1+13]), 2),
            
            /* STATUS BARANG */
            'warehouse'             => $value[1+14],
            'stock_date'            => ( excelDateToDate($value[1+15], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'product_status'        => $value[1+16],
            
            /* CLIENT INFO */
            'wholesaler'                => $value[1+17],
            'client'                    => $value[1+18],
            
            /* SOLD INVOICE TO CLIENT */
            'client_invoice_date'   => ( excelDateToDate($value[1+22], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'client_invoice_code'   => strtoupper($value[1+23]),
            'client_invoice_pcs'    => intval($value[1+24]),
            
            'form-sold_125'         => round(floatval($value[1+25]), 2), // NEW FIELD !!!
            'form-x_125'            => round(floatval($value[1+26]), 2), // NEW FIELD !!!            
            'form-sold_100'         => round(floatval($value[1+27]), 2), // NEW FIELD !!!
            'form-x_100'            => round(floatval($value[1+28]), 2), // NEW FIELD !!!
            
            'form-sold_110'         => round(floatval($value[5+25]), 2),
            'form-x_110'            => round(floatval($value[5+26]), 2),            
            'form-sold_115'         => round(floatval($value[5+27]), 2),
            'form-x_115'            => round(floatval($value[5+28]), 2),
            'form-disc_adjustment'  => round(floatval($value[5+29]), 2),
            'client_invoice_sold_24k' => round(floatval($value[5+30]), 2),
            'gold_price'            => intval(str_replace(array(',', '.'), '' , trim($value[5+32], 'IDR'))),
            
            /* TYPE OF PAYMENT */
            'payment_ct_ld'         => $value[5+33],
            'payment_rosok'         => $value[5+34],
            'payment_checks'        => $value[5+35],
            'payment_cash'          => $value[5+36],
            'payment_credit_card'   => $value[5+37],
            'payment_return_goods'  => $value[5+38],
            'payment_balance'       => round(floatval($value[5+ (empty(floatval($value[5+39])) ? 31 : 39) ]), 2),
            
            /* HISTORY OF TRANSACTIONS */
            'transaction_history'   => $value[5+41],
            'form-description'      => $value[5+42], // keterangan / detail barang ...
        );
        
        // calculate client_x ...
        $tempX = intval($value[1+9]);
        $cor['client_x'] = $cor['form-x_'.$tempX];
        if(empty($cor['client_x']))     $cor['client_x'] = $tempX / 100;
        
        // adjust product_type title ...
        if(stripos($cor['product_type'], '3D') === FALSE && $tempX == 115)
        {
            $cor['product_type'] .= ' 3D';
        }
        
        // adjust Invoice Client Total Weight ...
        $cor['client_invoice_sold_24k'] -= $cor['form-disc_adjustment'];
        
        // synchronize product with other entity ...
        $this->sync_product($cor, 'cor-jewelry');
        
        // push product to database ...
        $this->push_product($cor, 'cor-jewelry', $value[2] , $cor['form-description']);
    }
    
    function upload_diamond($value = array(), $mySetting = array())
    {
        // check product_type?
        if( ! (substr($value[2], 0, 1) == 'D' && strlen($value[2]) <= 3) )
        {
            return false; // skip record ...
        }
        
        $test_title = intval($value[1]);
        // renew title !!
        $value[1] = (empty($test_title)? $this->Entry->get_serial_title('diamond') : substr( $test_title + 1000000 , 1) );
        
        // grouping value ...
        $dmd = array(
            /* WAN DETAIL INFORMATION */
            'product_type'          => strtoupper($value[2]),
            'barcode'               => round(floatval($value[3]), 2),
            'sell_barcode'          => round(floatval($value[4]), 2),
            'product_status'        => $value[5],            
            'warehouse'             => $value[7],
            
            /* ITEM DESCRIPTION / SPECIFICATIONS */
            'carat'                 => implode(chr(10), array_filter(array($value[13], $value[14], $value[15], $value[16])) ),
            'gold_carat'            => $value[17],
            'gold_weight'           => round(floatval($value[18]), 2),
            'item_ref_code'         => implode(chr(10), array_filter(array($value[20], $value[21])) ),
            
            /* VENDOR & SUPPLIER DETAIL */
            'vendor'                => strtoupper($value[22]),
            'vendor_item_code'      => $value[23],
            'vendor_invoice_code'   => strtoupper($value[24]),
            'vendor_invoice_date'   => ( excelDateToDate($value[25], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'vendor_note'           => ( !empty($value[26]) && !empty($value[27]) ? $value[26].' ('.$value[27].')' : $value[26].$value[27] ),
            'vendor_currency'       => ( strtoupper($value[28]) == 'HKD' ? 'HKD' : 'USD' ),
            'vendor_barcode'        => round(floatval($value[29]), 2),
            'vendor_x'              => round(floatval($value[30]), 2),
            'vendor_usd'            => round(floatval($value[31]), 2),
            'vendor_hkd'            => round(floatval($value[32]), 2),
            
            /* SOLD & RETURN REPORT TO VD */
            'report_date'           => ( excelDateToDate($value[38], $rawDate) ? date('m/d/Y', $rawDate ) : '' ),
            'report_type'           => ( strtoupper($value[39]) == 'RR' || strtoupper($value[39]) == 'SR' ? strtoupper($value[39]) : 'Consignment' ),
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
        
        // update report_type !!
        if(!empty($dmd['return_date'])) $dmd['report_type'] = 'RR';
        
        // synchronize product with other entity ...
        $this->sync_product($dmd, 'diamond');
        
        // push product to database ...
        $this->push_product($dmd, 'diamond', $value[1] , $dmd['form-description']);
    }
    
    function update_surat_jalan($data, $myEntry = array())
    {
        foreach(array('diamond', 'cor_jewelry', 'logistic') as $key => $value)
        {
            $data['EntryMeta'][$value] = array_unique(array_filter($data['EntryMeta'][$value]));
        }
        
        // ========================== >>
        // product ...
        // ========================== >>
        $pushdata = array();
        if(!empty($data['EntryMeta']['diamond']) || !empty($data['EntryMeta']['cor_jewelry']))
        {
            if($data['Entry'][3]['value'] == 2) // on repair at WAN ...
            {
                $pushdata['form-product_status'] = 'REPARASI #'.$data['Entry'][0]['value'];
            }
            else if($data['Entry'][3]['value'] == 0) // On Process ...
            {
                $pushdata['form-product_status'] = 'OTW #'.$data['Entry'][0]['value'];
            }

            // destination ...
            if(!empty($data['EntryMeta']['warehouse_destination']))
            {
                $pushdata['form-warehouse'] = $data['EntryMeta']['warehouse_destination'];
                $pushdata['form-exhibition'] = '';

                if($data['Entry'][3]['value'] == 1) // Accepted ...
                {
                    $pushdata['form-product_status'] = 'STOCK';
                    $pushdata['form-stock_date'] = $data['EntryMeta']['date'];
                }
            }
            else if(!empty($data['EntryMeta']['exhibition_destination']))
            {
                $pushdata['form-exhibition'] = $data['EntryMeta']['exhibition_destination'];
                if($data['Entry'][3]['value'] == 1) // Accepted ...
                {
                    $pushdata['form-product_status'] = 'EXHIBITION';
                }
            }
            
            // RETURN AS PAYMENT status variable ...
            $middle_status = '';
            if(strpos($data['EntryMeta']['delivery_type'], 'Payment') !== FALSE)
            {
                $middle_status = ' AS PAYMENT';
            }
            
            // vendor ...
            if(!empty($data['EntryMeta']['vendor']))
            {
                $pushdata['form-vendor'] = $data['EntryMeta']['vendor'];
                if(!empty($data['EntryMeta']['warehouse_origin']))
                {
                    if($data['Entry'][3]['value'] == 1) // Accepted ...
                    {
                        $pushdata['form-product_status'] = 'RETURN'.$middle_status.' #'.$data['Entry'][0]['value'];
                        $pushdata['form-report_type'] = 'RR';
                        $pushdata['form-return_date'] = $data['EntryMeta']['date'];
                        if(!empty($data['Entry'][1]['value']))
                        {
                            $pushdata['form-return_detail'] = $data['Entry'][1]['value'];
                        }
                    }
                }
                else if(!empty($data['EntryMeta']['warehouse_destination']))
                {
                    $pushdata['form-report_type'] = 'Consignment';
                }
            }

            // client ...
            if(!empty($data['EntryMeta']['client']))
            {
                $pushdata['form-client'] = $data['EntryMeta']['client'];
                if($data['Entry'][3]['value'] == 1) // Accepted ...
                {
                    if(!empty($data['EntryMeta']['warehouse_origin']) || !empty($data['EntryMeta']['exhibition_origin'])) 
                    {
                        $pushdata['form-product_status'] = 'CONSIGNMENT'; // will be SOLD if has been paid ...
                    }
                    else if(!empty($data['EntryMeta']['warehouse_destination']))
                    {
                        $pushdata['form-product_status'] = 'STOCK (CLIENT RETURN'.$middle_status.' #'.$data['Entry'][0]['value'].')';
                    }
                }
            }
            
            // salesman ...
            if(!empty($data['EntryMeta']['salesman']))
            {
                $pushdata['form-salesman'] = $data['EntryMeta']['salesman'];
                if(!empty($data['EntryMeta']['warehouse_origin']) || !empty($data['EntryMeta']['exhibition_origin']))
                {
                    $pushdata['form-product_status'] = 'KLLG #'.$data['Entry'][0]['value'];
                }
            }
            
            $pushdata = array_map('trim', $pushdata);
        }
        
        // push to database product ...
        $invoice_price = 0; // for invoice update later ...
        foreach(array('diamond', 'cor_jewelry') as $typekey => $typevalue)
        {
            if(!empty($data['EntryMeta'][$typevalue]))
            {
                $query = $this->Entry->findAllByEntryTypeAndSlug(get_slug($typevalue), $data['EntryMeta'][$typevalue] );
                foreach($query as $key => $value)
                {
                    $dbkey_haystack = array_column($value['EntryMeta'], 'key');
                    foreach($pushdata as $subkey => $subvalue)
                    {
                        $dbkey = array_search($subkey, $dbkey_haystack);
                        if(empty($subvalue))
                        {
                            if($dbkey !== FALSE)
                            {
                                $this->EntryMeta->delete($value['EntryMeta'][$dbkey]['id']);
                            }
                        }
                        else
                        {
                            if($dbkey === FALSE)
                            {
                                $this->EntryMeta->create();
                                $this->EntryMeta->save(array('EntryMeta' => array(
                                    'entry_id'  => $value['Entry']['id'],
                                    'key'       => $subkey,
                                    'value'     => $subvalue
                                )));
                            }
                            else if($value['EntryMeta'][$dbkey]['value'] != $subvalue)
                            {
                                $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                                if($subkey == 'form-return_detail')
                                {
                                    $subvalue = $value['EntryMeta'][$dbkey]['value'].chr(10).$subvalue;
                                }
                                else if($subkey == 'form-product_status' && $subvalue == 'CONSIGNMENT' && stripos($value['EntryMeta'][$dbkey]['value'], 'sold') !== FALSE || $subkey == 'form-report_type' && $subvalue == 'Consignment')
                                {
                                    $subvalue = $value['EntryMeta'][$dbkey]['value'];
                                }
                                $this->EntryMeta->saveField('value', $subvalue );
                            }
                        }
                    }
                    
                    // search for product price ...
                    if($data['Entry'][3]['value'] == 1) // Accepted ...
                    {
                        if(!empty($data['EntryMeta']['warehouse_origin']))
                        {
                            if(!empty($data['EntryMeta']['dmd_vendor_invoice']))
                            {
                                $dbkey = array_search('form-vendor_usd', $dbkey_haystack);
                                if($dbkey !== FALSE)    $invoice_price += $value['EntryMeta'][$dbkey]['value'];
                            }
                            else if(!empty($data['EntryMeta']['cor_vendor_invoice']))
                            {
                                $dbkey = array_search('form-item_weight', $dbkey_haystack);
                                if($dbkey !== FALSE)    $invoice_price += $value['EntryMeta'][$dbkey]['value'];
                            }
                        }
                        else if(!empty($data['EntryMeta']['warehouse_destination']))
                        {
                            if(!empty($data['EntryMeta']['dmd_client_invoice']))
                            {
                                $dbkey = array_search('form-total_sold_price', $dbkey_haystack);
                                if($dbkey !== FALSE)    $invoice_price += $value['EntryMeta'][$dbkey]['value'];
                            }
                            else if(!empty($data['EntryMeta']['cor_client_invoice']))
                            {
                                $dbkey = array_search('form-item_weight', $dbkey_haystack);
                                if($dbkey !== FALSE)
                                {
                                    $client_x = 1;
                                    $x_dbkey = array_search('form-client_x', $dbkey_haystack);
                                    if($x_dbkey !== FALSE && is_numeric($value['EntryMeta'][$x_dbkey]['value']))
                                    {
                                        $client_x = $value['EntryMeta'][$x_dbkey]['value'];
                                    }
                                    $invoice_price += $value['EntryMeta'][$dbkey]['value'] * $client_x;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // ========================== >>
        // logistic ...
        // ========================== >>
        if(!empty($data['EntryMeta']['logistic']))
        {
            $logiskey = array_search('form-logistic', array_map(function($el){ return $el['key']; }, $data['EntryMeta']));
            $logismove = array();
            foreach(array('origin', 'destination') as $key => $value)
            {
                if(!empty($data['EntryMeta']['warehouse_'.$value]))
                {
                    $logismove[$value] = array(
                        'storage' => 'warehouse',
                        'content' => $data['EntryMeta']['warehouse_'.$value]
                    );
                }
                else if(!empty($data['EntryMeta']['exhibition_'.$value]))
                {
                    $logismove[$value] = array(
                        'storage' => 'exhibition',
                        'content' => $data['EntryMeta']['exhibition_'.$value]
                    );
                }
            }
            
            $query = $this->Entry->findAllByEntryTypeAndSlug('logistic', $data['EntryMeta']['logistic'] );
            foreach($query as $key => $value)
            {
                $dbkey_haystack = array_column($value['EntryMeta'], 'key');
                $totalkey = array_search($value['Entry']['slug'], $data['EntryMeta']['logistic'] );
                
                // AMBIL STOCK ...
                if(!empty($logismove['origin']) && empty($myEntry)) // ADD MODE ONLY !!
                {
                    $dbkey = array_search('form-'.$logismove['origin']['storage'], $dbkey_haystack);
                    $pecah_storage = explode('|', $value['EntryMeta'][$dbkey]['value'] );
                    $storage_key = key(preg_grep("/^".$logismove['origin']['content']."_.*/", $pecah_storage));
                    $pecah_total = explode('_', $pecah_storage[$storage_key]);
                    $pecah_total[1] -= $data['EntryMeta'][$logiskey]['total'][$totalkey];
                    $pecah_storage[$storage_key] = implode('_', $pecah_total);
                    $value['EntryMeta'][$dbkey]['value'] = implode('|', $pecah_storage);
                    $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                    $this->EntryMeta->saveField('value', $value['EntryMeta'][$dbkey]['value'] );
                }
                
                // TAMBAH STOCK ...
                if(!empty($logismove['destination']) && $data['Entry'][3]['value'] == 1) // Accepted ...
                {
                    $dbkey = array_search('form-'.$logismove['destination']['storage'], $dbkey_haystack);
                    if($dbkey === FALSE)
                    {
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $value['Entry']['id'],
                            'key'       => 'form-'.$logismove['destination']['storage'],
                            'value'     => $logismove['destination']['content'].'_'.$data['EntryMeta'][$logiskey]['total'][$totalkey]
                        )));
                    }
                    else
                    {
                        $pecah_storage = explode('|', $value['EntryMeta'][$dbkey]['value'] );
                        $storage_key = key(preg_grep("/^".$logismove['destination']['content']."_.*/", $pecah_storage));
                        if(is_numeric($storage_key))
                        {
                            $pecah_total = explode('_', $pecah_storage[$storage_key]);
                            $pecah_total[1] += $data['EntryMeta'][$logiskey]['total'][$totalkey];
                            $pecah_storage[$storage_key] = implode('_', $pecah_total);
                            $value['EntryMeta'][$dbkey]['value'] = implode('|', $pecah_storage);
                        }
                        else
                        {
                            $value['EntryMeta'][$dbkey]['value'] .= '|'.$logismove['destination']['content'].'_'.$data['EntryMeta'][$logiskey]['total'][$totalkey];
                        }
                        
                        $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                        $this->EntryMeta->saveField('value', $value['EntryMeta'][$dbkey]['value'] );
                    }
                }
            }
        }
        
        // ========================== >>
        // UPDATE INVOICE DATA !!
        // ========================== >>
        if( (!empty($data['EntryMeta']['diamond']) || !empty($data['EntryMeta']['cor_jewelry'])) && strpos($data['EntryMeta']['delivery_type'], 'Payment') === FALSE && $data['Entry'][3]['value'] == 1 ) // Accepted ...
        {
            $entry_type = '';
            $invslug = '';
            if(!empty($data['EntryMeta']['dmd_vendor_invoice']))
            {
                $entry_type = 'dmd-vendor-invoice';
                $invslug = $data['EntryMeta']['dmd_vendor_invoice'];
            }
            else if(!empty($data['EntryMeta']['cor_vendor_invoice']))
            {
                $entry_type = 'cor-vendor-invoice';
                $invslug = $data['EntryMeta']['cor_vendor_invoice'];
            }
            else if(!empty($data['EntryMeta']['dmd_client_invoice']))
            {
                $entry_type = 'dmd-client-invoice';
                $invslug = $data['EntryMeta']['dmd_client_invoice'];
            }
            else if(!empty($data['EntryMeta']['cor_client_invoice']))
            {
                $entry_type = 'cor-client-invoice';
                $invslug = $data['EntryMeta']['cor_client_invoice'];
            }
            
            // final process !!
            if(!empty($entry_type) && !empty($invslug))
            {
                $itemsent = count($data['EntryMeta']['diamond']) + count($data['EntryMeta']['cor_jewelry']);
                $pcs = 0;
                if($invoice_price > 0)
                {
                    $pcs = $itemsent *= -1;
                    $invoice_price *= -1;
                }
                
                $value = $this->Entry->findByEntryTypeAndSlug($entry_type, $invslug);
                $dbkey_haystack = array_column($value['EntryMeta'], 'key');
                foreach(array_filter(array(
                    'form-total_item_sent'  => $itemsent,
                    'form-total_pcs'        => $pcs,
                    'form-'.(strpos($entry_type, 'dmd-') !== FALSE?'total_price':'total_weight') => $invoice_price
                )) as $subkey => $subvalue)
                {
                    $dbkey = array_search($subkey, $dbkey_haystack);
                    if($dbkey === FALSE)
                    {
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $value['Entry']['id'],
                            'key'       => $subkey,
                            'value'     => $subvalue
                        )));
                    }
                    else
                    {
                        $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                        $this->EntryMeta->saveField('value', $value['EntryMeta'][$dbkey]['value'] + $subvalue );
                    }
                }
            }
        }
    }
    
    function update_product_by_invoice($myTypeSlug, $data)
    {
        $new_total_pcs = 0;
        if($myTypeSlug == 'dmd-vendor-invoice')
        {
            // to search vd barcode for each product ...
            $prodkey = array_search('temp-diamond', array_map(function($el){ return $el['key']; }, $data['EntryMeta']));

            $data['EntryMeta']['temp-diamond'] = array_unique(array_filter($data['EntryMeta']['temp-diamond']));
            
            $pushdata = array(
                'form-vendor_invoice_code'  => $data['Entry'][0]['slug'],
                'form-vendor_invoice_date'  => $data['EntryMeta']['date'],
                'form-vendor'               => $data['EntryMeta']['vendor'],
                'form-vendor_currency'      => $data['EntryMeta']['currency'],
                'form-vendor_x'             => $data['EntryMeta']['temp-capital_x'],
            );
            
            $query = $this->Entry->findAllByEntryTypeAndSlug('diamond', $data['EntryMeta']['temp-diamond'] );
            foreach($query as $key => $value)
            {
                $dbkey_haystack = array_column($value['EntryMeta'], 'key');
                
                $pushdata['form-vendor_barcode'] = $data['EntryMeta'][$prodkey]['total'][array_search($value['Entry']['slug'], $data['EntryMeta']['temp-diamond'] )];
                $pushdata['form-vendor_usd'] = round($pushdata['form-vendor_barcode'] * (is_numeric($pushdata['form-vendor_x'])?$pushdata['form-vendor_x']:1) , 2);
                $pushdata['form-vendor_hkd'] = round($pushdata['form-vendor_usd'] * $data['EntryMeta']['hkd_rate'], 2);
                
                // barcode X
                if(is_numeric($data['EntryMeta']['temp-barcode_x']))
                {
                    $pushdata['form-barcode'] = round( $pushdata['form-vendor_usd'] / $data['EntryMeta']['temp-barcode_x'] , 2);
                    $pushdata['form-sell_barcode'] = ceil($pushdata['form-barcode']);
                }

                foreach(array_filter( array_map('trim', $pushdata) ) as $subkey => $subvalue)
                {
                    $dbkey = array_search($subkey, $dbkey_haystack);
                    if($dbkey === FALSE)
                    {
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $value['Entry']['id'],
                            'key'       => $subkey,
                            'value'     => $subvalue
                        )));
                    }
                    else if($value['EntryMeta'][$dbkey]['value'] != $subvalue)
                    {
                        $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                        $this->EntryMeta->saveField('value', $subvalue );
                    }
                }
            }
            $new_total_pcs = count($query);
        }
        else if($myTypeSlug == 'cor-vendor-invoice')
        {
            // to search item weight for each product ...
            $prodkey = array_search('temp-cor_jewelry', array_map(function($el){ return $el['key']; }, $data['EntryMeta']));
            
            $data['EntryMeta']['temp-cor_jewelry'] = array_unique(array_filter($data['EntryMeta']['temp-cor_jewelry']));
            
            $pushdata = array(
                'form-vendor_invoice_code'  => $data['Entry'][0]['slug'],
                'form-vendor'               => $data['EntryMeta']['vendor'],
                'form-vendor_pcs'           => count($data['EntryMeta']['temp-cor_jewelry']),
                'form-vendor_gr'            => $data['EntryMeta']['total_weight'],
            );
            
            $query = $this->Entry->findAllByEntryTypeAndSlug('cor-jewelry', $data['EntryMeta']['temp-cor_jewelry'] );
            foreach($query as $key => $value)
            {
                $dbkey_haystack = array_column($value['EntryMeta'], 'key');
                $pushdata['form-item_weight'] = $data['EntryMeta'][$prodkey]['total'][array_search($value['Entry']['slug'], $data['EntryMeta']['temp-cor_jewelry'] )];
                foreach(array_filter( array_map('trim', $pushdata) ) as $subkey => $subvalue)
                {
                    $dbkey = array_search($subkey, $dbkey_haystack);
                    if($dbkey === FALSE)
                    {
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $value['Entry']['id'],
                            'key'       => $subkey,
                            'value'     => $subvalue
                        )));
                    }
                    else if($value['EntryMeta'][$dbkey]['value'] != $subvalue)
                    {
                        $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                        $this->EntryMeta->saveField('value', $subvalue );
                    }
                }
            }
            $new_total_pcs = count($query);
        }
        else if($myTypeSlug == 'dmd-client-invoice')
        {
            // to search sell barcode for each product ...
            $prodkey = array_search('temp-diamond', array_map(function($el){ return $el['key']; }, $data['EntryMeta']));

            $data['EntryMeta']['temp-diamond'] = array_unique(array_filter($data['EntryMeta']['temp-diamond']));
            
            $pushdata = array(
                'form-client_invoice_code'  => $data['Entry'][0]['slug'],
                'form-client_invoice_date'  => $data['EntryMeta']['date'],                
                'form-client'               => $data['EntryMeta']['client'],
                'form-client_x'             => $data['EntryMeta']['temp-diamond_sell_x'],                
                'form-wholesaler'           => $data['EntryMeta']['wholesaler'],
                'form-salesman'             => $data['EntryMeta']['salesman'],                
                'form-rp_rate'              => $data['EntryMeta']['rp_rate'],
                // warehouse / exhibition ...
                'form-warehouse'            => $data['EntryMeta']['warehouse'],
                'form-exhibition'           => $data['EntryMeta']['exhibition'],
            );
            
            $query = $this->Entry->findAllByEntryTypeAndSlug('diamond', $data['EntryMeta']['temp-diamond'] );
            foreach($query as $key => $value)
            {
                $dbkey_haystack = array_column($value['EntryMeta'], 'key');
                
                $pushdata['form-sell_barcode'] = $data['EntryMeta'][$prodkey]['total'][array_search($value['Entry']['slug'], $data['EntryMeta']['temp-diamond'] )];                
                $pushdata['form-barcode'] = ( $value['EntryMeta'][array_search('form-barcode', $dbkey_haystack)]['value'] == 1 ? floor($pushdata['form-sell_barcode']) :'');                
                $pushdata['form-total_sold_price'] = round($pushdata['form-sell_barcode'] * (is_numeric($pushdata['form-client_x'])?$pushdata['form-client_x']:1) , 2);

                foreach(array_filter( array_map('trim', $pushdata) ) as $subkey => $subvalue)
                {
                    $dbkey = array_search($subkey, $dbkey_haystack);
                    if($dbkey === FALSE)
                    {
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $value['Entry']['id'],
                            'key'       => $subkey,
                            'value'     => $subvalue
                        )));
                    }
                    else if($value['EntryMeta'][$dbkey]['value'] != $subvalue && $subkey != 'form-warehouse' && $subkey != 'form-exhibition')
                    {
                        $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                        $this->EntryMeta->saveField('value', $subvalue );
                    }
                }
            }
            $new_total_pcs = count($query);
        }
        else if($myTypeSlug == 'cor-client-invoice')
        {
            $cor_haystack = array_filter(array_map(function($el){ return $el['key']; }, $data['EntryMeta']), function($v){
                return strpos($v, 'temp-cor_jewelry') !== FALSE;
            });
            
            $pushdata = array(
                'form-client_invoice_code'  => $data['Entry'][0]['slug'],
                'form-client_invoice_date'  => $data['EntryMeta']['date'],
                'form-client'               => $data['EntryMeta']['client'],
                'form-wholesaler'           => $data['EntryMeta']['wholesaler'],
                'form-salesman'             => $data['EntryMeta']['salesman'],
                'form-client_invoice_pcs'   => array_sum(array_map(function($prodvalue) use (&$data){
                    $data['EntryMeta'][$prodvalue] = array_unique(array_filter($data['EntryMeta'][$prodvalue]));
                    return count($data['EntryMeta'][$prodvalue]);
                }, $cor_haystack)),
                'form-client_invoice_sold_24k' => $data['EntryMeta']['total_weight'],
                'form-gold_price'           => $data['EntryMeta']['gold_price'],
                // warehouse / exhibition ...
                'form-warehouse'            => $data['EntryMeta']['warehouse'],
                'form-exhibition'           => $data['EntryMeta']['exhibition'],
            );
            
            foreach($cor_haystack as $prodkey => $prodvalue )
            {
                $pushdata['form-client_x'] = $data['EntryMeta']['x'.str_replace('temp-cor_jewelry','',$prodvalue)];
                $query = $this->Entry->findAllByEntryTypeAndSlug('cor-jewelry', $data['EntryMeta'][$prodvalue] );
                foreach($query as $key => $value)
                {
                    $dbkey_haystack = array_column($value['EntryMeta'], 'key');
                    $pushdata['form-item_weight'] = $data['EntryMeta'][$prodkey]['total'][array_search($value['Entry']['slug'], $data['EntryMeta'][$prodvalue] )];
                    foreach(array_filter( array_map('trim', $pushdata) ) as $subkey => $subvalue)
                    {
                        $dbkey = array_search($subkey, $dbkey_haystack);
                        if($dbkey === FALSE)
                        {
                            $this->EntryMeta->create();
                            $this->EntryMeta->save(array('EntryMeta' => array(
                                'entry_id'  => $value['Entry']['id'],
                                'key'       => $subkey,
                                'value'     => $subvalue
                            )));
                        }
                        else if($value['EntryMeta'][$dbkey]['value'] != $subvalue && $subkey != 'form-warehouse' && $subkey != 'form-exhibition')    
                        {
                            $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                            $this->EntryMeta->saveField('value', $subvalue );
                        }
                    }
                }
            }
            $new_total_pcs = $pushdata['form-client_invoice_pcs'];
        }
        
        // re-calculate total PCS !!
        if($data['EntryMeta']['total_pcs'] != $new_total_pcs)
        {
            $total_pcs = $this->EntryMeta->findByEntryIdAndKey($data['Entry']['id'], 'form-total_pcs');
            $this->EntryMeta->id = $total_pcs['EntryMeta']['id'];
            $this->EntryMeta->saveField('value', $new_total_pcs );
        }
        
        // update client data too ...
        if(strpos($myTypeSlug, '-client-invoice') !== FALSE)
        {
            $pushdata = array_filter( array_map('trim', array(
                'form-salesman'     => $data['EntryMeta']['salesman'],
                'form-warehouse'    => $data['EntryMeta']['warehouse'],
                'form-exhibition'   => $data['EntryMeta']['exhibition'],
            ) ) );
            
            if(!empty($pushdata))
            {
                $query = $this->Entry->findByEntryTypeAndSlug('client', $data['EntryMeta']['client'] );
                $dbkey_haystack = array_column($query['EntryMeta'], 'key');
                
                foreach($pushdata as $subkey => $subvalue)
                {
                    $dbkey = array_search($subkey, $dbkey_haystack);
                    
                    if($dbkey === FALSE)
                    {
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $query['Entry']['id'],
                            'key'       => $subkey,
                            'value'     => $subvalue
                        )));
                    }
                    else if(strpos( '|'.$query['EntryMeta'][$dbkey]['value'].'|', '|'.$subvalue.'|' ) === FALSE)
                    {
                        $this->EntryMeta->id = $query['EntryMeta'][$dbkey]['id'];
                        $this->EntryMeta->saveField('value', $query['EntryMeta'][$dbkey]['value'].'|'.$subvalue );
                    }
                }
            }
        }
    }
}