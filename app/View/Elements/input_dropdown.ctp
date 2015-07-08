<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );
	
	$required = "";
	if(strpos(strtolower($validation), 'not_empty') !== FALSE)
	{
		$required = 'REQUIRED';
	}
?>
<div class="control-group" <?php echo (empty($display)?'':'style="display:none"'); ?>>
	<label class="control-label" <?php echo (!empty($required)&&!$view_mode?'style="color: red;"':''); ?>>
        <?php echo string_unslug($shortkey); ?>
    </label>
	<div class="controls">
		<?php
            if($view_mode)
            {
                echo '<div class="view-mode '.$shortkey.'">';
                
                $value = $list[array_search($value, array_column($list, 'id') )]['name'];
                if(empty($value))
                {
                    echo '-';
                }
                else
                {
                    if($shortkey == 'status')
                    {
                        echo '<span class="label label-success">'.$value.'</span>';
                    }
                    else
                    {
                        echo $value;
                    }
                }
                echo '</div>';
            }
        ?>
        
        <div class="<?php echo ($view_mode?'hide':''); ?>">
            <select class="<?php echo ($shortkey=='status'?'input-medium':'input-xlarge'); ?> <?php echo $shortkey; ?>" <?php echo $required.' '.(strpos($key, "discount")===FALSE?'':'style="width:50px;"');?> name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value]">
                <?php
                    if(empty($required))
                    {
                        echo '<option value="">'.$placeholder.'</option>';
                    }
                    $value = isset($_POST['data'][$model][$counter]['value'])?$_POST['data'][$model][$counter]['value']:$value;
                    foreach ($list as $key10 => $value10)
                    {
                        // group header ...
                        if(substr($value10['name'], 0, 1) == '[' && substr($value10['name'], -1) == ']')
                        {
                            echo '<option style="font-style:italic;font-size:18px;color:blue;" DISABLED>'.$value10['name'].'</option>';
                        }
                        else
                        {
                            echo "<option ".(strtolower($value10['id']) == strtolower($value)?'SELECTED':'')." value=\"".$value10['id']."\">".$value10['name']."</option>";
                        }
                    }
                ?>
            </select>
        </div>
                    
        <?php
			if(!empty($p))
			{
				echo '<p class="help-block">'.$p.'</p>';
			}
		?>
	</div>
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $optionlist; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][optionlist]"/>	
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>