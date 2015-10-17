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
                    $('div.inner-header > div:last > div:last').after("<div class='btn-group /*hide*/'><form accept='application/vnd.ms-excel' accept-charset='utf-8' method='post' enctype='multipart/form-data' action='#' style='margin:0 0 10px 0;' class='form-upload-excel'><input REQUIRED type='file' style='width:200px;' accept='.xls,.xlsx' name='data[fileurl]' onchange='checkfile(this);'><button class='btn' type='submit'>Upload</button></form></div>"); // upload Excel ...
                    
                    $('div.inner-header > div:last > div:last').before("<form /*class='hide'*/ id='download-excel' action='"+site+"entries/download_jewelry' method='POST'><input type='hidden' name='data[record]'><button style='margin-bottom:10px;' class='btn btn-inverse right-btn fr' type='submit'><i class='icon-download-alt icon-white'></i> Download Excel</button></form>"); // download Excel ...

                    $('form#download-excel').submit(function(){
                        var records = [];
                        $('input.check-record:checked').each(function(i,el){
                            records.push($(el).val());
                        });

                        if(records.length > 0)
                        {
                            $(this).find('input[name="data[record]"]').val( records.join(',') );
                        }
                        else
                        {
                            alert('Please select one or more COR JEWELRY to be downloaded into excel format.');
                            $('input.check-record:first').focus();
                            return false;
                        }
                    });
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
			$('table#myTableList thead tr').prepend('<th><input type="checkbox" id="check-all" /></th>');
			$('table#myTableList tbody tr').each(function(i,el){
				$(this).prepend('<td style="min-width: 0px;"><input type="checkbox" class="check-record" value="'+$(this).attr('alt')+'" onclick="javascript:$.fn.updateAttachButton();" /></td>');
			});

			$('input#check-all').change(function(){
				$('input.check-record').attr('checked' , $(this).attr('checked')?true:false);
                $('input.check-record').change(); // update background color on each TR record...
				$.fn.updateAttachButton();
			});
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
                    var records = [];
                    $('input.check-record:checked').each(function(i,el){
                        records.push($(el).val());
                    });

                    if(records.length > 0)
                    {
                        if(confirm('Are you sure to execute this BULK action ?'))
                        {
                            $(this).find('input#action-records').val( records.join(',') );
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
                    
                    var gram_result = $(this).find('td.form-item_weight').text();
                    
                    if($('#myTypeSlug').val() == 'cc-payment')
                    {
                        var item_weight = gram_result;
                        gram_result = parseFloat(gram_result);
                        
                        // search client X from invoice first ...
                        var pecah_client_x = $('#client_x').val().split('|');
                        var index_client_x = {
                            "Italy (125%)" : 0,
                            "Korea (100%)" : 1,
                            "999 Simple (110%)" : 2,
                            "999 3D (115%)" : 3,
                        };

                        var client_x = pecah_client_x[index_client_x[$(this).find('td.form-product_type p').text()]];
                        if(!$.isNumeric(client_x))
                        {
                            client_x = $(this).find('td.form-client_x').text();
                            if(!$.isNumeric(client_x))
                            {
                                client_x = 1;
                            }
                        }
                        
                        gram_result *= parseFloat(client_x);
                        
                        richvalue += ' ['+item_weight+' x '+client_x+']';
                    }
                    else // cor vendor OR cor-client-invoice ...
                    {
                        richvalue += ' / ' + $(this).find('td.form-product_brand h5').text();
                        
                        var item_weight = parseFloat(gram_result);
                        if(item_weight != 1)
                        {
                            gram_result = item_weight;
                        }
                    }
                    
                    $("input#"+targetID).val(richvalue).nextAll("input[type=hidden]").val( $(this).find("input[type=hidden].slug-code").val() );
					$("input#"+targetID).change();

					// update other attribute ...
                    var $trytotal = $("input#"+targetID).nextAll('input[type=number]');
                    if($trytotal.length > 0)
                    {
                        $trytotal.removeAttr('readonly');
                        if(gram_result > 0)
                        {
                            $trytotal.val( gram_result.toFixed(2) ).keyup();
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
		$('a.get-started').attr('href',site+'admin/entries/<?php echo $myType['Type']['slug'].'/'.(empty($myEntry)?'':$myEntry['Entry']['slug'].'/').'add'.(!empty($extensionPaging['type'])?'?type='.$extensionPaging['type']:''); ?>');
		
		// disable language selector ONLY IF one language available !!		
		var myLangSelector = ($('#colorbox').length > 0 && $('#colorbox').is(':visible')? $('#colorbox').find('div.lang-selector:first') : $('div.lang-selector')  );
		if(myLangSelector.find('ul.dropdown-menu li').length <= 1)	myLangSelector.hide();
        
        // UPDATE TITLE HEADER !!
        <?php
            if($isAjax == 0 && strpos($this->request->query['key'], '_invoice_code') !== FALSE && !empty($this->request->query['value']) )
            {
                $query = $this->Get->meta_details($this->request->query['value']);
                if(!empty($query))
                {
                    ?>
        $('div.title:last > h2').append(' <span style="color:red;">INV# <?php echo $query['Entry']['title']; ?></span>');
                    <?php
                }
            }
        ?>
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
                        if(!empty($popup) && $this->request->query['key'] == $shortkey && substr($this->request->query['value'] , 0 , 1) != '!')
                        {
                            if($shortkey != 'product_type')
                            {
                                $hideKeyQuery = 'hide';
                            }
                        }
                        
                        $datefield = '';
                        if($shortkey == 'product_type' || $shortkey == 'product_status')
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
                            }
                        }
                        
                        echo "<th ".($value['input_type'] == 'textarea' || $value['input_type'] == 'ckeditor'?"style='min-width:200px;'":"")." class='".$hideKeyQuery." ".$datefield."'>";
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
			<form id="global-action" style="margin: 0;" action="#" accept-charset="utf-8" method="post" enctype="multipart/form-data">
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
		<td class="main-title">
			<?php
				if($imageUsed == 1)
				{
					echo '<div class="thumbs">';
					echo (empty($popup)&&!empty($value['Entry']['main_image'])?$this->Html->link($this->Html->image('upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']], array('alt'=>$value['ParentImageEntry']['title'],'title' => $value['ParentImageEntry']['title'])),'/img/upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']],array("escape"=>false,"class"=>"popup-image")):$this->Html->image('upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']], array('alt'=>$value['ParentImageEntry']['title'],'title' => $value['ParentImageEntry']['title'])));
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
                            if($shortkey != 'product_type')
                            {
                                $hideKeyQuery = 'hide';
                            }
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
                                $entrytype = 'cor-vendor-invoice';
                            }
                            else if($shortkey == 'client_invoice_code')
                            {
                                $entrytype = 'cor-client-invoice';
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
                                
                                if($entrytype == 'product-type')
                                {
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
                        }
                        else
                        {
                        	// SUPER CUSTOMIZED OUTPUT STYLE ...
                            if($shortkey == 'payment_balance')
                            {
                                echo '<strong>'.$displayValue.' gr</strong>';
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