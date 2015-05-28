<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );
	
	$required = "";
	if(strpos(strtolower($validation), 'not_empty') !== FALSE && empty($value) ) // SPECIAL CASE FOR INPUT FILE !!
	{
		$required = 'REQUIRED';
	}
?>
<script>
	$(document).ready(function(){
        $('input[type=file].<?php echo $shortkey; ?>').change( function() {
            //check whether browser fully supports all File API
            if (window.File && window.FileReader && window.FileList && window.Blob)
            {
                //get the file size from file input field
                var fsize = $(this)[0].files[0].size;
                var maxsize = 2097152; // 2 mb max...

                if(fsize>maxsize) //do something if file size more than 2 mb
                {
                    alert("Please attach file with size no more than 2 MB.");
                }else{
                    return true;
                }
            }else{
                alert("Please upgrade your browser, because your current browser lacks some new features we need!");
            }
            
            $(this).val('');
            $(this).focus();
        });
	});
</script>
<div class="control-group" <?php echo (empty($display)?'':'style="display:none"'); ?>>            
	<label class="control-label" <?php echo (!empty($required)?'style="color: red;"':''); ?>>
        <?php echo string_unslug($shortkey); ?>
    </label>
	<div class="controls">
        <?php
            if(!empty($value))
            {
                ?>
        <input type="hidden" value="<?php echo $value; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value]"/>
                <?php
                $result = "<a title='CLICK TO DOWNLOAD FILE' href='".$this->Get->get_linkpath()."files/".$value."'>".$value."</a>";
                echo '<p>'.$result.'</p>';
            }
        ?>    
		<input <?php echo $required; ?> class="<?php echo $shortkey; ?>" type="file" placeholder="<?php echo $placeholder; ?>" name="<?php echo $key; ?>"/>
		<?php
            if(empty($value))
            {
                echo '<p class="help-block">';
                echo (empty($p)?'Upload File which is downloadable by guest.':$p);
                echo '</p>';
            }
            else
            {
                echo '<p class="help-block" style="color:red;">';
                echo 'IGNORE this field if there are no any changes to the previous file.';
                echo '<br>NB: If you add new file, old file will be overwrited!';
                echo '</p>';
            }
        ?>
        <p class="help-block" style="color:red;">
            PS: Maximum File Size is 2 MB.
        </p>
	</div>
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>