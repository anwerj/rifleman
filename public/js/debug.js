var splunk = {
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
    }
};

var fn = {
    updateQuery : function(query, append){
        var newQuery = append ? $('#query').val()+' '+query : query;
        $('#query').val(newQuery).focus();
    },
    hit : function(path){
        window.location.href = path+'?query='+$('#query').val();
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
                message_(form, 'success',data);
                $('#i'+data.id).removeClass (function (index, className) {
                    return (className.match (/(^|\s)status_\S+/g) || []).join(' ');
                }).addClass('status_'+data.status_code);
            }
        }).fail(function(xhr){
            message_(form, 'danger',xhr.response);
        });
    }

    var render_ = function(data){
        $('#main').html(ejs.render($('#mainTemplate').html(), {
            _ : data,
            fn: {
                item : ejs.compile($('#itemTemplate').html()),
            }
        }));
        $('a.r_').unbind('click').click(function(){
            setTimeout(init_,100);
        });
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
    };

    var init_ = function(){
        var hashed = window.location.hash.slice(1) || '/debug/';
        $.ajax({
            url : hashed,
            data : {query : $('#query').val()}
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
