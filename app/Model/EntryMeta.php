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
	
	public function __construct( $id = false, $table = NULL, $ds = NULL )
	{
		parent::__construct($id, $table, $ds);
		
		// set needed database model ...
		$this->Type = ClassRegistry::init('Type');
		$this->TypeMeta = ClassRegistry::init('TypeMeta');
		$this->Entry = ClassRegistry::init('Entry');
        $this->EntryMeta = $this; // just as alias ...
		$this->Account = ClassRegistry::init('Account');
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
    
    function upload_diamond($value = array(), $mySetting = array())
    {
//        dpr($value);
//        return;
                
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
            'sold_price_rp'         => round(floatval($value[57]), 2),
            'rp_rate'               => round(floatval($value[58]), 2),
            
            /* TYPE OF PAYMENT */
            
            
        );
        
        dpr($dmd);
        

    }
    
    function upload_jewelry($value = array(), $mySetting = array())
    {
        dpr($value);
        return;
    }
}
