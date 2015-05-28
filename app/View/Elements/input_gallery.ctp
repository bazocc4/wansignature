<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );

	$required = "";
	if(strpos(strtolower($validation), 'not_empty') !== FALSE)
	{
		$required = 'REQUIRED';
	}
?>
<script>
	$(document).ready(function(){
        // gallery sortable...
        $('div#<?php echo $key; ?>').sortable({ opacity: 0.6, cursor: 'move'});
        
        // print total pictures...
        $('div#<?php echo $key; ?>').prevAll('.galleryCount:first').find('span').html( $('div#<?php echo $key; ?>').find('div.photo').length );

        // cek validation ...
		<?php if(!empty($required)): ?>
		$('form').submit(function(e){
			if( !$.trim($('div#<?php echo $key; ?>').html()) )
			{
				$('div#<?php echo $key; ?>').prevAll('a.get-from-library:first').focus();
				alert('Field <?php echo strtoupper(string_unslug($shortkey)); ?> could not be empty!');
				return false;
			}
		});
		<?php endif; ?>
        
        // realtime max_length validation...
        <?php
            $posMaxLength = strpos($validation, 'max_length');
            if($posMaxLength !== FALSE)
            {
                $maxchar = 0;
                $tempstart = $posMaxLength+11;
                $caripentung = strpos($validation, '|' , $posMaxLength);
                if($caripentung === FALSE)
                {
                    $maxchar = substr($validation, $tempstart);
                }
                else
                {
                    $maxchar = substr($validation, $tempstart , $caripentung - $tempstart );
                }
                
                ?>
        $('div#<?php echo $key; ?>').bind('DOMNodeInserted DOMNodeRemoved', function(event) {
            
            var totalphoto = $(this).find('div.photo').length;            
            var maxchar = <?php echo $maxchar; ?>;
            var $obj = $(this).closest('div.gallery-group').find('a.get-from-library');
            
            if (event.type == 'DOMNodeRemoved') // special adjustment ...
            {
                totalphoto--;
            }
            
            if(totalphoto < maxchar )
            {
                $obj.removeClass('disabled');
                $obj.css('pointer-events' , '');
            }
            else
            {
                $obj.addClass('disabled');
                $obj.css('pointer-events' , 'none');
            }
        });
        
        // initialize checking ...
        if($('div#<?php echo $key; ?>').find('div.photo').length >= <?php echo $maxchar; ?> )
        {
            $('div#<?php echo $key; ?>').closest('div.gallery-group').find('a.get-from-library').addClass('disabled').css('pointer-events' , 'none');
        }
                <?php
            }
        ?>
	});
</script>
<div class="gallery-group" <?php echo (empty($display)?'':'style="display:none"'); ?>>
<?php
	echo '<span class="galleryCount" '.(!empty($required)?'style="color: red;"':'').'>'.string_unslug($shortkey).' Pictures (<span></span>)</span>';
    echo $this->Html->link('Add Picture',array('action'=>'media_popup_single',1,'myInputWrapper',$key,'admin'=>false),array('class'=>'btn btn-inverse fr get-from-library'));

    if(!empty($p))
	{
		echo '<p class="help-block">'.$p.'</p>';
	}
    
    echo '<div class="inner-content pictures '.$shortkey.'" id="'.$key.'">';
    if(!empty($myEntry))
    {
        foreach ($myEntry['ChildEntry'] as $index => $findDetail)
        {
            $findDetail = $findDetail['Entry']; // SPECIAL CASE, COZ IT'S BEEN MODIFIED IN CONTROLLER !!
            if($findDetail['entry_type'] == $key)
            {
                ?>
                    <div class="photo">
                        <div class="image">
                            <?php echo $this->Html->image('upload/thumb/'.$findDetail['main_image'].'.'.$myImageTypeList[$findDetail['main_image']], array('width'=>150,'alt'=>$findDetail['title'],'title'=>$findDetail['title'])); ?>
                        </div>
                        <div class="description">
                            <p><?php echo $findDetail['title']; ?></p>
                            <a href="javascript:void(0)" onclick="javascript:deleteChildPic(this);" class="icon-remove icon-white"></a>
                        </div>
                        <input type="hidden" value="<?php echo $findDetail['main_image']; ?>" name="data[Entry][fieldimage][<?php echo $key; ?>][]" />
                    </div>                          
                <?php                            
            }
        }
    }
    echo '</div>';
?>    
</div>