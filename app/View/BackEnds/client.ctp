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
        
			$('table#myTableList tr').css('cursor' , 'default').click(function(e){ if($('td').is(e.target) && $(this).find('input[type=checkbox]').length > 0) $(this).find('input[type=checkbox]').click(); });

			// submit bulk action checkbox !!
			if($('form#global-action').length > 0)
            {
                $('form#global-action').submit(function(){				
                    var records = [];
                    $('input.check-record').each(function(i,el){
                        if($(el).attr('checked'))
                        {
                            records.push($(el).val());
                        }
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
            // UPDATE TITLE HEADER !!
            <?php
                if($isAjax == 0 && !empty($this->request->query['alias']))
                {
                    ?>
            $('#colorbox div.title > h2').append(' <span style="color:red;">(<?php echo string_unslug($this->request->query['alias']); ?>)</span>');
                    <?php
                }
            ?>
        
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

					// Update other attribute...
					if($('input#query-alias').val() != 'wholesaler')
                    {
                        if($('input.wholesaler').length > 0)
                        {
                            var wholesaler = $(this).find("td.form-wholesaler h5");
                            if(wholesaler.length > 0)
                            {                            $('input#wholesaler').val(wholesaler.text()).nextAll('input[type=hidden].wholesaler').val(wholesaler.next('input[type=hidden]').val());
                            }
                        }

                        if($('input.diamond_sell_x').length > 0)
                        {
                            var diamond_sell_x = $(this).find('td.form-diamond_sell_x').text();
                            if($.isNumeric(diamond_sell_x))
                            {
                                $('input.diamond_sell_x').val(diamond_sell_x).trigger('keyup');
                            }
                        }

                        if($('input[type=number][class^="x_1"]').length > 0)
                        {
                            var $mytr = $(this);
                            $('input[type=number][class^="x_1"]').each(function(i,el){
                                var value = $mytr.find('td[class^="form-x_1"]:eq('+i+')').text();
                                if($.isNumeric(value))
                                {
                                    $(el).val(value).trigger('keyup');
                                }
                            });
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
                        if(!empty($popup))
                        {
                            if($this->request->query['key'] == $shortkey && substr($this->request->query['value'] , 0 , 1) != '!')
                            {
                                $hideKeyQuery = 'hide';
                            }
                            else if($this->request->query['key'] == 'kategori' && $this->request->query['value'] != 'Retailer' && $shortkey == 'wholesaler')
                            {
                                $hideKeyQuery = 'hide';
                            }
                        }
                        
                        $datefield = '';
                        switch($value['input_type'])
                        {
                            case 'datepicker':
                            case 'datetimepicker':
                            case 'multidate':
                            case 'browse':
                                $datefield = 'date-field';
                                break;
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
					echo (empty($popup)?$this->Html->link($this->Html->image('upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']], array('alt'=>$value['ParentImageEntry']['title'],'title' => $value['ParentImageEntry']['title'])),array('action'=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']).'/edit/'.$value['Entry']['slug'].(!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?'?type='.$myChildType['Type']['slug']:'')),array("escape"=>false)):$this->Html->image('upload/'.$value['Entry']['main_image'].'.'.$myImageTypeList[$value['Entry']['main_image']], array('alt'=>$value['ParentImageEntry']['title'],'title' => $value['ParentImageEntry']['title'])));
					echo '</div>';
				}
        
                $editUrl = array('action'=>$myType['Type']['slug'].(empty($myEntry)?'':'/'.$myEntry['Entry']['slug']),'edit',$value['Entry']['slug'] ,'?'=> (!empty($myEntry)&&$myType['Type']['slug']!=$myChildType['Type']['slug']?array('type'=>$myChildType['Type']['slug']):'')   );
			?>
			<input class="slug-code" type="hidden" value="<?php echo $value['Entry']['slug']; ?>" />
			<h5 class="title-code"><?php echo (empty($popup)?$this->Html->link($value['Entry']['title'], $editUrl ):$value['Entry']['title']); ?></h5>
			
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
                        if(!empty($popup))
                        {
                            if($this->request->query['key'] == $shortkey && substr($this->request->query['value'] , 0 , 1) != '!')
                            {
                                $hideKeyQuery = 'hide';
                            }
                            else if($this->request->query['key'] == 'kategori' && $this->request->query['value'] != 'Retailer' && $shortkey == 'wholesaler')
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
								$mydetails = $this->Get->meta_details($brokevalue , $browse_slug );
								if(!empty($mydetails))
								{
									$emptybrowse = 1;
									$outputResult = (empty($mydetails['EntryMeta']['name'])?$mydetails['Entry']['title']:$mydetails['EntryMeta']['name']);
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
                            $entrytype = '';
                            if($shortkey == 'wholesaler')
                            {
                                $entrytype = 'client';
                            }
                            else
                            {
                                $entrytype = get_slug($shortkey);
                            }
                            
                        	$entrydetail = $this->Get->meta_details($displayValue , $entrytype );
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
                                if($entrydetail['Entry']['entry_type'] == 'client')
                                {
                                    if(!empty($entrydetail['EntryMeta']['kode_pelanggan']))
                                    {
                                        echo $entrydetail['EntryMeta']['kode_pelanggan'];
                                    }
                                    else if(!empty($entrydetail['EntryMeta']['alamat']))
                                    {
                                        echo $entrydetail['EntryMeta']['alamat'];
                                    }
                                }
                                else
                                {
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
                echo $this->Html->link('<i class="icon-edit icon-white"></i>', $editUrl, array('escape'=>false, 'class'=>'btn btn-info','data-toggle'=>'tooltip', 'title'=>'CLICK TO EDIT / VIEW DETAIL') );
                
/*
                if($myType['Type']['slug'] != 'pages')
				{
					$confirm = null;
					$targetURL = 'entries/change_status/'.$value['Entry']['id'];
                    echo '&nbsp;&nbsp;';
					if($value['Entry']['status'] == 0)
					{
						echo '<a data-toggle="tooltip" title="CLICK TO PUBLISH RECORD" href="javascript:void(0)" onclick="changeLocation(\''.$targetURL.'\')" class="btn btn-success"><i class="icon-ok icon-white"></i></a>';					
					}
					else
					{
						$confirm = 'Are you sure to set '.strtoupper($value['Entry']['title']).' as draft ?';
						echo '<a data-toggle="tooltip" title="CLICK TO DRAFT RECORD" href="javascript:void(0)" onclick="show_confirm(\''.$confirm.'\',\''.$targetURL.'\')" class="btn btn-warning"><i class="icon-ban-circle icon-white"></i></a>';
					}
				}
*/
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