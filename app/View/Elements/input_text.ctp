<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );
	$validation = strtolower($validation);
	
	$required = "";
	if(strpos($validation, 'not_empty') !== FALSE)
	{
		$required = 'REQUIRED';
	}
	
	$detail_type = "text";
	if(strpos($validation, 'is_email') !== FALSE)
	{
		$detail_type = 'email';
        if(empty($inputsize))   $inputsize = 'input-large';
	}
	else if(strpos($validation, 'is_numeric') !== FALSE)
	{
		$detail_type = 'number';
        if(empty($inputsize))   $inputsize = 'input-medium';
	}
	else if(strpos($validation, 'is_url') !== FALSE)
	{
		$detail_type = 'url';
	}

	// add class title if the field is title !!
	$classtitle = "";
	if($model == 'Entry' && $counter == 0)
	{
		$classtitle = "Title";
	}

	// characters limitation !!
	$maxchar = 0;
	$posMaxLength = strpos($validation, 'max_length');
	if($posMaxLength !== FALSE)
	{
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
	}

	// set specific input class !!
	if(empty($inputsize))
	{
		$inputsize = 'input-xlarge';
	}
	
	if($shortkey == 'discount' || $shortkey == 'weight' || $shortkey == 'qty' || $shortkey == 'Bunga Cek' || $shortkey == 'loan_interest_rate' || $shortkey == 'additional_charge' || $shortkey == 'gold_loss')
	{
		$inputsize = 'input-mini';
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
                echo '<span class="view-mode '.$shortkey.'">';
                if(empty($value))
                {
                    echo '-';
                }
                else
                {
                    if($detail_type == 'number')
                    {
                        echo toMoney($value , true , true);
                    }
                    else
                    {
                        echo $value;
                    }
                }
                echo '</span>';
            }
        ?>
    
	    <span class="<?php echo ($view_mode?'hide':''); ?>">
		<input <?php echo ($maxchar > 0?'maxlength="'.$maxchar.'"':''); ?> <?php echo ($detail_type=='number'?'step="any" '.($shortkey=='amount'?'':'min="0"'):''); ?> <?php echo (!empty($readonly)?'readonly="true"':''); ?> <?php echo $required; ?> class="<?php echo $inputsize.' '.$shortkey.' '.$classtitle; ?>" type="<?php echo $detail_type; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo (isset($_POST['data'][$model][$counter]['value'])?$_POST['data'][$model][$counter]['value']:$value); ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value]"/>
        </span>
		<?php
            // footer string !!
			if($shortkey == 'discount')
			{
				echo '% OFF';
			}
			else if($shortkey == "amount")
			{
                $unit_amount = '';
                $max_balance = '';
                if(strpos($myType['Type']['slug'], 'dmd') !== FALSE)
                {
                    $unit_amount = 'USD';
                    $max_balance = 'total_price';
                }
                else
                {
                    $unit_amount = 'GR';
                    $max_balance = 'total_weight';
                }
                
                echo $unit_amount.' <span style="color:red;" class="rate_amount"></span>';
			}
            else if($shortkey == 'total_price' || strpos($shortkey, 'balance') !== FALSE && strpos($myType['Type']['slug'], 'dmd') !== FALSE)
            {
                echo 'USD';
            }
			else if(strpos($shortkey, 'weight') !== FALSE || strpos($shortkey, 'balance') !== FALSE && strpos($myType['Type']['slug'], 'cor') !== FALSE)
			{
				echo 'GR';
			}
			else if($shortkey == 'qty')
			{
				echo 'unit(s)';
			}
            else if($shortkey == 'Bunga Cek' || $shortkey == 'loan_interest_rate')
			{
				echo '% / month';
			}
            else if($shortkey == 'loan_period')
            {
                echo ' month(s)';
            }
            else if($shortkey == 'additional_charge' || $shortkey == 'gold_loss')
            {
                echo '% <span style="color:red;">= <span class="total_'.$shortkey.'"></span> <span class="unit_'.$shortkey.'"></span></span>';
            }
            else if($shortkey == 'hkd_rate')
            {
                echo 'HKD = $1 USD.';
            }
            else if($shortkey == 'rp_rate')
            {
                echo 'IDR = $1 USD.';
            }
            else if($shortkey == 'gold_price')
            {
                echo 'IDR = 1 gram Gold Bar.';
            }
            else if($shortkey == 'gold_bar_rate')
            {
                echo '<span class="currency_duplicator"></span> = 1 gram Gold Bar.';
                ?>
<script>
	$(document).ready(function(){
		$('input#cost-currency').change(function(){
            $('span.currency_duplicator').text( $(this).val() );
        }).trigger('change');
	});
</script>                
                <?php
            }
            else if($shortkey == 'additional_cost')
            {
                echo '<span class="currency_duplicator"></span> <span style="color:red" class="result_rate"></span>';
            }
            else if($shortkey == 'rate_value')
			{
				echo '<span class="currency_duplicator"></span> = $1 USD.';
                ?>
<script>
	$(document).ready(function(){
		$('input.Currency.Title').keyup(function(){
            $('span.currency_duplicator').text( $(this).val() );
        }).trigger('keyup');
	});
</script>                
                <?php
			}
            else if(strpos($shortkey , 'stock') !== FALSE)
			{
				echo 'pcs';
			}
		?>
		<p class="help-block">
            <?php echo $p; ?>
        </p>
	</div>
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>
<?php
    if($shortkey == 'amount')
    {
        ?>
<div class="control-group">
    <label class="control-label">Balance</label>
    <div class="controls">
        <div class="view-mode">
            <span id="display_balance"></span> <?php echo $unit_amount; ?>
            <input type="hidden" id="neutral_balance" value="<?php echo (empty($myParentEntry['EntryMeta']['payment_balance'])?'0':$myParentEntry['EntryMeta']['payment_balance']); ?>">
        </div>
        <p class="help-block">
            Pembayaran invoice menjadi <span class="label label-success">LUNAS</span> <strong>APABILA</strong> balance mencapai nilai <?php echo '<span class="label label-success">'.toMoney($myParentEntry['EntryMeta'][$max_balance]  , true , true).' '.$unit_amount.'</span> (Invoice '.string_unslug($max_balance).').'; ?>
        </p>
    </div>
</div>
<script>
    $('input.amount').keyup(function(){
        var result = parseFloat($('#neutral_balance').val());
        if( $.isNumeric($(this).val()) )
        {
            if($('input.statement:first').is(':checked'))
            {
                result += parseFloat($(this).val());
            }
            else
            {
                result -= parseFloat($(this).val());
            }
        }
        $('#display_balance').text( number_format(result,2) );
    });
    
    $(document).ready(function(){
		$('form').submit(function(){
            var amount = parseFloat($('input.amount').val());
            if(amount < 0)
            {
                $('input.amount').val( amount * -1 );
                $('input.statement').not(':checked').attr('checked', true);
            }
        });
	});
</script>
        <?php
    }
?>