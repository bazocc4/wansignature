<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );
	$var_stream = $shortkey.'_stream';	
	$browse_slug = get_slug($shortkey);
?>
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
                            
                            echo '</div>';
                        }
                        ?>
        <div class="<?php echo ($view_mode?'hide':''); ?>">
        <?php
            echo '<input REQUIRED id="'.$browse_slug.$raw_stream.'" class="input-xlarge" type="text" name="data['.$model.']['.$counter.'][temp][]" value="'.$richvalue.'" readonly="true"/>';
            echo '&nbsp;'.$this->Html->link('Browse',array('controller'=>'entries','action'=>$browse_slug,'admin'=>true,'?'=>array('popup'=>'init', 'stream'=>$raw_stream)),array('class'=>'btn btn-info get-from-table'));
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
		<a data-storage="" data-content="" data-key="" data-value="" href="javascript:void(0)" class="add-raw underline <?php echo ($view_mode?'hide':''); ?>">Add a <?php echo str_replace('_', ' ', $shortkey); ?></a>
		<p class="help-block">
        
		    <?php if(!$view_mode): ?>
			Want to create new one? Click <?php echo $this->Html->link('here<img alt="External Icon" src="'.$imagePath.'img/external-icon.gif">',array('controller'=>'entries','action'=>$browse_slug.'/add'),array("onclick"=>"javascript:openRequestedSinglePopup(this.href); return false;","escape"=>false)); ?>.<br/>
            <?php endif; ?>
            
	        <?php echo $p; ?>
	    </p>
	</div>
	
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>

<script type="text/javascript">
// special counter variable ...    
var <?php echo $var_stream; ?> = <?php echo $raw_stream; ?>;

$(document).ready(function(){
    $('div.<?php echo $browse_slug; ?>-group').closest('div.control-group').find('a.add-raw').click(function(){
        var content = '<div class="row-fluid <?php echo $browse_slug; ?>-detail bottom-spacer <?php echo ($view_mode?'hide':''); ?>">';            
        content += '<input REQUIRED id="<?php echo $browse_slug; ?>'+<?php echo $var_stream; ?>+'" class="input-xlarge" type="text" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][temp][]" readonly="true"/>';            		            

        var storage = '';
        if($(this).attr('data-storage').length > 0 && $(this).attr('data-content').length > 0)
        {
            storage += '&storage='+$(this).attr('data-storage')+'&content='+$(this).attr('data-content');
        }
        if($(this).attr('data-key').length > 0 && $(this).attr('data-value').length > 0)
        {
            storage += '&key='+$(this).attr('data-key')+'&value='+$(this).attr('data-value');
        }

        content += '&nbsp;<a class="btn btn-info get-from-table" href="'+linkpath+'admin/entries/<?php echo $browse_slug; ?>?popup=init&stream='+<?php echo $var_stream; ?>+storage+'">Browse</a>';
        content += '<input class="<?php echo $shortkey; ?>" type="hidden" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value][]" />';
        content += '&nbsp;<a class="btn btn-danger del-raw" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>';
        content += '</div>';

        $('div.<?php echo $browse_slug; ?>-group').append(content);
        <?php echo $var_stream; ?>++;
    });
        
    // if NO browse record displayed, then show it one !!
    <?php
        if($raw_stream == 1)
        {
            ?>
    $('div.<?php echo $browse_slug; ?>-group').closest('div.control-group').find('a.add-raw').click();            
            <?php
        }
    ?>
        
    ($('#colorbox').length>0&&$('#colorbox').is(':visible')?$('#colorbox').children().last().children():$(document)).on("click",'div.<?php echo $browse_slug; ?>-group a.del-raw',function(e){
        $(this).closest('div.<?php echo $browse_slug; ?>-detail').animate({opacity : 0 , height : 0, marginBottom : 0},1000,function(){
            $(this).detach();
        });
    });
});
</script>