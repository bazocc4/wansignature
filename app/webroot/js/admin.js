var jcrop_api = new Array();
(function($){    
    // colorbox initialization !!
    $.fn.generalColorbox = function(objclass){
        if($('.'+objclass).length > 0)
        {
            $(document).on('click', '.'+objclass , function(e){
                e.preventDefault();
                $.colorbox({
                    href:$(this).attr('href'),
//                    fixed: true,
                    reposition: false,
                    maxWidth:'95%',
//                    maxHeight:'95%',
                    title:$(this).attr('title'),
                });
            });
        }
    }
    
    $(document).on('submit', 'form.form-upload-excel', function(){
        
        if($(this).find('button[type=submit] img').length > 0)
        {
            alert('Please wait, uploading excel is still in process...');
            return false;
        }

        // final confirmation ...
        if(!confirm('Are you sure to upload this database excel?\nNB: This process will take some minutes, depending on how large the data is.'))
        {
            return false;
        }

        $(this).find('button[type=submit]').html('<img src="'+site+'images/ajax-loader.gif" alt="loading">');
        
    });
    
    $(document).ready(function()
	{
        $.fn.generalColorbox('popup-image');
        
		// disable right-click for image !!
		$('img').bind('contextmenu', function(e) {
			return false;
		}); 
        
        // Disable input form field but still send the value !!
        $('form').bind('submit', function(){
            $(this).find(':disabled').removeAttr('disabled');
        });

	    // trigger for all form to show dialog box when user close windows but data didn't(forgot) save
		($('#colorbox').length>0&&$('#colorbox').is(':visible')?$('#colorbox').children().last().children():$(document)).on("change",'form.notif-change',function(e){
	        window.onbeforeunload=function()
			{
	             return 'You have unsaved changes. Are you sure you want to leave this page?';    
	        };
	    });
	    
		($('#colorbox').length>0&&$('#colorbox').is(':visible')?$('#colorbox').children().last().children():$(document)).on("submit",'form.notif-change',function(e){
	    	window.onbeforeunload=function(){};
	    });
	    
	    $(document).on('click','a.removeID',function(e){
            e.preventDefault();            
            $(this).siblings("input[type=hidden] , input.targetID").val("").trigger('change');
	    });
	    
	    // AJAX IN EDIT FORM (CHANGE LANGUAGE)
		$('div#child-content').on("click", '.ajax_myform', function(e){
			e.preventDefault();
			var myobj = $(this);
			var myid = 'ajaxed';
			var url = myobj.attr('href');
			
			if(url == "#")
			{
				// just change now language
				var now_language = $(this).html().substr(0,2);
				$('a#lang_identifier').html( now_language );
				$('input[type=hidden]#myLanguage').val( now_language.toLowerCase() );
			}
			else
			{
				var spinner = '<div class="loading" style="height:'+$('#'+myid).height()+'px;"></div>';
				$.ajaxSetup({cache: false});
				$('div#'+myid).empty();
				$('div#'+myid).html(spinner).load(url,[],function(){
					history.pushState(null, '', url);
					// get hidden data
					var now_language = $('input[type=hidden]#myLanguage').val().toUpperCase();
					var entry_title = $('input[type=text].Title').val();
					var entry_image_id = $('input[type=hidden]#mySelectCoverId').val();
					var entry_image_type = $('input[type=hidden]#entry_image_type').val();
					
					// change now language
					$('a#lang_identifier').html( now_language );
					
					// change form title
					$('h2#form-title-entry').html(url.indexOf('lang=') >= 0? 'TRANSLATE ('+entry_title+')' : entry_title);
					// refresh cover image...
					$('img#mySelectCoverAlbum').attr('src',site+'img/upload/'+entry_image_id+'.'+entry_image_type);
					if(entry_image_id == 0)
					{	
						$('.select').html('Select Cover');
						$('.remove').hide();
					}
					else
					{
						$('.select').html('Change Cover');
						$('.remove').show();
					}
				});
			}
		});
		
		// ------------------------ JCROP FUNCTION ------------------------ //
		$.fn.jCropSetSelectCoord = function(counter){
			var x1 = $('input[type=text]#x1_'+counter).val();
			var y1 = $('input[type=text]#y1_'+counter).val();
			var x2 = $('input[type=text]#x2_'+counter).val();
			var y2 = $('input[type=text]#y2_'+counter).val();
			jcrop_api[counter].setSelect([x1 , y1 , x2 , y2]);
		}
		
		$.fn.jCropSetSelectSize = function(counter){
			var x1 = $('input[type=text]#x1_'+counter).val();
			var y1 = $('input[type=text]#y1_'+counter).val();
			var x2 = parseInt($('input[type=text]#x1_'+counter).val()) + parseInt($('input[type=text]#w_'+counter).val());
			var y2 = parseInt($('input[type=text]#y1_'+counter).val()) + parseInt($('input[type=text]#h_'+counter).val());
			jcrop_api[counter].setSelect([x1 , y1 , x2 , y2]);
		}
		// -------------------- END OF JCROP FUNCTION --------------------- //
	});
})(jQuery);