function bootstrap_hack(){
	// badge on click
	$('table .badge').bind('click', function(){
		window.location = $(this).find('a').attr('href');
	});
}

function init_sortable(){
	$('table.sortable tbody').sortable({ opacity: 0.6, cursor: 'move' });;
}

function init_popup(){
	$('.popup.url').colorbox();
	$('.popup.inline-url').colorbox();
}

function resize_sidebar()
{
    // sidebar line
	var content_height = $('.content').height();
	var sidebar_height = $('.sidebar').height();
		
	if(sidebar_height < content_height){
		$('.sidebar').css('height', $('.content').css('height'));
	}
}

function post_submit(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}

$(document).ready(function(){	
	$('body').imagesLoaded().always(function(){ resize_sidebar(); });
	
	$('div#child-content').on("click", '#child-menu .btn-group .btn', function(){	
		$('#child-menu .btn').removeClass('active');
		$(this).addClass('active');
	});
	
	// change record background color in table when checkbox on checked !!
    $(document).on("change", "input[type=checkbox].check-record", function(e, ignoreAttachButton ){
        var $mytr = $(this).closest('tr');        
        var entry_id = $mytr.attr('alt');
        var checked_data = $('#checked-data').val();
        
        if($(this).is(':checked'))
        {
            $mytr.addClass('on-checked');
            if(checked_data.indexOf(','+entry_id+',') < 0)
            {
                $('#checked-data').val(checked_data+entry_id+',');
                
                // fetch tr element too ...
                if($('div#checked-row').length)
                {
                    $mytr.clone(true).appendTo( $('div#checked-row') );
                }
            }
        }
        else
        {
            $mytr.removeClass('on-checked');
            $('#checked-data').val( checked_data.replace(','+entry_id+',' , ',' ) );
            
            // remove tr element too ...
            if($('div#checked-row > tr[alt='+entry_id+']').length)
            {
                $('div#checked-row > tr[alt='+entry_id+']').remove();
            }
        }
        
        // update #count-check-all ...
        var total_checked = $('#checked-data').val().split(',').length - 2;
        total_checked = (total_checked > 0?'('+total_checked+')':'');
        $('span#count-check-all').html(total_checked);
        
        if(ignoreAttachButton == null)
        {
            $.fn.updateAttachButton();
        }
    });
    
    // init sortable function
	init_sortable();
	
	// init popup function
	init_popup();
	
	// init bootstrap hack
	bootstrap_hack();
});

// on resize
$(window).resize(function(){ resize_sidebar(); });