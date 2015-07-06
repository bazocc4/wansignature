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
    
    public function download_diamond()
    {
        // download diamond command here ...
    }
    
    public function download_jewelry()
    {
        // download cor jewelry command here ...
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
                
        /**  Define how many rows we want for each "chunk" and other helper variable  **/
        $chunkSize = $counterRow = 50;
        $maxCols = 71;
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
            dpr('Processing Excel record : '.$startRow.' - '.($startRow + $chunkSize - 1).' '.$printSpace[abs( (floor($counterChunk / $intervalSpace) % 2) * $intervalSpace - ($counterChunk % $intervalSpace) )].'... Please wait a moment ...');
            scrollBottomWithFlush();
            
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
        if(strpos($myChildTypeSlug , '-payment') !== FALSE)
		{
			$this->request->params['page'] = 0; // must be one full page !!
            $_SESSION['order_by'] = 'form-date asc';
		}
        
        // query diamond product_type ...
        if($myType['Type']['slug'] == 'surat-jalan' || $myChildTypeSlug == 'dv-payment' || $myChildTypeSlug == 'dc-payment')
        {
            $this->set('diamondType', $this->EntryMeta->get_diamond_type() );
        }
        
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
				return;
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
		$this->Entry->recursive = 2;
		$myEntry = $this->meta_details($this->request->params['entry'] , (!empty($this->request->query['type'])?$this->request->query['type']:$myType['Type']['slug']) );
		$this->Entry->recursive = 1;
        
        if(empty($myEntry))
        {
            throw new NotFoundException('Error 404 - Not Found');
            return;
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
					$_SESSION['searchMe'] = $searchMe;
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
                array_push($options['conditions'], array(
                    'EntryMeta.key_value LIKE' => '%{#}form-product_status=%'.($this->request->query['storage'] == 'exhibition'?'consignment':'stock').'%{#}form-'.$this->request->query['storage'].'='.$this->request->query['content'].'{#}%'
                ));
            }
        }
        
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
            $options['conditions']['OR'] = array(
                array('Entry.title LIKE' => '%'.$_SESSION['searchMe'].'%'), 
                array('Entry.description LIKE' => '%'.$_SESSION['searchMe'].'%'),
                array('ParentEntry.title LIKE' => '%'.$_SESSION['searchMe'].'%')
            );
            
			if($this->mySetting['table_view']=='complex')
			{
                array_push($options['conditions']['OR'] , array('REPLACE(REPLACE(EntryMeta.key_value , "-" , " "),"_"," ") LIKE' => '%'.string_unslug($_SESSION['searchMe']).'%') );
			}
		}
        
        // ========================================= >>
        // FINAL SORT based on certain criteria !!
        // ========================================= >>
        if(!empty($innerFieldMeta))
        {
            $explodeSorting = explode(' ', $_SESSION['order_by']);
            if($innerFieldMeta == 'gallery')    $explodeSorting[0] = 'count-'.$explodeSorting[0];
            
            $sqlOrderValue = 'TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(EntryMeta.key_value, "{#}'.$explodeSorting[0].'=", -1), "{#}", 1))';
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
            
            $options['order'] = array('CASE WHEN EntryMeta.key_value LIKE "%{#}'.$explodeSorting[0].'=%" THEN '.$sqlOrderValue.' ELSE NULL END '.$explodeSorting[1]);
        }
        else 
        {
            $options['order'] = array('Entry.'.(isset($innerFieldMeta)||empty($_SESSION['order_by'])||empty($this->request->params['admin'])?$this->generalOrder:$_SESSION['order_by']));
        }
        
        if(strpos( serialize($options) , 'EntryMeta.key_value') !== FALSE)
		{
            $options['joins'] = array(array(
				'table' => '(SELECT EntryMeta.entry_id, CONCAT("{#}", GROUP_CONCAT(EntryMeta.key, "=", EntryMeta.value SEPARATOR "{#}"), "{#}") as key_value FROM cms_entry_metas as EntryMeta GROUP BY EntryMeta.entry_id)',
	            'alias' => 'EntryMeta',
	            'type' => 'LEFT',
	            'conditions' => array('Entry.id = EntryMeta.entry_id')
			));
		}
        
        // ========================================= >>
		// EXECUTE MAIN QUERY !!
		// ========================================= >>
        $data['totalList'] = $this->Entry->find('count' ,$options);
        if($paging >= 1)
        {
            $options['limit'] = $countPage;
            $options['page'] = $paging;
        }
		$data['myList'] = array_map('breakEntryMetas', $this->Entry->find('all' ,$options));
        
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
            if(empty($lang_code) && !empty($myEntry) && substr($myEntry['Entry']['lang_code'], 0,2) != $this->request->data['language'])
			{
				$myEntry = $this->Entry->findByLangCode($this->request->data['language'].substr($myEntry['Entry']['lang_code'], 2));
			}	
			// PREPARE DATA !!	
			$this->request->data['Entry']['title'] = $this->request->data['Entry'][0]['value'];
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
					$state = 0;					
					foreach ($myAutomaticValidation as $key2 => $value2) // check for validation for each attribute key... 
					{
						if($value['key'] == $value2['key']) // if find the same key...
						{
							$state = 1;
							$myValid = explode('|' , $value2['validation']);
							foreach ($myValid as $key3 => $value3) 
							{
								$tempMsg = $this->Validation->blazeValidate($value['value'],$value3 , $value['key']);
								$errMsg .= ( strpos($errMsg, $tempMsg) === FALSE ?$tempMsg:"");
							}
							break;
						}
					}
					
					// if attribute key doesn't exist in type metas, therefore it must be added to type metas respectively...
					if($state == 0 && !empty($value['input_type']) && empty($lang_code))
					{
						$this->request->data['TypeMeta'] = $value;
						$this->request->data['TypeMeta']['type_id'] = (empty($myEntry)?$myType['Type']['id']:$myChildType['Type']['id']);
						$this->request->data['TypeMeta']['value'] = $value['optionlist'];
						$this->TypeMeta->create();
						$this->TypeMeta->save($this->request->data);
					}
				}
				// LAST CHECK ERROR MESSAGE !!
				if(!empty($errMsg))
				{
					$this->Session->setFlash($errMsg,'failed');
					return;
				}
				// ------------------------------------- end of entry details...
				$this->Entry->create();
				$this->Entry->save($this->request->data);
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
						$this->EntryMeta->create();
						$this->EntryMeta->save($this->request->data);
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
        
        note 2: pada saat bayar invoice, jikalau ada cash amount + payment jewelry dlm transaksi tsb,
                maka sistem otomatis langsung buat 2 record payment (1 untuk payment original input,
                dan 1 untuk payment return goods)...
        */
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
            $tempChild = $this->Entry->find('all' , array(
	            'conditions' => array(
	                'Entry.parent_id' => $myEntry['Entry']['id']
	            ),
	            'order' => array('Entry.'.$this->generalOrder )
	        ));
	        
	        foreach ($tempChild as $key => $value) 
        	{
        		$tempChild[$key] = breakEntryMetas($value);
        	}

	        $data['myEntry']['ChildEntry'] = $tempChild;
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
