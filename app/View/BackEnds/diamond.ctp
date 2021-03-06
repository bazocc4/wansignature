<?php
	$this->Get->create($data);
	if(is_array($data)) extract($data , EXTR_SKIP);

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
        echo $this->element('admin_header', array('extensionPaging' => $extensionPaging));
		echo '<div class="inner-content '.(empty($popup)?'':'layout-content-popup').'" id="inner-content">';
		echo '<div class="autoscroll" id="ajaxed">';
        
        if(empty($popup))
        {
            ?>
            <script type="text/javascript" language="javascript">
                function checkfile(sender) 
                {
                    var validExts = new Array(".xls",".xlsx");
                    var fileExt = sender.value;
                    fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
                    if (validExts.indexOf(fileExt) < 0) {

                      alert("Invalid file selected, valid files are of " +
                               validExts.toString() + " types.");

                      $(sender).val("");

                      return false;
                    }
                    else return true;
                }

                $(document).ready(function(){
                    // modify element header view !!
                    <?php
                        if(empty($this->request->query['type-alias']))
                        {
                            if( ! $staticRecordTemplate)
                            {
                                ?>
                    $('div.inner-header > div:last > div:last').after("<div class='btn-group'><form accept='application/vnd.ms-excel' accept-charset='utf-8' method='post' enctype='multipart/form-data' action='#' style='margin:0 0 10px 0;' class='form-upload-excel'><input REQUIRED type='file' style='width:200px;' accept='.xls,.xlsx' name='data[fileurl]' onchange='checkfile(this);'><button class='btn' type='submit'>Upload</button></form></div>"); // upload Excel ...            
                                <?php
                            }
                        }
                        else
                        {
                            ?>
                    $('div.inner-header > div:last > div:last').after("<div class='btn-group'>\
<select class='input-medium right-btn' id='select-cidm'>\
<?php
    for($i = 1 ; $i <= 12 ; ++$i)
    {
        echo "<option ".($this->request->query['cidm']==$i?'SELECTED':'')." value='".$i."'>".strtoupper(date("F", mktime(0, 0, 0, $i, 10)))."</option>";
    }
?>\
</select>\
<select class='input-small right-btn' id='select-cidy'>\
<?php
    $nowYear = date('Y');
    for($i = 0 ; $i < 20 ; ++$i)
    {
        $tempYear = $nowYear - $i;
        
        echo "<option ".($this->request->query['cidy']==$tempYear?'SELECTED':'')." value='".$tempYear."'>".$tempYear."</option>";
    }
?>\
</select>\
<span><a id='search-this-month' class='btn btn-success right-btn' style='margin-top:-10px;' href='"+site+"admin/entries/<?php echo $myType['Type']['slug']; ?>?key=product_status&value=sold&type-alias=<?php echo $this->request->query['type-alias']; ?>'><i class='icon-white icon-search'></i> Search Monthly</a></span>\
</div>"); // search by client invoice date (month & year)...
                    
                    $('a#search-this-month').click(function(){
                        var cidm = $('#select-cidm').val();
                        var cidy = $('#select-cidy').val();
                        $(this).attr('href', $(this).attr('href')+'&cidm='+cidm+'&cidy='+cidy );
                    });
                            <?php
                        }
            
                        // DOWNLOAD SETTING VARIABLE !!
                        if( ! $staticRecordTemplate)
                        {
                            $download_query = '';
                            $submit_func = false;
                            $download_title = '';
                            if(!empty($this->request->query['cidm']) && !empty($this->request->query['cidy']))
                            {
                                $download_query = '?cidm='.$this->request->query['cidm'].'&cidy='.$this->request->query['cidy'];
                                $download_title = 'Download Monthly Report';
                            }
                            else
                            {
                                $submit_func = true;
                                $download_title = 'Download Excel';
                            }
                            
                            ?>
                    $('div.inner-header > div:last > div:last').before("<form id='download-excel' action='"+site+"entries/download_diamond<?php echo $download_query; ?>' method='POST'><input value=',' type='hidden' name='data[record]'><button style='margin-bottom:10px;' class='btn btn-inverse right-btn fr' type='submit'><i class='icon-download-alt icon-white'></i> <?php echo $download_title; ?></button></form>"); // download Excel ...            
                            <?php
                            
                            if($submit_func)
                            {
                                ?>
                        $('form#download-excel').submit(function(){
                            var checked_data = $('#checked-data').val();
                            var total_checked = checked_data.split(',').length - 2;
                            if(total_checked > 0)
                            {
                                $(this).find('input[name="data[record]"]').val( checked_data.substr(1, checked_data.length - 2 ) );
                            }
                            else
                            {
                                alert('Please select one or more DIAMOND to be downloaded into excel format.');
                                $('input#check-all').focus();
                                return false;
                            }
                        });            
                                <?php
                            }
                        }
                    ?>
                });
            </script>
            <?php
        }
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
        
		<?php else: ?>
			$('table#myTableList tbody tr').css('cursor' , 'pointer');
			$('input[type=checkbox]').css('cursor' , 'default');

			$('table#myTableList tbody tr').click(function(e){
				if(!$('input[type=checkbox]').is(e.target))
				{
					var targetID = $('input#query-alias').val() + ($('input#query-stream').length > 0?$('input#query-stream').val():'');
                    
                    var richvalue = $(this).find("h5.title-code").text() + ' ' + $(this).find('td.form-product_type h5').text();
                    
                    var usd_result = 0;
                    if($('#myTypeSlug').val() == 'sr-dmd-payment')
                    {
                        var barcode = $(this).find('td.form-vendor_barcode').text();
                        usd_result = parseFloat($(this).find('td.form-vendor_barcode input[type=hidden]').val());
                        
                        var currency = $(this).find('td.form-vendor_currency').text();
                        
                        var vendor_x = $(this).find('td.form-vendor_x').text();
                        if(vendor_x == '-')
                        {
                            vendor_x = 1;
                        }
                        usd_result *= parseFloat(vendor_x);
                        
                        var vendor_rate = '';
                        if(currency == 'HKD')
                        {
                            vendor_rate = ($.isNumeric($('input.hkd_rate').val())?$('input.hkd_rate').val():7.75);
                            usd_result /= parseFloat(vendor_rate);
                            vendor_rate = ' / '+vendor_rate;
                        }
                        
                        richvalue += ' ['+barcode+' '+currency+' x '+vendor_x+vendor_rate+']';
                    }
                    else if($('#myTypeSlug').val() == 'dmd-vendor-invoice')
                    {
                        var barcode = parseFloat($(this).find('td.form-vendor_barcode input[type=hidden]').val());
                        if(barcode != 1)
                        {
                            usd_result = barcode;
                            var inv_cur = $('input[type=radio].currency:checked').val();
                            var this_cur = $(this).find('td.form-vendor_currency').text();
                            
                            var hkd_rate = $('input.hkd_rate').val();
                            if($.isNumeric(hkd_rate))
                            {
                                if(inv_cur == 'HKD' && this_cur == 'USD')
                                {
                                    usd_result *= parseFloat(hkd_rate);
                                }
                                else if(inv_cur == 'USD' && this_cur == 'HKD')
                                {
                                    usd_result /= parseFloat(hkd_rate);
                                }
                            }
                        }
                    }
                    else // diamond client ...
                    {
                        var barcode = $(this).find('td.form-sell_barcode').text();
                        if(barcode == '-')
                        {
                            barcode = $(this).find('td.form-barcode').text();
                            usd_result = parseFloat($(this).find('td.form-barcode input[type=hidden]').val());
                        }
                        else
                        {
                            usd_result = parseFloat($(this).find('td.form-sell_barcode input[type=hidden]').val());
                        }
                        
                        if($('#myTypeSlug').val() == 'dc-payment')
                        {
                            var client_x = $(this).find('td.form-client_x').text();
                            if(client_x == '-')
                            {
                                client_x = ( $('#client_x').val()=='' ? 1 : $('#client_x').val() );
                            }
                            usd_result *= parseFloat(client_x);
                            
                            richvalue += ' ['+barcode+' x '+client_x+']';
                        }
                        else // invoice ...
                        {
                            if(usd_result == 1)
                            {
                                usd_result = 0;
                            }
                        }
                    }
                    
                    $("input#"+targetID).val(richvalue).nextAll("input[type=hidden]").val( $(this).find("input[type=hidden].slug-code").val() );
					$("input#"+targetID).change();

					// update other attribute ...
                    var $trytotal = $("input#"+targetID).nextAll('input[type=number]');
                    if($trytotal.length > 0)
                    {
                        $trytotal.removeAttr('readonly');
                        if(usd_result > 0)
                        {
                            $trytotal.val( usd_result.toFixed(2) ).keyup();
                        }
                    }

					if(!e.isTrigger)    $.colorbox.close();
				}
			});
		<?php endif; ?>
		// ---------------------------------------------------------------------- >>>
		// FOR AJAX REASON !!
		// ---------------------------------------------------------------------- >>>
		
		// UPDATE SEARCH LINK !!
		$('a.searchMeLink').attr('href',site+'admin/entries/<?php echo $myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']); ?>/index/1<?php echo get_more_extension($extensionPaging); ?>');
		
		// UPDATE ADD NEW DATABASE LINK !!
        <?php
            if(empty($this->request->query['type-alias']))
            {
                ?>
        $('a.get-started').attr('href',site+'admin/entries/<?php echo $myType['Type']['slug'].'/'.(empty($myEntry)?'':$myEntry['Entry']['slug'].'/').'add'.(!empty($extensionPaging['type'])?'?type='.$extensionPaging['type']:''); ?>');
                <?php
            }
            else
            {
                if($isAjax == 0)
                {
                    ?>
        $('a.get-started').html('Pay to Vendor').attr('href', site+'admin/entries/sr-dmd-payment/add').click(function(e){
            e.preventDefault();            
            var checked_data = $('#checked-data').val();
            var total_checked = checked_data.split(',').length - 2;
            if(total_checked > 0)
            {
                post_submit( $(this).attr('href') , {srid: checked_data.substr(1, checked_data.length - 2 ) });
            }
            else
            {
                alert('Please select one or more SOLD DIAMOND to be paid to Vendor.');
                $('input#check-all').focus();
            }
        });
                    <?php
                }
            }
        ?>
		
		// disable language selector ONLY IF one language available !!		
		var myLangSelector = ($('#colorbox').length > 0 && $('#colorbox').is(':visible')? $('#colorbox').find('div.lang-selector:first') : $('div.lang-selector')  );
		if(myLangSelector.find('ul.dropdown-menu li').length <= 1)	myLangSelector.hide();
        
        // UPDATE TITLE HEADER !!
        <?php
            if($isAjax == 0)
            {
                if(strpos($this->request->query['key'], '_invoice_code') !== FALSE && !empty($this->request->query['value']))
                {
                    $query = $this->Get->meta_details($this->request->query['value']);
                    if(!empty($query))
                    {
                        ?>
            $('div.title:last > h2').append(' <span style="color:red;">INV# <?php echo $query['Entry']['title']; ?></span>');
                        <?php
                    }
                }
                else if($this->request->query['key'] == 'report_type' && $this->request->query['value'] == '!R')
                {
                    ?>
            $('div.title:last > h2').append(' <span style="color:red;">(Consignment)</span>');
                    <?php
                }
                else if($this->request->query['key'] == 'product_status' && !empty($this->request->query['value']) )
                {
                    ?>
            $('div.title:last > h2').append(' <span style="color:red;">(<?php echo $this->request->query['value']; ?>)</span>');
                    <?php
                    if($this->request->query['value'] == 'sold')
                    {
                        ?>
            $('div.title:last > p.title-description:first').append(' <span style="color:red;">(Laporan Penjualan)</span>');
                        <?php
                    }
                }
            }
        ?>
	});
</script>
<?php if($totalList <= 0){ ?>
	<div class="empty-state item">
		<div class="wrapper-empty-state">
			<div class="pic"></div>
			<?php
                if(empty($this->request->query['type-alias']))
                {
                    echo '<h2>No Items Found!</h2>';
                    
                    echo (!($myType['Type']['slug'] == 'pages' && $user['role_id'] >= 2 || !empty($popup) || $staticRecordTemplate)?$this->Html->link('Get Started',array('action'=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'add','?'=> (!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?array('type'=>$myChildType['Type']['slug']):'') ),array('class'=>'btn btn-primary')):'');
                }
                else
                {
                    if(!empty($this->request->query['cidm']) && !empty($this->request->query['cidy']) )
                    {
                        echo '<h2>No Items Found on '.date("F", mktime(0, 0, 0, $this->request->query['cidm'] , 10)).' '.$this->request->query['cidy'].'!</h2>';
                    }
                    else
                    {
                        echo '<h2>No Items Found on selected month!</h2>';
                    }
                }
            ?>
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
    
            // CUSTOM CASE - change order of column !!
            if($this->request->query['type-alias'] == 'sr-dmd-monthly')
            {
                $haystack = array_column($myAutomatic, 'key');
                
                // fetch most behind field first ...
                $clientArray = array_splice( $myAutomatic, array_search('form-client_invoice_code', $haystack), 2 );
                $reportArray = array_splice( $myAutomatic, array_search('form-report_date', $haystack), 3 );
                $statusArray = array_splice( $myAutomatic, array_search('form-product_status', $haystack), 1 );
                
                array_splice( $myAutomatic , array_search('form-barcode', $haystack) , 0, array_merge($clientArray, $statusArray, $reportArray) );
            }
    
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
		<th class="date-field">
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
                        if(!empty($popup) && $this->request->query['key'] == $shortkey && substr($this->request->query['value'] , 0 , 1) != '!')
                        {
                            $hideKeyQuery = 'hide';
                        }
                        
                        $datefield = '';
                        if($shortkey == 'carat' || $shortkey == 'item_ref_code' || $shortkey == 'product_status' || $shortkey == 'temp_report')
                        {
                            $datefield = 'date-field';
                        }
                        else
                        {
                            switch($value['input_type'])
                            {
                                case 'datepicker':
                                case 'datetimepicker':
                                case 'multidate':
                                    $datefield = 'date-field';
                                    break;
                                case 'textarea':
                                case 'ckeditor':
                                    $datefield = 'ck-field';
                                    break;
                            }
                        }
                        
                        echo "<th class='".$hideKeyQuery." ".$datefield."'>";
                        echo $this->Html->link(string_unslug($shortkey).($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' desc'?" asc":" desc") ));
						echo "</th>";
					}
				}
			}
        
            // show gallery count !!
            if($gallery)
            {
                echo '<th>Gallery Count</th>';
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
/*
                $entityTitle = "status";
                echo $this->Html->link(string_unslug($entityTitle).($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' desc'?" asc":" desc") ));
*/
                $entityTitle = "modified_by";
                echo $this->Html->link('last updated by'.($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' desc'?" asc":" desc") ));
            ?>
		</th>
		<?php
			if(empty($popup))
			{
				?>
		<th class="action">
			<form id="global-action" style="margin: 0;" action="#" accept-charset="utf-8" method="post" enctype="multipart/form-data" class="<?php echo ($user['role_id']<=2?'':'hide'); ?>">
				<select REQUIRED name="data[action]" class="input-small">
					<option style="font-weight: bold;" value="">Action :</option>
					<?php
/*
                        if($myType['Type']['slug'] != 'pages')
                        {
                            ?>
                    <option value="active">Publish</option>
					<option value="disable">Draft</option>                            
                            <?php
                        }
*/
                    ?>
					<option value="delete">Delete</option>
				</select>
				<input type="hidden" name="data[record]" id="action-records" />
				<button type="submit" style="margin-top: -10px;" class="btn btn-success"><strong>GO!</strong></button>
			</form>
		</th>	
				<?php
			}
		?>
	</tr>
	</thead>
	
	<tbody>
	<?php		
		$orderlist = "";
		foreach ($myList as $value):
		$orderlist .= $value['Entry']['sort_order'].",";
	?>	
	<tr class="orderlist" alt="<?php echo $value['Entry']['id']; ?>">
		<td>
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
                        if(!empty($popup) && $this->request->query['key'] == $shortkey && substr($this->request->query['value'] , 0 , 1) != '!')
                        {
                            $hideKeyQuery = 'hide';
                        }
                        
                        echo "<td class='".$value10['key']." ".$hideKeyQuery."'>";
                        if(empty($displayValue))
                        {
                        	if($value10['input_type'] == 'gallery' && !empty($value['EntryMeta']['count-'.$value10['key']]))
                        	{
                        		$queryURL = array('anchor' => $shortkey );
                        		if( !empty($myEntry) && $myType['Type']['slug']!=$myChildType['Type']['slug'] )
                        		{
                        			$queryURL['type'] = $myChildType['Type']['slug'];
                        		}
                        		echo '<span class="badge badge-info">'.(empty($popup)?$this->Html->link($value['EntryMeta']['count-'.$value10['key']].' <i class="icon-picture icon-white"></i>',array('action'=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']) , 'edit' , $value['Entry']['slug'] , '?' => $queryURL ), array('escape'=>false, 'data-toggle'=>'tooltip','title' => 'Click to see all images.')):$value['EntryMeta']['count-'.$value10['key']].' <i class="icon-picture icon-white"></i>').'</span>';
                        	}
                        	else
                        	{
                        		echo '-';	
                        	}
                        }
                        else if($value10['input_type'] == 'multibrowse')
						{
							$browse_slug = get_slug($shortkey);
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
									$outputResult = (empty($mydetails['EntryMeta']['name'])?$mydetails['Entry']['title']:$mydetails['EntryMeta']['name']).(!empty($broketotal)?' ('.$broketotal.' pcs)':'');
									echo '<p>'.(empty($popup)?$this->Html->link($outputResult,array('controller'=>'entries','action'=>$mydetails['Entry']['entry_type'],'edit',$mydetails['Entry']['slug']),array('target'=>'_blank')):$outputResult).'</p>';
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
                            if($shortkey == 'vendor_invoice_code')
                            {
                                $entrytype = 'dmd-vendor-invoice';
                            }
                            else if($shortkey == 'client_invoice_code')
                            {
                                $entrytype = 'dmd-client-invoice';
                            }
                            else if($shortkey == 'wholesaler')
                            {
                                $entrytype = 'client';
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
							else
							{
								$outputResult = (empty($entrydetail['EntryMeta']['name'])?$entrydetail['Entry']['title']:$entrydetail['EntryMeta']['name']);
								echo '<h5>'.(empty($popup)?$this->Html->link($outputResult,array("controller"=>"entries","action"=>$entrydetail['Entry']['entry_type']."/edit/".$entrydetail['Entry']['slug']),array('target'=>'_blank')):$outputResult).'</h5>';
                                echo '<input type="hidden" value="'.$entrydetail['Entry']['slug'].'">';
							}
                        }
                        else
                        {
                            // SUPER CUSTOMIZED OUTPUT STYLE ...
                            if($shortkey == 'payment_balance')
                            {
                                echo '<strong>'.toMoney($displayValue  , true , true).' USD</strong>';
                                echo '<input type="hidden" value="'.$displayValue.'">';
                            }
                            else
                            {
                                echo $this->Get->outputConverter($value10['input_type'] , $displayValue , $myImageTypeList , $shortkey);
                            }
                        }                        
                        echo "</td>";
					}
				}
			}
        
            // show gallery count !!
            if($gallery)
            {
                echo "<td>";
                if(empty($value['EntryMeta']['count-'.$value['Entry']['entry_type']]))
                {
                    echo "-";
                }
                else
                {
                    echo "<span class='label label-inverse'>&nbsp;";
                    echo $value['EntryMeta']['count-'.$value['Entry']['entry_type']]." <i class='icon-picture icon-white'></i>";
                    echo "&nbsp;</span>";
                }
                echo "</td>";
            }
		?>
		<td><?php echo date_converter($value['Entry']['modified'], $mySetting['date_format'] , $mySetting['time_format']); ?></td>
		<td style='min-width: 0px;' <?php echo (empty($popup)?'':'class="offbutt"'); ?>>
<!--
			<span class="label <?php echo $value['Entry']['status']==0?'label-important':'label-success'; ?>">
				<?php
					if($value['Entry']['status'] == 0)
						echo "Draft";
					else
						echo "Published";
				?>
			</span>
-->
            <span class="label label-inverse">
				<?php echo $value['AccountModifiedBy']['username']; ?>
			</span>
		</td>
		<?php
			if(empty($popup))
			{
                echo "<td class='action-btn'>";
                
                echo $this->Html->link('<i class="icon-retweet icon-white"></i>', array('action' => 'surat-jalan', '?' => array('key' => str_replace('-','_',$myType['Type']['slug']), 'value' => $value['Entry']['slug'] ) ) , array('escape'=>false, 'class'=>'btn btn-primary','data-toggle'=>'tooltip', 'title'=>'CLICK TO VIEW PRODUCT TRANSFER HISTORY', 'target'=>'_blank') ); // view history perpindahan produk terpilih ...
                echo '&nbsp;&nbsp;';
                
                echo $this->Html->link('<i class="icon-edit icon-white"></i>', $editUrl, array('escape'=>false, 'class'=>'btn btn-info','data-toggle'=>'tooltip', 'title'=>'CLICK TO EDIT / VIEW DETAIL') );
				?>
            &nbsp;<a data-toggle="tooltip" title="CLICK TO DELETE RECORD" href="javascript:void(0)" onclick="show_confirm('Are you sure want to delete <?php echo strtoupper($value['Entry']['title']); ?> ?','entries/delete/<?php echo $value['Entry']['id']; ?>')" class="btn btn-danger <?php echo ($user['role_id']>2&&$value['Entry']['created_by']!=$user['id']?'hide':''); ?>"><i class="icon-trash icon-white"></i></a>
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
		echo $this->element('admin_footer', array('extensionPaging' => $extensionPaging));
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
