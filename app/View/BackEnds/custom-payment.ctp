<?php
	$this->Get->create($data);
	if(is_array($data)) extract($data , EXTR_SKIP);

    // is it Diamond or Cor Jewelry payment ?
    $DMD = (strpos($myType['Type']['slug'], 'dmd-')!==FALSE?true:false);

    // is it Vendor or Client payment ?
    $VENDOR = (strpos($myType['Type']['slug'], '-vendor-')!==FALSE || strpos($myType['Type']['slug'], 'sr-')!==FALSE ?true:false);

    // initialize $extensionPaging for URL Query ...
    $extensionPaging = $this->request->query;
    unset($extensionPaging['lang']);
	if(!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug'])
	{
		$extensionPaging['type'] = $myChildType['Type']['slug'];
	}
	if(empty($popup))
	{
		$_SESSION['now'] = str_replace('&amp;','&',htmlentities($_SERVER['REQUEST_URI']));
	}
    else
    {
        $extensionPaging['popup'] = 'ajax';
    }
    // end of initialize $extensionPaging ...

	if($isAjax == 0)
	{
        if(!empty($myEntry) && $myEntry['EntryMeta']['payment_balance'] >= $myEntry['EntryMeta'][$DMD?'total_price':'total_weight'] )
		{
            ?>
			<div class="alert alert-info full fl">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<i class="icon-info-sign"></i> Transaksi pembayaran invoice <?php echo strtoupper($myEntry['Entry']['title']); ?> sudah lunas.
			</div>
			<?php
		}
		echo $this->element('admin_header', array('extensionPaging' => $extensionPaging));
		echo '<div class="inner-content '.(empty($popup)?'':'layout-content-popup').'" id="inner-content">';
		echo '<div class="autoscroll" id="ajaxed">';
	}
	else
	{
		if($search == "yes")
		{
			echo '<div class="autoscroll" id="ajaxed">';
		}
		?>
			<script>
				$(document).ready(function(){
					$('#cmsAlert').css('display' , 'none');
				});
			</script>
		<?php
	}
?>
<script>
	$(document).ready(function(){
		// attach checkbox on each record...
		if($('form#global-action').length > 0 || $('input#query-stream').length > 0 )
		{
            var checked_data = $('#checked-data').val();
			$('table#myTableList thead tr').prepend('<th><input type="checkbox" id="check-all" /><span style="color:red;" id="count-check-all"></span></th>');
			$('table#myTableList tbody tr').each(function(i,el){
                var entry_id = $(this).attr('alt');
				$(this).prepend('<td style="min-width: 0px;"><input type="checkbox" class="check-record" value="'+entry_id+'" '+(checked_data.indexOf(','+entry_id+',') >= 0?'CHECKED':'')+' /></td>');
			});

			$('input#check-all').change(function(e,init){
                if(init == null)
                {
                    $('input.check-record').attr('checked' , $(this).is(':checked') );
                }
                
                // update background color on each TR record...
                $('input.check-record').trigger('change', ['ignoreAttachButton']);
                
                // just a single call for this event ...
                $.fn.updateAttachButton();
                
			}).trigger('change', ['init']);
		}
		
		<?php if(empty($popup)): ?>
            // ADD & DELETE BUTTON have the same life fate !!
            if($('a.get-started').length == 0)
            {
                $('form#global-action > select > option[value=delete]').detach();
                $('table#myTableList i.icon-trash').parent('a').detach();
                if($('form#global-action > select > option').length == 1)
                {
                    $('form#global-action').detach();
                }
            }
        
			$('table#myTableList tr').css('cursor' , 'default').click(function(e){ if($('td').is(e.target) && $(this).find('input[type=checkbox]').length > 0) $(this).find('input[type=checkbox]').click(); });

			// submit bulk action checkbox !!
			if($('form#global-action').length > 0)
            {
                $('form#global-action').submit(function(){
                    var checked_data = $('#checked-data').val();
                    var total_checked = checked_data.split(',').length - 2;
                    if(total_checked > 0)
                    {
                        if(confirm('Are you sure to execute this BULK action ?'))
                        {
                            $(this).find('input#action-records').val( checked_data.substr(1, checked_data.length - 2 ) );
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        alert('Please select the record first before doing action !!');
                        return false;
                    }
                });
            }
			
			// ---------------------------------------------------------------------- >>>
			// FOR AJAX REASON !!
			// ---------------------------------------------------------------------- >>>
			$('p#id-title-description').html('Last updated by <a href="#"><?php echo (empty($lastModified['AccountModifiedBy']['username'])?$lastModified['AccountModifiedBy']['email']:$lastModified['AccountModifiedBy']['username']).'</a> at '.date_converter($lastModified['Entry']['modified'], $mySetting['date_format'] , $mySetting['time_format']); ?>');
			$('p#id-title-description').css('display','<?php echo (empty($totalList)?'none':'block'); ?>');
			
			// UPDATE TITLE HEADER !!
			$('div.title > h2').html('<?php echo (empty($myEntry)?$myType['Type']['name']:$myEntry['Entry']['title'].' - '.$myChildType['Type']['name']); ?>');
			
		<?php else: ?>
			$('table#myTableList tbody tr').css('cursor' , 'pointer');
			$('input[type=checkbox]').css('cursor' , 'default');

			$('table#myTableList tbody tr').click(function(e){
				if(!$('input[type=checkbox]').is(e.target))
				{
					var targetID = $('input#query-alias').val() + ($('input#query-stream').length > 0?$('input#query-stream').val():'');
                    
                    var richvalue = '';
					if($(this).find("td.form-name").length > 0)
					{
					    richvalue = $(this).find("td.form-name").text()+' ('+$(this).find("h5.title-code").text()+')';
					}
					else
					{
					    richvalue = $(this).find("h5.title-code").text();
					}
                    
                    $("input#"+targetID).val(richvalue).nextAll("input[type=hidden]").val( $(this).find("input[type=hidden].slug-code").val() );
					$("input#"+targetID).change();

					// update other attribute ...
                    var $trytotal = $("input#"+targetID).nextAll('input[type=number]');
                    if($trytotal.length > 0)
                    {
                        $trytotal.removeAttr('readonly').focus();
                    }

					if(!e.isTrigger)    $.colorbox.close();
				}
			});
		<?php endif; ?>
		// ---------------------------------------------------------------------- >>>
		// FOR AJAX REASON !!
		// ---------------------------------------------------------------------- >>>        
        <?php
            if($VENDOR)
            {
                ?>
        // UPDATE SEARCH LINK !!
		$('a.searchMeLink').attr('href',site+'admin/entries/<?php echo $myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']); ?>/index/1<?php echo get_more_extension($extensionPaging); ?>');
                <?php
            }
            else // client payment ...
            {
                if($isAjax == 0)
                {
                    ?>
        // CHANGE INNER HEADER SPAN SIZE !!
        $('div.inner-header:last > div:first').removeClass('span5').addClass('span9');
        $('div.inner-header:last > div:last').removeClass('span7').addClass('span3');
        
		// DELETE SEARCH LINK !!
		$('a.searchMeLink').closest('div.input-prepend').hide();
        
        // HIDE SORT UTILITY !!
        $('a.order_by:first').closest('div.btn-group').hide();            
                    <?php
                }
            }
        ?>
		
		// UPDATE ADD NEW DATABASE LINK !!
		$('a.get-started').attr('href',site+'admin/entries/<?php echo $myType['Type']['slug'].'/'.(empty($myEntry)?'':$myEntry['Entry']['slug'].'/').'add'.(!empty($extensionPaging['type'])?'?type='.$extensionPaging['type']:''); ?>');
		
		// disable language selector ONLY IF one language available !!		
		var myLangSelector = ($('#colorbox').length > 0 && $('#colorbox').is(':visible')? $('#colorbox').find('div.lang-selector:first') : $('div.lang-selector')  );
		if(myLangSelector.find('ul.dropdown-menu li').length <= 1)	myLangSelector.hide();
        
        // change thead column order ...
        $('table#myTableList thead a[alt^="form-date"]').closest('th').after( $('table#myTableList thead a[alt*="_to_"]').closest('th') );
        
        $('table#myTableList thead a[alt^="form-statement"]').closest('th').before( $('table#myTableList thead a[alt^="form-amount"]').closest('th') );
        
        <?php
            if( ! $VENDOR )
            {
                ?>
        // replace thead a element => with its html only ...
        $('table#myTableList thead th').each(function(i,el){
            if($(el).find('a').length > 0)
            {
                $(el).html( $(el).find('a').html() );
            }
        });            
                <?php
            }
        ?>
        
        // change tbody column order ...
        $('table#myTableList tbody tr').each(function(i,el){
            $(el).find('td.form-date').after( $(el).find('td.main-title') );
            $(el).find('td.form-statement').before( $(el).find('td.form-amount') );
            
            // append additional_cost currency ...
            if($(el).find('td.form-additional_cost').length)
            {
                $(el).find('td.form-additional_cost strong').append(' ' + $(el).find('td.form-cost_currency a').text() );
            }
            
            // set receiver payment ...
            if($(el).find('td.form-receiver').length)
            {
                $(el).find('td.form-receiver').html( $(el).find('td.form-vendor').text() == '-' ? $(el).find('td.form-warehouse').html() : $(el).find('td.form-vendor').html() );
            }
        });
        
        $('a.withdraw_checks').click(function(e){
            if(!confirm('Are you sure to withdraw this checks today?<?php
                if( ! $VENDOR )
                {
                    echo '\nNB: This action will automatically update invoice balance too.';
                }
            ?>'))
            {
                e.preventDefault();
            }
        });
	});
</script>
<?php if($totalList <= 0){ ?>
	<div class="empty-state item">
		<div class="wrapper-empty-state">
			<div class="pic"></div>
			<h2>No Items Found!</h2>
			<?php echo (!($myType['Type']['slug'] == 'pages' && $user['role_id'] >= 2 || !empty($popup))?$this->Html->link('Get Started',array('action'=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'add','?'=> (!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?array('type'=>$myChildType['Type']['slug']):'') ),array('class'=>'btn btn-primary')):''); ?>
		</div>
	</div>
<?php }else{ ?>
<table id="myTableList" class="list">
	<thead>
	<tr>
		<?php
            $sortASC = '&#9650;';
            $sortDESC = '&#9660;';
			$myAutomatic = (empty($myChildType)?$myType['TypeMeta']:$myChildType['TypeMeta']);
			$titlekey = "Title";
			foreach ($myAutomatic as $key => $value)
			{
				if($value['key'] == 'title_key')
				{
					$titlekey = $value['value'];
					break;
				}
			}
		?>
		<th>
		    <?php
                echo $this->Html->link($titlekey.' ('.$totalList.')'.($_SESSION['order_by'] == 'title ASC'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == 'title DESC'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$_SESSION['order_by'] == 'title DESC'?"a_to_z":"z_to_a"));
            ?>
		</th>
		
		<?php
			// if this is a parent Entry !!
			if(empty($myEntry) && empty($popup)) 
			{
				foreach ($myType['ChildType'] as $key10 => $value10)
				{
					echo '<th>'.$value10['name'].'</th>';
				}
			}
			
			// check for simple or complex table view !!
			if($mySetting['table_view'] == "complex")
			{
				foreach ( $myAutomatic as $key => $value) 
				{
					if(substr($value['key'], 0,5) == 'form-')
					{
						$entityTitle = $value['key'];
                        $hideKeyQuery = '';
                        $shortkey = substr($entityTitle, 5);
                        if(!empty($popup) && $this->request->query['key'] == $shortkey && substr($this->request->query['value'] , 0 , 1) != '!' || $shortkey == 'hkd_rate' || $shortkey == 'rp_rate' || $shortkey == 'gold_price' || $shortkey == 'cost_currency' || $shortkey == 'gold_bar_rate' || $shortkey == 'vendor' || $shortkey == 'warehouse')
                        {
                            $hideKeyQuery = 'hide';
                        }
                        
                        $datefield = '';
                        switch($value['input_type'])
                        {
                            case 'datepicker':
                            case 'datetimepicker':
                            case 'multidate':
                                $datefield = 'date-field';
                                break;
                            case 'multibrowse':
                                $datefield = 'product-field';
                                break;
                        }
                        
                        echo "<th ".($value['input_type'] == 'textarea' || $value['input_type'] == 'ckeditor'?"style='min-width:200px;'":"")." class='".$hideKeyQuery." ".$datefield."'>";
                        echo $this->Html->link(string_unslug($shortkey).($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' desc'?" asc":" desc") ));
						echo "</th>";
                        
                        if($shortkey == 'statement')
                        {
                            if( ! $VENDOR)
                            {
                                echo '<th>accumulated balance</th>';
                            }
                            echo '<th>status</th>';
                        }
					}
				}
			}
		?>		
		<th class="date-field">
            <?php
                $entityTitle = "modified";
                echo $this->Html->link('last '.string_unslug($entityTitle).($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' desc'?" asc":" desc") ));
            ?>
        </th>
        <th>
		    <?php
                $entityTitle = "modified_by";
                echo $this->Html->link('last updated by'.($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' desc'?" asc":" desc") ));
            ?>
		</th>
		<?php
			if(empty($popup))
			{
				?>
		<th class="action">[ action ]</th>	
				<?php
			}
		?>
	</tr>
	</thead>
	
	<tbody>
	<?php
        $walking_balance = 0;
		$orderlist = "";
		foreach ($myList as $value):
		$orderlist .= $value['Entry']['sort_order'].",";
	?>	
	<tr class="orderlist" alt="<?php echo $value['Entry']['id']; ?>">
		<td class="main-title">
			<?php
				if($imageUsed == 1)
				{
					echo '<div class="thumbs">';
					echo (empty($popup)&&!empty($value['Entry']['main_image'])?$this->Html->link($this->Html->image('upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']], array('alt'=>$value['ParentImageEntry']['title'],'title' => $value['ParentImageEntry']['title'])),'/img/upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']],array("escape"=>false,"class"=>"popup-image","title"=>$value['Entry']['title'])):$this->Html->image('upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']], array('alt'=>$value['ParentImageEntry']['title'],'title' => $value['ParentImageEntry']['title'])));
					echo '</div>';
				}
        
                $editUrl = array('action'=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'edit',$value['Entry']['slug'] ,'?'=> (!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?array('type'=>$myChildType['Type']['slug']):'')   );
			?>
			<input class="slug-code" type="hidden" value="<?php echo $value['Entry']['slug']; ?>" />
			<h5 class="title-code"><?php echo (empty($popup)?$this->Html->link($value['Entry']['title'],$editUrl):$value['Entry']['title']); ?></h5>
            <?php
                if(!empty($value['Entry']['description']))
                {
                    $description = nl2br($value['Entry']['description']);
                    echo '<p>'.(strlen($description) > 30? '<a href="#" data-placement="right" data-toggle="tooltip" title="'.$description.'">'.substr($description,0,30).'...</a>' : $description).'</p>';
                }
            ?>
		</td>
		<?php
			if(empty($myEntry) && empty($popup)) // if this is a parent Entry !!
			{
				foreach ($myType['ChildType'] as $key10 => $value10)
				{
					$childCount = 0;
					foreach ($value['EntryMeta'] as $key20 => $value20) 
					{
						if($value20['key'] == 'count-'.$value10['slug'])
						{
							$childCount = $value20['value'];
							break;
						}
					}
					echo '<td><span class="badge badge-info">'.$this->Html->link($childCount,array('action'=>$myType['Type']['slug'],$value['Entry']['slug'],'?'=>array('type'=>$value10['slug'], 'lang'=>$_SESSION['lang']))).'</span></td>';
				}
			}

			// check for simple or complex table view !!
			if($mySetting['table_view'] == "complex")
			{				 
				foreach ( $myAutomatic as $key10 => $value10) 
				{
					if(substr($value10['key'], 0,5) == 'form-')
					{
						$shortkey = substr($value10['key'], 5);
                        $displayValue = $value['EntryMeta'][$shortkey];
                        $hideKeyQuery = '';
                        if(!empty($popup) && $this->request->query['key'] == $shortkey && substr($this->request->query['value'] , 0 , 1) != '!' || $shortkey == 'hkd_rate' || $shortkey == 'rp_rate' || $shortkey == 'gold_price' || $shortkey == 'cost_currency' || $shortkey == 'gold_bar_rate' || $shortkey == 'vendor' || $shortkey == 'warehouse')
                        {
                            $hideKeyQuery = 'hide';
                        }
                        
                        echo "<td class='".$value10['key']." ".$hideKeyQuery."'>";
                        if(empty($displayValue))
                        {
                        	echo '-';
                        }
                        else if($value10['input_type'] == 'multibrowse')
						{
							$browse_slug = '';
                            if($shortkey == 'payment_jewelry')
                            {
                                $browse_slug = 'cor-jewelry';
                            }
                            else if($shortkey == 'payment_diamond')
                            {
                                $browse_slug = 'diamond';
                            }
                            else
                            {
                                $browse_slug = get_slug($shortkey);
                            }
                            
							$displayValue = explode('|', $displayValue);
							$emptybrowse = 0;
							foreach ($displayValue as $brokekey => $brokevalue) 
							{
                                $brokeWithTotal = explode('_', $brokevalue);
                                
                                $brokevalue = $brokeWithTotal[0];
                                $broketotal = $brokeWithTotal[1];
                                
								$mydetails = $this->Get->meta_details($brokevalue , $browse_slug );
								if(!empty($mydetails))
								{
									$emptybrowse = 1;
									$outputResult = (empty($mydetails['EntryMeta']['name'])?$mydetails['Entry']['title']:$mydetails['EntryMeta']['name']);
                                    
                                    if(strpos($shortkey, 'diamond') !== FALSE)
                                    {
                                        $outputResult .= ' '.$diamondType[$mydetails['EntryMeta']['product_type']];
                                    }
                                    
									echo '<p>'.(empty($popup)?$this->Html->link($outputResult,array('controller'=>'entries','action'=>$mydetails['Entry']['entry_type'],'edit',$mydetails['Entry']['slug']),array('target'=>'_blank')):$outputResult).(!empty($broketotal)?' ('.$broketotal.' '.($DMD?'USD':'gr').')':'').'</p>';
                                    echo '<input data-total="'.$broketotal.'" type="hidden" value="'.$mydetails['Entry']['slug'].'">';
								}
							}
							
							if($emptybrowse == 0)
							{
								echo '-';
							}
						}
                        else if($value10['input_type'] == 'browse')
                        {
                        	$entrytype = '';
                            if($shortkey == 'cost_currency')
                            {
                                $entrytype = 'usd-rate';
                            }
                            else if($shortkey == 'warehouse_payer')
                            {
                                $entrytype = 'warehouse';
                            }
                            else
                            {
                                $entrytype = get_slug($shortkey);
                            }
                            
                            $entrydetail = $this->Get->meta_details($displayValue , $entrytype);
							if(empty($entrydetail))
							{
								echo $displayValue;
							}
                            else if($shortkey == 'bank')
                            {
                                $imgLink = $this->Get->image_link(array('id' => $entrydetail['Entry']['main_image']));
                                echo '<div class="thumbs">';
                                echo '<img src="'.$imgLink['display'].'" alt="'.$entrydetail['Entry']['title'].'">';
                                echo '</div>';
                            }
							else
							{
								$outputResult = (empty($entrydetail['EntryMeta']['name'])?$entrydetail['Entry']['title']:$entrydetail['EntryMeta']['name']);
								echo '<h5>'.(empty($popup)?$this->Html->link($outputResult,array("controller"=>"entries","action"=>$entrydetail['Entry']['entry_type']."/edit/".$entrydetail['Entry']['slug']),array('target'=>'_blank')):$outputResult).'</h5>';
                                echo '<input type="hidden" value="'.$entrydetail['Entry']['slug'].'">';
                                
                                echo '<p>';                                
                                // Try to use Primary EntryMeta first !!
                                $targetMetaKey = NULL;
                                foreach($entrydetail['EntryMeta'] as $metakey => $metavalue)
                                {
                                    if(substr($metavalue['key'] , 0 , 5) == 'form-')
                                    {
                                        $targetMetaKey = $metakey;
                                        break;
                                    }
                                }
                                
                                if(isset($targetMetaKey))
                                {
                                    // test if value is a date value or not !!
                                    if(strtotime($entrydetail['EntryMeta'][$targetMetaKey]['value']) && !is_numeric($entrydetail['EntryMeta'][$targetMetaKey]['value']))
                                    {
                                        echo date_converter($entrydetail['EntryMeta'][$targetMetaKey]['value'] , $mySetting['date_format']);
                                    }
                                    else
                                    {
                                        echo $entrydetail['EntryMeta'][$targetMetaKey]['value'];
                                    }
                                }
                                else
                                {
                                    $description = nl2br($entrydetail['Entry']['description']);
                            	    echo (strlen($description) > 30? '<a href="#" data-toggle="tooltip" title="'.$description.'">'.substr($description,0,30).'...</a>' : $description);
                                }                                
                                echo '</p>';
							}
                        }
                        else
                        {
                            // SUPER CUSTOMIZED OUTPUT STYLE ...
                            if($shortkey == 'amount')
                            {
                                echo '<strong>'.toMoney($displayValue  , true , true).' '.($DMD?'USD':'gr').'</strong>';
                            }
                            else if($shortkey == 'additional_charge' || $shortkey == 'gold_loss')
                            {
                                echo $displayValue.'%';
                            }
                            else if($shortkey == 'loan_period')
                            {
                                echo '<strong>'.$displayValue.' mo.</strong>';
                            }
                            else if($shortkey == 'statement')
                            {
                                echo '<span class="label '.($displayValue=='Debit'?'label-info':'label-important').'">'.$displayValue.'</span>';
                            }
                            else
                            {
                                echo $this->Get->outputConverter($value10['input_type'] , $displayValue , $myImageTypeList , $shortkey);
                            }
                        }                        
                        echo "</td>";
                        
                        if($shortkey == 'statement')
                        {
                            $allowed_balance = true;
                            if( !empty($value['EntryMeta']['checks_date']) && strtotime($value['EntryMeta']['checks_date']) > strtotime(date('m/d/Y')) || !empty($value['EntryMeta']['loan_period']) )
                            {
                                $allowed_balance = false;
                            }
                            
                            if( ! $VENDOR )
                            {
                                echo "<td class='form-balance'>";
                                if($value['Entry']['status'] == 1 && $allowed_balance)
                                {
                                    $walking_balance += ($value['EntryMeta']['statement']=='Debit'?1:-1) * $value['EntryMeta']['amount'] * ($VENDOR?1:-1);
                                }
                                echo '<strong>'.toMoney( $walking_balance , true , true).' '.($DMD?'USD':'gr').'</strong>';
                                echo "</td>";
                            }
                            
                            // echo payment status too ...
                            echo "<td>";
                            if($value['Entry']['status']==0) // Pending
                            {
                                echo '<span class="label label-important">Pending</span>';
                            }
                            else if($allowed_balance == false) // Waiting
                            {
                                echo '<span class="label label-info">Waiting</span>';
                                if(!empty($value['EntryMeta']['checks_date']))
                                {
                                    echo '<br><a title="CLICK TO WITHDRAW FUND CHECKS TODAY." data-toggle="tooltip" class="btn btn-mini withdraw_checks" href="'.$imagePath.'entry_metas/withdraw_checks/'.$value['Entry']['id'].'">Withdraw</a>';
                                }
                            }
                            else // Complete
                            {
                                echo '<span class="label label-success">Complete</span>';
                            }
                            echo "</td>";
                        }
					}
				}
			}
		?>
		<td><?php echo date_converter($value['Entry']['modified'], $mySetting['date_format'] , $mySetting['time_format']); ?></td>
		<td style='min-width: 0px;' <?php echo (empty($popup)?'':'class="offbutt"'); ?>>
            <span class="label label-inverse">
				<?php echo $value['AccountModifiedBy']['username']; ?>
			</span>
		</td>
		<?php
			if(empty($popup))
			{
                echo "<td class='action-btn'>";
                echo $this->Html->link('<i class="icon-edit icon-white"></i>', $editUrl, array('escape'=>false, 'class'=>'btn btn-info','data-toggle'=>'tooltip', 'title'=>'CLICK TO EDIT / VIEW DETAIL') );
                
                ?>
            &nbsp;<a data-toggle="tooltip" title="CLICK TO DELETE RECORD" href="javascript:void(0)" onclick="show_confirm('Are you sure want to delete <?php echo strtoupper($value['Entry']['title']); ?> ?','entries/delete/<?php echo $value['Entry']['id']; ?>')" class="btn btn-danger"><i class="icon-trash icon-white"></i></a>
				<?php
				echo "</td>";
			}				
		?>
	</tr>
	
	<?php
		endforeach;
	?>
	</tbody>
</table>
<input type="hidden" id="determine" value="<?php echo $orderlist; ?>" />
<div class="clear"></div>
<input type="hidden" value="<?php echo $countPage; ?>" id="myCountPage"/>
<input type="hidden" value="<?php echo $left_limit; ?>" id="myLeftLimit"/>
<input type="hidden" value="<?php echo $right_limit; ?>" id="myRightLimit"/>
<?php
	if($isAjax == 0 || $isAjax == 1 && $search == "yes")
	{
		echo '</div>';
        if($paging > 0)
        {
            echo $this->element('admin_footer', array('extensionPaging' => $extensionPaging));
        }
		echo '<div class="clear"></div>';
		echo ($isAjax==0?"</div>":"");
	}
?>

<?php } ?>
<script type="text/javascript">
    $(document).ready(function(){
        <?php if(empty($popup)): ?>
            if(window.opener != null && window.name.length > 0)
            {
            	setTimeout("window.close()" , delayCloseWindow);
            }
        <?php endif; ?>
        
        // apply doubleScroll event !!
        $.fn.doubleScroll('autoscroll');
    });         
</script>