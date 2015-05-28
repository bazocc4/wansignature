<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );	

	$browse_slug = get_slug($shortkey);
    $metaDetails = array();
    $metaslug = (isset($_POST['data'][$model][$counter]['value'])?$_POST['data'][$model][$counter]['value']:$value);
    if(!empty($metaslug))
    {
        $metaDetails = $this->Get->meta_details( $metaslug , $browse_slug);
        
        // check language is matching or not !!
        if(!empty($lang) && !empty($metaDetails))
        {
            $pecahlang = explode('-', $metaDetails['Entry']['lang_code']);
            if($lang != $pecahlang[0])
            {
                $tempDetails = $this->Get->meta_details(NULL , $browse_slug , NULL , NULL , NULL , $lang.'-'.$pecahlang[1]);
                if(!empty($tempDetails))
                {
                    $metaDetails = $tempDetails;
                    $metaslug = $metaDetails['Entry']['slug'];
                }
            }
        }
    }

	$required = "";
	if(strpos(strtolower($validation), 'not_empty') !== FALSE)
	{
		$required = 'REQUIRED';
	}
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('a#<?php echo $browse_slug; ?>-view-detail').click(function(e){
            
            var nowval = $.trim($(this).closest('div.controls').find('input[type=hidden].<?php echo $shortkey; ?>').val());
            if(nowval.length > 0)
            {
                $(this).attr('href' , site+'admin/entries/<?php echo $browse_slug; ?>/edit/'+nowval);
            }
            else
            {
                e.preventDefault();
                alert('Please browse an item first!');
            }                
        });
    });
</script>
<div class="control-group" <?php echo (empty($display)?'':'style="display:none"'); ?>>            
	<label class="control-label" <?php echo (!empty($required)?'style="color: red;"':''); ?>>
        <?php echo string_unslug($shortkey); ?>
    </label>
	<div class="controls">
		
		<input <?php echo $required; ?> <?php echo (empty($display)?'id="'.$browse_slug.'"':''); ?> class="targetID input-large" placeholder="<?php echo $placeholder; ?>" value="<?php echo $metaDetails['Entry']['title']; ?>" type="text" readonly="true"/>
        <?php
            echo $this->Html->link('Browse',array('controller'=>'entries','action'=>$browse_slug,'admin'=>true,'?'=>array('popup'=>'init')),array('class'=>'btn btn-info get-from-table'));
        ?>
        <input class="<?php echo $shortkey; ?>" type="hidden" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value]" value="<?php echo $metaslug; ?>"/>
        
        <?php if(empty($required)): ?>
            <a class="btn btn-danger removeID" href="javascript:void(0)">Clear</a>  
        <?php endif; ?>
        
        <a target="_blank" id="<?php echo $browse_slug; ?>-view-detail" class="btn btn-primary" href="#">View Detail</a>  
		
		<p class="help-block">
			Want to create new one? Click <?php echo $this->Html->link('here<img alt="External Icon" src="'.$imagePath.'img/external-icon.gif">',array('controller'=>'entries','action'=>$browse_slug.'/add'),array("target"=>"SingleSecondaryWindowName","onclick"=>"javascript:openRequestedSinglePopup(this.href); return false;","escape"=>false)); ?>.<br/>
            <?php echo $p; ?>
        </p>
	</div>
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>