(function($){
	$(document).ready(function()
	{
        // trigger keyup on some element ...
        if($('input.additional_cost').length > 0)
        {
            $('input.additional_cost').keyup(function(e,init){
                var ratevalue = $('input.gold_bar_rate').val();
                var value = $(this).val();

                if($.isNumeric(value) && $.isNumeric(ratevalue))
                {
                    var result = (parseFloat(value) / parseFloat(ratevalue)).toFixed(2);

                    $('span.cost_rate_text').html('= <span class="additional_cost_gram">'+result+'</span> gram Gold Bar.');
                }
                else
                {
                    $('span.cost_rate_text').html('');
                }

                // trigger additional_charge too ...
                if($('input.additional_charge').length > 0)
                {
                    $('input.additional_charge').trigger('keyup', [init]);
                }
                else if($.isFunction($.fn.calculateTotalWeight))
                {
                    $.fn.calculateTotalWeight(init);
                }

            }).trigger('keyup', ['init']);
        }

        if($('input.gold_bar_rate').length > 0)
        {
            $('input.gold_bar_rate').keyup(function(){
                $('input.additional_cost').keyup();
            });
        }

        // onkeyup Gold Loss ...
        if($('input.gold_loss').length > 0)
        {
            $('span.unit_gold_loss').text('gram');
            $('input.gold_loss').keyup(function(e,init){
                var cor = parseFloat($('span.total_cor_jewelry input[type=hidden]').val());
                var result = ( $.isNumeric( $(this).val() ) ? cor * parseFloat($(this).val()) / 100 : 0 );
                $('span.total_gold_loss').html(number_format(result,2)+'<input type="hidden" value="'+result+'">');

                // trigger additional_charge too ...
                if($('input.additional_charge').length > 0)
                {
                    $('input.additional_charge').trigger('keyup', [init]);
                }
                else if($.isFunction($.fn.calculateTotalWeight))
                {
                    $.fn.calculateTotalWeight(init);
                }
            });
        }
	});
})(jQuery);