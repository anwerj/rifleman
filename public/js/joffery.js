$.fn.joffery = function(opt) {
    var _o = function(el){
         var t = this;
         el.prop('contentEditable',true);
         el.addClass('jfy_container');
         el.on('click',function(){
             console.log('ss');
             if(t.isNew()){
                el.append(t.getNewFilter());
             }
         });

         this.isNew = function () {
             return true;
         }

         this.getNewFilter = function () {
             return '<div class="input-group jfy_filter">\
             <select class="jfy_o"></select>\
             <select class="jfy_f"></select>\
             </div>';
         }
    };

    this.each(function () {
        var o = new _o($(this));
    });

}
