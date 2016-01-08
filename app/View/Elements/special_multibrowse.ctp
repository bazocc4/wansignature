<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );
	$var_stream = $shortkey.'_stream';

    $browse_slug = '';
    $browse_alias = get_slug($shortkey);
    // CUSTOM BROWSE SLUG ...
    if(strpos($shortkey, '_jewelry') !== FALSE)
    {
        $browse_slug = 'cor-jewelry';
    }
    else if(strpos($shortkey, 'diamond') !== FALSE)
    {
        $browse_slug = 'diamond';
    }
    else
    {
        $browse_slug = $browse_alias;
    }

    // unit of additional number ...
    $unit = '';
    $unit_size = '';
    $unit_step_min = '';
    if(strpos($shortkey, 'diamond') !== FALSE)
    {
        $unit = 'USD';
        $unit_size = 'input-small';
        $unit_step_min = 'step="any" min="0"';
    }
    else if(strpos($shortkey, '_jewelry') !== FALSE)
    {
        $unit = 'gram';
        $unit_size = 'input-small';
        $unit_step_min = 'step="any" min="0"';
    }
    else
    {
        $unit = 'pcs';
        $unit_size = 'input-mini';
        $unit_step_min = 'min="1"';
    }
?>
<div class="control-group" <?php echo (empty($display)?'':'style="display:none"'); ?>>
	<label class="control-label" <?php echo (strpos(strtolower($validation), 'not_empty') !== FALSE && !$view_mode?'style="color: red;"':''); ?>>
        <?php echo string_unslug($shortkey); ?>
    </label>
	<div class="controls <?php echo $browse_alias; ?>-group">
		<?php
			$raw_stream = 1;
            $popupExtensions = array('popup'=>'init');

            if($browse_alias != $browse_slug)
            {
                $popupExtensions['alias'] = $browse_alias;
            }

            if(is_array($request_query))
            {
                $popupExtensions = array_merge($popupExtensions, $request_query);
            }
			
			// Check data POST first !!
			if(!empty($_POST['data'][$model][$counter]['value']) && !$view_mode)
			{
				foreach ($_POST['data'][$model][$counter]['value'] as $metakey => $metavalue) 
				{
					if(!empty($metavalue))
					{
						echo '<div class="row-fluid '.$browse_alias.'-detail bottom-spacer">';					
						echo '<input REQUIRED id="'.$browse_alias.$raw_stream.'" class="input-xlarge" type="text" name="data['.$model.']['.$counter.'][temp][]" value="'.$_POST['data'][$model][$counter]['temp'][$metakey].'" readonly="true"/>';
                        
                        echo '&nbsp;<input REQUIRED type="number" '.$unit_step_min.' class="'.$unit_size.'" placeholder="'.$unit.'" name="data['.$model.']['.$counter.'][total][]" value="'.$_POST['data'][$model][$counter]['total'][$metakey].'">';
                        
						$popupExtensions['stream'] = $raw_stream;
                        echo '&nbsp;'.$this->Html->link('Browse',array('controller'=>'entries','action'=>$browse_slug,'admin'=>true,'?'=>$popupExtensions),array('class'=>'btn btn-info get-from-table'));
	                    echo '<input class="'.$shortkey.'" type="hidden" name="data['.$model.']['.$counter.'][value][]" value="'.$metavalue.'"/>';
	                    echo '&nbsp;<a class="btn btn-danger del-raw" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>';					
						echo '</div>';
						
						$raw_stream++;
					}
				}
			}
			else if(!empty($value))
			{
				$metaslugs = explode('|', $value);
				foreach ($metaslugs as $metakey => $metavalue) 
				{
                    $metaWithTotal = explode('_', $metavalue );
                    
                    $metavalue = $metaWithTotal[0];
                    $metatotal = $metaWithTotal[1];
                    
					$metaDetails = $this->Get->meta_details($metavalue , $browse_slug);
					if(!empty($metaDetails))
					{
                        // check language is matching or not !!
                        if(!empty($lang))
                        {
                            $pecahlang = explode('-', $metaDetails['Entry']['lang_code']);
                            if($lang != $pecahlang[0])
                            {
                                $tempDetails = $this->Get->meta_details(NULL , $browse_slug , NULL , NULL , NULL , $lang.'-'.$pecahlang[1]);
                                if(!empty($tempDetails))
                                {
                                    $metaDetails = $tempDetails;
                                }
                            }
                        }
                        
						echo '<div class="row-fluid '.$browse_alias.'-detail '.($view_mode?'':'bottom-spacer').'">';
                        
                        $richvalue = '';
                        if(!empty($metaDetails['EntryMeta']['name']))
                        {
                            $richvalue = $metaDetails['EntryMeta']['name'].' ('.$metaDetails['Entry']['title'].')';
                        }
                        else
                        {
                            $richvalue = $metaDetails['Entry']['title'];
                        }
                        
                        // print additional information too !!
                        if(!empty($metaDetails['EntryMeta']['product_type']))
                        {
                            $query = $this->Get->meta_details($metaDetails['EntryMeta']['product_type'], 'product-type');
                            $richvalue .= ' '.$query['Entry']['title'];
                            if($query['EntryMeta']['category'] != 'Diamond')
                            {
                                $richvalue .= ' / '.$query['EntryMeta']['category'];
                            }
                        }
                        if(!empty($metaDetails['EntryMeta']['product_brand']))
                        {
                            $query = $this->Get->meta_details($metaDetails['EntryMeta']['product_brand'], 'product-brand');
                            $richvalue .= ' / '.$query['Entry']['title'];
                        }
                        
                        if($view_mode)
                        {
                            echo '<div class="view-mode '.$shortkey.'">';
                            
                            echo ($metakey+1).'.) ';
                            if(!empty($metaDetails['Entry']['main_image']))
                            {
                                $imgLink = $this->Get->image_link(array('id' => $metaDetails['Entry']['main_image']));
                                echo '<img src="'.$imgLink['display'].'" />';
                            }
                            echo $richvalue;
                            if(!empty($metatotal))
                            {
                                echo ' ('.toMoney($metatotal,true,true).' '.$unit.')';
                            }
                            
                            echo '</div>';
                        }
						?>
        <div class="<?php echo ($view_mode?'hide':''); ?>">
        <?php
            echo '<input REQUIRED id="'.$browse_alias.$raw_stream.'" class="input-xlarge" type="text" name="data['.$model.']['.$counter.'][temp][]" value="'.$richvalue.'" readonly="true"/>';
            echo '&nbsp;<input REQUIRED type="number" '.$unit_step_min.' class="'.$unit_size.'" placeholder="'.$unit.'" name="data['.$model.']['.$counter.'][total][]" value="'.$metatotal.'">';

            $popupExtensions['stream'] = $raw_stream;
            echo '&nbsp;'.$this->Html->link('Browse',array('controller'=>'entries','action'=>$browse_slug,'admin'=>true,'?'=>$popupExtensions),array('class'=>'btn btn-info get-from-table'));
            echo '<input class="'.$shortkey.'" type="hidden" name="data['.$model.']['.$counter.'][value][]" value="'.$metaDetails['Entry']['slug'].'"/>';
            echo '&nbsp;<a class="btn btn-danger del-raw" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>';
        ?>
        </div>			
						<?php
						echo '</div>'; // end of $browse_alias.'-detail '
						
						$raw_stream++;
					}
				}
			}

            if($raw_stream == 1 && $view_mode)
            {
                echo '<div class="view-mode">-</div>';
            }
		?>
	</div>
	
	<div class="controls">
		<a data-storage="" data-content="" href="javascript:void(0)" class="add-raw underline <?php echo ($view_mode?'hide':''); ?>">Add a <?php echo str_replace('_', ' ', $shortkey); ?></a>
		<p class="help-block">
		
		    <?php if(!$view_mode && !($user['role_id']>2 && ($browse_slug == 'vendor' || $browse_slug == 'bank' || $browse_slug == 'usd-rate' || $browse_slug == 'warehouse')) ): ?>
			Want to create new one? Click <?php echo $this->Html->link('here<img alt="External Icon" src="'.$imagePath.'img/external-icon.gif">',array('controller'=>'entries','action'=>$browse_slug.'/add'),array("onclick"=>"javascript:openRequestedSinglePopup(this.href); return false;","escape"=>false)); ?>.<br/>
            <?php endif; ?>
            
	        <?php echo $p; ?>
	    </p>
	    
	    <strong>
	    <?php
            if(strpos($shortkey, 'diamond') !== FALSE)
            {
                echo 'TOTAL DIAMOND PRICE : $<span class="total_'.$shortkey.'"></span> USD';
            }
            else if(strpos($shortkey, '_jewelry') !== FALSE)
            {
                echo 'TOTAL JEWELRY PRICE : <span class="total_'.$shortkey.'"></span> gram';
            }
        ?>
        </strong>
	</div>
	
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>
<?php
    // unset this var because later will be using javascript value !!
    unset($popupExtensions['stream']);
?>
<script type="text/javascript">
// special counter variable ...    
var <?php echo $var_stream; ?> = <?php echo $raw_stream; ?>;

$(document).ready(function(){
    $('div.<?php echo $browse_alias; ?>-group').closest('div.control-group').find('a.add-raw').click(function(){
        
        var placeholder = '';
        if($('input[type=radio].currency').length > 0)
        {
            placeholder = $('input[type=radio].currency:checked').val();
        }
        else
        {
            placeholder = '<?php echo $unit; ?>';
        }
        
        var content = '<div class="row-fluid <?php echo $browse_alias; ?>-detail bottom-spacer <?php echo ($view_mode?'hide':''); ?>">';
        content += '<input REQUIRED id="<?php echo $browse_alias; ?>'+<?php echo $var_stream; ?>+'" class="input-xlarge" type="text" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][temp][]" readonly="true"/>';

        content += '&nbsp;<input REQUIRED type="number" <?php echo $unit_step_min; ?> class="<?php echo $unit_size; ?>" placeholder="'+placeholder+'" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][total][]" readonly="true"/>';

        var storage = '';
        if($(this).attr('data-storage').length > 0 && $(this).attr('data-content').length > 0)
        {
            storage += '&storage='+$(this).attr('data-storage')+'&content='+$(this).attr('data-content');
        }
        
        content += '&nbsp;<a class="btn btn-info get-from-table" href="'+linkpath+'admin/entries/<?php echo $browse_slug.get_more_extension($popupExtensions); ?>&stream='+<?php echo $var_stream; ?>+storage+'">Browse</a>';
        content += '<input class="<?php echo $shortkey; ?>" type="hidden" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value][]" />';
        content += '&nbsp;<a class="btn btn-danger del-raw" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>';
        content += '</div>';

        $('div.<?php echo $browse_alias; ?>-group').append(content);
        <?php echo $var_stream; ?>++;
    });
        
    // if NO browse record displayed, then show it one !!
    <?php
        if($raw_stream == 1)
        {
            ?>
    $('div.<?php echo $browse_alias; ?>-group').closest('div.control-group').find('a.add-raw').click();            
            <?php
        }
    ?>
    
    ($('#colorbox').length>0&&$('#colorbox').is(':visible')?$('#colorbox').children().last().children():$(document)).on("click",'div.<?php echo $browse_alias; ?>-group a.del-raw',function(e){
        $(this).closest('div.<?php echo $browse_alias; ?>-detail').animate({opacity : 0 , height : 0, marginBottom : 0},1000,function(){
            $(this).find('input[type=number]').val('').trigger('keyup');
            $(this).detach();
        });
    });
    
    // CALCULATE TOTAL PRICE ...
    if($('span.total_<?php echo $shortkey; ?>').length > 0)
    {
        $('div.<?php echo $browse_alias; ?>-group').on('keyup', 'input[type=number]', function(e, init){
            var totalprice = 0;
            $('div.<?php echo $browse_alias; ?>-group input[type=number]').each(function(){
                if( $.isNumeric( $(this).val() ) )
                {
                    totalprice += parseFloat( $(this).val() );
                }
            });
            
            // divide by certain currency rate ...
            if($('input[type=radio].currency').length > 0 && $('input[type=radio].currency:checked').val() == 'HKD' && $.isNumeric($('input.hkd_rate').val()) )
            {
                totalprice /= parseFloat($('input.hkd_rate').val());
            }

            // show it off !!
            $('span.total_<?php echo $shortkey; ?>').html(number_format(totalprice,2)+'<input type="hidden" value="'+totalprice.toFixed(2)+'">');
            
            // update other attribute related ...
            if($('#myTypeSlug').val().indexOf('-payment') >= 0)
            {
                <?php
                    if(strpos($shortkey, 'payment_') === FALSE)
                    {
                        ?>
                if($('input.gold_loss').length > 0)
                {
                    $('input.gold_loss').trigger('keyup', [init]);
                }
                else if($('input.additional_charge').length > 0)
                {
                    $('input.additional_charge').trigger('keyup', [init]);
                }            
                        <?php
                    }
                ?>
            }
            else if($('#myTypeSlug').val().indexOf('-invoice') >= 0)
            {
                if($('input.capital_x').length > 0)
                {
                    $('input.capital_x').trigger('keyup', [init]);
                }
                else if($('input.gold_loss').length > 0)
                {
                    $('input.gold_loss').trigger('keyup', [init]);
                }
                else if($('input.disc_adjustment').length > 0)
                {
                    $('input.disc_adjustment').trigger('keyup', [init]);
                }
            }
            
        }).find('input[type=number]:first').trigger('keyup', ['init']);
    }
});    
</script>