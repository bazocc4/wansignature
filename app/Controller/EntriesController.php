<?php
class EntriesController extends AppController {
	public $name = 'Entries';
    public $components = array('RequestHandler','Session','Validation','Auth');
	public $helpers = array('Form', 'Html', 'Js', 'Time', 'Get','Paypal','Text','Rss');
	
	private $frontEndFolder = '/FrontEnds/';
	private $backEndFolder = '/BackEnds/';
    private $generalOrder = 'sort_order DESC';
	private $onlyActiveEntries = FALSE; // if it's in admin panel, show active/disabled, and if it's on the front, show only active pages !!
	
	public function beforeFilter(){
        parent::beforeFilter();
		$this->Auth->allow('index','get_list_entry','get_detail_entry');
    }
	
	/**
	 * fork our target routes for entry view (pages, entry, list of entries) in Front End web
	 * FYI : (Front End Default URL Structure)
	 * en.domainmu.com		-->		Home web pages (auto detect for language selection)
	 * www.domainmu.com/en	-->		--//--
	 * www.domainmu.com		-->		--//--
	 * filename: home.ctp
	 * 
	 * en.domainmu.com/about-us							-->		view pages
	 * filename: about-us.ctp (always parent language slug) !!
	 * 
	 * en.domainmu.com/books/							-->		view all lists of books entries
	 * en.domainmu.com/books/3							-->		same as above, but that is in page 3
	 * en.domainmu.com/books/?key=author&value=abas		-->		same as above, but only entries that have certain key and certain value defined (value in URL must be already slugged)
	 * filename: books.ctp
	 * 
	 * en.domainmu.com/books/sport				-->		view the detail of the 'Sport' book
	 * filename: books_detail.ctp
	 * 
	 * en.domainmu.com/books/sport/?type=news 	-->		view all child lists from certain parent entry and certain entry type
	 * en.domainmu.com/books/sport/3?type=news						-->	same as above, but that is in page 3
	 * en.domainmu.com/books/sport/?type=news&key=author&value=abas	-->	same as above, but only entries that have certain key and certain value defined
	 * filename: news.ctp
	 *
	 * en.domainmu.com/books/sport/easy-bicycle		-->	view the detail of the 'Easy Bicycle' entry
	 * filename: news_detail.ctp
	 * @return void
	 * @public
	 **/
	function index() // front End view !!
	{
		// dpr($this->request->params);
		// exit;

		$result = '';
		$myRenderFile = '';
		$myDetailEntryMarkFile = 'detail';
		$this->onlyActiveEntries = TRUE;
		
        $temp_lang = $this->Entry->get_lang_url($this->RequestHandler->isRss()&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:NULL);
		$language = $temp_lang['language'];
		$indent = $temp_lang['indent'];

		if( $this->RequestHandler->isRss() )
		{
			App::uses('Sanitize', 'Utility');
			$myList = array();
			$nowType = $this->Type->findBySlug($this->request->params['pass'][$indent]);

			// if certain type not found, and then query all types for rss...
			if(empty($nowType))
			{
				// view all the Type, but not Child !!
				$myTypes = $this->Type->find('all',array(
					'conditions' => array(
						'Type.parent_id' => 0
					),
					'order' => array('Type.id')
				));
				
				foreach ($myTypes as $key => $value) 
				{
					if($value['Type']['slug'] == 'media')	{continue;}
					$tempdata = $this->_admin_default( $value , 1,NULL,NULL,NULL,NULL,NULL,NULL,$language,'manualset');
					$myList = array_merge($myList , $tempdata['myList']);
				}
			}
			else
			{
				$tempdata = $this->_admin_default( $nowType , 1,NULL,NULL,NULL,NULL,NULL,NULL,$language,'manualset');
				$myList = array_merge($myList , $tempdata['myList']);
			}
			
			// sort the feed !!
			$myList = orderby_metavalue($myList, 'Entry' , 'created' , 'DESC');
	        return $this->set('myList' , $myList);
	    }
		else if($this->request->is('ajax'))
		{
			$this->layout = 'ajax';
		}
		else // our frontEnd layout !!
		{
			$this->layout = 'frontend';
		}

		// this is for redirecting home !!
		if(empty($this->request->params['pass'][$indent]))
		{
			$thisIsHomeUrl = true;
			$this->request->params['pass'][$indent] = 'home';
		}
		else if(strtolower($this->request->params['pass'][$indent]) == 'home')
		{
			$this->redirect('/'.(empty($indent)?'':$language.'/'));
		}
		
		// ---------------------------------- >>>
		// additional set to view file !!
		// ---------------------------------- >>>
		$this->set('language' , $language);

		// set additional url_lang !!
		$url_lang = "";
		if(substr(strtolower($this->mySetting['language'][0]), 0,2) != $language)
		{
			$url_lang = $language.'/';
		}
		$this->set('url_lang' , $url_lang);
		// ---------------------------------- >>>
		// end of additional set to view file !!
		// ---------------------------------- >>>
		
		// Tree of division beginsss !!
		if(empty($this->request->params['pass'][$indent+1]))
		{
			// if this want to list all entries...
			if(substr($this->request->url, strlen($this->request->url)-1) == '/' && $this->request->params['pass'][$indent+0] != 'home')
			{
				$myTypeSlug = $this->request->params['pass'][$indent+0];
				$myType = $this->Type->findBySlug($myTypeSlug);                
                // check if Type has pagination field, then redirect to its first page ...
                foreach($myType['TypeMeta'] as $key => $value){
                    if($value['key'] == 'pagination')
                    {
                        $this->redirect('/'.$this->request->url.'1'.get_more_extension($this->request->query));
                    }
                }
				$result = $this->_admin_default($myType, 0 , NULL , $this->request->query['key'] , $this->request->query['value'] ,NULL,$this->request->data['search'],NULL, $language);
				$myRenderFile = $myTypeSlug;
			}
			else // if this want to view pages...
			{
				$myEntrySlug = $this->Entry->_convertEntrySlug($this->request->params['pass'][$indent+0]);
				$myEntry = $this->meta_details($myEntrySlug , 'pages');

				// other language version of "homepage"
				if($myEntrySlug == 'home' && substr(strtolower($this->mySetting['language'][0]), 0,2) != $language)
				{
					$myEntry = $this->Entry->findByLangCode( $language.'-'.$myEntry['Entry']['id'] );
				}
				
				$tempdata = array();
				swap_value($tempdata, $this->request->data);
				$result = $this->_admin_default_edit(NULL , $myEntry);
				swap_value($tempdata, $this->request->data);

				if($myEntrySlug == 'home')
				{
					// load slider data !!
					$slideshow = $this->_admin_default( $this->Type->findBySlug('slideshow') , 0 , NULL , NULL , NULL ,NULL,NULL,NULL, $language , 'manualset');
					$this->set('slideshow', $slideshow['myList']);
				}
				else if($myEntrySlug == 'contact')
				{
					// if submit contact form !!
	            	if(isset($_POST['submitcontact']))
					{
						$this->submit_contact();
					}
				}
				else if($myEntrySlug == 'search')
				{
					// forbid access page without params !!
					if(empty($this->request->data))
					{
						$this->redirect('/'.(empty($indent)?'':$language.'/'));
					}

					$globalresult = array();
					$search_types = array('slideshow'); // array of module to be searched

					foreach ($search_types as $key => $value) 
					{
						$tempresult = $this->_admin_default( $this->Type->findBySlug( $value ) , 0, NULL, NULL, NULL, NULL, $this->request->data['search'] ,NULL, $language , 'manualset');

						$globalresult = array_merge($globalresult, $tempresult['myList']);
					}

					// RENEW THE RESULT !!
					$result['myList'] = orderby_metavalue( $globalresult , 'Entry', 'modified' , 'DESC');
					$result['totalList'] = count($result['myList']);
					$this->set('data' , $result);
				}
				else
				{
					if(empty($myEntry))
					{						
						$this->redirect(redirectSessionNow($_SERVER['REQUEST_URI'].'/'));
					}
				}
				
				// convert render file name to its parent language !!
				if(substr(strtolower($this->mySetting['language'][0]), 0,2) != $language)
				{
					$entry_lang_parent = $this->Entry->findById( substr($myEntry['Entry']['lang_code'], 3) );
					if(!empty($entry_lang_parent))
					{
						$myEntrySlug = $entry_lang_parent['Entry']['slug'];
					}
				}
				$myRenderFile = $myEntrySlug;
			}
		}
		else if(empty($this->request->params['pass'][$indent+2]))
		{
			// if this want to view all child list from certain parent Entry...
			if(substr($this->request->url, strlen($this->request->url)-1) == '/')
			{
				$myTypeSlug = $this->request->params['pass'][$indent+0];
				$myType = $this->Type->findBySlug($myTypeSlug);
								
				$myEntrySlug = $this->Entry->_convertEntrySlug($this->request->params['pass'][$indent+1]);
				$myEntry = $this->meta_details($myEntrySlug , $myTypeSlug);
				
				$result = $this->_admin_default($myType, 0 , $myEntry , $this->request->query['key'], $this->request->query['value'], $this->request->query['type'] , $this->request->data['search'],NULL, $language);
                // check if ChildType has pagination field, then redirect to its first page ...
                foreach($result['myChildType']['TypeMeta'] as $key => $value){
                    if($value['key'] == 'pagination')
                    {
                        $this->redirect('/'.$this->request->url.'1'.get_more_extension($this->request->query));
                    }
                }                
				$myRenderFile = $this->request->query['type'];
			}
			else
			{
				$myTypeSlug = $this->request->params['pass'][$indent+0];
				$myType = $this->Type->findBySlug($myTypeSlug);
				// if this want to list all entries with paging limitation
				if(is_numeric($this->request->params['pass'][$indent+1]))
				{					
					$myPaging = $this->request->params['pass'][$indent+1];
					$result = $this->_admin_default($myType, $myPaging , NULL , $this->request->query['key'] , $this->request->query['value'] ,NULL,$this->request->data['search'],NULL, $language);
					$myRenderFile = $myTypeSlug;
				}
				else // if this want to view details of the entry...
				{										
					$myEntrySlug = $this->Entry->_convertEntrySlug($this->request->params['pass'][$indent+1]);
					$myEntry = $this->meta_details($myEntrySlug , $myTypeSlug);
					
					$tempdata = array();
					swap_value($tempdata, $this->request->data);
					$result = $this->_admin_default_edit($myType , $myEntry);
					swap_value($tempdata, $this->request->data);
					
					$myRenderFile = $myEntry['Entry']['entry_type'].'_'.$myDetailEntryMarkFile;
				}
			}
		}
		else // MAX LEVEL...
		{
			$myTypeSlug = $this->request->params['pass'][$indent+0];
			$myType = $this->Type->findBySlug($myTypeSlug);			
			
			$myParentEntrySlug = $this->Entry->_convertEntrySlug($this->request->params['pass'][$indent+1]);
			$myParentEntry = $this->meta_details($myParentEntrySlug , $myTypeSlug);
			// if this want to list all CHILD entries with paging limitation
			if(is_numeric($this->request->params['pass'][$indent+2]))
			{					
				$myPaging = $this->request->params['pass'][$indent+2];
				$result = $this->_admin_default($myType, $myPaging , $myParentEntry , NULL, NULL, $this->request->query['type'], $this->request->data['search'],NULL, $language);
				$myRenderFile = $this->request->query['type'];
			}
			else // if this want to view details of the child entry...
			{				
				$myEntrySlug = $this->Entry->_convertEntrySlug($this->request->params['pass'][$indent+2]);
				$myEntry = $this->meta_details($myEntrySlug , NULL , $myParentEntry['Entry']['id']);
				
				$tempdata = array();
				swap_value($tempdata, $this->request->data);
				$result = $this->_admin_default_edit($myType , $myEntry , $myParentEntry , $myEntry['Entry']['entry_type']);
				swap_value($tempdata, $this->request->data);
				
				$myRenderFile = $myEntry['Entry']['entry_type'].'_'.$myDetailEntryMarkFile;
			}
		}
		
		// SAVE TO SHOPPING CART IF THIS IS FORM SUBMIT !!
		if($_POST['type'] == 'addtocart')
		{
			$temp['item_number'] = $_POST['item_number'];
			$temp['quantity'] = $_POST['quantity'];
			$duplicateProduct = false;
			if(empty($_SESSION['shoppingcart']))
			{
				$_SESSION['shoppingcart'] = array();
			}
			else
			{
				foreach ($_SESSION['shoppingcart'] as $key => $value) 
				{
					if($value['item_number'] == $temp['item_number'])
					{
						$duplicateProduct = true;
						$_SESSION['shoppingcart'][$key] = $temp;
						break;
					}
				}
			}
			
			if(!$duplicateProduct)
			{
				array_push($_SESSION['shoppingcart'] , $temp);
			}
			$this->Session->setFlash('Shopping Cart has been added. Please click <a href="'.$this->get_host_name().'shoppingcart/step1">Shopping Cart</a> menu for details.','success');
		}
		// END OF SHOPPING CART !!

		$this->onlyActiveEntries = FALSE;		
		$this->setTitle(!empty($result['myChildType'])? $result['myChildType']['Type']['name'] : (!empty($result['myType'])? $result['myType']['Type']['name'] : ($thisIsHomeUrl?'':$myEntry['Entry']['title']) ) );
		$this->render($this->frontEndFolder.$myRenderFile);

		// on detail entity page, is it allow using $_SERVER['HTTP_REFERER'] page to go back onto its master page ...
		$_SESSION['allowRefererURL'] = isset($result['myList']);
	}

	function submit_contact()
    {
    	$result = array();
        $valid = true;

        # check reCAPTCHA response!
		App::import('Vendor', 'recaptchalib');
		$resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY,
            $_SERVER["REMOTE_ADDR"],
            $_POST["recaptcha_challenge_field"],
            $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) 
        {
        	# set the error code so that we can display it
            $this->set('recaptcha_error' , $resp->error );
            $valid = false;
        }
        
        $result['success'] = 0;
        if($valid)
        {
            // prepare the email...
			App::uses('CakeEmail', 'Network/Email');
		    $Email = new CakeEmail();

		    $mybody = "<strong>Message from ".$this->mySetting['title']." Website Guest</strong><br/><br/>";
            $mybody .= "Name : ".$_POST['namecontact']."<br/>";
            // Upload File !!
            if(!empty($_FILES['filecontact']['tmp_name']))
            {
                $Email->attachments(array(
                    $_FILES['filecontact']['name'] => array(
                        'file' => $_FILES['filecontact']['tmp_name'],
                        'mimetype' => $_FILES['filecontact']['type']
                    )
                ));
                
                $mybody .= "Reference Image Upload : (attached)<br/>";
            }
            $mybody .= "<br/>Content :<br/>".nl2br($_POST['pesancontact'])."<br/>";

            // Execute E-mail ...
			try{
				if( $Email->from(array($_POST['emailcontact']=>$_POST['namecontact']))
			          ->to( array_map("trim" , explode(',' , $this->mySetting['custom-email_contact'] )) )
			          ->subject(html_entity_decode($this->mySetting['title'], ENT_COMPAT, 'UTF-8')." - Contact Message")
			          ->emailFormat('html')
			          ->template('default','default')
			          ->send($mybody) )
				{
					$result['success'] = 1;
				}
				else // Failure, without any exceptions
				{
					$result['success'] = -1;
				}
			} catch(Exception $e){
				// Failure, with exception
				$result['success'] = -2;
			}
        }
        
        $this->set('contact', $result);
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
					$testImage = $this->TypeMeta->find('first' , array(
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
			$myEntry = $this->Entry->findBySlug($this->request->params['entry']);
			if(!empty($this->request->query['type']))
			{				
				$myChildTypeSlug = $this->request->query['type'];
			}
			else 
			{
				$myChildTypeSlug = $myType['Type']['slug'];
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
				$this->Session->setFlash('Your selection data status has been <strong>activated</strong> successfully.','success');
			}
			else if($this->request->data['action'] == 'disable')
			{
				foreach ($pecah as $key => $value) 
				{
					$this->change_status($value, 0 , 'localcall');
				}
				$this->Session->setFlash('Your selection data status has been <strong>disabled</strong> successfully.','success');
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
		$this->_admin_default($myType , $this->request->params['page'] , $myEntry , $this->request->query['key'] , $this->request->query['value'] , $myChildTypeSlug , $this->request->data['search_by'] , $this->request->query['popup'] , strtolower($this->request->query['lang']));
		$myTypeSlug = (empty($myChildTypeSlug)?$myType['Type']['slug']:$myChildTypeSlug);
		
		// send to each appropriate view
		$str = substr(WWW_ROOT, 0 , strlen(WWW_ROOT)-1); // buang DS trakhir...
		$str = substr($str, 0 , strripos($str, DS)+1); // buang webroot...
		$src = $str.'View'.str_replace('/', DS, $this->backEndFolder).$myTypeSlug.'.ctp';
		
		if(file_exists($src))
		{
			$this->render($this->backEndFolder.$myTypeSlug);
		}
		else
		{
			$this->render('admin_default');
		}
	}

	/**
	* target route for adding new entry (stagging mode)
	* @return void
	* @public
	**/
	function index_add()
	{
		$this->layout = 'frontend';
		$myType = $this->Type->findBySlug($this->request->params['type']);	
		// if this action is going to add CHILD list...
		if(!empty($this->request->params['entry']))
		{
			$myEntry = $this->Entry->findBySlug($this->request->params['entry']);
			if(!empty($this->request->query['type']))
			{				
				$myChildTypeSlug = $this->request->query['type'];
			}
			else 
			{
				$myChildTypeSlug = $myType['Type']['slug'];
			}
		}
		$status = 0;
		$nowType = (empty($myChildTypeSlug)?$myType:$this->Type->findBySlug($myChildTypeSlug));
		foreach ($nowType['TypeMeta'] as $key => $value) 
		{
			if($value['key'] == 'stagging' && $value['value']=='enable')
			{
				$status = 1;
				break;
			}
		}
		if($status == 0)
		{
			throw new NotFoundException('Error 404 - Not Found'); 
			return;
		}
		// main add function ...
		$this->_admin_default_add($myType , $myEntry , $myChildTypeSlug);
		
		$myTemplate = (empty($myChildTypeSlug)?$myType['Type']['slug']:$myChildTypeSlug).'_add';		
		// send to each appropriate view
		$str = substr(WWW_ROOT, 0 , strlen(WWW_ROOT)-1); // buang DS trakhir...
		$str = substr($str, 0 , strripos($str, DS)+1); // buang webroot...
		$src = $str.'View'.str_replace('/', DS, $this->frontEndFolder).$myTemplate.'.ctp';
		
		// add / edit must use the same view .ctp, but with different action !!
		if(file_exists($src))
		{
			$this->render($this->frontEndFolder.$myTemplate);
		}
		else
		{
			$this->render('default_add');
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
			$myEntry = $this->Entry->findBySlug($this->request->params['entry']);
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
		
		$myTemplate = ($myType['Type']['slug']=='pages'?$myEntry['Entry']['slug']:(empty($myChildTypeSlug)?$myType['Type']['slug']:$myChildTypeSlug)).'_add';
		
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
	* target route for editing certain entry based on passed url parameter (stagging mode)
	* @return void
	* @public
	**/
	function index_edit()
	{
		$this->layout = 'frontend';
		
		$myType = $this->Type->findBySlug($this->request->params['type']);
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
			$myParentEntry = $this->Entry->findBySlug($this->request->params['entry_parent']);
			if(!empty($this->request->query['type']))
			{				
				$myChildTypeSlug = $this->request->query['type'];
			}
			else 
			{
				$myChildTypeSlug = $myType['Type']['slug'];
			}
		}
		$status = 0;
		$nowType = (empty($myChildTypeSlug)?$myType:$this->Type->findBySlug($myChildTypeSlug));
		foreach ($nowType['TypeMeta'] as $key => $value) 
		{
			if($value['key'] == 'stagging' && $value['value']=='enable')
			{
				$status = 1;
				break;
			}
		}
		if($status == 0)
		{
			throw new NotFoundException('Error 404 - Not Found'); 
			return;
		}
		// main edit function ...
		$this->_admin_default_edit($myType , $myEntry , $myParentEntry , $myChildTypeSlug , strtolower($this->request->query['lang']));
		
		$myTemplate = (empty($myChildTypeSlug)?$myType['Type']['slug']:$myChildTypeSlug).'_add';		
		// send to each appropriate view
		$str = substr(WWW_ROOT, 0 , strlen(WWW_ROOT)-1); // buang DS trakhir...
		$str = substr($str, 0 , strripos($str, DS)+1); // buang webroot...
		$src = $str.'View'.str_replace('/', DS, $this->frontEndFolder).$myTemplate.'.ctp';
		
		// add / edit must use the same view .ctp, but with different action !!
		if(file_exists($src))
		{
			$this->render($this->frontEndFolder.$myTemplate);
		}
		else
		{
			$this->render('default_add');
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
			$myParentEntry = $this->Entry->findBySlug($this->request->params['entry_parent']);
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
		
		$myTemplate = ($myType['Type']['slug']=='pages'?$myEntry['Entry']['slug']:(empty($myChildTypeSlug)?$myType['Type']['slug']:$myChildTypeSlug)).'_add';		
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
	* get a bunch of entries based on parameter given
	* @param string $myTypeSlug contains slug database type
	* @param string $myEntrySlug[optional] contains slug of the parent Entry (used if want to search certain child Entry)
	* @param string $myChildTypeSlug[optional] contains slug of child type database (used if want to search certain child Entry)
	* @return void echoing json result
	* @public
	**/
	function get_list_entry($myTypeSlug , $myEntrySlug = NULL , $myChildTypeSlug = NULL)
	{
		$this->autoRender = FALSE;
		if($myTypeSlug == 'pages')
		{
			// manually set pages data !!
			$myType['Type']['name'] = 'Pages';
			
			$myType['Type']['slug'] = 'pages';
			$myType['Type']['parent_id'] = 0;
		}
		else
		{
			$myType = $this->Type->findBySlug($myTypeSlug);
		}
		$myEntry = (empty($myEntrySlug)?NULL:$this->meta_details($myEntrySlug , $myType['Type']['slug']));
		
		$this->onlyActiveEntries = TRUE;
		$json = $this->_admin_default($myType , 0 , $myEntry , NULL , NULL , $myChildTypeSlug);
		$this->onlyActiveEntries = FALSE;
		
		echo json_encode($json);
	}
	
	/**
	* get specific entry from entry lists based on entry id 
	* @param integer $myEntryId contains id of the entry
	* @return void echoing json result
	* @public
	**/
	function get_detail_entry($myEntryId)
	{
		$this->autoRender = FALSE;
		$myEntry = $this->Entry->findById($myEntryId);
		
		// if this is a child Entry...
		if($myEntry['Entry']['parent_id'] > 0)
		{
			$myParentEntry = $this->Entry->findById($myEntry['Entry']['parent_id']);			
			$myType = $this->Type->findBySlug($myParentEntry['Entry']['entry_type']); // PARENT TYPE...
			
			$myChildTypeSlug = $myEntry['Entry']['entry_type'];
		}
		else // if this is a parent Entry ...
		{
			$myType = $this->Type->findBySlug($myEntry['Entry']['entry_type']);
		}
		
		$tempdata = array();
		swap_value($tempdata, $this->request->data);
		$json = $this->_admin_default_edit($myType , $myEntry , $myParentEntry , $myChildTypeSlug);
		swap_value($tempdata, $this->request->data);
		
		echo json_encode($json);
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
			$_SESSION['lang'] = strtolower(empty($lang)?(empty($_SESSION['lang'])||empty($this->request->params['admin'])?substr($this->mySetting['language'][0], 0,2):$_SESSION['lang']):$lang);
		} 
		else 
		{
			$data['isAjax'] = 0;
			unset($_SESSION['searchMe']);
			$_SESSION['lang'] = strtolower(empty($lang)?substr($this->mySetting['language'][0], 0,2):$lang);
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
                if(stripos($_SESSION['order_by'] , $value['key'] ) !== FALSE)
                {
                    $innerFieldMeta = $value['input_type'];
                    break;
                }                    
            }
            if(!$innerFieldMeta)
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
		
		// our list conditions... ----------------------------------------------------------------------------------////
		$joinEntryMeta = false;
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

		if($this->onlyActiveEntries)
		{
			$options['conditions']['Entry.status'] = 1;
		}

		if($myType['Type']['slug'] != 'media')
		{
			$options['conditions']['Entry.lang_code LIKE'] = $_SESSION['lang'].'-%';
			$data['language'] = $_SESSION['lang'];
		}

		if( !empty($myMetaKey) )
		{
			$joinEntryMeta = true;            
            if(!empty($myMetaValue))
            {
                $options['conditions']['SUBSTR(EntryMeta.key , 6)'] = $myMetaKey;
                $options['conditions']['REPLACE(REPLACE(EntryMeta.value , "-" , "_"),"_"," ") LIKE'] = '%'.string_unslug($myMetaValue).'%';
            }
            else
            {
                $options['conditions']['NOT'] = array(
                    array('SUBSTR(EntryMeta.key , 6)' => $myMetaKey)
                );
            }
		}

		// ========================================= >>
		// NEW FUNCTION JOIN !!
		// ========================================= >>
		if(!empty($_SESSION['searchMe']))
		{	
			if(empty($options['conditions']['OR']))
			{
				$options['conditions']['OR'] = array();
			}
			array_push($options['conditions']['OR'] , array('Entry.title LIKE' => '%'.$_SESSION['searchMe'].'%') );
			array_push($options['conditions']['OR'] , array('Entry.description LIKE' => '%'.$_SESSION['searchMe'].'%') );
			array_push($options['conditions']['OR'] , array('ParentEntry.title LIKE' => '%'.$_SESSION['searchMe'].'%') );
			if($this->mySetting['table_view']=='complex')
			{
				$joinEntryMeta = true;
				array_push($options['conditions']['OR'] , array('REPLACE(REPLACE(EntryMeta.value , "-" , "_"),"_"," ") LIKE' => '%'.string_unslug($_SESSION['searchMe']).'%') );
			}
		}

		if($joinEntryMeta)
		{
			$options['joins'] = array(array(
				'table' => 'entry_metas',
	            'alias' => 'EntryMeta',
	            'type' => 'LEFT',
	            'conditions' => array(
	                'Entry.id = EntryMeta.entry_id'
	            )
			));
			$options['group'] = array('Entry.id');
		}

		// ========================================= >>
		// FIND LAST MODIFIED !!
		// ========================================= >>
		$tempOpt = $options;
		$tempOpt['order'] = array('Entry.modified DESC');
		$lastModified = $this->Entry->find('first' , $tempOpt);
		$data['lastModified'] = $lastModified;		
		
		// ================================================================ >>
		// check for description or image is used for this entry or not ??
		// ================================================================ >>
		$tempOpt = $options;
		$tempOpt['conditions']['LENGTH(Entry.description) >'] = 0;
		$checkSQL = $this->Entry->find('first' , $tempOpt);
		$data['descriptionUsed'] = (empty($checkSQL)?0:1);
		
		$tempOpt = $options;
		$tempOpt['conditions']['Entry.main_image >'] = 0;
		$checkSQL = $this->Entry->find('first' , $tempOpt);		
		$data['imageUsed'] = (empty($checkSQL)?0:1);

		// ========================================= >>
		// EXECUTE MAIN QUERY !!
		// ========================================= >>
		$options['order'] = array('Entry.'.(isset($innerFieldMeta)||empty($_SESSION['order_by'])||empty($this->request->params['admin'])?$this->generalOrder:$_SESSION['order_by']));
		$mysql = $this->Entry->find('all' ,$options);
		
		// MODIFY OUR ENTRYMETA FIRST !!		
		foreach ($mysql as $key => $value) 
		{
			$mysql[$key] = $value = breakEntryMetas($value);
			// ----------------------------------------- >>>
            // ADDITIONAL FILTERING METHOD !!
            // ----------------------------------------- >>>
            if(FALSE)
            {
                unset($mysql[$key]);
                continue;
            }
			// ----------------------------------------- >>>
            // END OF ADDITIONAL FILTERING METHOD !!
            // ----------------------------------------- >>>
		}
		$mysql = array_values($mysql);
		
		// Final Sort based on certain criteria !!
        if($innerFieldMeta)
        {
            $explodeSorting = explode(' ', $_SESSION['order_by']);
            $mysql = orderby_metavalue( $mysql , 'EntryMeta', substr($explodeSorting[0] , 5) , $explodeSorting[1] , $innerFieldMeta );
        }
        
		// SECOND FILTER GO NOW !!!
		$offset = ($paging==0? 0 : ($paging-1) * $countPage);
		$endset = $offset + $countPage;				
		$data['totalList'] = count($mysql);
		$data['myList'] = array();
		for($key = $offset ; !empty($mysql[$key]) ; ++$key)
		{
			if(!($key < $endset || $paging==0))
			{
				break;
			}
			array_push($data['myList'] , $mysql[$key]);
		}

		// set New countPage
		$newCountPage = ceil($data['totalList'] / $countPage);
		$data['countPage'] = $newCountPage;
		
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
		
		// IS ALLOWING ORDER CHANGE OR NOT ??
		$data['isOrderChange'] = (empty($_SESSION['order_by']) || substr($_SESSION['order_by'], 0 , 10) == 'sort_order'?1:0);
		
		// --------------------------------------------- LANGUAGE OPTION LINK ------------------------------------------ //
		if(!empty($myEntry))
		{
			$temp100 = $this->Entry->find('all' , array(
				'conditions' => array(
					'Entry.lang_code LIKE' => '%-'.substr($myEntry['Entry']['lang_code'], 3)
				)
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
		if(!empty($myEntry))
		{
			$temp100 = $this->Entry->find('all' , array(
				'conditions' => array(
					'Entry.lang_code LIKE' => '%-'.substr($myEntry['Entry']['lang_code'], 3)
				)
			));
			foreach ($temp100 as $key => $value) 
			{
				$parent_language[ substr($value['Entry']['lang_code'], 0,2) ] = $value['Entry']['slug'];
			}
			$data['parent_language'] = $parent_language;
		}
		$data['lang'] = strtolower(empty($myEntry)?(empty($_SESSION['lang'])? substr($this->mySetting['language'][0], 0,2):$_SESSION['lang']):substr($myEntry['Entry']['lang_code'], 0,2));
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
							$this->request->data['EntryMeta']['value'] = implode('|', array_unique(array_filter($value['value'])));
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
    
    function _add_update_id_meta($myTypeSlug , $myChildTypeSlug = NULL , $myParentEntry = array() , $myEntry = array())
	{
		// $this->request->data['EntryMeta']['entry_id'] => not needed to be set, coz it's already set in parent function !!
        $this->request->data = breakEntryMetas($this->request->data);
        $this->request->data['Entry']['id'] = $this->request->data['EntryMeta']['entry_id'];
        $this->request->data['imagePath'] = $this->get_linkpath();
        
        // ADDITIONAL FUNCTION HERE AFTER INSERT / UPDATE RECORD !!
        // ...............
        // ===================================================== >>
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
		if(!empty($myParentEntry))
		{
			$temp100 = $this->Entry->find('all' , array(
				'conditions' => array(
					'Entry.lang_code LIKE' => '%-'.substr($myParentEntry['Entry']['lang_code'], 3)
				)
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
                // generate slug from title if title has changed...
                if(strtolower($this->request->data['Entry']['title']) != strtolower($myEntry['Entry']['title']))
                {
                    $this->request->data['Entry']['slug'] = $this->get_slug($this->request->data['Entry']['title']);
                }
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
									$this->request->data['EntryMeta']['value'] = implode('|', array_unique(array_filter($value['value'])));
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
				unlink(WWW_ROOT.'img'.DS.'upload'.DS.'thumb'.DS.$myid.'.'.$mytype);

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
			
			//Resize original file for thumb...
			$this->Entry->createThumb($myid , $mytype , $myMediaSettings);
			
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
