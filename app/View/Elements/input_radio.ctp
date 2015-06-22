<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );
	
	$required = '';
	if(strpos(strtolower($validation), 'not_empty') !== FALSE)
	{
		$required = 'REQUIRED';
	}
?>
<div class="control-group" <?php echo (empty($display)?'':'style="display:none"'); ?>>            
	<label class="control-label" <?php echo (!empty($required)&&!$view_mode?'style="color: red;"':''); ?>>
        <?php echo string_unslug($shortkey); ?>
    </label>
	<div class="controls <?php echo ($view_mode?'':'radio'); ?>">
		<?php
            // VALUE ID as array keys !!!
            $labelclass = array(
                'Cek Lunas' => 'btn btn-mini btn-success text-uppercase',
                'Cek Titip' => 'btn btn-mini btn-inverse text-uppercase',
                
                'Credit' => 'btn btn-mini btn-danger text-uppercase',
                'Debit' => 'btn btn-mini btn-primary text-uppercase',
                
                'Warehouse' => 'btn btn-mini btn-primary text-uppercase',
                'Exhibition' => 'btn btn-mini btn-inverse text-uppercase',
            );

			$value = isset($_POST['data'][$model][$counter]['value'])?$_POST['data'][$model][$counter]['value']:$value;

            if($view_mode)
            {
                echo '<div class="view-mode '.$shortkey.'">';
                if(empty($value))
                {
                    echo '-';
                }
                else
                {
                    echo '<label class="'.$labelclass[$value].'">'.$value.'</label>';
                }
                echo '</div>';
            }

            echo '<div class="'.($view_mode?'hide':'').'">';
			foreach ($list as $key10 => $value10)
			{
                $labelfor = 'data-'.$model.'-'.$counter.'-'.get_slug($value10['id']);
                $checked = (strtolower($value10['id']) == strtolower($value) || $key10 == 0 && !empty($required)?'CHECKED':'');
                
                echo "<input id='".$labelfor."' class='".$shortkey."' ".$required." ".$checked." value='".$value10['id']."' name='data[".$model."][".$counter."][value]' type='radio' /><label class='".$labelclass[$value10['id']]."' for='".$labelfor."'>".$value10['name']."</label>";
			}
            echo '</div>';

			if(!empty($p))
			{
				echo '<p style="color:red;" class="help-block">'.$p.'</p>';
			}
		?>
	</div>
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $optionlist; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][optionlist]"/>	
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>