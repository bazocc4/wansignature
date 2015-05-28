$(document).ready(function(){
	// on thumbnail hover
	$('div#child-content').on('mouseenter','.photo',function(){
		$(this).find('.description').fadeIn('fast');		
	});
	
	$('div#child-content').on('mouseleave','.photo',function(){
		$(this).find('.description').fadeOut('fast');		
	});
	
	// insert into post
	$('table tr').hover(function(){
		$(this).find('a.insert-into-post').css('display', 'block');
	}, function(){
		$(this).find('a.insert-into-post').css('display', 'none');
	});
    
    // ======================= >>
    // colorbox initialization !!
    // ======================= >>
    
    // POPUP MEDIA LIBRARY
    $(document).on('click', '.get-from-library, #upload' , function(e){
        e.preventDefault();        
        $.colorbox({
            href: $(this).attr('href'),
            onLoad: function() {
                $('#cboxClose').hide();
            }
        });        
    });
    
    // POPUP ADMIN_DEFAULT.CTP
    $(document).on('click','.get-from-table',function(e){        
        e.preventDefault();
        $.colorbox({
            reposition: false,
            href: $(this).attr('href')+($('a#lang_identifier').length>0?'&lang='+$('a#lang_identifier').text().toLowerCase():''),
            onLoad: function() {
                $('#cboxClose').show();
            },
            onComplete: function(){
                // apply doubleScroll event !!
                $.fn.doubleScroll('autoscroll');
            }
        });
    });
	
	// --------------------------- >>
	// on thumbnail hover - popup
	// --------------------------- >>
	$(document).on('mouseenter','div#upload-popup .photo',function(){
		$(this).find('.description').fadeIn('fast');
	});	
	$(document).on('mouseleave','div#upload-popup .photo',function(){
		$(this).find('.description').fadeOut('fast');
	});

	// --------------------------- >>
	// switch between tabs
	// --------------------------- >>
	$(document).on('click', '.upload-popup .tabs > ul li a', function(e){
		e.preventDefault();
		
		// check if need to ajax or not...
		var willajax = 0;
		if($(this).attr('href') == "#tabs1" && !$(this).parent().hasClass('active'))
		{
			willajax = 1;
		}
						
		$('.upload-popup .tabs > ul li').removeClass('active');
		$(this).parent().addClass('active');
		
		var id_now = $(this).attr('href');
		$('.tabs-container').css('display', 'none');
		$(id_now).css('display', 'block');
		
		// ajax for insert media library...
		if(willajax == 1)
		{
			$.fn.ajax_mylink($(this),"popup-ajaxed" , null , "media" , "altforurl");
		}
	});

	// --------------------------- >>
	// ajax media pagination
	// --------------------------- >>
	$(document).on('click','div#upload-popup .ajax_mymedia', function(e){
		e.preventDefault();
		if(!($(this).parent("li").hasClass("disabled") || $(this).parent("li").hasClass("active")))
		{				
			$.fn.ajax_mylink($(this),"popup-ajaxed" , null , "media");
		}
	});

	// --------------------------- >>
	// close button event
	// --------------------------- >>
	$(document).on('click','.upload-popup .sidebar-title .close', function(){
		if( $('#colorbox').length>0 && $('#colorbox').is(':visible') )
		{
			$.colorbox.close();
		}	
		else
		{
			window.close();
		}
	});

	$(document).bind({
        cbox_cleanup : function(){
            // delete temp thumbnails from blueImp.
            if($('form#fileupload tr.template-download').length > 0)
            {
                $.ajaxSetup({cache: false});
                $.get(site+'entry_metas/deleteTempThumbnails');
            }
        },
		cbox_closed : function(){
			// clean variable in "add field MASTER DATABASE popup"
			this_element = '';
            
            // media library module
            if($('div.sidebar ul li a#media').hasClass('active'))
            {
                window.location = site + 'admin/entries/'+$('input#myTypeSlug').val();
            }	
		}
	});
});