var ejsOptions = {
    open : '{%',
    close : '%}'
};
var core = {
    url : tbc.splunk_url,
    cls : {
        eid_tsk : {f:'"request.task_id"='},
        eid_req : {f:'"request.request_id"='},
    },
    get : function(eid){
        for(var i in this.cls){
            if(!eid.hasClass(i)){ continue; }
            return this.url+this.cls[i].f+eid.text();
        }
    },
    fn : {
        main : ejs.compile($('#mainTemplate').html(), ejsOptions),
        item : ejs.compile($('#itemTemplate').html(), ejsOptions),
        form : ejs.compile($('#formTemplate').html(), ejsOptions),
    }
};

var fn = {
    updateQuery : function(query, append){
        var newQuery = append ? $('#query').val()+' '+query : query;
        $('#query').val(newQuery).focus();
    },
    hit : function(path){
        window.location.href = path+'?query='+$('#query').val();
    },
    move : function (page) {
        $('#fpage').val(page);
        fn.init_();
    }
};

$(document).ready(function(){

    var message_ = function(to, type, data){
        var map = {
            success : 'Action taken successfully',
            danger : 'Something went wrong'
        }
        var html = $('<div/>',{
            class : 'alert alert-'+type,
            text : map[type]
        });
        $(to).find('.messages').append(html);
        setTimeout(function(){
            html.remove();
        },3000);
    }

    var submit_ = function(form){
        $.ajax({
            url : $(form).attr('action'),
            method : $(form).attr('method'),
            data : $(form).serialize()
        }).done(function(data){
            if(data.id){
                var html = core.fn.item({
                    _  : data,
                    fn : core.fn
                });
                $('#i'+data.id).replaceWith(html);
                binder_();
            }
        }).fail(function(xhr){
            message_(form, 'danger',xhr.response);
        });
    }

    var binder_ = function () {
        $('form.r_').unbind('submit').submit(function(e){
            e.preventDefault();
            submit_(this);
        });
        $('a.qup_').unbind('click').click(function(e){
            fn.updateQuery($(this).data('query'),true);
        });
        $('a.eid_').unbind('hover').hover(function(e){
            if($(this).attr('href')){ return };
            $(this).attr('href', splunk.get($(this))).attr('target','_blank');
        });
    }

    var render_ = function(data){
        $('#main').html(core.fn.main({
            _ : data,
            fn: core.fn
        }));
        binder_();
    };

    fn.init_ = function(){
        var url = '/testcases?' + $('#fquery').serialize();
        $.ajax({
            url : url,
        }).done(function(data){
            render_(data);
        });
        return false;
    };
    fn.init_();

    $('#fquery').submit(function(e){
        e.preventDefault();
    });

});
