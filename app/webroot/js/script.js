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

$(document).ready(function(){	
	$('body').imagesLoaded().always(function(){ resize_sidebar(); });
	
	$('div#child-content').on("click", '#child-menu .btn-group .btn', function(){	
		$('#child-menu .btn').removeClass('active');
		$(this).addClass('active');
	});
	
	// list button hover
	$('div#child-content').on("mouseenter", 'table tr', function(){	
		$(this).find('td .btn').css('display', 'inline');
	});
	
	$('div#child-content').on("mouseleave", 'table tr', function(){	
		$(this).find('td .btn').css('display', 'none');
	});
    
    // change record background color in table when checkbox on checked !!
    $(document).on("change", "input[type=checkbox].check-record", function(){
        if($(this).is(':checked'))
        {
            $(this).closest('tr').addClass('on-checked');
        }
        else
        {
            $(this).closest('tr').removeClass('on-checked');
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