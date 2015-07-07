<?php
	$this->Get->create($data);
	if(is_array($data)) extract($data , EXTR_SKIP);

    // is it Diamond or Cor Jewelry payment ?
    $DMD = (strpos($myType['Type']['slug'], 'dmd-')!==FALSE?true:false);

    // is it Vendor or Client payment ?
    $VENDOR = (strpos($myType['Type']['slug'], '-vendor-')!==FALSE?true:false);

	if($isAjax == 0)
	{
		echo $this->element('admin_header_add');
		?>
		<script>
			$(document).ready(function(){
				// disable language selector ONLY IF one language available !!
				if($('div.lang-selector ul.dropdown-menu li').length <= 1)
				{
					$('div.lang-selector').hide();
				}

				// focus on anchor query url IF ANY ...
				<?php if(!empty($this->request->query['anchor'])): ?>
					$('div#form-<?php echo $this->request->query['anchor']; ?>').prevAll('a.get-from-library:first').focus();
				<?php endif; ?>
                
                // Hide main_image !!
                $('div.thumbs').hide();
                $('div.change-pic').hide();
			});
		</script>
		<?php
		echo '<div id="ajaxed" class="inner-content">';
	}
	else 
	{
		?>
		<script>
			$(document).ready(function(){
				$('#cmsAlert').css('display' , 'none');
			});
		</script>
		<?php
	}
	$myChildTypeLink = (!empty($myParentEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?'?type='.$myChildType['Type']['slug']:'');
	$myTranslation = ( empty($lang)||empty($myEntry) ? '' : (empty($myChildTypeLink)?'?':'&').'lang='.$lang);
	$targetSubmit = (empty($myType)?'pages':$myType['Type']['slug']).(empty($myChildType)?'':'/'.$myParentEntry['Entry']['slug']).(empty($myEntry)?'/add':'/edit/'.$myEntry['Entry']['slug']).$myChildTypeLink.$myTranslation;
	$saveButton = (empty($myEntry)?'Add New as Complete Payment':(empty($lang)?'Save Changes':'Add Translation'));
	echo $this->Form->create('Entry', array('action'=>$targetSubmit,'type'=>'file','class'=>'notif-change form-horizontal fl','inputDefaults' => array('label' =>false , 'div' => false)));	
?>
	<fieldset>
		<script>
			$(document).ready(function(){
				if($('p#id-title-description').length > 0)
				{
					$('p#id-title-description').html('Last updated by <a href="#"><?php echo (empty($myEntry['AccountModifiedBy']['username'])?$myEntry['AccountModifiedBy']['email']:$myEntry['AccountModifiedBy']['username']).'</a> at '.date_converter($myEntry['Entry']['modified'], $mySetting['date_format'] , $mySetting['time_format']); ?>');
					$('p#id-title-description').css('display','<?php echo (!empty($lang)?'none':'block'); ?>');
				}
				
				// media sortable
				if($("div#myPictureWrapper").length > 0)
				{
					$("div#myPictureWrapper").sortable({ opacity: 0.6, cursor: 'move'});
					// print total pictures...
					$('div#myPictureWrapper').prevAll('.galleryCount:first').find('span').html( $('div#myPictureWrapper').find('div.photo').length );
				}
				
				// save as draft button !!
				$('button#save-as-draft').click(function(){
					// set last status button as draft & submit form !!
					$('select.status:last').val('0');
					$('button#save-button').click();
				});
                
                // update empty payment rate ...
                var amount_key_rate = '';
                if($('input.rp_rate').length > 0)
                {
                    amount_key_rate = 'rp_rate';
                    if($('input.rp_rate').val() == '')
                    {
                        $('input.rp_rate').val('<?php echo $myParentEntry['EntryMeta']['rp_rate']; ?>');
                    }
                }
                else if($('input.gold_price').length > 0)
                {
                    amount_key_rate = 'gold_price';
                    if($('input.gold_price').val() == '')
                    {
                        $('input.gold_price').val('<?php echo $myParentEntry['EntryMeta']['gold_price']; ?>');
                    }
                }
                else if($('input.hkd_rate').length > 0)
                {
                    amount_key_rate = 'hkd_rate';
                    if($('input.hkd_rate').val() == '')
                    {
                        $('input.hkd_rate').val('<?php echo $myParentEntry['EntryMeta']['hkd_rate']; ?>');
                    }
                }
                
                // onkeyup Amount ...
                if(amount_key_rate.length > 0)
                {
                    $('input.amount').keyup(function(){
                        var amount_rate = $('input.'+amount_key_rate).val();
                        var result = $(this).val();
                        if($.isNumeric(amount_rate) && $.isNumeric(result))
                        {
                            result = parseFloat(result) * parseFloat(amount_rate);
                            if(amount_key_rate == 'hkd_rate')
                            {
                                $('span.rate_amount').html('= $'+number_format(result,2)+' HKD');
                            }
                            else // IDR
                            {
                                $('span.rate_amount').html('= Rp. '+number_format(result) );
                            }
                        }
                        else
                        {
                            $('span.rate_amount').html('');
                        }
                    });
                }
                
                // onkeyup Additional Charge ...
                $('span.unit_additional_charge').text('<?php echo ($DMD?'USD':'gram'); ?>');
                if($('span.unit_additional_charge').text() == 'USD')
                {
                    $('span.total_additional_charge').before('$');
                }
                $('input.additional_charge').keyup(function(e,init){
                    
                    var source = 0;
                    if($('span.total_diamond').length > 0)  source += parseFloat($('span.total_diamond input[type=hidden]').val());
                    if($('span.total_cor_jewelry').length > 0)  source += parseFloat($('span.total_cor_jewelry input[type=hidden]').val());
                    if($('span.total_gold_loss').length > 0)    source += parseFloat($('span.total_gold_loss input[type=hidden]').val());
                    if($('span.additional_cost_gram').length > 0)   source += parseFloat( $('span.additional_cost_gram').text() );
                    
                    var result = ( $.isNumeric( $(this).val() ) ? source * parseFloat($(this).val()) / 100 : 0 );
                    $('span.total_additional_charge').html(number_format(result,2));
                    
                    var barter = 0;
                    if($('span.total_payment_jewelry').length > 0)  barter += parseFloat($('span.total_payment_jewelry input[type=hidden]').val());
                    
                    // update amount too ...
                    if(init == null)
                    {
                        var amount = source + result - barter;
                        $('input.amount').val(amount.toFixed(2));
                    }
                    $('input.amount').keyup();
                });
                
                // trigger keyup on some element ...
                if($('input.additional_cost').length > 0)
                {
                    $('input.additional_cost').keyup(function(e,init){
                        var ratevalue = $('input.gold_bar_rate').val();
                        var value = $(this).val();

                        if($.isNumeric(value) && $.isNumeric(ratevalue))
                        {
                            var result = (parseFloat(value) / parseFloat(ratevalue)).toFixed(2);

                            $('span.cost_rate_text').html('= <span class="additional_cost_gram">'+result+'</span> gram Gold Bar.');
                        }
                        else
                        {
                            $('span.cost_rate_text').html('');
                        }

                        // trigger additional_charge too ...
                        $('input.additional_charge').trigger('keyup', [init]);

                    }).trigger('keyup', ['init']);
                }
                
                if($('input.gold_bar_rate').length > 0)
                {
                    $('input.gold_bar_rate').keyup(function(){
                        $('input.additional_cost').keyup();
                    });
                }
                
                // onkeyup Gold Loss ...
                if($('input.gold_loss').length > 0)
                {
                    $('span.unit_gold_loss').text('gram');
                    $('input.gold_loss').keyup(function(e,init){
                        var cor = parseFloat($('span.total_cor_jewelry input[type=hidden]').val());
                        var result = ( $.isNumeric( $(this).val() ) ? cor * parseFloat($(this).val()) / 100 : 0 );
                        $('span.total_gold_loss').html(number_format(result,2)+'<input type="hidden" value="'+result+'">');

                        // trigger additional_charge too ...
                        $('input.additional_charge').trigger('keyup', [init]);
                    });
                }
			});
		</script>
		<p class="notes important" style="color: red;font-weight: bold;">* Red input MUST NOT be empty.</p>
		<input type="hidden" value="<?php echo (isset($_POST['data']['language'])?$_POST['data']['language']:(empty($lang)?substr($myEntry['Entry']['lang_code'], 0,2):$lang)); ?>" name="data[language]" id="myLanguage"/>
		<input type="hidden" value="<?php echo (isset($_POST['data']['Entry'][2]['value'])?$_POST['data']['Entry'][2]['value']:(empty($myEntry)?'0':$myEntry['Entry']['main_image'])); ?>" name="data[Entry][2][value]" id="mySelectCoverId"/>
		<input type='hidden' id="entry_image_type" value="<?php echo $myImageTypeList[isset($_POST['data']['Entry'][2]['value'])?$_POST['data']['Entry'][2]['value']:(empty($myEntry)?'0':$myEntry['Entry']['main_image'])]; ?>" />
		<?php
			$myAutomatic = (empty($myChildType)?$myType['TypeMeta']:$myChildType['TypeMeta']);
			$titlekey = "title";
			foreach ($myAutomatic as $key => $value)
			{
				if($value['key'] == 'title_key')
				{
					$titlekey = $value['value'];
					break;
				}
			}
			
			$value = array();
			$value['key'] = 'form-'.Inflector::slug($titlekey);
			$value['validation'] = 'not_empty';
			$value['model'] = 'Entry';
			$value['counter'] = 0;
			$value['input_type'] = 'text';
            $value['p'] = "Keterangan singkat mengenai transaksi ini.";
			$value['value'] = (isset($_POST['data'][$value['model']][$value['counter']]['value'])?$_POST['data'][$value['model']][$value['counter']]['value']:$myEntry[$value['model']]['title']);
			echo $this->element('input_'.$value['input_type'] , $value);

            // Our CKEditor Description Field !!
			$value = array();
			$value['key'] = 'form-'.($VENDOR?'vendor':'client').'_outstanding';
			$value['validation'] = '';
			$value['model'] = 'Entry';
			$value['counter'] = 1;
			$value['input_type'] = 'ckeditor';
			$value['value'] = (isset($_POST['data'][$value['model']][$value['counter']]['value'])?$_POST['data'][$value['model']][$value['counter']]['value']:$myEntry[$value['model']]['description']);
			echo $this->element('input_'.$value['input_type'] , $value);
		?>
		<!-- BEGIN TO LIST META ATTRIBUTES -->
		<?php
			$counter = 3;
			foreach ($myAutomatic as $key => $value)
			{
				if(substr($value['key'], 0 , 5) == 'form-')
				{
					// SPECIAL CHECK !!
					if($value['key'] == 'form-subcategory' && !empty($myEntry))
					{	
						$subcat_optvalue = $this->Get->meta_details($myEntry['EntryMeta']['category'] , 'category');
						$value['optionlist'] = $subcat_optvalue['EntryMeta']['subcategory'];
					}
					else
					{
						$value['optionlist'] = $value['value'];
					}
					unset($value['value']);

					// now get value from EntryMeta if existed !!
					foreach ($myEntry['EntryMeta'] as $key10 => $value10) 
					{						
						if($value['key'] == $value10['key'])
						{
							$value['value'] = $value10['value'];
							break;
						}
					}
					$value['model'] = 'EntryMeta';
					$value['counter'] = $counter++;
					$value['p'] = $value['instruction'];
					switch ($value['input_type']) 
					{
						case 'checkbox':
						case 'radio':
						case 'dropdown':
							$temp = explode(chr(13).chr(10), $value['optionlist']);
							foreach ($temp as $key50 => $value50) 
							{
								$value['list'][$key50]['id'] = $value['list'][$key50]['name'] = $value50;
							}
							break;
						default:
							break;
					}
                    
                    // view mode ...
                    if(!empty($myEntry))
                    {
                        $value['view_mode'] = true;
                        
                        if($value['key'] == 'form-cost_currency')
                        {
                            $value['display'] = 'none';
                        }
                    }
                    
					echo $this->element((strpos($value['key'], ($DMD?'diamond':'_jewelry') )!==FALSE?'special':'input').'_'.$value['input_type'] , $value);
				}
			}
			// HIDE THE BROKEN INPUT TYPE !!!!!!!!!!!!!
			foreach ($myEntry['EntryMeta'] as $key => $value)
			{
				if(substr($value['key'], 0 , 5) == 'form-')
				{
					$broken = 1;
					foreach ($myAutomatic as $key20 => $value20) 
					{
						if($value['key'] == $value20['key'])
						{
							$broken = 0;
							break;
						}
					}
					if($broken == 1)
					{
						$value['display'] = 'none';
						$value['model'] = 'EntryMeta';
						$value['counter'] = $counter++;
						echo $this->element('input_textarea' , $value);
					}
				}
			}
		?>		
		<!-- END OF META ATTRIBUTES -->
		
		<?php
			// show status field if update (NEW ZPANEL FEATURE) !!
			$value = array();
			$value['counter'] = 3;
			$value['key'] = 'form-status';
			$value['validation'] = 'not_empty';
			$value['model'] = 'Entry';
			$value['input_type'] = 'dropdown';
			$value['list'][0]['id'] = '1';
			$value['list'][0]['name'] = 'Complete';
			$value['list'][1]['id'] = '0';
			$value['list'][1]['name'] = 'Pending';
            
            $value['value'] = (isset($_POST['data'][$value['model']][$value['counter']]['value'])?$_POST['data'][$value['model']][$value['counter']]['value']:$myEntry[$value['model']]['status']);
			
            $value['display'] = (empty($myEntry)||empty($myType)?'none':'');
            if($value['value'] == 1)
            {
                $value['view_mode'] = true;
            }
			echo $this->element('input_'.$value['input_type'] , $value);
			
			// is used gallery function ...
            if($gallery)
            {
                echo '<strong class="galleryCount">Gallery Pictures (<span></span>)</strong>';

                $nowTypeSlug = (empty($myChildType)?$myType['Type']['slug']:$myChildType['Type']['slug']);                
                echo $this->Html->link('Add Picture',array('action'=>'media_popup_single',1,'myPictureWrapper',$nowTypeSlug,'admin'=>false),array('class'=>'btn btn-inverse fr get-from-library'));
                
                echo '<div class="inner-content pictures" id="myPictureWrapper">';
                if(!empty($this->request->data['Entry']['image']) && is_array($this->request->data['Entry']['image']) )
                {
                	foreach ($this->request->data['Entry']['image'] as $key => $value) 
                	{
                		$myImage = $this->Get->meta_details(NULL , 'media' , NULL , $value);
                		?>
                			<div class="photo">
                                <div class="image">
                                    <?php echo $this->Html->image('upload/'.$myImage['Entry']['id'].'.'.$myImageTypeList[$myImage['Entry']['id']], array('width'=>150,'alt'=>$myImage['Entry']['title'],'title'=>$myImage['Entry']['title'])); ?>
                                </div>
                                <div class="description">
                                    <p><?php echo $myImage['Entry']['title']; ?></p>
                                    <a href="javascript:void(0)" onclick="javascript:deleteChildPic(this);" class="icon-remove icon-white"></a>
                                </div>
                                <input type="hidden" value="<?php echo $myImage['Entry']['id']; ?>" name="data[Entry][image][]" />
                            </div>
                		<?php
                	}
                }
                else if(!empty($myEntry))
                {
                    foreach ($myEntry['ChildEntry'] as $index => $findDetail)
                    {
                        $findDetail = $findDetail['Entry']; // SPECIAL CASE, COZ IT'S BEEN MODIFIED IN CONTROLLER !!
                        if($findDetail['entry_type'] == $nowTypeSlug)
                        {
                            ?>
                                <div class="photo">
                                    <div class="image">
                                        <?php echo $this->Html->image('upload/'.$findDetail['main_image'].'.'.$myImageTypeList[$findDetail['main_image']], array('width'=>150,'alt'=>$findDetail['title'],'title'=>$findDetail['title'])); ?>
                                    </div>
                                    <div class="description">
                                        <p><?php echo $findDetail['title']; ?></p>
                                        <a href="javascript:void(0)" onclick="javascript:deleteChildPic(this);" class="icon-remove icon-white"></a>
                                    </div>
                                    <input type="hidden" value="<?php echo $findDetail['main_image']; ?>" name="data[Entry][image][]" />
                                </div>                          
                            <?php                            
                        }
                    }
                }
                echo '</div>';
            }			
		?>
		
		<!-- myTypeSlug is for media upload settings purpose !! -->
		<input type="hidden" value="<?php echo (empty($myChildType)?$myType['Type']['slug']:$myChildType['Type']['slug']); ?>" id="myTypeSlug"/>
		
		<?php
            // get client / vendor X ...
            if($VENDOR)
            {
                if($DMD)
                {
                    $vendor_x = $this->Get->meta_details($myParentEntry['EntryMeta']['vendor'] , 'vendor');
                    ?>
        <input type="hidden" id="vendor_x" value="<?php echo $vendor_x['EntryMeta']['capital_x']; ?>">            
                    <?php
                }
            }
            else // client ...
            {
                if($DMD)
                {
                    $client_x = $this->Get->meta_details($myParentEntry['EntryMeta']['client'] , 'client');
                    $client_x = $client_x['EntryMeta']['diamond_sell_x'];
                }
                else
                {
                    $client_x = implode('|', array(
                        $myParentEntry['EntryMeta']['x_125'],
                        $myParentEntry['EntryMeta']['x_100'],
                        $myParentEntry['EntryMeta']['x_110'],
                        $myParentEntry['EntryMeta']['x_115'],
                    ));
                }
                ?>
        <input type="hidden" id="client_x" value="<?php echo $client_x; ?>">        
                <?php
            }
        ?>
		
	<!-- SAVE BUTTON -->
		<div class="control-action">
			<!-- always use submit button to submit form -->
			<button id="save-button" type="submit" class="btn btn-primary"><?php echo $saveButton; ?></button>
			<?php
				if(empty($myEntry) && !empty($myType))
				{
					echo '<button id="save-as-draft" type="button" class="btn btn-inverse">Save as Pending Payment</button>';
				}

                $langUrlCancel = '';
                if(!empty($lang))
                {
                    $langUrlCancel = (empty($myChildTypeLink)?'?':'&').'lang='.$lang;
                }
                else if(!empty($myEntry))
                {
                    $langUrlCancel = (empty($myChildTypeLink)?'?':'&').'lang='.substr( $myEntry['Entry']['lang_code'] , 0,2);
                }
			?>
        	<button type="button" class="btn" onclick="javascript: window.location=site+'admin/entries/<?php echo (empty($myType)?'pages':$myType['Type']['slug']).(empty($myChildType)?'':'/'.$myParentEntry['Entry']['slug']).$myChildTypeLink.$langUrlCancel; ?>'">Cancel</button>
		</div>
	</fieldset>
<?php echo $this->Form->end(); ?>
	<div class="clear"></div>
<?php
	if($isAjax == 0)
	{
		echo '</div>';
	}
?>