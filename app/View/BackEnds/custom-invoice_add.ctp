<?php
	$this->Get->create($data);
	if(is_array($data)) extract($data , EXTR_SKIP);

    // is it Diamond or Cor Jewelry invoice ?
    $DMD = (strpos($myType['Type']['slug'], 'dmd-')!==FALSE?true:false);

    // is it Vendor or Client invoice ?
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
	$saveButton = (empty($myEntry)?'Add New':(empty($lang)?'Save Changes':'Add Translation'));
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
				
				// CUSTOMIZED SCRIPT !!
                if($('input[type=radio].sale_venue').length > 0)
                {
                    $('input[type=radio].sale_venue').change(function(){
                        if($(this).is(':checked'))
                        {
                            if($(this).val() == 'Warehouse')
                            {
                        $('input#exhibition').closest('.control-group').hide();
						$('input#exhibition').val('').nextAll('input[type=hidden].exhibition').val('');
						$('input#warehouse').closest('.control-group').show();
                            }
                            else
                            {
                        $('input#warehouse').closest('.control-group').hide();
						$('input#warehouse').val('').nextAll('input[type=hidden].warehouse').val('');
						$('input#exhibition').closest('.control-group').show();
                            }
                        }
                    }).trigger('change');
                }
                
                <?php
                    // initialize empty rate at very first stage ...
                    if(empty($myEntry) && empty($this->request->data))
                    {
                        if($VENDOR)
                        {
                            if($DMD)
                            {
                                $hkdrate = $this->Get->meta_details(NULL , 'usd-rate' , NULL , NULL , NULL , NULL , 'hkd');
                                ?>
                $('input.hkd_rate').val('<?php echo $hkdrate['EntryMeta']['rate_value']; ?>');
                                <?php
                            }
                        }
                        else // client ...
                        {
                            $rprate = $this->Get->meta_details(NULL , 'usd-rate' , NULL , NULL , NULL , NULL , 'idr');
                            if($DMD)
                            {
                                ?>
                $('input.rp_rate').val('<?php echo $rprate['EntryMeta']['rate_value']; ?>');
                                <?php
                            }
                            else // cor ...
                            {
                                $goldrate = $this->Get->meta_details(NULL , 'usd-rate' , NULL , NULL , NULL , NULL , 'gold bar%');
                                $result = round($rprate['EntryMeta']['rate_value'] / $goldrate['EntryMeta']['rate_value']);
                                ?>
                $('input.gold_price').val('<?php echo $result; ?>');
                                <?php
                            }
                        }
                    }
                ?>
                
                // Auto calculate Total Weight for Cor Client ...
                var $sold = $('input[type=number][class^="sold_1"]'); // this is readonly input !!
                if($sold.length > 0)
                {
                    $('input[type=number][class^="x_1"], input.disc_adjustment').keyup(function(e, init){
                        if(init == null)
                        {
                            var result = 0;
                            $sold.each(function(i,el){
                                
                                var value = parseFloat($('span[class^="total_cor_jewelry"]:eq('+i+') input[type=hidden]').val());
                                $(el).val( value.toFixed(2) );
                                
                                var multiplier = $('input[type=number][class^="x_1"]:eq('+i+')').val();
                                if(!$.isNumeric(multiplier))
                                {
                                    multiplier = 1;
                                }

                                result += value * parseFloat(multiplier);
                            });

                            var discount = $('input.disc_adjustment').val();
                            if($.isNumeric(discount))
                            {
                                result -= parseFloat(discount);
                            }

                            $('input.total_weight').val(result.toFixed(2));
                        }
                    });
                    
                    // also hide all "TOTAL JEWELRY PRICE" info ...
                    $('span[class^="total_cor_jewelry"]').parent('strong').hide();
                }
                
                // form submit pre-check ...
                $('form').submit(function(){
                    var produk = '<?php echo ( $DMD ? 'diamond' : 'cor-jewelry' ); ?>';
                    var jml = $('div[class*="'+produk+'-"][class*="-group"] div[class*="'+produk+'-"][class*="-detail"] input[type=hidden][value]').length;
                    if($('input.total_pcs').val() != jml)
                    {
                        alert('Jumlah produk tidak sesuai dengan total perhiasan yang didaftarkan!\nMohon mengecek kembali inputan Anda.');
                        $('input.total_pcs').focus();
                        return false;
                    }
                });
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
			$value['value'] = (isset($_POST['data'][$value['model']][$value['counter']]['value'])?$_POST['data'][$value['model']][$value['counter']]['value']:$myEntry[$value['model']]['title']);
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
                    
                    // on-the-fly validation ...
                    if($value['key'] == 'form-warehouse' || $value['key'] == 'form-exhibition')
                    {
                        $value['validation'] .= 'not_empty|';
                    }
                    else if(strpos($value['key'], 'form-sold_1') !== FALSE)
                    {
                        $value['readonly'] = 'readonly';
                    }
                    
                    // view mode ...
                    if(!empty($myEntry))
                    {
                        $value['view_mode'] = true;
                        
                        if($value['key'] == 'form-sale_venue')
                        {
                            $value['display'] = 'none';
                        }
                    }
                    
					echo $this->element('input_'.$value['input_type'] , $value);
                    
                    // =========================================================== >>>
                    // echo products to help calculate total price / weight result ...
                    // =========================================================== >>>
                    if(empty($myEntry))
                    {
                        if($DMD && $VENDOR)
                        {
                            if($value['key'] == 'form-hkd_rate')
                            {
                                ?>
            <script>
                $(document).ready(function(){
                    $('input[type=radio].currency').change(function(){
                        $('div.diamond-group input[type=number]').attr('placeholder', $(this).val() );
                        $('div.diamond-group input[type=number]:first').trigger('keyup');
                    });
                    
                    $('input.hkd_rate').keyup(function(){
                        if($('input[type=radio].currency:checked').val() == 'HKD' && $.isNumeric( $(this).val() ))
                        {
                            $('input[type=radio].currency').trigger('change');
                        }
                    });
                    
                    $('input.capital_x').keyup(function(e, init){
                        if(init == null)
                        {
                            var capital_x = 1;
                            if($.isNumeric( $(this).val() ))
                            {
                                capital_x = parseFloat($(this).val());
                            }

                            var result = capital_x * parseFloat( $('span.total_diamond input[type=hidden]').val() );
                            $('input.total_price').val( result.toFixed(2) );
                        }
                    });
                });
            </script>                    
                                <?php
                                
                                echo $this->element('input_text' , array(
                                    'key'           => 'temp-capital_x',
                                    'validation'    => 'is_numeric|',
                                    'p'             => 'Vendor Invoice X value',
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++,
                                    'inputsize'     => 'input-mini'
                                ));
                                
                                echo $this->element('special_multibrowse' , array(
                                    'key'           => 'temp-diamond',
                                    'validation'    => 'not_empty|',
                                    'p'             => "Diamond purchased from this invoice <span style='color:red;'>(with <strong>Vendor Barcode</strong> input, based on invoice currency).</span>",
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++
                                ));
                            }
                        }
                        else if(!$DMD && $VENDOR)
                        {
                            if($value['key'] == 'form-total_item_sent')
                            {
                                ?>
            <script>
                $(document).ready(function(){
                    $.fn.calculateTotalWeight = function(init){
                        if(init == null)
                        {
                            var source = 0;
                            if($('span.total_cor_jewelry').length > 0)  source += parseFloat($('span.total_cor_jewelry input[type=hidden]').val());
                            if($('span.total_gold_loss').length > 0)    source += parseFloat($('span.total_gold_loss input[type=hidden]').val());
                            if($('span.additional_cost_gram').length > 0)   source += parseFloat( $('span.additional_cost_gram').text() );
                            
                            $('input.total_weight').val( source.toFixed(2) );
                        }
                    }
                });
            </script>
            <script src="<?php echo $imagePath; ?>js/gold_loss.js"></script>                   
                                <?php
                                
                                echo $this->element('special_multibrowse' , array(
                                    'key'           => 'temp-cor_jewelry',
                                    'validation'    => 'not_empty|',
                                    'p'             => "Cor Jewelry purchased from this invoice <span style='color:red;'>(with <strong>Item Weight</strong> input).</span>",
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++
                                ));
                                
                                echo $this->element('input_text' , array(
                                    'key'           => 'temp-gold_loss',
                                    'validation'    => 'is_numeric|',
                                    'p'             => 'Nilai prosentase susut perhiasan.',
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++,
                                    'inputsize'     => 'input-mini'
                                ));
                                
                                echo $this->element('input_browse' , array(
                                    'key'           => 'temp-cost_currency',
                                    'p'             => 'Cost currency from vendor side.',
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++
                                ));
                                
                                echo $this->element('input_text' , array(
                                    'key'           => 'temp-gold_bar_rate',
                                    'validation'    => 'is_numeric|',
                                    'p'             => 'Selected cost currency rate value per 1 gram Gold Bar.',
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++
                                ));
                                
                                echo $this->element('input_text' , array(
                                    'key'           => 'temp-additional_cost',
                                    'validation'    => 'is_numeric|',
                                    'p'             => 'Tambahan ongkos yang dibebankan dari vendor dalam satuan currency terpilih (ongkos kerja, pasang, dll).',
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++
                                ));
                            }
                        }
                        else if($DMD && !$VENDOR)
                        {
                            if($value['key'] == 'form-rp_rate')
                            {
                                ?>
            <script>
                $(document).ready(function(){
                    $('input.diamond_sell_x, input.disc_adjustment').keyup(function(e, init){
                        if(init == null)
                        {
                            var diamond_sell_x = 1;
                            if($.isNumeric( $('input.diamond_sell_x').val() ))
                            {
                                diamond_sell_x = parseFloat($('input.diamond_sell_x').val());
                            }
                            
                            var disc = 0;
                            if($.isNumeric( $('input.disc_adjustment').val() ))
                            {
                                disc = parseFloat( $('input.disc_adjustment').val() );
                            }

                            var result = diamond_sell_x * parseFloat( $('span.total_diamond input[type=hidden]').val() ) - disc;
                            $('input.total_price').val( result.toFixed(2) );
                        }
                    });
                });
            </script>                    
                                <?php
                                
                                echo $this->element('input_text' , array(
                                    'key'           => 'temp-diamond_sell_x',
                                    'validation'    => 'is_numeric|',
                                    'p'             => 'Client Invoice X value',
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++,
                                    'inputsize'     => 'input-mini'
                                ));
                                
                                echo $this->element('special_multibrowse' , array(
                                    'key'           => 'temp-diamond',
                                    'validation'    => 'not_empty|',
                                    'p'             => "Diamond sold from this invoice <span style='color:red;'>(with <strong>$ USD Sell Barcode</strong> input).</span>",
                                    'model'         => 'EntryMeta',
                                    'counter'       => $counter++
                                ));
                            }
                        }
                        else if(!$DMD && !$VENDOR)
                        {
                            if($value['key'] == 'form-gold_price')
                            {
                                echo $this->element('special_multibrowse' , array(
                                    'key'           => 'temp-cor_jewelry_125',
                                    'p'             => "Cor Jewelry sold from this invoice <span style='color:red;'>( MADE IN ITALY ).</span>",
                                    'model'         => 'EntryMeta',
                                    'request_query' => array(
                                        'key' => 'product_type',
                                        'value' => 'made-in-italy'
                                    ),
                                    'counter'       => $counter++
                                ));
                            }
                            else if($value['key'] == 'form-x_125')
                            {
                                echo $this->element('special_multibrowse' , array(
                                    'key'           => 'temp-cor_jewelry_100',
                                    'p'             => "Cor Jewelry sold from this invoice <span style='color:red;'>( MADE IN KOREA ).</span>",
                                    'model'         => 'EntryMeta',
                                    'request_query' => array(
                                        'key' => 'product_type',
                                        'value' => 'made-in-korea'
                                    ),
                                    'counter'       => $counter++
                                ));
                            }
                            else if($value['key'] == 'form-x_100')
                            {
                                echo $this->element('special_multibrowse' , array(
                                    'key'           => 'temp-cor_jewelry_110',
                                    'p'             => "Cor Jewelry sold from this invoice <span style='color:red;'>( 999 SIMPLE ).</span>",
                                    'model'         => 'EntryMeta',
                                    'request_query' => array(
                                        'key' => 'product_type|product_type|product_type',
                                        'value' => '!made-in-italy|!made-in-korea|!3D'
                                    ),
                                    'counter'       => $counter++
                                ));
                            }
                            else if($value['key'] == 'form-x_110')
                            {
                                echo $this->element('special_multibrowse' , array(
                                    'key'           => 'temp-cor_jewelry_115',
                                    'p'             => "Cor Jewelry sold from this invoice <span style='color:red;'>( 999 3D ).</span>",
                                    'model'         => 'EntryMeta',
                                    'request_query' => array(
                                        'key' => 'product_type',
                                        'value' => '3D'
                                    ),
                                    'counter'       => $counter++
                                ));
                            }
                        }
                    }
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
			// Our CKEditor Description Field !!
			$value = array();
			$value['key'] = 'form-description';
			$value['validation'] = '';
			$value['model'] = 'Entry';
			$value['counter'] = 1;
			$value['input_type'] = 'ckeditor';
			$value['value'] = (isset($_POST['data'][$value['model']][$value['counter']]['value'])?$_POST['data'][$value['model']][$value['counter']]['value']:$myEntry[$value['model']]['description']);
			echo $this->element('input_'.$value['input_type'] , $value);

			// show status field if update (NEW ZPANEL FEATURE) !!
			$value = array();
			$value['counter'] = 3;
			$value['key'] = 'form-status';
			$value['validation'] = 'not_empty';
			$value['model'] = 'Entry';
			$value['input_type'] = 'dropdown';
			$value['list'][0]['id'] = '1';
			$value['list'][0]['name'] = 'Published';
			$value['list'][1]['id'] = '0';
			$value['list'][1]['name'] = 'Draft';
            $value['value'] = (isset($_POST['data'][$value['model']][$value['counter']]['value'])?$_POST['data'][$value['model']][$value['counter']]['value']:$myEntry[$value['model']]['status']);
//			$value['display'] = (empty($myEntry)||empty($myType)?'none':'');
            $value['display'] = 'none';
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
	<!-- SAVE BUTTON -->
		<div class="control-action">
			<!-- always use submit button to submit form -->
			<button id="save-button" type="submit" class="btn btn-primary"><?php echo $saveButton; ?></button>
			<?php
				if(empty($myEntry) && !empty($myType))
				{
					echo '<button id="save-as-draft" type="button" class="btn btn-inverse hide">Save as Draft</button>';
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