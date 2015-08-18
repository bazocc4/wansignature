<?php
class Entry extends AppModel {
	var $name = 'Entry';
	var $validate = array(
		'entry_type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'slug' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			// 'isUnique' => array(
				// 'rule' => array('isUnique'),
				// 'message' => 'Invalid slug defined. Please contact your administrator for further solution.',
				// //'allowEmpty' => false,
				// //'required' => false,
				// //'last' => false, // Stop validation after this rule
				// //'on' => 'create', // Limit validation to 'create' or 'update' operations
			// ),
		),
		'main_image' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'parent_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'count' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'created_by' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'modified_by' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'sort_order' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please complete all required fields.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'ParentEntry' => array(
			'className' => 'Entry',
			'foreignKey' => 'parent_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ParentImageEntry' => array(
			'className' => 'Entry',
			'foreignKey' => 'main_image',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'AccountCreatedBy' => array(
			'className' => 'Account',
			'foreignKey' => 'created_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'AccountModifiedBy' => array(
			'className' => 'Account',
			'foreignKey' => 'modified_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ChildEntry' => array(
			'className' => 'Entry',
			'foreignKey' => 'parent_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => 'ChildEntry.sort_order DESC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ChildMainEntry' => array(
			'className' => 'Entry',
			'foreignKey' => 'main_image',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'EntryMeta' => array(
			'className' => 'EntryMeta',
			'foreignKey' => 'entry_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => 'EntryMeta.id ASC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
    
    private $Resize=null;
	
	// DATABASE MODEL...
	var $Type = NULL;
	var $TypeMeta = NULL;
	var $Setting = NULL;
    
    var $Entry = NULL;
	var $EntryMeta = NULL;
	var $Account = NULL;
    
    // CURRENT USER DETAIL ...
    var $myCreator = NULL;

	public function __construct( $id = false, $table = NULL, $ds = NULL )
	{
		parent::__construct($id, $table, $ds);
		
		App::uses('JqImgcropComponent' , 'Controller/Component');
		$this->Resize = new JqImgcropComponent(new ComponentCollection()); //make instance
		
		// set needed database model ...
		$this->Type = ClassRegistry::init('Type');
		$this->TypeMeta = ClassRegistry::init('TypeMeta');
		$this->Setting = ClassRegistry::init('Setting');
        
        $this->Entry = $this; // just as alias ...
		$this->EntryMeta = ClassRegistry::init('EntryMeta');
		$this->Account = ClassRegistry::init('Account');
        
        // set current user ...
        $this->myCreator = $this->getCurrentUser();
	}
    
    function _convertEntrySlug($slug)
    {
        return $slug;
    }
    
    function _reorderAfterTranslate($lang_code)
    {
        $pecahlang = explode('-', $lang_code);
        
        $acuan = $this->findById($pecahlang[1]);
        $bahasa_acuan = explode('-', $acuan['Entry']['lang_code']);        
        $querysrc = $this->find('all', array(
            'conditions' => array(
                'Entry.entry_type' => $acuan['Entry']['entry_type'],
                'Entry.parent_id' => $acuan['Entry']['parent_id'],
                'Entry.lang_code LIKE' => $bahasa_acuan[0].'-%'
            ),
            'order' => array('Entry.sort_order DESC')
        ));
        
        $target = $this->findByLangCode($lang_code);        
        $querydst = $this->find('all', array(
            'conditions' => array(
                'Entry.entry_type' => $target['Entry']['entry_type'],
                'Entry.parent_id' => $target['Entry']['parent_id'],
                'Entry.lang_code LIKE' => $pecahlang[0].'-%'
            ),
            'order' => array('Entry.sort_order DESC')
        ));
        // INDEXING TARGET !!
        $src = $dst = '';        
        foreach($querydst as $key => $value)
        {
            $src .= $value['Entry']['sort_order'].',';            
            $dst .= $value['Entry']['id'].',';
            $querydst[ $value['Entry']['lang_code'] ] = $value['Entry'];
        }
        
        // search acuan position !!
        $acuankey = '';
        foreach($querysrc as $key => $value)
        {
            if($value['Entry']['id'] == $acuan['Entry']['id'])
            {
                $acuankey = $key;
                break;
            }
        }
        
        // BEGIN MAIN PROCESS !!
        $counter = 2;
        $maxlimit = count($querysrc) - 1;
        $failedtest = 0;
        $resultid = 0;
        while(TRUE)
        {
            $key = ( $counter%2==0 ? -1 : 1 );            
            $key *= floor($counter/2);
            $key += $acuankey;
            if($key >= 0 && $key <= $maxlimit) // array limit validation !!
            {
                $failedtest = 0;
                $dst_lang_code = explode('-', $querysrc[$key]['Entry']['lang_code']);
                $tempcode = $pecahlang[0].'-'.$dst_lang_code[1];
                if(!empty($querydst[$tempcode]))
                {
                    $resultid = $querydst[$tempcode]['id'];
                    break;
                }
            }
            else
            {
                $failedtest++;
                if($failedtest >= 2)
                {
                    // nothing to be sorted !!
                    break;
                }
            }
            $counter++;    
        }
        
        if(!empty($resultid))
        {
            // turn string into array !!
            $src = explode(',', $src );
            $dst = explode(',', $dst );
            
            // moving ID element !!
            $moving = array_splice($dst , 0 , 1);
            array_splice($dst , array_search($resultid , $dst ) + ( $counter%2==0 ? 1 : 0 ) , 0 , $moving );
            
            // start reorderList !!
            $this->_reorderList($src , $dst );
        }
    }
    
    /**
	 * re-order entry sort_order for entries view order through ajax (cont. from Controller)
	 * @return void
	 * @public
	 **/
    function _reorderList($src = array() , $dst = array(), $lang = NULL)
    {
        unset($src[count($src)-1]);
		unset($dst[count($dst)-1]);		
		foreach ($dst as $key => $value) 
		{
			$fast_dst[$value] = $src[$key];
		}
		
        $movedpos = array(); // helper variable for updating other language order !!
		foreach ($src as $key => $value) 
		{			
			$temp = $this->findBySortOrder($value);
            
            if($temp['Entry']['id'] != $dst[$key])
            {
                $movedpos[$key] = $temp['Entry']['id'];
            }
            
			$this->id = $temp['Entry']['id'];
			$result[$this->id] = $fast_dst[$this->id];
		}
        
		foreach ($result as $key => $value) 
		{			
			$this->id = $key;
			$this->saveField('sort_order' , $value);
		}
        
        // NEW FEATURE 2015 : Update sort_order in other language too !!
        if(!empty($movedpos) && !empty($lang))
        {
            // check moving orientation !!
            $initkey = key($movedpos);
            $startid = current($movedpos);
            $finishid = current(array_reverse($movedpos));
            
            next($movedpos);
            
            if(current($movedpos) != $dst[$initkey])
            {
                swap_value($startid , $finishid);
            }
            
            $startquery = $this->findById($startid);
            $startlang = explode('-' , $startquery['Entry']['lang_code']);
            $startlist = $this->find('all', array(
                'conditions' => array(
                    'Entry.lang_code LIKE' => '%-'.$startlang[1],
                    'Entry.id <>' => $startid
                )
            ));
            
            $finishquery = $this->findById($finishid);
            $finishlang = explode('-', $finishquery['Entry']['lang_code']);
            $finishlist = $this->find('all', array(
                'conditions' => array(                    
                    'Entry.lang_code LIKE' => '%-'.$finishlang[1],
                    'Entry.id <>' => $finishid
                )
            ));
            if(!empty($finishlist))
            {
                // INDEXING array !!
                foreach($finishlist as $key => $value)
                {
                    $bahasa = explode('-', $value['Entry']['lang_code']);
                    $finishlist[$bahasa[0]] = $value['Entry'];
                }
            }
            
            // BEGIN MAIN PROCESS !!
            if(!empty($startlist))
            {
                foreach($startlist as $key => $value)
                {
                    $bahasa = explode('-', $value['Entry']['lang_code']);
                    if(!empty( $finishlist[$bahasa[0]] ))
                    {
                        $so_awal = $value['Entry']['sort_order'];
                        $so_akhir = $finishlist[$bahasa[0]]['sort_order'];
                        
                        $arahorder = 'ASC';
                        if($so_akhir < $so_awal)
                        {
                            swap_value($so_awal , $so_akhir);
                            $arahorder = 'DESC';
                        }
                        
                        $querybetween = $this->find('all', array(
                            'conditions' => array(
                                'Entry.entry_type' => $value['Entry']['entry_type'],
                                'Entry.parent_id' => $value['Entry']['parent_id'],
                                'Entry.lang_code LIKE' => $bahasa[0].'-%',
                                'Entry.sort_order BETWEEN ? AND ?' => array($so_awal , $so_akhir)
                            ),
                            'order' => array('Entry.sort_order '.$arahorder)
                        ));
                        
                        $tempso = 0;
                        foreach($querybetween as $betkey => $betvalue)
                        {
                            $this->id = $betvalue['Entry']['id'];
                            if($betkey > 0)
                            {
                                $this->saveField('sort_order' , $tempso );
                            }
                            else
                            {
                                $this->saveField('sort_order' , $querybetween[count($querybetween)-1]['Entry']['sort_order'] );
                            }
                            
                            $tempso = $betvalue['Entry']['sort_order'];
                        }
                    }
                }
            }
            // END OF SORTING OTHER LANGUAGE PROCESS !!
        }
    }
	
	/**
	 * to get a valid slug entry process
	 * @param string $slug contains source slug want to be processed
	 * @param integer $id contains id of the entry
	 * @return string $mySlug contains new generated slug.
	 * @public
	 **/
	function get_valid_slug($slug , $id = NULL)
	{	
		$counter = 0;
		$mySlug = $slug;
        $options = array();
		if(!empty($id))
		{
			$options['conditions']['Entry.id <>'] = $id;
		}
		while(TRUE)
		{
			$options['conditions']['Entry.slug'] = $mySlug;
			$findSlug = $this->find('count' , $options);
			if(empty($findSlug))
			{
				break;
			}
			else
			{
				$mySlug = $slug.'-'.(++$counter);
			}
		}
		return $mySlug;
	}
	
	/**
	 * function that be executed before save an entry (automated by cakephp)
	 * @return boolean
	 * @public
	 **/
	function beforeSave()
	{
		if(!empty($this->data['Entry']['slug']))
		{
			$this->data['Entry']['slug'] = $this->get_valid_slug($this->data['Entry']['slug'] , $this->id);
		}
        
        // renew the modifier !!
        if(!empty($this->myCreator))
        {
            $this->data['Entry']['modified_by'] = $this->myCreator['id'];
        }
		return true;
	}
	
	function updateCountField($parent_id , $myChildTypeSlug , $deletemode = false)
	{
		if($parent_id > 0)
		{
			//------------------- firstly create count-type in EntryMeta... ---------------- /////
			$totalchild = $this->find('count', array(
				'conditions' => array(
					'Entry.entry_type' => $myChildTypeSlug,
					'Entry.parent_id' => $parent_id
				)
			));
			if($deletemode)
			{
				$totalchild--;
			}
			
			$updateCountType = $this->EntryMeta->find('first' , array(
				'conditions' => array(
					'EntryMeta.entry_id' => $parent_id,
					'EntryMeta.key' => 'count-'.$myChildTypeSlug
				)
			));
			if(!empty($updateCountType))
			{
				$this->EntryMeta->id = $updateCountType['EntryMeta']['id'];
				$this->EntryMeta->saveField('value' , $totalchild);
			}
			else
			{
				if($totalchild > 0)
				{
					$input = array();
					$input['EntryMeta']['entry_id'] = $parent_id;
					$input['EntryMeta']['key'] = 'count-'.$myChildTypeSlug;
					$input['EntryMeta']['value'] = $totalchild;
					$this->EntryMeta->create();
					$this->EntryMeta->save($input);
				}
			}
			
			//----------------------- add COUNT to parent Entry -------------------------- /////
			$totalchild = $this->find('count', array(
				'conditions' => array(
					'Entry.parent_id' => $parent_id
				)
			));
			if($deletemode)
			{
				$totalchild--;
			}
			
			$tempid = $this->id;
			
			$this->id = $parent_id;
			$this->saveField('count' , $totalchild);
			
			$this->id = $tempid;
		}
	}
	
	/**
     * function that be executed after save a record (automated by cakephp)
     * @param boolean $created will be true if a new record was created (rather than an update).
     * @param array $options is the same one passed to Model::save().
     * @public
     **/
    function afterSave($created, $options = array())
    {   
        if($created)
        {
            $temp = $this->field('sort_order');
            if($temp == 0)
            {
                $this->saveField('sort_order' , $this->id);
            }
            // lang_code update
            $temp = $this->field('lang_code');
            if(empty($temp))
            {
                // search for default language...
                $query = $this->Setting->findByKey('language');
                $this->saveField('lang_code' , substr($query['Setting']['value'] , 0 , 2).'-'.$this->id);
            }
            else if(strlen($temp) == 2)
            {
                $this->saveField('lang_code' , $temp.'-'.$this->id);
            }

            $this->updateCountField($this->field('parent_id') , $this->field('entry_type'));
        }
    }
	
	/**
     * function that be executed before delete an entry (automated by cakephp)
     * @return boolean
     * @public
     **/
	function beforeDelete()
	{
		$this->updateCountField($this->field('parent_id') , $this->field('entry_type') , true);
		return true;
	}
	
	/**
	* delete selected media from database and dir img/upload
	* @param integer $id get media id
	* @return boolean
	* @public
	**/
	public function deleteMedia($id = NULL)
	{	
		if($id!=NULL)
		{
			$row=$this->findById($id);
			foreach ($row['EntryMeta'] as $key => $value) 
			{
				if($value['key'] == 'image_type')
				{
					$imageType = $value['value'];
					break;
				}
			}
			
			$file=sprintf('%s.%s',$row['Entry']['id'],$imageType);
			
			// Delete File from directory first
			$destDisplay=sprintf('%simg'.DS.'upload'.DS.'%s',WWW_ROOT,$file);
			$destThumbnails=sprintf('%simg'.DS.'upload'.DS.'thumbnails'.DS.'%s.%s',WWW_ROOT,$row['Entry']['title'],$imageType);
			
			// Delete file
			unlink($destDisplay);
			unlink($destThumbnails);
			
			// special case deleter !!
			if(strtolower($imageType) == 'jpg' || strtolower($imageType) == 'jpeg')
			{
				unlink(sprintf('%simg'.DS.'upload'.DS.'thumbnails'.DS.'%s.jpg',WWW_ROOT,$row['Entry']['title']));
				unlink(sprintf('%simg'.DS.'upload'.DS.'thumbnails'.DS.'%s.jpeg',WWW_ROOT,$row['Entry']['title']));
			}
			
			$this->delete($id);
			$this->EntryMeta->deleteAll(array('EntryMeta.entry_id' => $id));
			return true;
		}		
		return false;
	}

	public function makeChildImageEntry($data = array() , $myType = array())
	{
		$parentImage = $this->findById($data['value']);        
        $parentImage = breakEntryMetas($parentImage);
        
        // set the type of this entry...
		$input['Entry']['entry_type'] = 'media';
		$input['Entry']['title'] = $parentImage['Entry']['title'];
		// generate slug from title...
		$input['Entry']['slug'] = $this->get_slug($input['Entry']['title']);
		// write my creator...
		$input['Entry']['modified_by'] = $this->myCreator['id'];
		$input['Entry']['parent_id'] = $parentImage['Entry']['id'];
		if(empty($data['childImageId']))
		{
			$input['Entry']['created_by'] = $this->myCreator['id'];
			$this->create();
		}
		else
		{
			$this->id = $data['childImageId'];
			$this->EntryMeta->deleteAll(array('EntryMeta.entry_id' => $this->id));
		}
		$this->save($input);
		
		// save the image type...
		$myid = $this->id;
		$input['EntryMeta']['entry_id'] = $myid;
		$input['EntryMeta']['key'] = 'image_type';
		$input['EntryMeta']['value'] = $parentImage['EntryMeta']['image_type'];
		$this->EntryMeta->create();
		$this->EntryMeta->save($input);
		
		// save the image size...
		$input['EntryMeta']['key'] = 'image_size';
		$input['EntryMeta']['value'] = $this->createChildDisplay($parentImage['Entry']['id'] , $parentImage['EntryMeta']['image_type'] , $myid , $data['w'] , $data['h'] , $data['x1'] , $data['y1']);
		$this->EntryMeta->create();
		$this->EntryMeta->save($input);
		
		// SAVE OTHER ATTRIBUTE OF NEW CROPPED IMAGES !!
		if(is_numeric($data['x1']) && is_numeric($data['y1']) && is_numeric($data['w']) && is_numeric($data['h']))
		{
			$input['EntryMeta']['key'] = 'image_x';
			$input['EntryMeta']['value'] = $data['x1'];
			$this->EntryMeta->create();
			$this->EntryMeta->save($input);
			$input['EntryMeta']['key'] = 'image_y';
			$input['EntryMeta']['value'] = $data['y1'];
			$this->EntryMeta->create();
			$this->EntryMeta->save($input);
			$input['EntryMeta']['key'] = 'image_width';
			$input['EntryMeta']['value'] = $data['w'];
			$this->EntryMeta->create();
			$this->EntryMeta->save($input);
			$input['EntryMeta']['key'] = 'image_height';
			$input['EntryMeta']['value'] = $data['h'];
			$this->EntryMeta->create();
			$this->EntryMeta->save($input);
		}
		return $myid;
	}

	/**
	 * create a display image
	 * @param integer $myid contains id of the image entry
	 * @param string $mytype contains extension type of the image (like .jpg, .png, etc)
	 * @param string $myMediaSettings contains array of media settings want to be used
	 * @return true
	 * @public
	 **/
	public function createDisplay($myid , $mytype , $myMediaSettings)
	{	
		$src = WWW_ROOT.'img'.DS.'upload'.DS.'original'.DS.$myid.'.'.$mytype;
		$dest = WWW_ROOT.'img'.DS.'upload'.DS.$myid.'.'.$mytype;
		return $this->Resize->image_resize($src, $dest, $myMediaSettings['display_width'], $myMediaSettings['display_height'] , $myMediaSettings['display_crop']);
	}
	
	public function createChildDisplay($myid , $mytype , $myChildId , $width , $height , $x , $y)
	{	
		$src = WWW_ROOT.'img'.DS.'upload'.DS.$myid.'.'.$mytype;
		$dest = WWW_ROOT.'img'.DS.'upload'.DS.$myChildId.'.'.$mytype;
		return $this->Resize->image_resize($src, $dest, $width, $height , 2 , $x , $y);
	}
	
	/**
	 * retrieve media settings from certain type meta, if doesn't exist, then retrieve from global settings.
	 * @param array $myType contains query data from selected entry type
	 * @return array $result contains all the media settings will be used
	 * @public
	 **/
	public function getMediaSettings($myType = array())
	{
		// choose from settings first...
		$result = $this->Setting->get_settings();
		// if in type meta exist, use that :D
		$myTypeMeta = $this->TypeMeta->findAllByTypeId($myType['Type']['id']);
		foreach ($myTypeMeta as $key => $value) 
		{
			if($value['TypeMeta']['key'] == 'display_width')
			{
				$result['display_width'] = $value['TypeMeta']['value'];
			}
			else if($value['TypeMeta']['key'] == 'display_height')
			{
				$result['display_height'] = $value['TypeMeta']['value'];
			}
			else if($value['TypeMeta']['key'] == 'display_crop')
			{
				$result['display_crop'] = ($value['TypeMeta']['value']==2?0:$value['TypeMeta']['value']);
			}
			else if($value['TypeMeta']['key'] == 'thumb_width')
			{
				$result['thumb_width'] = $value['TypeMeta']['value'];
			}
			else if($value['TypeMeta']['key'] == 'thumb_height')
			{
				$result['thumb_height'] = $value['TypeMeta']['value'];
			}
			else if($value['TypeMeta']['key'] == 'thumb_crop')
			{
				$result['thumb_crop'] = $value['TypeMeta']['value'];
			}
		}
		return $result;
	}

	function get_lang_url($custom_referer = NULL)
	{
		// -------------- LANGUAGE URL POSITION ----------------------------- //
		$lang_pos = '';
		$indentation = '';
		if(isLocalhost())
		{
			$lang_pos = 2;
			$indentation = $lang_pos - 1;
		}
		else
		{
			$lang_pos = 1;
			$indentation = $lang_pos;
		}		
		// ----------- END OF LANGUAGE URL POSITION ------------------------- //
		$domain_lang = strtolower(substr($_SERVER['SERVER_NAME'], 0,2));
		$mySetting = $this->Setting->get_settings();
		
		foreach ($mySetting['language'] as $key => $value) 
		{
			if($domain_lang == strtolower(substr($value, 0,2)))
			{
				$result['language'] = $domain_lang;
				$result['indent'] = 0;
				return $result;
			}
		}
		// NOW FOR SECOND CHECK !!
        $url_set = "";
        if(!empty($custom_referer))
        {
            $url_set = explode('/', strtolower($custom_referer));
            array_splice($url_set , 0 , 2);
        }
        else
        {
            $url_set = explode('/', strtolower($_SERVER['REQUEST_URI']));
        }		
		foreach ($mySetting['language'] as $key => $value) 
		{
			if($url_set[$lang_pos] == strtolower(substr($value, 0,2)))
			{	
				$result['language'] = $url_set[$lang_pos];
				$result['indent'] = $indentation;
				return $result;
			}
		}
		$result['language'] = strtolower(substr($mySetting['language'][0], 0,2));
		$result['indent'] = 0;
		return $result;
	}

	// imported from GET Helpers !!
	function meta_details($slug = NULL , $entry_type = NULL , $parentId = NULL , $id = NULL , $ordering = NULL , $lang = NULL , $title = NULL , $status = NULL)
	{
        $options = array();
		if(!is_null($slug))
		{
			$options['conditions']['Entry.slug'] = $slug;
		}		
		if(!is_null($entry_type))
		{
			$options['conditions']['Entry.entry_type'] = $entry_type;
		}
		if(!is_null($parentId))
		{
			$options['conditions']['Entry.parent_id'] = $parentId;
		}
		if(!is_null($id))
		{
			$options['conditions']['Entry.id'] = $id;
		}
        if(!is_null($ordering))
        {
            if(substr($ordering , 0 , 6) == 'Entry.')
            {
                $options['order'] = array($ordering);
            }
            else
            {
                $options['order'] = array('Entry.sort_order '.$ordering);
            }
        }
		if(!is_null($lang))
		{
			$options['conditions']['Entry.lang_code LIKE'] = $lang.(strpos($lang, '-') === FALSE?'-%':'');
		}
		if(!is_null($title))
		{
			$options['conditions']['Entry.title '.(strpos($title,'%')!==FALSE?'LIKE':'')] = $title;
		}
        
        $result = array();
        // Skip if no options parameter !!
        if(!empty($options))
        {
            if(!is_null($status))
            {
                $options['conditions']['Entry.status'] = $status;
            }
            
            $result = $this->find('first',$options);            
            if(!empty($result))
            {
                $result = breakEntryMetas($result);
            }
        }
		return $result;
	}
	
	// ---------------------------------------------- >>
	// for batch upload image in media library !!
	// ---------------------------------------------- >>
	public function autoUploadImage($filename , $myTypeSlug = 'products')
	{
		$default_path = WWW_ROOT.'img'.DS.'upload'.DS.'original'.DS.$filename;
		
		$path_parts = pathinfo($filename);
		
		if(!file_exists($default_path))
		{
			$search_image = $this->find('first', array(
				'conditions' => array(
					'Entry.entry_type' => 'media',
					'Entry.title' => $path_parts['filename']
				)
			));
			
			if(empty($search_image))
			{
				return 0;
			}
			else
			{
				return $search_image['Entry']['id'];
			}
		}
		
		$input = array();
		$input['Entry']['entry_type'] = 'media';
		$input['Entry']['title'] = $path_parts['filename'];
		// generate slug from title...
		$input['Entry']['slug'] = get_slug($input['Entry']['title']);
		$this->create();
		$this->save($input);
		
		$myid = $this->id;
		$mytype = $path_parts['extension'];
		// rename the filename...
		rename( $default_path , WWW_ROOT.'img'.DS.'upload'.DS.'original'.DS.$myid.'.'.$mytype);
		
		// now generate for display and thumb image according to the media settings...
		$myType = $this->Type->findBySlug($myTypeSlug);
		$myMediaSettings = $this->getMediaSettings($myType);
		
		// save the image type...
		$input = array();		
		$input['EntryMeta']['entry_id'] = $myid;
		$input['EntryMeta']['key'] = 'image_type';
		$input['EntryMeta']['value'] = $mytype;
		$this->EntryMeta->create();
		$this->EntryMeta->save($input);
		// save the image size...
		$input['EntryMeta']['key'] = 'image_size';
		$input['EntryMeta']['value'] = $this->createDisplay($myid , $mytype , $myMediaSettings);
		$this->EntryMeta->create();
		$this->EntryMeta->save($input);
		
		// REMOVE ORIGINAL IMAGE FILE !!
		unlink(WWW_ROOT.'img'.DS.'upload'.DS.'original'.DS.$myid.'.'.$mytype);
		
		return $myid;
	}
	
	public function billFormatNumber($entry_type , $limit = 4)
    {
        // for trans ID, minimum of x digits...
        $bill_sample = date('ym' , gmt_adjustment()).'.';
		
        $trans = $this->find('first' , array(
            'conditions' => array(
                'Entry.entry_type' => $entry_type,
                'Entry.title LIKE' => $bill_sample.'%'
            ),
            'order' => array('Entry.id DESC')
        ));
        
        $result = '';
        $index = 1;
        if(!empty($trans))
        {
            $pecah = explode('.', $trans['Entry']['title']);
            $index = $pecah[count($pecah) - 1] + 1;
        }
        
        $result = $bill_sample.sprintf('%0'.$limit.'d' , $index);
        return $result;
    }

    function getActiveComments($data , $sorted = NULL , $metatable = NULL)
	{
		if(empty($sorted))
		{
			$mysql = $data;
		}
		else
		{
			$mysql = orderby_metavalue($data , $metatable , 'created' , 'DESC');
		}

		$result = array();

		if(empty($metatable))
		{
			foreach ($mysql as $key => $value) 
			{
				if($value['status'] == 1)
				{
					array_push($result, $value);
				}
			}
		}
		else
		{
			foreach ($mysql as $key => $value) 
			{
				if($value[$metatable]['status'] == 1)
				{
					array_push($result, $value);
				}
			}
		}
		return $result;
	}

	/*
	Check if this entry data has already have all of language version or not.
	- return true if there is still exist language that has not been translated yet, otherwise false.
	*/
	function checkRemainingLang($id , $mySetting)
	{
		$entry = $this->findById($id);
		$langcode = explode('-', $entry['Entry']['lang_code']);

		if(!empty($langcode[1]) && is_numeric($langcode[1]))
		{
			$temp = $this->find('count' , array(
				'conditions' => array(
					'Entry.lang_code LIKE' => '%-'.$langcode[1]
				)
			));

			$countLang = count($mySetting['language']);

			if($temp < $countLang)
			{
				return $entry['Entry']['slug'];
			}
		}
		
		return false;
	}
    
    /*
    Check certain EntryType has gallery feature turn ON / OFF !!
    */
    function checkGalleryType($myAutomaticValidation = array())
    {
        foreach ($myAutomaticValidation as $key => $value) 
        {
            if($value['key'] == 'gallery')
            {
                if($value['value'] == 'enable')
                {
                    return true;
                }
                break;
            }
        }
        return false;
    }
    
    function update_invoice_payment($myParentEntry = array(), $data = array())
    {
        $allowed_balance = true;
        if( !empty($data['EntryMeta']['checks_date']) && strtotime($data['EntryMeta']['checks_date']) > strtotime(date('m/d/Y')) || !empty($data['EntryMeta']['loan_period']) )
        {
            $allowed_balance = false;
        }
        
        // update invoice payment balance ...
        $updated_balance = ($data['EntryMeta']['statement']=='Debit'?1:-1) * $data['EntryMeta']['amount'] * ( strpos($myParentEntry['Entry']['entry_type'], '-vendor-') !== FALSE ?1:-1);
        
        $return_status = 0;
        if($updated_balance < 0)
        {
            $return_status = 1;
        }
        
        if($allowed_balance)
        {
            $dbkey_invoice = array_column($myParentEntry['EntryMeta'], 'key');
            $dbkey = array_search('form-payment_balance', $dbkey_invoice);
            if($dbkey === FALSE)
            {
                $this->EntryMeta->create();
                $this->EntryMeta->save(array('EntryMeta' => array(
                    'entry_id'  => $myParentEntry['Entry']['id'],
                    'key'       => 'form-payment_balance',
                    'value'     => $updated_balance
                )));
            }
            else
            {
                $updated_balance += $myParentEntry['EntryMeta'][$dbkey]['value'];

                $this->EntryMeta->id = $myParentEntry['EntryMeta'][$dbkey]['id'];
                $this->EntryMeta->saveField('value', $updated_balance );
            }
        }
        
        // firstly, filter array products !!
        foreach(array('diamond', 'cor_jewelry', 'payment_jewelry') as $key => $value)
        {
            $data['EntryMeta'][$value] = array_unique(array_filter($data['EntryMeta'][$value]));
        }
        
        if(!empty($data['EntryMeta']['payment_jewelry']))
        {
            $subkey = 'form-transaction_history';
            $subvalue = 'RETURN '.(strpos($myParentEntry['Entry']['entry_type'], '-client-') !== FALSE?'FROM CLIENT':'TO VENDOR').' AS PAYMENT FOR INV# '.$myParentEntry['Entry']['title'];
            
            $query = $this->Entry->findAllByEntryTypeAndSlug('cor-jewelry', $data['EntryMeta']['payment_jewelry'] );
            foreach($query as $key => $value)
            {
                $dbkey_haystack = array_column($value['EntryMeta'], 'key');
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
                    $this->EntryMeta->saveField('value', $value['EntryMeta'][$dbkey]['value'].chr(10).$subvalue );
                }
            }
        }
        
        // =============================================================== >>>
        // update products attribute (client outstanding, payment blabla ) ...
        // =============================================================== >>>
        if(strpos($myParentEntry['Entry']['entry_type'], '-client-') !== FALSE)
        {
            // for diamond and cor-jewelry !!
            foreach(array('diamond', 'cor_jewelry') as $typekey => $typevalue)
            {
                if(!empty($data['EntryMeta'][$typevalue]))
                {
                    $subkey = 'form-payment_'.strtolower(str_replace(' ','_', $data['EntryMeta']['type']));
                    $subvalue = $data['Entry'][0]['value'];
                    
                    $subkey_sold = 'form-product_status';
                    $subvalue_sold = 'SOLD';
                    
                    $query = $this->Entry->findAllByEntryTypeAndSlug(get_slug($typevalue), $data['EntryMeta'][$typevalue] );
                    foreach($query as $key => $value)
                    {
                        // update client outstanding ...
                        if(stripos($value['Entry']['description'], $data['Entry'][1]['value']) === FALSE)
                        {
                            $this->Entry->id = $value['Entry']['id'];
                            $this->Entry->saveField('description', trim($value['Entry']['description'].chr(10).$data['Entry'][1]['value']) );
                        }
                        
                        // update payment description ...
                        $dbkey_haystack = array_column($value['EntryMeta'], 'key');
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
                            $this->EntryMeta->saveField('value', $value['EntryMeta'][$dbkey]['value'].chr(10).$subvalue );
                        }
                        
                        // check product status !!
                        if(empty($return_status))
                        {
                            $dbkey = array_search($subkey_sold, $dbkey_haystack);
                            if($dbkey === FALSE)
                            {
                                $this->EntryMeta->create();
                                $this->EntryMeta->save(array('EntryMeta' => array(
                                    'entry_id'  => $value['Entry']['id'],
                                    'key'       => $subkey_sold,
                                    'value'     => $subvalue_sold
                                )));
                            }
                            else if(stripos($value['EntryMeta'][$dbkey]['value'], $subvalue_sold ) === FALSE)
                            {
                                $this->EntryMeta->id = $value['EntryMeta'][$dbkey]['id'];
                                $this->EntryMeta->saveField('value', $subvalue_sold );
                            }
                        }
                    }
                }
            }
            
            // update all products payment_balance on selected invoice !!
            if($allowed_balance)
            {
                $query = $this->EntryMeta->findAllByKeyAndValue('form-client_invoice_code', $myParentEntry['Entry']['slug']);
                foreach($query as $key => $value)
                {
                    $search_balance = $this->EntryMeta->findByEntryIdAndKey($value['EntryMeta']['entry_id'], 'form-payment_balance');
                    if(empty($search_balance))
                    {
                        $this->EntryMeta->create();
                        $this->EntryMeta->save(array('EntryMeta' => array(
                            'entry_id'  => $value['EntryMeta']['entry_id'],
                            'key'       => 'form-payment_balance',
                            'value'     => $updated_balance
                        )));
                    }
                    else
                    {
                        $this->EntryMeta->id = $search_balance['EntryMeta']['id'];
                        $this->EntryMeta->saveField('value', $updated_balance);
                    }
                }
            }
        }
        else if(strpos($myParentEntry['Entry']['entry_type'], '-vendor-') !== FALSE)
        {
            if(empty($return_status))
            {
                // for diamond and cor-jewelry !!
                foreach(array('diamond', 'cor_jewelry') as $typekey => $typevalue)
                {
                    if(!empty($data['EntryMeta'][$typevalue]))
                    {
                        $subkey = 'form-report_type';
                        $subvalue = 'SR';

                        $query = $this->Entry->findAllByEntryTypeAndSlug(get_slug($typevalue), $data['EntryMeta'][$typevalue] );
                        foreach($query as $key => $value)
                        {
                            $dbkey_haystack = array_column($value['EntryMeta'], 'key');
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
                }
            }
        }
        // end of function ...
    }
}