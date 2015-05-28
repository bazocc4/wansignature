<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );
    $browse_slug = get_slug($shortkey);
	$required = "";
	if(strpos(strtolower($validation), 'not_empty') !== FALSE)
	{
		$required = 'REQUIRED';
	}
?>
<script>
    $.fn.transformDatePicker = function(last){
        // init date picker function
		$('div.<?php echo $browse_slug; ?>-group input.dpicker'+(last==null?'':':last') ).datepicker({
		    changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            yearRange: "-80:+20",
		});
    }
    
	$(document).ready(function(){
		$.fn.transformDatePicker();
        
        $('div.<?php echo $browse_slug; ?>-group').closest('div.control-group').find('a.add-raw').click(function(){
            var content = '<div class="row-fluid bottom-spacer">';
            content += '<input REQUIRED class="input-small dpicker" type="text"/> <a class="btn btn-danger del-raw" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>';
            content += '</div>';            
            $('div.<?php echo $browse_slug; ?>-group').append(content);            
            $.fn.transformDatePicker('last');
        });
        
		$('div.<?php echo $browse_slug; ?>-group').on("click",'a.del-raw',function(e){
            $(this).closest('div.row-fluid').animate({opacity : 0 , height : 0, marginBottom : 0},1000,function(){
                $(this).detach();
            });
        });
        
        $('form').submit(function(){
            var result = "";            
            $('div.<?php echo $browse_slug; ?>-group div.row-fluid input').each(function(i,el){
                result += (i==0?'':'|')+$(el).val();
            });
            
            $('input.<?php echo $shortkey; ?>[type=hidden]').val(result);
        });
	});
</script>
<div class="control-group" <?php echo (empty($display)?'':'style="display:none"'); ?>>
	<label class="control-label" <?php echo (!empty($required)?'style="color: red;"':''); ?>>
        <?php echo string_unslug($shortkey); ?>
    </label>
	<div class="controls <?php echo $browse_slug; ?>-group">
	    <?php
            // Check data POST first !!
            if(isset($_POST['data'][$model][$counter]['value']))
            {
                $value = $_POST['data'][$model][$counter]['value'];
            }

            if(!empty($value))
            {
                $metaslugs = explode('|', $value);
                foreach ($metaslugs as $metakey => $metavalue) 
                {                    
                    ?>
                <div class="row-fluid bottom-spacer">
                    <input REQUIRED class="input-small dpicker" type="text" value="<?php echo $metavalue; ?>"/> <a class="btn btn-danger del-raw" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>
                </div>
                    <?php
                }
            }
        ?>
	</div>
	
	<div class="controls">
	    <input class="<?php echo $shortkey; ?>" type="hidden" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value]">
		<a href="javascript:void(0)" class="add-raw" style="text-decoration: underline;">Add a <?php echo str_replace('_', ' ', $shortkey); ?></a>
		<p class="help-block">
	        <?php echo $p; ?>
	    </p>
	</div>
	
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>