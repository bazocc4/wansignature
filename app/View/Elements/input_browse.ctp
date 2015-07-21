<?php
	if(is_array($data)) extract($data , EXTR_SKIP);
	$shortkey = substr($key, 5 );	

	$browse_slug = '';
    $browse_alias = get_slug($shortkey);
    // CUSTOM BROWSE SLUG ...
    if($shortkey == 'wholesaler')
    {
        $browse_slug = 'client';
    }
    else if($shortkey == 'warehouse_origin' || $shortkey == 'warehouse_destination')
    {
        $browse_slug = 'warehouse';
    }
    else if($shortkey == 'exhibition_origin' || $shortkey == 'exhibition_destination')
    {
        $browse_slug = 'exhibition';
    }
    else if($shortkey == 'cost_currency')
    {
        $browse_slug = 'usd-rate';
    }
    else if($shortkey == 'vendor_invoice_code')
    {
        $browse_slug = ( $myType['Type']['slug'] == 'diamond' ? 'dmd-vendor-invoice' : 'cor-vendor-invoice' );
    }
    else if($shortkey == 'client_invoice_code')
    {
        $browse_slug = ( $myType['Type']['slug'] == 'diamond' ? 'dmd-client-invoice' : 'cor-client-invoice' );
    }
    else
    {
        $browse_slug = $browse_alias;
    }

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
        $('a#<?php echo $shortkey; ?>_view_detail').click(function(e){
            
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
	<label class="control-label" <?php echo (!empty($required)&&!$view_mode?'style="color: red;"':''); ?>>
        <?php echo string_unslug($shortkey); ?>
    </label>
	<div class="controls">		
		<?php
            if($view_mode)
            {
                echo '<div class="view-mode '.$shortkey.'">';                
                if(empty($metaDetails))
                {
                    echo '-';
                    echo '</div>';
                }
                else
                {
                    if(!empty($metaDetails['Entry']['main_image']))
                    {
                        $imgLink = $this->Get->image_link(array('id' => $metaDetails['Entry']['main_image']));
                        echo '<img src="'.$imgLink['display'].'" />';
                    }
                    
                    echo $metaDetails['Entry']['title'];
                    
                    // print additional information too !!
                    if($metaDetails['Entry']['entry_type'] == 'warehouse')
                    {
                        echo ' / '.nl2br($metaDetails['EntryMeta']['alamat']).' / '.$metaDetails['EntryMeta']['telepon'];
                    }
                    else if($metaDetails['Entry']['entry_type'] == 'exhibition')
                    {
                        echo ' / '.(!empty($metaDetails['EntryMeta']['start_date'])?date_converter($metaDetails['EntryMeta']['start_date'], $mySetting['date_format']):'[start date]').' s/d '.(!empty($metaDetails['EntryMeta']['end_date'])?date_converter($metaDetails['EntryMeta']['end_date'], $mySetting['date_format']):'[end date]').' / '.nl2br($metaDetails['EntryMeta']['alamat']).' / '.$metaDetails['EntryMeta']['telepon'];
                    }
                    else if($metaDetails['Entry']['entry_type'] == 'client')
                    {
                        echo ' / '.strtoupper($metaDetails['EntryMeta']['kode_pelanggan']).' / '.$metaDetails['EntryMeta']['kategori'].' / '.nl2br($metaDetails['EntryMeta']['alamat']).' / '.$metaDetails['EntryMeta']['telepon'];
                    }
                    else if($metaDetails['Entry']['entry_type'] == 'vendor' || $metaDetails['Entry']['entry_type'] == 'salesman')
                    {
                        echo ' / '.nl2br($metaDetails['EntryMeta']['alamat']).' / '.$metaDetails['EntryMeta']['telepon'];
                    }
                    else if(strpos($metaDetails['Entry']['entry_type'] , '-invoice') !== FALSE)
                    {
                        echo ' <i class="icon-calendar"></i> '.date_converter($metaDetails['EntryMeta']['date'] , $mySetting['date_format']);
                    }
                    
                    echo '</div>';
                    ?>
            <p class="help-block">
                Want to view its detail? Click <?php echo '<a target="_blank" href="'.$imagePath.'admin/entries/'.$metaDetails['Entry']['entry_type'].'/edit/'.$metaDetails['Entry']['slug'].'">here<img alt="External Icon" src="'.$imagePath.'img/external-icon.gif"></a>'; ?>.
            </p>    
                    <?php
                }
                
                echo '<p class="help-block">'.$p.'</p>';
            }
        ?>
        
        <div class="<?php echo ($view_mode?'hide':''); ?>">
            <input <?php echo $required; ?> <?php echo 'id="'.$browse_alias.'"'; ?> class="targetID input-large" placeholder="<?php echo $placeholder; ?>" value="<?php echo $metaDetails['Entry']['title']; ?>" type="text" readonly="true"/>
            <?php            
                $popupExtensions = array('popup'=>'init');

                if($browse_alias != $browse_slug)
                {
                    $popupExtensions['alias'] = $browse_alias;
                    if($browse_alias == 'wholesaler')
                    {
                        $popupExtensions['key'] = 'kategori';
                        $popupExtensions['value'] = 'Wholesaler';
                    }
                }

                if($shortkey == 'product_type')
                {
                    if($myType['Type']['slug'] == 'diamond')
                    {
                        $popupExtensions['key'] = 'category';
                        $popupExtensions['value'] = 'diamond';
                    }
                    else if($myType['Type']['slug'] == 'cor-jewelry')
                    {
                        $popupExtensions['key'] = 'category';
                        $popupExtensions['value'] = '!diamond';
                    }
                }

                echo $this->Html->link('Browse',array('controller'=>'entries','action'=>$browse_slug,'admin'=>true,'?'=>$popupExtensions),array('class'=>'btn btn-info get-from-table'));
            ?>
            <input class="<?php echo $shortkey; ?>" type="hidden" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][value]" value="<?php echo $metaslug; ?>"/>

            <?php if(empty($required)): ?>
                <a class="btn btn-danger removeID" href="javascript:void(0)">Clear</a>  
            <?php endif; ?>

            <a target="_blank" id="<?php echo $shortkey; ?>_view_detail" class="btn btn-primary" href="#">View Detail</a>
            
            <p class="help-block">
                Want to create new one? Click <?php echo $this->Html->link('here<img alt="External Icon" src="'.$imagePath.'img/external-icon.gif">',array('controller'=>'entries','action'=>$browse_slug.'/add'),array("onclick"=>"javascript:openRequestedSinglePopup(this.href); return false;","escape"=>false)); ?>.<br/>
                <?php echo $p; ?>
            </p>
        </div>
	</div>
	<input type="hidden" value="<?php echo $key; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][key]"/>
	<input type="hidden" value="<?php echo $input_type; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][input_type]"/>
	<input type="hidden" value="<?php echo $validation; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][validation]"/>
	<input type="hidden" value="<?php echo $p; ?>" name="data[<?php echo $model; ?>][<?php echo $counter; ?>][instruction]"/>
</div>