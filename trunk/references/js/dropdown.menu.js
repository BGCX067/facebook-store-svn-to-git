(function($){
    $.fn.extend({
        menuMultiCol: function(options) {
            //Settings list and the default values
            var defaults = {
                cols: 6,
                col_width: 131
            };
            //Define global variable
            var list_items  = {};
            var total_items = 0;
            var list_result = {};
            
            var o = $.extend(defaults, options);

            return this.each(function() {
                //Assign current element to variable, in this case is UL element
                var obj = $(this);

                //Get all LI in the UL
                var total_items = obj._getNumberItems("li");
                var total_subcol = obj._getNumberItems(".subcol");
                list_items = obj._getListItems(".subcol", "li");
                var max_boxes = _getMaxBoxes(list_items, o.cols);
                console.log(max_boxes);
                
                console.log(total_items);
                list_result = _calculate(list_items, total_subcol, 0, total_items, o.cols, max_boxes);
                console.log(list_result);
                obj._overwriteCss(list_result, o.col_width);
            });
        },
        
        _getNumberItems: function(element){
            return $(element, this).length;
        },
        
        _getListItems: function(sub_class, element){
            var index = 0;
            var list_items = {};

            this.find(sub_class).each(function(){
                var sub_obj = $(this);
                list_items[index] = sub_obj._getNumberItems(element);
                index++;
            });

            return list_items;
        },
        
        _overwriteCss: function(list_result, col_width){
            var index = 0;
            this.find(".subcol").each(function(){
                var subcol_width = list_result[index].col * (col_width + 2);
                $(this).css("width", subcol_width);
                index++;
            });
        }
    });

    function _calculate(list_items, total_subcol, index_start, total_items, cols, max_boxes){
        //Initial result
        var list_result = {};
        
        
        var min_row     = _divideInt(total_items, cols);
        //var max_row     = min_row + accepted_level;
        //var max_boxes   = max_row*cols - total_items;

        for(var first_row = min_row; first_row > 0; first_row--)
        {
            var temp_max_boxes  = max_boxes;
            var remain_cols     = cols;
            var temp_total_items    = total_items;
            var first_list_row  = {};
            var is_break_recursive = false;

            for(var nth in list_items)
            {
                if(nth >= index_start)
                {
                    var nth_cols = _divideInt(list_items[nth], first_row);
                    temp_total_items -= list_items[nth];
console.log(temp_max_boxes);                    
                    temp_max_boxes -= first_row*nth_cols - list_items[nth];
console.log(nth + " " + temp_total_items + " " + list_items[nth] + "%" + first_row + "=" + nth_cols + " " + remain_cols);
console.log(temp_max_boxes);
                    if(nth_cols < remain_cols)
                    {
                        if(temp_total_items == 0 && total_subcol == parseInt(nth, 10) + 1)
                        {
                            nth_cols = remain_cols;
                            first_list_row[nth] ={
                                col: nth_cols,
                                row: first_row
                            };
                            list_result = first_list_row;
                            is_break_recursive = true;
                        }
                        else
                        {
                            first_list_row[nth] ={
                                col: nth_cols,
                                row: first_row
                            };
                        }
                        remain_cols -= nth_cols;
                        console.log(first_list_row);
                        
                    }
                    else if(nth_cols == remain_cols)
                    {
                        first_list_row[nth] = {
                            col: nth_cols,
                            row: first_row
                        }
                        
                        if(temp_total_items > 0)
                        {
                            var after_list_row = _calculate(list_items, total_subcol, nth + 1, temp_total_items,
                                                        cols, temp_max_boxes);

                            if(_isEmptyObject(after_list_row))
                            {
                                list_result = {};
                            }
                            else
                            {
                                list_result = _mergeObjects(first_list_row, after_list_row);
                            }
                        }
                        else
                        {
                            list_result = first_list_row;
                        }
                        
                        is_break_recursive = true;
                    }
                    else
                    {
                        list_result = {};
                        is_break_recursive = true;
                    }
                    console.log(is_break_recursive);
                    if(is_break_recursive)
                    {
                        break;
                    }
                }
            }
            
            if(temp_max_boxes >= 0 && !_isEmptyObject(list_result))
            {
                break;
            }
            
        }
        return list_result;
    }

    function _divideInt(a, b)
    {
        result = parseInt(a/b, 10);
        if(a % b > 0 || result == 0)
        {
            result++;
        }
        return result;
    }

    function _mergeObjects(obj1,obj2){
        var obj3 = {};
        
        for (var id1 in obj1)
        {
            if(id1 != undefined)
            {
                obj3[id1] = obj1[id1];
            }
        }
        for (var id2 in obj2)
        {
            if(id2 != undefined)
            {
                obj3[id2] = obj2[id2];
            }
        }
        return obj3;
    }
    
    function _isEmptyObject(obj)
    {
        var count = 0;
        for (var id in obj)
        {
            if(id != undefined)
            {
                count++;
            }    
        }
        if(count == 0)
            return true;
        else
            return false;
    }
    
    function _getMaxBoxes(list_items, cols){
        var max_boxes = 0;
        for(var id in list_items)
        {
            max_boxes += cols - (list_items[id] % cols);
        }
        return max_boxes;
    }
})(jQuery);

