<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );
	$var_stream = $shortkey.'_stream';	
	$browse_slug = get_slug($shortkey);

    // unit of additional number ...
    $unit = '';
    if($shortkey == 'diamond')
    {
        $unit = 'USD';
    }
    else if(strpos($shortkey, '_jewelry') !== FALSE)
    {
        $unit = 'gram';
    }
    else
    {
        $unit = 'pcs';
    }
?>
<script type="text/javascript">
	var <?php echo $var_stream; ?>;
			
	$(document).ready(function(){
		$('div.<?php echo $browse_slug; ?>-group').closest('div.control-group').find('a.add-raw').click(function(){
            var content = '<div class="row-fluid <?php echo $browse_slug; ?>-detail bottom-spacer">';            
            content += '<input REQUIRED id="<?php echo $browse_slug; ?>'+<?php echo $var_stream; ?>+'" class="input-xlarge" type="text" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][temp][]" readonly="true"/>';
            
            content += '&nbsp;<input REQUIRED type="number" min="1" class="input-mini" placeholder="<?php echo $unit; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][total][]" readonly="true"/>';
            
            var storage = '';
            if($(this).attr('data-storage').length > 0 && $(this).attr('data-content').length > 0)
            {
                storage += '&storage='+$(this).attr('data-storage')+'&content='+$(this).attr('data-content');
            }
            
            content += '&nbsp;<a class="btn btn-info get-from-table" href="'+linkpath+'admin/entries/<?php echo $browse_slug; ?>?popup=init&stream='+<?php echo $var_stream; ?>+storage+'">Browse</a>';
            content += '<input class="<?php echo $shortkey; ?>" type="hidden" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value][]" />';
            content += '&nbsp;<a class="btn btn-danger del-raw" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>';
            content += '</div>';
            
            $('div.<?php echo $browse_slug; ?>-group').append(content);
            <?php echo $var_stream; ?>++;
        });
        
		($('#colorbox').length>0&&$('#colorbox').is(':visible')?$('#colorbox').children().last().children():$(document)).on("click",'div.<?php echo $browse_slug; ?>-group a.del-raw',function(e){
            $(this).closest('div.<?php echo $browse_slug; ?>-detail').animate({opacity : 0 , height : 0, marginBottom : 0},1000,function(){
                $(this).find('input[type=number]').val('').trigger('keyup');
                $(this).detach();
            });
        });
	});
</script>
<div class="control-group" <?php echo (empty($display)?'':'style="display:none"'); ?>>
	<label class="control-label" <?php echo (strpos(strtolower($validation), 'not_empty') !== FALSE && !$view_mode?'style="color: red;"':''); ?>>
        <?php echo string_unslug($shortkey); ?>
    </label>
	<div class="controls <?php echo $browse_slug; ?>-group">
		<?php
			$raw_stream = 1;
			
			// Check data POST first !!
			if(!empty($_POST['data'][$model][$counter]['value']) && !$view_mode)
			{
				foreach ($_POST['data'][$model][$counter]['value'] as $metakey => $metavalue) 
				{
					if(!empty($metavalue))
					{
						echo '<div class="row-fluid '.$browse_slug.'-detail bottom-spacer">';					
						echo '<input REQUIRED id="'.$browse_slug.$raw_stream.'" class="input-xlarge" type="text" name="data['.$model.']['.$counter.'][temp][]" value="'.$_POST['data'][$model][$counter]['temp'][$metakey].'" readonly="true"/>';
                        
                        echo '&nbsp;<input REQUIRED type="number" min="1" class="input-mini" placeholder="'.$unit.'" name="data['.$model.']['.$counter.'][total][]" value="'.$_POST['data'][$model][$counter]['total'][$metakey].'">';
                        
						echo '&nbsp;'.$this->Html->link('Browse',array('controller'=>'entries','action'=>$browse_slug,'admin'=>true,'?'=>array('popup'=>'init', 'stream'=>$raw_stream)),array('class'=>'btn btn-info get-from-table'));
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
                        
						echo '<div class="row-fluid '.$browse_slug.'-detail '.($view_mode?'':'bottom-spacer').'">';
                        
                        if($view_mode)
                        {
                            echo '<div class="view-mode '.$shortkey.'">';
                            echo ($metakey+1).'.) '.$metaDetails['Entry']['title'];
                            if(!empty($metatotal))
                            {
                                echo ' ('.$metatotal.' '.$unit.')';
                            }
                            echo '</div>';
                        }
					
						?>
        <div class="<?php echo ($view_mode?'hide':''); ?>">
        <?php
            $richvalue = '';
            if(!empty($metaDetails['EntryMeta']['name']))
            {
                $richvalue = $metaDetails['EntryMeta']['name'].' ('.$metaDetails['Entry']['title'].')';
            }
            else
            {
                $richvalue = $metaDetails['Entry']['title'];
            }

            echo '<input REQUIRED id="'.$browse_slug.$raw_stream.'" class="input-xlarge" type="text" name="data['.$model.']['.$counter.'][temp][]" value="'.$richvalue.'" readonly="true"/>';
            echo '&nbsp;<input REQUIRED type="number" min="1" class="input-mini" placeholder="'.$unit.'" name="data['.$model.']['.$counter.'][total][]" value="'.$metatotal.'">';

            echo '&nbsp;'.$this->Html->link('Browse',array('controller'=>'entries','action'=>$browse_slug,'admin'=>true,'?'=>array('popup'=>'init', 'stream'=>$raw_stream)),array('class'=>'btn btn-info get-from-table'));
            echo '<input class="'.$shortkey.'" type="hidden" name="data['.$model.']['.$counter.'][value][]" value="'.$metaDetails['Entry']['slug'].'"/>';
            echo '&nbsp;<a class="btn btn-danger del-raw" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>';
        ?>
        </div>			
						<?php
						
						echo '</div>';
						
						$raw_stream++;
					}
				}
			}
		?>
	</div>
	
	<script>
        <?php echo $var_stream; ?> = <?php echo $raw_stream; ?>;
        
        // if NO browse record displayed, then show it one !!
        <?php if($raw_stream == 1): ?>
$(document).ready(function(){
    <?php if($view_mode): ?>
	$('div.<?php echo $browse_slug; ?>-group').html('<div class="view-mode">-</div>');
    <?php else: ?>
    $('div.<?php echo $browse_slug; ?>-group').closest('div.control-group').find('a.add-raw').click();
    <?php endif; ?>
});
        <?php endif; ?>
    </script>
    
	<div class="controls">
		<a data-storage="" data-content="" href="javascript:void(0)" class="add-raw <?php echo ($view_mode?'hide':''); ?>" style="text-decoration: underline;">Add a <?php echo str_replace('_', ' ', $shortkey); ?></a>
		<p class="help-block">
		
		    <?php if(!$view_mode): ?>
			Want to create new one? Click <?php echo $this->Html->link('here<img alt="External Icon" src="'.$imagePath.'img/external-icon.gif">',array('controller'=>'entries','action'=>$browse_slug.'/add'),array("target"=>"SingleSecondaryWindowName","onclick"=>"javascript:openRequestedSinglePopup(this.href); return false;","escape"=>false)); ?>.<br/>
            <?php endif; ?>
            
	        <?php echo $p; ?>
	    </p>
	</div>
	
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>