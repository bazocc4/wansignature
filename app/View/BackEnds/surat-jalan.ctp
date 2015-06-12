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
        
			<?php if($isOrderChange == 1): ?>
				// table sortable
				$("table.list tbody").sortable({ opacity: 0.6, cursor: 'move',
					stop: function(event, ui) {
						var tmp = '';
						// construct
						$('table.list tbody tr.orderlist').each(function(){
							tmp += $(this).attr('alt') + ',';
						});
						$.ajaxSetup({cache: false});
						$.post(site+'entries/reorder_list',{
							src_order: $('input[type=hidden]#determine').val(),
							dst_order: tmp,
                            lang: $('a#lang_identifier').text().toLowerCase()
						});
					}
				});
			<?php else: ?>
				$('table#myTableList tr').css('cursor' , 'default');
			<?php endif; ?>

			// submit bulk action checkbox !!
			if($('form#global-action').length > 0)
            {
                $('form#global-action').submit(function(){
                    
                    if($(this).find('select#action-status').val() == 'active')
                    {
                        $('input.check-record:checked').each(function(i,el){
                            if($(el).closest('tr').find('a.change-accepted').length == 0)
                            {
                                $(el).attr('checked' , false).change();
                            }
                        });
                    }
                    
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
			
			// UPDATE TITLE HEADER !!
			$('div.title > h2').html('<?php echo (empty($myEntry)?$myType['Type']['name']:$myEntry['Entry']['title'].' - '.$myChildType['Type']['name']); ?>');
			
		<?php else: ?>
			$('table#myTableList tbody tr').css('cursor' , 'pointer');
			$('input[type=checkbox]').css('cursor' , 'default');

			$('table#myTableList tbody tr').click(function(e){
				if(!$('input[type=checkbox]').is(e.target))
				{
					var targetID = ($('input#query-alias').length > 0?$('input#query-alias').val():'<?php echo (empty($myEntry)?$myType['Type']['slug']:$myChildType['Type']['slug']); ?>')+($('input#query-stream').length > 0?$('input#query-stream').val():'');
					if($(this).find("td.form-name").length > 0)
					{
					    $("input#"+targetID).val( $(this).find("td.form-name").text()+' ('+$(this).find("h5.title-code").text()+')');
					}
					else
					{
					    $("input#"+targetID).val( $(this).find("h5.title-code").text() );
					}
					
					$("input#"+targetID).nextAll("input[type=hidden]").val( $(this).find("input[type=hidden].slug-code").val() );
					$("input#"+targetID).change();

					// Update the subcategory dropdown value, if existed !!
					if($('select.subcategory').length > 0)
					{
						$('select.subcategory').html('');
						
						var catcheck = $(this).find("td.form-subcategory").html();
						
						if(catcheck != '-')
						{
							var subcat = catcheck.split('<br>');
						
							$.each(subcat , function(i,el){
                                el = $.trim(el);
								$('select.subcategory').append('<option value="'+el+'">'+el+'</option>');
							});
						}
						
					}

					$.colorbox.close();
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
                echo $this->Html->link($titlekey.' ('.$totalList.')'.($_SESSION['order_by'] == 'title ASC'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == 'title DESC'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$_SESSION['order_by'] == 'title ASC'?"z_to_a":"a_to_z"));
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
                        switch($value['input_type'])
                        {
                            case 'datepicker':
                            case 'datetimepicker':
                            case 'multidate':
                                $datefield = 'date-field';
                                break;
                        }
                        
                        echo "<th ".($value['input_type'] == 'textarea' || $value['input_type'] == 'ckeditor'?"style='min-width:200px;'":"")." class='".$hideKeyQuery." ".$datefield."'>";
                        echo $this->Html->link(string_unslug($shortkey).($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' asc'?" desc":" asc") ));
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
                echo $this->Html->link('last '.string_unslug($entityTitle).($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' asc'?" desc":" asc") ));
            ?>
        </th>
        <th>
            <?php
                $entityTitle = "modified_by";
                echo $this->Html->link('last updated by'.($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' asc'?" desc":" asc") ));
            ?>
        </th>
		<th>
		    <?php
                $entityTitle = "status";
                echo $this->Html->link(string_unslug($entityTitle).($_SESSION['order_by'] == $entityTitle.' asc'?' <span class="sort-symbol">'.$sortASC.'</span>':($_SESSION['order_by'] == $entityTitle.' desc'?' <span class="sort-symbol">'.$sortDESC.'</span>':'')),array("action"=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'index',$paging,'?'=>$extensionPaging) , array("class"=>"ajax_mypage" , "escape" => false , "title" => "Click to Sort" , "alt"=>$entityTitle.($_SESSION['order_by'] == $entityTitle.' asc'?" desc":" asc") ));                
            ?>
		</th>
		<?php
			if(empty($popup))
			{
				?>
		<th class="action">
			<form id="global-action" style="margin: 0;" action="#" accept-charset="utf-8" method="post" enctype="multipart/form-data">
				<select REQUIRED name="data[action]" class="input-small" id="action-status">
					<option style="font-weight: bold;" value="">Action :</option>
					<option value="active">Accepted</option>
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
					echo (empty($popup)?$this->Html->link($this->Html->image('upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']], array('alt'=>$value['ParentImageEntry']['title'],'title' => $value['ParentImageEntry']['title'])),array('action'=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']).'/edit/'.$value['Entry']['slug'].(!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?'?type='.$myChildType['Type']['slug']:'')),array("escape"=>false)):$this->Html->image('upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']], array('alt'=>$value['ParentImageEntry']['title'],'title' => $value['ParentImageEntry']['title'])));
					echo '</div>';
				}
        
                $editUrl = array('action'=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'edit',$value['Entry']['slug'] ,'?'=> (!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?array('type'=>$myChildType['Type']['slug']):'')   );
			?>
			<input class="slug-code" type="hidden" value="<?php echo $value['Entry']['slug']; ?>" />
			<h5 class="title-code"><?php echo (empty($popup)?$this->Html->link($value['Entry']['title'],$editUrl):$value['Entry']['title']); ?></h5>
			<p>
				<?php
					if($descriptionUsed == 1 && !empty($value['Entry']['description']))
					{
						$description = nl2br($value['Entry']['description']);
						echo (strlen($description) > 30? '<a href="#" data-toggle="tooltip" title="'.$value['Entry']['description'].'">'.substr($description,0,30).'...</a>' : $description);
					}
				?>
			</p>
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
                                    echo '<input type="hidden" value="'.$mydetails['Entry']['slug'].'">';
								}
							}
							
							if($emptybrowse == 0)
							{
								echo '-';
							}
						}
                        else if($value10['input_type'] == 'browse')
                        {
                        	$entrydetail = $this->Get->meta_details($displayValue , get_slug($shortkey));
							if(empty($entrydetail))
							{
								echo $displayValue;
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
                                    if(strtotime($entrydetail['EntryMeta'][$targetMetaKey]['value']))
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
                            	    echo (strlen($description) > 30? '<a href="#" data-toggle="tooltip" title="'.$entrydetail['Entry']['description'].'">'.substr($description,0,30).'...</a>' : $description);
                                }                                
                                echo '</p>';
							}
                        }
                        else
                        {
                        	echo $this->Get->outputConverter($value10['input_type'] , $displayValue , $myImageTypeList , $shortkey);
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
		<td>
		    <span class="label label-inverse">
				<?php echo $value['AccountModifiedBy']['username']; ?>
			</span>
		</td>
		<td style='min-width: 0px;' <?php echo (empty($popup)?'':'class="offbutt"'); ?>>
			<span class="label <?php echo $value['Entry']['status']==0?'label-important':'label-success'; ?>">
				<?php
					if($value['Entry']['status'] == 0)
						echo "On Process";
					else
						echo "Accepted";
				?>
			</span>            
		</td>
		<?php
			if(empty($popup))
			{
                echo "<td class='action-btn'>";
                echo $this->Html->link('<i class="icon-edit icon-white"></i>', $editUrl, array('escape'=>false, 'class'=>'btn btn-primary','data-toggle'=>'tooltip', 'title'=>'CLICK TO EDIT / VIEW DETAIL') );
                
                if($value['Entry']['status'] == 0)
				{
					$confirm = 'Apakah Anda yakin pengiriman barang dari Surat Jalan '.strtoupper($value['Entry']['title']).' sudah diterima seluruhnya ?';
					$targetURL = 'entries/change_status/'.$value['Entry']['id'];
                    
                    echo '&nbsp;&nbsp;<a data-toggle="tooltip" title="CLICK TO SET AS ACCEPTED" href="javascript:void(0)" onclick="show_confirm(\''.$confirm.'\',\''.$targetURL.'\')" class="btn btn-info change-accepted"><i class="icon-ok icon-white"></i></a>';					
				}
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