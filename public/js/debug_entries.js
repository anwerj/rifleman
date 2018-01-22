var fn = {
    method_ : function (item, elem) {
        var method = elem.text();
        var index = item.index();
        var target = $('.item:gt('+index+')[data-method="'+method+'"]');

        fn.scroll_(target);
    },
    code_ : function (item, force) {
        var elem = $(item).parentsUntil('.item');

        if(elem.hasClass('active') && !force)
        {
            elem.removeClass('active');
            return;
        }
        elem.addClass('active');

        if (elem.hasClass('has_code') && !force)
        {
            return;
        }

        $.ajax({
            url : '/debug',
            data : ({
                file : {
                    class : elem.find('.func_class').text(),
                    method : elem.find('.func_method').text()
                }
            })
        }).done(function (html) {
            elem.addClass('has_code');
            elem.find('.func_code').first().html(html).on('click', function (e) {
                if ($(e.target).is('.pln')){
                    fn.method_(elem.parent(), $(e.target));
                }
            });
            PR.prettyPrint();
        })
    },
    scroll_ : function (target) {
        var offset = target && target.offset();
        if(!offset)
        {
            return;
        }
        $('html, body').animate({scrollTop :(offset.top-100), scrollLeft : (offset.left-50)});
        target.animate({width : '400px'})
    }
};

$(document).ready(function(){

    var style_ = function(func){
        return 'margin-left:' + ((parseInt(func.level) - 10)*40)+"px";
    };

    var render_ = function(data){
        var ejsOptions = {
            open : '{%',
            close : '%}'
        };
        var main = ejs.compile($('#mainTemplate').html(), ejsOptions);
        $('#main').html(main({
            _ : data,
            fn: {
                style : style_,
                funcName : ejs.compile($('#funcTemplate').html(), ejsOptions),
            }
        }));
    };

    var init_ = function(){
        tbc.query_data.query = $('#query').val();
        $.ajax({
            url : '/debug/'+preRunId,
            data : tbc.query_data
        }).done(function(data){
            render_(data);
        });
        return false;
    };
    init_();

    $('#fquery').submit(function(e){
        e.preventDefault();
    });

});
