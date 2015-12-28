$.fn.extend({
    simpleSlider: function(options)
    {
        options = $.extend({item:4}, options);

        return this.each(function()
        {

            var li = $('ul li', this);
            var count = li.length;


            if (count <= options.item)
            {
                $('.go_next, .go_prev', this).remove();
                return;
            }

            var nextBtn = $('.go_next', this);

            nextBtn.parent().attr('rel', -options.item);

            $('.go_next, .go_prev', this).click(function()
            {
                li.hide();
                var parent = $(this).parent();
                var index = parseInt($(this).parent().attr('rel'));

                if ($(this).hasClass('go_next')){
					index = (index == -1)? 0 : index + options.item;
					if (index >= count)
					{
						index = 0;
					}
					
                }
                else
                {
					index = index - options.item;
                    if (index < 0)
                    {
                        var mod = (count % options.item);
						index = (mod == 0)? count - options.item : count - mod;
                    }
                }
				
				for (var i=0;i<options.item;i++)
				{
					li.eq(index + i).fadeIn();
				}
				
				parent.attr('rel', index);				
                return false;
            });
        });
    }
});